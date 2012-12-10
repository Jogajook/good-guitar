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
$document->addStyleSheet(JURI::base() . 'modules/mod_zt_headline/assets/css/zt_featurelist.css');
$document->addScript(JURI::base() . 'modules/mod_zt_headline/assets/js/zt_featurelist.js');
$cssMod 	= "width:".$moduleWidth."px; height: ".$moduleHeight."px;";  
$listwidth = $params->get('zt_featurelist_list_width', 320);
$ThumbWidth = $params->get('thumbWidth');
$ThumbHeight = $params->get('thumbHeight');
$navWidth = $params->get('zt_featurelist_thumbwidth');
$navHeight = $params->get('zt_featurelist_thumbheight');
$itemHeight = $params->get('zt_featurelist_item_height');
$enableDes = $params->get('zt_featurelist_enable_description');
$mainwidth = $params->get('zt_featurelist_main_width');
$eventType = $params->get('zt_featurelist_eventtype', 1);
$pautime = $params->get('timming');
?>    
<div class="feature_list" style="<?php echo $cssMod;?>">
	<ul id="feature_tabs<?php echo $moduleId;?>" class="feature_tabs" style="width: <?php echo $listwidth;?>px;">
		<?php
		$j=0;
		foreach($arySelection as $item) {
			if($item){
			$aryItem = explode("[]",$item); 
			$contentType = $aryItem[0];
			$titleslide = $aryItem[1];
			$img_path = $aryItem[2];
		?>
		<li style="height: <?php echo $itemHeight;?>px;">
			<div class="detail">
				<img src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$navHeight.'&amp;w='.$navWidth; ?>">
				<span><?php echo $titleslide; ?></span>
			</div>
		</li> 
		<?php
			}
		$j++;
		}
		?> 
	</ul>
	<ul id="feature_output<?php echo $moduleId;?>" class="feature_output" style="width: <?php echo $mainwidth;?>px; height: <?php echo $ThumbHeight;?>px;">
		<?php
		$i=0;
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
		<li style="display: none;" style="width: <?php echo $ThumbWidth;?>px; height: <?php echo $ThumbHeight;?>px;">
			<?php if($linkimg){?>
			<a href="<?php echo $link; ?>"><img src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$ThumbHeight.'&w='.$ThumbWidth; ?>"></a>
			<?php }else{?>
			<img src="<?php echo JURI::root().'modules/mod_zt_headline/timthumb.php?src='.JURI::root().$img_path.'&amp;h='.$ThumbHeight.'&amp;w='.$ThumbWidth; ?>">
			<?php }?> 
			<?php if($enableDes){?>
			<p>
				<?php echo $introtext; ?></br>
				<a href="<?php echo $link; ?>"><?php echo JText::_('Read More');?></a>
			</p>
			<?php }?>
		</li> 
		<?php
			}
		$i++;
		}
		?> 
	</ul>
</div>
<script type="text/javascript">  
	Zoo(document).ready(function() {
		Zoo.featureList(
			Zoo("#feature_tabs<?php echo $moduleId;?> li"),
			Zoo("#feature_output<?php echo $moduleId;?> li"), {
				start_item : 0,
				transition_interval: <?php echo $pautime;?>,
				eventType: <?php echo $eventType;?>
			}
		);
	}); 
</script>