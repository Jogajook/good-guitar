<?php
/**
 * @package ZT News Module for Joomla! 2.5.0
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/

// no direct access
defined("_JEXEC")or die("Restrict accesses");

//Get modules helper
require_once(dirname(__FILE__).DS.'helper.php');

global $moduleId;

$moduleId 		= $module->id;
$categories 	= (array)$params->get('catid', array());
$k2categories 	= (array)$params->get('k2_catid', array());
$jvNews 		= new modZTNewsHelper($params);
$templateType 	= $params->get('template_type');

if($templateType == 'horizon') {
	if(count($categories) || count($k2categories)) {		
		$listSections 	= $jvNews->getAllCategories();
		$imgAlign 		= $params->get('img_align');
		
		require(JModuleHelper::getLayoutPath('mod_zt_news', $templateType . DS . 'default'));
	}
}
else {
	$listSections = $jvNews->getCategoryByVTemp();
	
	if($listSections) require(JModuleHelper::getLayoutPath('mod_zt_news', $templateType . DS . 'default'));
}