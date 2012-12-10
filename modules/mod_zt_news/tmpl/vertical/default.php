<?php 
/**
 * @package ZT News Module for Joomla! 2.5
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license JS files are GNU/GPL
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools');
$document = &JFactory::getDocument();
$document->addStyleSheet("modules/mod_zt_news/assets/css/vertical.css");
$document->addScript("modules/mod_zt_news/assets/js/jv_news.js");

$urlAjax 			= JURI::base().'modules/mod_zt_news/ajax.php';
$slideBarLeft 		= "margin-left:".($params->get('image_width'))."px";
$slideMaskWidth 	= "width:".($params->get('image_width'))."px";
$slideMaskHeight 	= "height:".($params->get('image_height'))."px";
$moduleId 			= $module->id;
?>
<script type="text/javascript">
window.addEvent('domready',function() {	
<?php foreach($listSections as $item) { ?>
	var jvNews3 = new JVNews({
		id:'<?php echo $item->id; ?>',
		moduleId:'<?php echo $module->id; ?>',
		urlAjax:'<?php echo $urlAjax; ?>',
		minItem:'<?php echo $params->get('v_min_item'); ?>',
		noHeadline:'<?php echo $params->get('v_no_latest_item'); ?>',
		noLink:'<?php echo $params->get('v_no_link_item'); ?>'
	});
<?php } ?>	
});
</script>
<?php
	$columns   = ($params->get('columns', 2) > count($listSections)) ? count($listSections) : $params->get('columns', 2);
	$seperator = 1;
	
	switch($columns)
	{
		case '1':
			$width = '100';
			break;
		case '2':
			$width = '49';
			break;
		case '3':
			$width = '32.9';
			break;
		case '4':
			$width = '24.5';
			break;
		case '5':
			$width = '19.5';
			break;
		default:
			$width = '49';
	}
?>
<div style="display: none;"><a href="http://www.joomvision.com" title="Joomla Templates">Joomla Templates</a> and Joomla Extensions by JoomVision.Com</div>
<div class="jv_news_wrap">
<?php
	for($i = 0; $i < count($listSections); $i++)
	{
		$amountName 	= "amount" . $listSections[$i]->id;
		$amountCookie 	= $jvNews->getCookie($amountName); 
		$noHeadLine 	= $noLink = '';
		
		if($amountCookie) {
			$aryAmount 	= explode(',', $amountCookie);
			$noHeadLine = $aryAmount[0];
			$noLink 	= $aryAmount[1];
		}

		$listLatestItem = $jvNews->getLatestItemByCatId($listSections[$i]->id);
		
		if(count($listSections) > 0)
		{
?>
	<?php if($seperator == 1) { ?>
		<div class="jv-frame-cat">
	<?php } ?>
		<div class="jv-category" style="width: <?php echo $width; ?>%">
			<div class="jvpadding">
				<div class="jv_news_cat_main">
					<div class="jv_news_cat_r">
						<div class="jv_news_bc">
							<div class="jv_news_bl">
								<div class="jv_news_br">
									<div class="jv_news_tc">
										<div class="jv_news_tl">
											<div class="jv_news_tr">
												<!--News Title Block-->
												<div id="news_title<?php echo $listSections[$i]->id; ?>" class="news_title">
													<div class="title expand">
														<a href="<?php echo $listSections[$i]->link; ?>"><?php echo $listSections[$i]->title; ?></a>
													</div>
													<div class="news_action">
														<div id="news_edit<?php echo $listSections[$i]->id; ?>" class="news_edit">&nbsp;</div>
													</div>
												</div>
												
												<!--News Section Block-->
												<div id="news_section<?php echo $listSections[$i]->id; ?>" class="news_section">
													<div class="news_option" id="news_option<?php echo $listSections[$i]->id; ?>">
														<?php $listAllCat = $jvNews->getCategoryChilds($listSections[$i]->id, true); ?>
														<form action="" method="post">
															<ul class="option_category">
															<?php
																$secName 	= 'sec' . $listSections[$i]->id;
																$catCookie 	= $jvNews->getCookie($secName); 
																$aryCat 	= array();
																if($catCookie) {
																	$aryCat = explode(',', $catCookie); 
																}
																
																foreach($listAllCat as $item)
																{
																	$getCate = $jvNews->getCategoryDetail($item); 
															?>		
																<li> 
																	<input type="checkbox" name="section" id="cat<?php echo $getCate->id; ?>" 
																	value="<?php echo $getCate->id; ?>" <?php if(in_array($getCate->id,$aryCat)) echo "checked"; ?> />
																	<label for="cat<?php echo $getCate->id; ?>"><?php echo $getCate->title;?></label>
																</li>
															<?php } ?>
															</ul>
															
															<hr />
															<input type="hidden" name="section<?php echo $listSections[$i]->id;?>" id="section<?php echo $listSections[$i]->id; ?>" 
															value="<?php echo $listSections[$i]->id;?>" />
															<table class="input_amount">
																<tr>
																	<td class="params_label">Amount of short articles:</td>
																	<td class="params_input">
																		<select id="no_article<?php echo $listSections[$i]->id; ?>" name="no_aritcle">
																			<?php 
																				for($j=1; $j <= 5; $j++)
																				{
																					if($noHeadLine != '')
																					{
																						if($j == $noHeadLine) echo "<option value='$j' selected>$j</option>";
																						else echo "<option value='$j'>$j</option>";
																					}
																					else
																					{
																						if($j == 3) echo "<option value='$j' selected>$j</option>";
																						else echo "<option value='$j'>$j</option>";
																					}
																				} 
																			?>
																		</select> 
																	</td>
																</tr>
																<tr>
																	<td class="params_label">Amount of articles links:</td>
																	<td class="params_input">
																	<select id="no_articles_links<?php echo $listSections[$i]->id; ?>" name="no_articles_links">
																		<?php 
																		for($j = 1; $j <= 10; $j++)
																		{
																			if($noLink != '')
																			{
																				if($j == $noLink) echo "<option value='$j' selected>$j</option>";
																				else echo "<option value='$j'>$j</option>";
																			}
																			else
																			{
																				if($j == 5) echo "<option value='$j' selected>$j</option>";
																				else echo "<option value='$j'>$j</option>";
																			}
																		} 
																		?>
																	</select>
																</td>
															</table>
															<p class="wrap_button">
																<input id="save<?php echo $listSections[$i]->id; ?>" type="button" value="Save" />
																<input type="button" id="cancel<?php echo $listSections[$i]->id; ?>" value="Cancel" />
																<input type="button" id="reset<?php echo $listSections[$i]->id; ?>" value="Reset" />
															</p>
														</form>
													</div>
												
													<div class="news_slidecontent" id="news_slidecontent<?php echo $listSections[$i]->id; ?>">
														<?php 
															if($noHeadLine !='') $lead = $noHeadLine;
															else $lead = (int) $params->get('v_no_latest_item', 3); 
															
															if(count($listLatestItem) <= $lead) $lead1 = count($listLatestItem);
															else $lead1 = $lead;
														?>
														<div id="news_mask<?php echo $listSections[$i]->id; ?>" class="news_mask" style="<?php echo $slideMaskHeight.";".$slideMaskWidth; ?>">			
															<?php for($k = 0; $k < $lead1; $k ++) { ?>
																<div class="news_img <?php if($k!=0) {echo "block";}else {echo "active";}?>">
																	<?php if($listLatestItem[$k]->thumb) {?>
																		<a href="<?php echo $listLatestItem[$k]->link; ?>" title="<?php echo $listLatestItem[$k]->title; ?>">
																			<img src="<?php echo $listLatestItem[$k]->thumb; ?>" />
																		</a>
																	<?php }?>
																</div>
															<?php }?>
														</div>
														<div id="news_bar<?php echo $listSections[$i]->id; ?>" class="news_bar" style="<?php echo $slideBarLeft;?>">
															<?php for($k = 0; $k < $lead1; $k ++) {?>
																<a href="<?php echo $listLatestItem[$k]->link; ?>" class="item <?php if($k==0) echo "selected"; ?>">
																	<span class=""><?php echo $listLatestItem[$k]->title; ?></span>
																</a>
															<?php } ?>
														</div>
													</div>
												
													<div id="wrap_news_more<?php echo $listSections[$i]->id; ?>" class="wrap_news_more">
														<?php if($lead < count($listLatestItem)) { ?>
															<ul class="news_more">
																<?php for($k= $lead; $k < count($listLatestItem); $k++) {?>
																	<li><a href="<?php echo $listLatestItem[$k]->link; ?>"> <?php echo $listLatestItem[$k]->title; ?></a></li>
																<?php }?>
															</ul>
														<?php } ?>
													</div>
												
													<div id="news_content_category<?php echo $listSections[$i]->id; ?>" class="news_content_category">	
														<?php 
															$secContent 	= "content" . $listSections[$i]->id;
															$contentCookie 	= $jvNews->getCookie($secContent); 
															
															if($contentCookie)
															{
																$aryCat = explode(',', $contentCookie);
																$html   = "";
																if(count($aryCat))
																{
																	foreach($aryCat as $item)
																	{
																		$catId 	= substr($item,0,strpos($item, '_'));	
																		$noItem = substr($item,strpos($item, '_') + 1);		
																		$html	.= $jvNews->getContentByCatId($catId, $noItem);	
																	}
																}
																echo $html; 
															}
														?>
													</div>
												</div>
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php if($seperator == $columns) { ?>
		</div>
	<?php } ?>
<?php
		$seperator = ($seperator == $columns) ? 1 : ($seperator ++);
		}
	}
?>
	<div class="clearfix"></div>
</div>