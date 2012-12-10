<?php
/**
 * @package ZT Headline module 
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2010- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools');

$document 	= JFactory::getDocument();
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/jquery-1.7.1.min.js'); 
$document->addScriptDeclaration('var Zoo = jQuery.noConflict();'); 
$document->addStyleSheet(JURI::base() . 'modules/mod_zt_headline/assets/css/zt_carousel.css');
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/jquery.roundabout.js');
$cssMod 	= "width:".$moduleWidth."px; height: ".$moduleHeight."px;";
$ThumbWidth = $params->get('thumbWidth');
$ThumbHeight = $params->get('thumbHeight');
$duration = $params->get('zt_carousel_duration', 3000); 
$autorun = $params->get('zt_carousel_autorun', 1);
$animSpeed = $params->get('trans_duration');
$pautime = $params->get('timming');
?>
<div id="zt_carousel">
	<ul id="myRoundabout_<?php echo $moduleId;?>" class="roundabout-holder" style="<?php echo $cssMod;?> display: block; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; position: relative; z-index: 100; ">
		<?php
		$j=0;
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
		<li class="roundabout-moveable-item" style="width: <?php echo $ThumbWidth;?>px; height: <?php echo $ThumbHeight;?>px;">
			<div class="roundabout-moveable-inner">
				<div class="roundabout-image">
					<img src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$ThumbHeight.'&amp;w='.$ThumbWidth; ?>" alt="<?php echo $title; ?>"/>
				</div>
				<div class="roundabout-description">
					<p class="caption">
						<a href="<?php echo $link; ?>"><?php echo $title; ?></a>
					</p>
				</div>
				<div class="roundabout-shadow">
					<img src="<?php echo JURI::root();?>modules/mod_zt_headline/assets/images/zt_carousel/shadow.png" alt=""/>
				</div>
			</div>
		</li>
		<?php
			}
		$j++;
		}
		?> 
	</ul>
	<ul id="pagenav<?php echo $moduleId;?>" class="pagenav">
		<?php
		$i=0;
		foreach($arySelection as $item) {
		if($item){ 
		?>
			<li class="items"><?php echo $i;?></li>
		<?php
			}
			$i++;
		}?>
	</ul>
</div>
<script type="text/javascript"> 
	Zoo('ul#myRoundabout_<?php echo $moduleId;?>').hide();        
	Zoo(window).bind('load', function($) {
		var interval;
		Zoo('ul#myRoundabout_<?php echo $moduleId;?>:hidden').fadeIn(500);                    
		Zoo('ul#myRoundabout_<?php echo $moduleId;?>').roundabout({
			pagenav: Zoo('ul#pagenav<?php echo $moduleId;?>'),
			duration: <?php echo $animSpeed;?>, 
			minOpacity: 1.0,
			minScale: 0.5,
			reflect: false 
		}).hover(
			function() { 
				clearInterval(interval);
			},
			function() {
				<?php if($autorun){?>
				interval = startAutoPlay();
				<?php }?>
			}
		);
		<?php if($autorun){?>
		interval = startAutoPlay();
		<?php }?>
		function startAutoPlay() {
			return setInterval(function() {
				Zoo('ul#myRoundabout_<?php echo $moduleId;?>').roundabout("animateToNextChild");
			}, <?php echo $pautime;?>);
		}
	});
</script> 