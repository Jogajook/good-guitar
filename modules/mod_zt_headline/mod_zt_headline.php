<?php
/**
 * @package ZT Headline module 
 * @author http://www.ZooTemplate.com
 * @copyright(C) 2010- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/

defined('_JEXEC') or die('Restricted access');// no direct access
require_once(dirname(__FILE__).DS.'helper.php');

global $moduleId, $cache_time, $layoutStyle; 
$moduleId 		= $module->id;
$cache_time		= $params->get('cache_thumb');
$layoutStyle 	= $params->get('layout_style');
$slideDelay 	= trim($params->get('timming'));
$jvCommon 		= new modZTHeadlineCommonHelper($params);
$linkimg = $params->get('link_limage');
$moduleWidth 			= $params->get('moduleWidth', 960);
$moduleHeight		 	= $params->get('moduleHeight', 960);
$intro_length = intval($params->get( 'intro_length', 50) ); 
$slideSelection 		= $params->get('text_data','');
if($slideSelection){
	$arySelection = explode("||", $slideSelection); 
}
if(count($arySelection))require(JModuleHelper::getLayoutPath('mod_zt_headline',$layoutStyle));
 
?>