<?php
/**
 * @package ZT Accordion Menu module for Joomla! 2.5
 * @author http://www.zootemplate.com
 * @copyright (C) 2010- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/

jimport('joomla.html.parameter');

class modZTAMenuHelper
{
	var $moduleId;	
	var $aryMenuItem = array();
	
	function __construct($moduleId)
	{
		$this->moduleId = $moduleId;		
	}
	
	/*
	 * Function show menu from menutype
	 * @Created by ZooTemplate
	 */
	 
	function showMenu($params, $itemId)
	{
		$menu 		= & JSite::getMenu();
		$rows 		= $menu->getItems('menutype', $params->get('menutype'));
		$children 	= array();
		$user		= JFactory::getUser();
		
		if(is_array($rows) && count($rows))
		{
			foreach($rows as $v)
			{
				if($user->id == 0 && $v->access == 0)
				{
					$pt 			= $v->parent_id;
					$list 			= @ $children[$pt] ? $children[$pt] : array();				
					array_push($list, $v);
					$children[$pt] 	= $list;
				}
				else
				{
					$pt 			= $v->parent_id;
					$list 			= @$children[$pt] ? $children[$pt] : array();				
					array_push($list, $v);
					$children[$pt] 	= $list;
				}
			}
		}
		
		if($params->get('event_type') == 0)
		{
			$this->mosHoverEventRecurseMenu(1, 0, $children, $params, $itemId);
		}
		else
		{
			echo $this->mosClickEventRecurseMenu(1, 0, $children, $params, $itemId, 1);			
		}
	}
	//End function

	/*
	 * Function check menu whether contain child menu
	 * @Created by ZooTemplate
	 */
	 
	function haveChidren($itemId)
	{
		$db 	= JFactory::getDBO();
		$sql 	= "SELECT COUNT(*) AS total FROM #__menu WHERE parent_id=".$itemId." AND parent_id >1  AND published = 1";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	//End function

	/*
	 * Function get menu and all child of menu using recurse function
	 * @Created by ZooTemplate
	 */
	 
	function mosHoverEventRecurseMenu($id, $level, &$children, &$params, $id1, $isAccept=1)
	{
		$active_id	= JRequest::getVar("Itemid");
		$str 		= $this->limitMenu($params, $level);
		$startLevel = $params->get('start_level', 0);
		
		if(@$children[$id])
		{
			if($isAccept == 1)
			{
				if($level == $startLevel || $str)
				{		
					if($this->haveChidren($id)  > 0 || $level == $startLevel)
					{
						if($level == $startLevel)
						{
							echo "<ul class=\"jv_maccordion menu\">";
						}
						else
						{
							echo "<ul class=\"jv_amenu_items jv_menu_content_".($level-1)." menu\">";
						}
					}
					else
					{
						echo "<ul class=\"jv_amenu_items jv_menu_content_".($level-1)." menu\">";
					}
				}
			}
			
			foreach($children[$id] as $row)
			{	
				if($params->get('follow_current_menu')) 
					$condition = (($level == 0 && $row->id == $id1)|| ($level !=0)) ? true :false; 
				else
					$condition = true;
					
				if($condition)
				{
					if($level == $startLevel || $str)
					{											
						$isHasChild = $this->haveChidren($row->id);
						$url 		= $this->getUrlFromMenuItem($row);	
									
						if($url == '') $url = "#";
						$itemId  = "jv_amenu".$this->moduleId."_".$row->id;
						
						$mactive = ($active_id == $row->id) ? "_active" : "";
						
						if($isHasChild)
						{
							echo "<li id=\"$itemId\" class=\"jv_amenu_item$mactive\">
									<div class=\"wrap_link jv_menu_toggler_$level\">
										<a style=\"display:block;\" href=\"$url\">".$row->title."</a>
										<a class=\"trigger\"></a>
									</div>";						
						}
						else
						{
							echo "<li id=\"$itemId\" class=\"jv_amenu_item$mactive last-child\"><a style=\"display:block;\" href=\"$url\">".$row->title."</a>";
						}
						
						echo "<div class=\"clear\"></div>";
						$this->mosHoverEventRecurseMenu($row->id, $level+1, $children, $params, $id1, 1);				
						echo "</li>";
					}
					else
					{
						$this->mosHoverEventRecurseMenu($row->id, $level+1, $children, $params, $id1, 1);
					}
				}
				else
				{
					$this->mosHoverEventRecurseMenu($row->id, 0, $children, $params, $id1, 0);
				}							
			}
			if($isAccept == 1){if($level == $startLevel || $str){ echo "</ul>";}}
		}
	}
	//End function
	
	/*
	 * Function get menu and all child when selecting event click type
	 * @Created by ZooTemplate
	 */
	 
	function mosClickEventRecurseMenu($id, $level, &$children, &$params, $id1, $isAccept = 1)
	{
		$active_id	= JRequest::getVar("Itemid");
		$str 		= $this->limitMenu($params, $level);		
		$startLevel = $params->get('start_level', 0);
		
		if(@$children[$id])
		{
			if($isAccept == 1)
			{ 
				if($level == $startLevel || $str)
				{ 			
					if($this->haveChidren($id)  > 0 || $level == $startLevel)
					{ 			
						if($level == $startLevel)
						{
							echo "<ul class=\"jv_maccordion menu\">";
						}
						else
						{
							echo "<ul class=\"jv_amenu_items jv_menu_content_".($level-1)." menu\">";
						}
					}
					else
					{
						echo "<ul class=\"jv_amenu_items jv_menu_content_".($level-1)." menu\">";
					}	
				}	
			}
			
			foreach($children[$id] as $row)
			{	
				if($params->get('follow_current_menu')) 
					$condition = (($level == 0 && $row->id == $id1)|| ($level !=0)) ? true :false; 
				else
					$condition = true;
					
				if($condition)
				{
					if($level == $startLevel || $str)
					{											
						$isHasChild = $this->haveChidren($row->id);
						$url 		= $this->getUrlFromMenuItem($row);	
									
						if($url == '') $url = "#";
						$itemId  = "jv_amenu".$this->moduleId."_".$row->id;
						
						$mactive = ($active_id == $row->id) ? "_active" : "";
						
						if($isHasChild)
						{
							$active		= '';
							if($row->id == $id1)
								$active = 'id="jv_menu_active_'.$id1.'"';
								
							echo "<li id=\"$itemId\" class=\"jv_amenu_item$mactive\">
									<div class=\"wrap_link\">
										<a style=\"display:block;\" href=\"$url\">".$row->title."</a>
										<a class=\"trigger jv_menu_toggler_$level\" $active></a>
									</div>";						
						}
						else
						{
							echo "<li id=\"$itemId\" class=\"jv_amenu_item$mactive last-child\"><a style=\"display:block;\" href=\"$url\">".$row->title."</a>";
						}
						
						echo "<div class=\"clear\"></div>";
						$this->mosClickEventRecurseMenu($row->id, $level+1, $children, $params, $id1, 1);				
						echo "</li>";
					}
					else
					{
						$this->mosClickEventRecurseMenu($row->id, $level+1, $children, $params, $id1, 1);
					}
				}
				else
				{
					$this->mosClickEventRecurseMenu($row->id, 0, $children, $params, $id1, 0);
				}							
			}
			if($isAccept == 1){ if($level == $startLevel || $str) {	echo "</ul>";} }
		}	
	}		
	//End
		
	/*
	 * Function limit item from start and end level
	 * @Created by ZooTemplate
	 */
	 
	function limitMenu($params,$level)
	{
		$startLevel = $params->get('start_level', 0);		
		$endLevel 	= $params->get('end_level', 0);
		$str 		= '';	
		
		if($startLevel !=0)
		{
			if($endLevel !=0)
			{
				$str = (($level>=$startLevel) && ($level <=$endLevel)) ? true : false;
			}
			else
			{ 
				$str = ($level>=$startLevel) ? true:false;
			}
		}
		else
		{
			if($endLevel!=0) 
				$str = ($level <=$endLevel) ? true : false;
		}
		
		if($str === '') $str = true;		
		$str = ($str) ? $str :false;
		
		return $str;
	}
	//End
	
	
	/*
	 * Function get link from row menu item
	 * @Created by ZooTemplate
	 */
	 
	function getUrlFromMenuItem($row)
	{
		$menu 		= &JSite::getMenu();
		$iParams 	= new JParameter($row->params);
		
		switch($row->type)
		{
			case 'separator':
				return '';
			break;
			case 'url':
				if ((strpos($row->link, 'index.php?') === 0) && (strpos($row->link, 'Itemid=') === false))
				{
					$url = $row->link.'&amp;Itemid='.$row->id;
				}
				else
				{
					$url = $row->link;
				}
			break;
			default :
				$router = JSite::getRouter();
				$url 	= $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$row->id : $row->link.'&Itemid='.$row->id;
			break;	
		}
		
		if($url !='')
		{
			$iSecure = $iParams->def('secure', 0);
			if ($row->home == 1)
			{
				$url = JURI::base();
			}
			elseif (strcasecmp(substr($url, 0, 4), 'http') && (strpos($row->link, 'index.php?') !== false))
			{
				$url = JRoute::_($url, true, $iSecure);
			}
			else
			{
				$url = str_replace('&', '&amp;', $url);
			}
		}		
		return $url;
	}
	
	/*
	 * build function
	 * @Created by ZooTemplate
	 */
	function getAllParentsId($Itemid, $pidArray = array())
	{
		$isHasChild 	= modZTAMenuHelper::haveChidren($Itemid);
		if($isHasChild && !in_array($Itemid, $pidArray))
			$pidArray[]	= $Itemid;
		
		$db				= JFactory::getDBO();
		$query			= "SELECT parent_id FROM #__menu WHERE id = $Itemid";
		$db->setQuery($query);
		$pid			= $db->loadResult();
		if($pid && !in_array($pid, $pidArray))
		{
			$pidArray[]	= $pid;
			$pidArray	=	modZTAMenuHelper::getAllParentsId($pid, $pidArray);
		}		
		return $pidArray;
	}
	//End function
}