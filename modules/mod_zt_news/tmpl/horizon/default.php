<?php 
/**
 * @package ZT News Module for Joomla! 2.5
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license JS files are GNU/GPL
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'default.css', 'modules/mod_zt_news/assets/css/');

$columns   = ($params->get('columns', 2) > count($listSections)) ? count($listSections) : $params->get('columns', 2);
$seperator = 1;
$listItems = $jvNews->getItemsByCatId();
$lead 	   = (int)$params->get('no_intro_items',1);
$lead	   = (count($listItems) <= $lead) ? count($listItems) : $lead;

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
	<div class="jv-frame-cat"> 
		<div class="jv-category">
			<div class="jvpadding">
				<!--Title Block-->
				<ul class="jv-title-category">
					<?php for($i = 0; $i < count($listSections); $i++) { ?>
					<li <?php echo (!$i) ? 'class="jv-firstitem"' : ''; echo ($i == count($listSections) - 1) ? 'class="jv-lastitem"' : ''; ?>>
						<a href="<?php echo $listSections[$i]->link ?>">
							<span class="jv-title-category"><?php echo $listSections[$i]->title ?></span>
						</a>
					</li>
					<?php } ?>
				</ul>
				<div class="cls"></div>
				
				<!--Lead block-->
				<ul class="lead">
					<?php for($j = 0; $j < $lead; $j++) : ?>
						<li class="jv-article-title">
							<h4>
								<a href="<?php echo $listItems[$j]->link; ?>" title="<?php echo $listItems[$j]->title; ?>">
									<?php echo $listItems[$j]->title; ?>
								</a>
							</h4>
							
							<?php if($listItems[$j]->thumb != '' && $params->get('is_image',1) == 1) { ?>
							<a href="<?php echo $listItems[$j]->link; ?>" title="<?php echo $listItems[$j]->title; ?>">
								<img src="<?php echo $listItems[$j]->thumb; ?>" alt="<?php echo $listItems[$j]->title; ?>" 
								title="<?php echo $listItems[$j]->title; ?>" 
								class="<?php echo ($imgAlign == "left") ? "jv-sectcont-thumb-left" : "jv-sectcont-thumb-right"; ?>" />
							</a>
							<?php } ?>
			
							<?php if($listItems[$j]->introtext != false) { ?>
								<p class="jv-sectcont-introtext"><?php echo ($listItems[$j]->introtext); ?></p>
							<?php } ?>
							
							<?php if($params->get('show_readmore') == 1) { ?>
							<p class="jv-news-readmore">
								<a class="readmore" href="<?php echo $listItems[$j]->link; ?>"><?php echo JTEXT::_('NEWS READ MORE'); ?></a>
							</p>
							<?php } ?>
						</li>
					<?php endfor; ?>
				</ul>
								
				<?php if($lead < count($listItems)) { ?>
				<p class="more_link"><?php echo JTEXT::_('NEWS MORE LINK'); ?></p>
					<ul class="article-item clearfix">
						<?php for($j = $lead; $j < count($listItems); $j++) { ?>
							<li>
								<a href="<?php echo $listItems[$j]->link; ?>"><?php echo $listItems[$j]->title; ?></a>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
		</div> 
	</div>   
	<div class="clearfix"></div>
</div>