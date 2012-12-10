<?php
/**
 * @package ZT Accordion Menu module for Joomla! 2.5
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
 
defined('_JEXEC') or die('Restricted access');
require_once(dirname(__FILE__).DS.'helper.php');

$path 			= JModuleHelper::getLayoutPath('mod_zt_accordion_menu','default');
$actItemId 		= JRequest::getVar ('Itemid', 1);
$isActiveExpand = $params->get('is_exppand_active');
$jvAMenuHelper 	= new modZTAMenuHelper($module->id);
$pids			= modZTAMenuHelper::getAllParentsId($actItemId);

if(file_exists($path))
{
     require($path);
}