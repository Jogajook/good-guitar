<?php
/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
define('DS', DIRECTORY_SEPARATOR);
$rootFolder 		= explode(DS, dirname(__FILE__));
//current level in diretoty structure
$currentfolderlevel = 2;
array_splice($rootFolder,-$currentfolderlevel);
$base_folder 		= implode(DS, $rootFolder);

if(is_dir($base_folder.DS.'libraries'.DS.'joomla'))
{
	define( '_JEXEC', 1 );
	define('JPATH_BASE', implode(DS, $rootFolder));
	
	require_once(JPATH_BASE .DS.'includes'.DS.'defines.php');
	require_once(JPATH_BASE .DS.'includes'.DS.'framework.php');
	require_once(JPATH_BASE .DS.'libraries/joomla/factory.php');	
	
	$mainframe 	= &JFactory::getApplication('site');
	$mainframe->initialise();		
	$action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$moduleId 	= isset($_REQUEST['moduleId']) ? $_REQUEST['moduleId'] : '';
	$isMerge 	= JRequest::getVar('isMerge', '');
	
	require_once 'zttwiter_ajax.php';
	
	$twitterAjaxHelper = new ZTTwitterAjaxHelper(JURI::base(), $moduleId);
	if($isMerge == 1)
	{					
		$html 		= $twitterAjaxHelper->callMethod('renderMergeHome', '');		
		$jsonData 	= array('twitter'=>$html);
		echo json_encode($jsonData);
	}
	else
	{
		
		$html 		= $twitterAjaxHelper->callMethod('renderSplitHome', '');
		$jsonData 	= array('twitter'=>$html);
		echo json_encode($jsonData);
	}	
}