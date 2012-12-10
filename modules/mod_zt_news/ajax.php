<?php
/**
 * @package ZT News Module for Joomla! 2.5.0
 * @author http://www.ZooTemplate.com
 * @copyright(C) 2011- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/

define('DS', DIRECTORY_SEPARATOR);
$rootFolder = explode(DS,dirname(__FILE__));

//current level in diretoty structure
$currentfolderlevel = 2;
array_splice($rootFolder, -$currentfolderlevel);
$base_folder = implode(DS,$rootFolder);

if(is_dir($base_folder.DS.'libraries'.DS.'joomla'))
{
	define('_JEXEC', 1);
	define('JPATH_BASE',implode(DS,$rootFolder));
	
	require_once(JPATH_BASE .DS . 'includes' . DS . 'defines.php');
	require_once(JPATH_BASE .DS . 'includes' . DS . 'framework.php');
	require_once(JPATH_BASE .DS . 'libraries/joomla/factory.php'); 
	
	$mainframe = &JFactory::getApplication('site');
	$mainframe->initialise();
	
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');	
	require_once(JPATH_BASE .DS.'configuration.php'); 
	require_once(JPATH_SITE . DS . 'modules' . DS . 'mod_zt_news'. DS . 'ztnews_ajax.php');
	
	$fcatId 	= isset($_REQUEST['fcatId']) ? $_REQUEST['fcatId'] : '';	
	$strCatId 	= isset($_REQUEST['catId']) ? $_REQUEST['catId'] : '';
	$moduleId 	= isset($_REQUEST['moduleId']) ? $_REQUEST['moduleId'] : '';
	$noHeadLine = isset($_REQUEST['noHeadline']) ? $_REQUEST['noHeadline'] : '';
	$noLink 	= isset($_REQUEST['noLink']) ? $_REQUEST['noLink'] : '';
	$action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$ajaxHelper = new ZTNewsAjaxHelper($moduleId);
	
	$str = $strItemOfCat = $strMoreLink = '';
	
	$ajaxHelper->renderHeadLine($fcatId, $noHeadLine, $noLink, $str, $strMoreLink); 
	
	switch($action)
	{
		case "save":									
			if($strCatId)
			{
				$strCatId = substr($strCatId, 0, strrpos($strCatId, ","));
				$aryCatId = explode(",", $strCatId);
				
				foreach($aryCatId as $item) {
					$strItemOfCat.= $ajaxHelper->getContentByCatId($item);
				}
			}
			
			$aryJson = array('fcatId'=>$fcatId,'strItem'=>$strItemOfCat,'strHeadLine'=>$str,'strMoreLink'=>$strMoreLink);
			echo json_encode($aryJson);
		break;
		
		case "reset":
			$aryJson = array('fcatId'=>$fcatId, 'strHeadLine'=>$str, 'strMoreLink'=>$strMoreLink);
			echo json_encode($aryJson);
		break;	
	}
}
?>