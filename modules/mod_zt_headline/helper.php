<?php
/**
 * @package ZT Headline module 
 * @author http://www.ZooTemplate.com
 * @copyright(C) 2010- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

$user 		= &JFactory::getUser();
$db 		= &JFactory::getDBO();
$menu 		= &JSite::getMenu();
$document	= &JFactory::getDocument();
$moduleclass_sfx = $params->get( 'moduleclass_sfx', 0 );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

if(is_dir(JPATH_SITE.DS.'components'.DS.'com_k2'))
{
  require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
  require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
}
 
class modZTHeadlineCommonHelper
{
	function __construct()
	{ 
	} 
	function getSlideContent($content_id)
	{ 
		$db         =& JFactory::getDBO();
		$user       =& JFactory::getUser();
		$userId     =(int) $user->get('id'); 
		$aid        = $user->get('aid', 0); 
		$nullDate   = $db->getNullDate(); 
		$date =& JFactory::getDate();
		$now = $date->toMySQL(); 
		if($content_id){ 
			$where      = 'a.state = 1'
						. ' AND a.id='.$content_id
						. ' AND( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
						. ' AND( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
						; 
			$query = 'SELECT a.*,a.id as key1, cc.id as key2, cc.title as cat_title, ' .
					 ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
					 ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
					 ' FROM #__content AS a' .             
					 ' INNER JOIN #__categories AS cc ON cc.id = a.catid' . 
					 ' WHERE '. $where .'' .
					 ' AND cc.published = 1'; 
			$db->setQuery($query);
			$row = $db->loadObject();  
			return $row;
		}  
	}  
	/*
	 * Function get items of k2 component
	 * @Created by ZooTemplate
	 */		 
	function getItemsByK2($k2_id){
		$app = &JFactory::getApplication();
		$db	=& JFactory::getDBO();    
		$date = & JFactory::getDate();
		$user = & JFactory::getUser();
		$aid = $user->get( 'aid', 0 );
		$now = $date->toMySQL(); 
		$jnow = &JFactory::getDate(); 
		$nullDate = $db->getNullDate();
		
		if($k2_id){
			$query = "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams 
				FROM #__k2_items as i 
				LEFT JOIN #__k2_categories c ON c.id = i.catid 
				WHERE i.published = 1 ";
	$query .= " AND i.access IN(".implode(',', $user->authorisedLevels()).") ";
	$query .= " AND i.trash = 0 AND c.published = 1 ";
	$query .= " AND c.access IN(".implode(',', $user->authorisedLevels()).") ";
	$query .= " AND c.trash = 0 
				AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) 
				AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." ) 
				AND i.id={$k2_id}";
				if($app->getLanguageFilter()) {
					$languageTag = JFactory::getLanguage()->getTag();
					$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
				} 
				$db->setQuery($query);
				$row = $db->loadObject();
			 
			return $row;
		}		
	}
	function introContent( $str, $limit = 100,$end_char = '&#8230;' ) {
		if(trim($str) == '') return $str;
		// always strip tags for text
		$str = strip_tags($str);
		preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/', $str, $matches);		
		if(strlen($matches[0]) == strlen($str))$end_char = '';
		return rtrim($matches[0]).$end_char;
	} 
}  
?>