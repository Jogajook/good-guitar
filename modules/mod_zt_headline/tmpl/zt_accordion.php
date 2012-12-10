<?php 
/**
 * @package ZT Headline module 
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2010- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/
JHTML::_('behavior.mootools'); 
$document 	= JFactory::getDocument();
$document->addStyleSheet('http://fonts.googleapis.com/css?family=Yanone Kaffeesatz');
$document->addStyleSheet(JURI::base() . 'modules/mod_zt_headline/assets/css/zt_accordion.css'); 
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/jquery-1.7.1.min.js');
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/plugins.js');
$document->addScriptDeclaration('var Zoo = jQuery.noConflict();');
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/zt_accordion.js');
$ThumbWidth = $params->get('thumbWidth');
$ThumbHeight = $params->get('thumbHeight');
$itemdisplay 	= $params->get('zt_accordion_no_item');
$itemExpand 	= $params->get('zt_accordion_item_expand', 350);
$box_position = $params->get('zt_carousel_boxdesc', 'left');
$box_width = $params->get('zt_accordion_box_width');
$itemWidth = $moduleWidth/(count($arySelection)-1);
$moduleWidth = $moduleWidth+2;
$animSpeed = $params->get('trans_duration');
?>
<script type="text/javascript"> 
	Zoo(document).ready(function() {
		Zoo('#zt_remi<?php echo $moduleId;?>').ZT_Accordion({
		  'openDim': <?php echo $itemExpand;?>,
		  'closeDim': <?php echo $itemWidth;?>,
		  'effect': 'easeOutCubic', 
		  'duration': <?php echo $animSpeed;?>, 
		  'openItem': null,
		  'position':'horizontal',
		  'fadeInTitle': true
		});
	});  
</script> 
<div id="zt_remi<?php echo $moduleId;?>" class="zt_accdisplay" style="width: <?php echo $moduleWidth;?>px; height: <?php echo $moduleHeight;?>px;">
	<?php
	$total = count($arySelection);
	$i = 1;
	foreach($arySelection as $item) {
		if($item){
			$aryItem = explode("[]",$item);
			$contentType = $aryItem[0];
			$title = $aryItem[1];
			$img_path = $aryItem[2];
			if($contentType=='content'){
				$content_id = $aryItem[3];
				$content_obj = $jvCommon->getSlideContent($content_id);
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($content_obj->slug, $content_obj->catslug));
				$introtext = $jvCommon->introContent($content_obj->introtext, $intro_length);
			}else if($contentType=='k2'){
				$k2_id = $aryItem[3];
				$k2content = $jvCommon->getItemsByK2($k2_id); 
				$link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($k2content->id.':'.urlencode($k2content->alias), $k2content->catid.':'.urlencode($k2content->categoryalias))));
				$introtext = $jvCommon->introContent($k2content->introtext, $intro_length);
			}else{
				$link = $aryItem[4];
				if($aryItem[3]){
					$str_custom = str_replace('[ztlt]', '<', str_replace('[ztgt]', '>', str_replace('[ztquote]', '\'', str_replace('[ztamp]', '&', str_replace('[ztdquote]', '"', $aryItem[3]))))); 
					$introtext = $jvCommon->introContent($str_custom, $intro_length); 
				}
			} 
	?>
	<div class="item <?php if($i==1){ echo "first"; }else if($i==($total-1)){ echo "last"; }?>">
		<div class="preview" style="width: <?php echo $itemExpand;?>px; height: <?php echo $moduleHeight;?>px;">
			<div class="accordion-info" style="height: <?php echo $moduleHeight;?>px;">
				<?php if($img_path){?> 
					<?php if($linkimg){?>
						<a href="<?php echo $link;?>" title="<?php echo $title; ?>"><img class="accordion-image" src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$ThumbHeight.'&amp;w='.$ThumbWidth; ?>" alt="" title="<?php echo $title; ?>"/></a>
					<?php }else{?>
						<img class="accordion-image" src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$ThumbHeight.'&amp;w='.$ThumbWidth; ?>" alt="" title="<?php echo $title; ?>"/>
				<?php }
				}?>
				<a class="accordion-des <?php echo $box_position;?>" style="width: <?php echo $box_width;?>px;" href="<?php echo $link;?>">
					<h3><?php echo $title; ?></h3>
					<p><?php echo $introtext ; ?></p>
				</a> 
			</div> 
			<div class="bkg-shadow">
				<span><?php echo $title; ?></span>
			</div>			
		</div> 
	</div>
	<?php
		}
	$i++;	
	}?>
</div>