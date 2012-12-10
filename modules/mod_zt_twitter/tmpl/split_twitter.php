<?php
/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
$css 			= JURI::base().'modules/mod_zt_twitter/assets/css/zt_twitter.css';
$urlAjax 		= JURI::base().'modules/mod_zt_twitter/request_ajax.php';
$document 		= &JFactory::getDocument();
$document->addStyleSheet($css);
$wrapAccordId 	= 'wrap_twitter_accord'.$module->id;       
$noTwitter 		= $params->get('no_twitter');
//$js 			= JURI::base().'modules/mod_zt_twitter/assets/js/zt_accordion.js';
$jsSplit 		= JURI::base().'modules/mod_zt_twitter/assets/js/zt_split_twitter.js';
$document->addScript($js);
$document->addScript($jsSplit);
$isAccord		= 0;
if($params->get('show_avatar_header') == 1 || $params->get('show_username_header') == 1) $isAccord = 1;
$strFollow 		= $params->get('usernames');
$aryUser 		= explode(',',$strFollow);
?>
<script type="text/javascript">
var urlAjax 		= '<?php echo $urlAjax;?>';
var moduleId		= '<?php echo $module->id;?>';
var wrapAccordId	= '<?php echo $wrapAccordId;?>';
var noTwitter		= '<?php echo $noTwitter;?>';
var isAccord		= <?php echo $isAccord;?>;
var twitterWrap		= 'jv_wrap_twitter<?php echo $module->id;?>';
var isMerge			= <?php echo $params->get('merge_twitter');?>;

window.addEvent('domready',function(){   
    new ZTSplitTwitter(urlAjax, moduleId, wrapAccordId, noTwitter, isAccord, twitterWrap, isMerge);          
});
</script>
<div style="display: none;"><a href="http://www.joomvision.com" title="Joomla Templates">Joomla Templates</a> and Joomla Extensions by JoomVision.Com</div>

<div id="jv_twitter_follow<?php echo $module->id; ?>" class="twitter_follow">
	<?php foreach($aryUser as $item ) {?>
	<div class="jv_twit_follow">Follow&nbsp;<a href="http://twitter.com/<?php echo $item; ?>"><?php echo $item;?></a> on twitter</div>
	<?php } ?>
</div>
<div class="jv_wrap_twitter twitter-ajax-loading" id="jv_wrap_twitter<?php echo $module->id; ?>"></div>