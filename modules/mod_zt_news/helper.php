<?php
/**
 * @package ZT News Module for Joomla! 2.5.0
 * @author http://www.ZooTemplate.com
 * @copyright(C) 2011- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/
// no direct access
defined("_JEXEC")or die("Restrict accesses");

require_once(JPATH_SITE. DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');

if(is_file(JPATH_SITE. DS . 'components' . DS . 'com_k2' . DS . 'k2.php')) {
	require_once(JPATH_SITE. DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');
}

class modZTNewsHelper
{
	var $params  = array();
	var $section = array();
	var $source	 = NULL;
	var $k2		 = false;
	
	function __construct($params)
	{
		$this->params = $params;
		$this->source = $params->get('source', 'category');
		
		if(is_file(JPATH_SITE. DS . 'components' . DS . 'com_k2' . DS . 'k2.php')) {
			$this->k2 = true;
		}
		
		$this->createdDirThumb();
	}
	
	
	function getCookie($name)
	{
		if(isset($_COOKIE[$name]) && trim($_COOKIE[$name] !=''))
			return urldecode($_COOKIE[$name]);			
		else
			return false;
	}
	
	
	function getCategories()
	{
		//Check source
		if($this->source == 'k2_category' && $this->k2)
			return $this->getK2Categories();
		//End
		
		$categories = (array)$this->params->get('categories', array());
		
		if(count($categories))
		{
			$strCatId = implode(',', $categories);
			$categoriesCondi = " AND c.id IN($strCatId)";
		}
		
		$db		= &JFactory::getDBO();
		$order 	= (string)$this->params->get('cat_ordering', 1);
		
		switch($order)
		{
			case "1":
				$orderBy = " ORDER BY c.title ASC";
				break;
			case "2":
				$orderBy = " ORDER BY c.title DESC";
				break;
			case "3":
				$orderBy = " ORDER BY c.ordering";
				break;				
		}
		
		$sql ="SELECT id, title, section
					 FROM #__categories AS c 
					 WHERE c.published = 1 ".(count($categories) ? $categoriesCondi : '') . $orderBy;
					 
		$db->setQuery($sql);
		$results 	= $db->loadObjectList();
		$rows 		= array();
		
		if(count($results))
		{
			$i = 0;
			foreach($results as $item)
			{
				$rows[$i]->id 		= $item->id;
				$rows[$i]->title 	= $item->title;
				$rows[$i]->link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item->id,$item->section));
				$i++;
			}
		}
		return $rows;
	}
	
	
	function getK2Categories()
	{
		$categories = (array)$this->params->get('categories', array());
		
		if(count($categories))
		{
			$strCatId = implode(',',$categories);
			$categoriesCondi = " AND c.id IN($strCatId)";
		}
		
		$db		= &JFactory::getDBO();
		$order 	= (string)$this->params->get('cat_ordering', 1);
		
		switch($order)
		{
			case "1":
				$orderBy = " ORDER BY c.title ASC";
				break;
			case "2":
				$orderBy = " ORDER BY c.title DESC";
				break;
			case "3":
				$orderBy = " ORDER BY c.ordering";
				break;				
		}
		
		$sql ="SELECT id,title,section
					 FROM #__categories AS c 
					 WHERE c.published = 1 ".(count($categories) ? $categoriesCondi:'') . $orderBy;
					 
		$db->setQuery($sql);
		$results 	= $db->loadObjectList();
		$rows 		= array();
		
		if(count($results))
		{
			$i=0;
			foreach($results as $item)
			{
				$rows[$i]->id 		= $item->id;
				$rows[$i]->title 	= $item->title;
				$rows[$i]->link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item->id,$item->section));
				$i++;
			}
		}
		return $rows;
	}
	
	
	function getItemsByCatId()
	{
		//Check source
		if($this->source == 'k2_category' && $this->k2)
			return $this->getItemsByK2CatId();
		//End
		
		global $mainframe;
		$db         = &JFactory::getDBO();
		$user       = &JFactory::getUser();
		$userId     = (int) $user->get('id'); 
		
		$cid 	= $this->params->get('catid', NULL);
		$count 	= (int)$this->params->get('no_intro_items') + (int)$this->params->get('no_link_items');
		$templatetype 		= $this->params->get('template_type');
		$intro_lenght 	= intval($this->params->get('intro_length', 200));
		$aid        	= $user->get('aid', 0);
		$imgWidth 		= $this->params->get('image_width');
		$imgHeight 		= $this->params->get('image_height');
		$contentConfig 	= &JComponentHelper::getParams('com_content');
		$access     	= !$contentConfig->get('shownoauth');
		$nullDate   	= $db->getNullDate();
		$date 			= &JFactory::getDate();
		$now 			= $date->toMySQL();
		
		$where      = 'a.state = 1'
		. ' AND(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')'
		. ' AND(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')'
		;
		if($templatetype=='latestnews'){
			$sql    = @implode(',', $cid);
			$where .= " AND a.catid IN({$sql})";
			
		}else{
			if(is_array($cid))
			{  
				$allChildren 	= array();  
				$categories 	= $this->getCategoryChilds($cid[0], true);
				$categories[] 	= $cid[0];
				$categories 	= @array_unique($categories);  
				$allChildren 	= @array_merge($allChildren, $categories); 
				$allChildren 	= @array_unique($allChildren);
				
				JArrayHelper::toInteger($allChildren);
				$sql    = @implode(',', $allChildren);
				$where .= " AND a.catid IN({$sql})"; 
			}
			else
			{
				$categories 	= $this->getCategoryChilds($cid, true);
				$categories[] 	= $cid;
				$categories 	= @array_unique($categories);
				JArrayHelper::toInteger($categories);
				$sql    = @implode(',', $categories);
				$where .= " AND a.catid IN({$sql})";
			}
		}
		
		// User Filter
		switch($this->params->get('user_id'))
		{
			case 'by_me':
				$where .= ' AND(created_by = ' .(int) $userId . ' OR modified_by = ' .(int) $userId . ')';
				break;
			case 'not_me':
				$where .= ' AND(created_by <> ' .(int) $userId . ' AND modified_by <> ' .(int) $userId . ')';
				break;
		}

		// Ordering 
		switch($this->params->get('cat_ordering'))
		{
			case '1':
				$ordering		= 'a.title ASC';
				break;
			case '2':
				$ordering		= 'a.title DESC';
				break;
			case '3':
				$ordering		= 'a.ordering';
				break;
			default:
				$ordering		= 'a.created DESC';
				break;
		} 
		// Content Items only
		$query = 'SELECT a.*, cc.title as cat_title,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' . 
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' . 
			' WHERE '. $where .'' .    
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;		
		$db->setQuery($query, 0, $count);
		
		$rows   = $db->loadObjectList(); 
		$i      = 0;
		$lists  = array();
		
		foreach($rows as $row)
		{		
			$imageurl  = $this->checkImage($row->introtext);
			$folderImg = DS.$row->id;
			
			$this->createdDirThumb();
			
			$lists[$i]->title = $row->title;
			$lists[$i]->alias = $row->alias;
			$lists[$i]->link  = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
			
			$lists[$i]->introtext = $this->introContent($row->introtext, $intro_lenght);
			$lists[$i]->created   = $row->created;
			$lists[$i]->thumb     = '';
			
			if($this->FileExists($imageurl))
			{
				$lists[$i]->thumb = $this->getThumb($row->introtext,$imgWidth,$imgHeight,false,$row->id);
				$images_size      =  $this->getImageSizes($lists[$i]->thumb);
				
				if($images_size[0] != $imgWidth || $images_size[1] != $imgHeight)
				{
					@unlink($lists[$i]->thumb);
					$lists[$i]->thumb = $this->getThumb($row->introtext,$imgWidth,$imgHeight,false,$row->id);
				}			
			} 
			$i++;
		}
		
		return $lists;
	}
	
	
	function getItemsByK2CatId()
	{
		global $mainframe;
		
		$db         = &JFactory::getDBO();
		$user       = &JFactory::getUser();
		$userId     = (int) $user->get('id'); 
		
		$cid 	= $this->params->get('k2_catid', NULL); 
		$count 	= (int)$this->params->get('no_intro_items') + (int)$this->params->get('no_link_items');
		$templatetype 		= $this->params->get('template_type');
		$intro_lenght 	= intval($this->params->get('intro_length', 200));
		$aid        	= $user->get('aid', 0);
		$imgWidth 		= $this->params->get('image_width');
		$imgHeight 		= $this->params->get('image_height');
		$contentConfig 	= &JComponentHelper::getParams('com_content');
		$access     	= !$contentConfig->get('shownoauth');
		$nullDate   	= $db->getNullDate();
		$date 			= &JFactory::getDate();
		$now 			= $date->toMySQL();
		
		$where      = 'a.published = 1'
		. ' AND(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')'
		. ' AND(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')'
		;
		if($templatetype=='latestnews'){
			$sql    = @implode(',', $cid);
			$where .= " AND a.catid IN({$sql})";
		}else{
			if(is_array($cid))
			{  
				$allChildren 	= array();  
				$categories 	= $this->getK2CategoryChilds($cid[0], true);
				$categories[] 	= $cid[0];
				$categories 	= @array_unique($categories);  
				$allChildren 	= @array_merge($allChildren, $categories); 
				$allChildren 	= @array_unique($allChildren);
				
				JArrayHelper::toInteger($allChildren); 
				$sql    = @implode(',', $allChildren);
				$where .= " AND a.catid IN({$sql})"; 
			}
			else
			{
				$categories 	= $this->getK2CategoryChilds($cid, true);
				$categories[] 	= $cid;
				$categories 	= @array_unique($categories);
				JArrayHelper::toInteger($categories);
				$sql    = @implode(',', $categories);
				$where .= " AND a.catid IN({$sql})";
			}
		}
		
		// User Filter
		switch($this->params->get('user_id'))
		{
			case 'by_me':
				$where .= ' AND(created_by = ' .(int) $userId . ' OR modified_by = ' .(int) $userId . ')';
				break;
			case 'not_me':
				$where .= ' AND(created_by <> ' .(int) $userId . ' AND modified_by <> ' .(int) $userId . ')';
				break;
		}

		// Ordering 
		switch($this->params->get('cat_ordering'))
		{
			case '1':
				$ordering		= 'a.title ASC';
				break;
			case '2':
				$ordering		= 'a.title DESC';
				break;
			case '3':
				$ordering		= 'a.ordering';
				break;
			default:
				$ordering		= 'a.created DESC';
				break;
		} 
		// Content Items only
		$query = 'SELECT a.*, cc.name as cat_title ' .			
			' FROM #__k2_items AS a' . 
			' INNER JOIN #__k2_categories AS cc ON cc.id = a.catid' . 
			' WHERE '. $where .'' .    
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;		
		$db->setQuery($query, 0, $count);
		
		$rows   = $db->loadObjectList(); 
		$i      = 0;
		$lists  = array();
		
		foreach($rows as $row)
		{		
			$imageurl  = $this->checkImage($row->introtext);
			$folderImg = DS.$row->id;
			
			$this->createdDirThumb();
			
			$lists[$i]->title = $row->title;
			$lists[$i]->alias = $row->alias;
			$lists[$i]->link  = JRoute::_(K2HelperRoute::getItemRoute($row->id, $row->catid));
			
			$lists[$i]->introtext = $this->introContent($row->introtext, $intro_lenght);
			$lists[$i]->created   = $row->created;
			$lists[$i]->thumb     = '';
			
			if($this->FileExists($imageurl))
			{
				$lists[$i]->thumb = $this->getThumb($row->introtext, $imgWidth, $imgHeight, false, $row->id);
				$images_size      =  $this->getImageSizes($lists[$i]->thumb);
				
				if($images_size[0] != $imgWidth || $images_size[1] != $imgHeight)
				{
					@unlink($lists[$i]->thumb);
					$lists[$i]->thumb = $this->getThumb($row->introtext,$imgWidth,$imgHeight,false,$row->id);
				}			
			} 
			$i++;
		}
		
		return $lists;
	}
	
	
	function getCategoryChilds($catid, $clear = false)
	{  
	    static $array = array();
        
		if($clear){
            $array = array();
        }
		
        $catid = (int) $catid;
        $db    = &JFactory::getDBO();
        $query = "SELECT * FROM #__categories WHERE parent_id=".$catid." AND published=1 ORDER BY id";
		
        $db->setQuery($query);
        $rows = $db->loadObjectList(); 
        
		foreach($rows as $row)
		{
            array_push($array, $row->id);
            if(modZTNewsHelper::hasChilds($row->id)) { 
                modZTNewsHelper::getCategoryChilds($row->id);
            } 
        }   
        return $array;
    }
	
	
	function getK2CategoryChilds($catid, $clear = false)
	{ 
        static $array = array();
        
		if($clear){
            $array = array();
        }
		
        $catid = (int) $catid;
        $db    = &JFactory::getDBO();
        $query = "SELECT * FROM #__k2_categories WHERE parent=".$catid." AND published=1 ORDER BY id";
		
        $db->setQuery($query);
        $rows = $db->loadObjectList(); 
        
		foreach($rows as $row)
		{
            array_push($array, $row->id);
            if(modZTNewsHelper::hasChilds($row->id)) { 
                modZTNewsHelper::getCategoryChilds($row->id);
            } 
        }   
        return $array;
    }



    function hasChilds($id)
	{

        $user 	= &JFactory::getUser(); 
        $id 	= (int)$id;
        $db 	= &JFactory::getDBO();
        $query 	= "SELECT * FROM #__categories WHERE parent_id={$id} AND published=1";
		
        $db->setQuery($query);
        $rows = $db->loadObjectList(); 
        
		if(count($rows)) {
            return true;
        }
		else {
            return false;
        }
    }
	
	
	function introContent($str, $limit = 100,$end_char = '&#8230;')
	{
		if(trim($str) == '') return $str;
		
		$str = strip_tags($str);
		preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/', $str, $matches);		
		if(strlen($matches[0]) == strlen($str))$end_char = '';
		
		return rtrim($matches[0]).$end_char;
	}	
	
	
	function createdDirThumb()
	{
		$thumbImgParentFolder = JPATH_BASE.DS.'cache'.DS.'zt-assets';
		if(!JFolder::exists($thumbImgParentFolder)){
			JFolder::create($thumbImgParentFolder);
		}
	}
	
	
	function getThumb($text, $tWidth,$tHeight, $reflections=false,$id=0)
	{
		global $moduleId;
		preg_match("/\<img.+?src=\"(.+?)\".+?\/>/", $text, $matches);
		
		$paths 		= array();
		$showbug 	= true;
		
		if(isset($matches[1]))
		{
			$image_path = $matches[1];
			$full_url 	= JURI::base();
			$parsed_url = parse_url($full_url);
			$paths[] 	= $full_url;
			
			if(isset($parsed_url['path']) && $parsed_url['path'] != "/") $paths[] = $parsed_url['path'];
			
			foreach($paths as $path) {
				if(strpos($image_path,$path) !== false) {
					$image_path = substr($image_path,strpos($image_path, $path)+strlen($path));
				}
			}
			// remove any / that begins the path
			if(substr($image_path, 0 , 1) == '/') $image_path = substr($image_path, 1);
			//if after removing the uri, still has protocol then the image
			//is remote and we don't support thumbs for external images
			if(strpos($image_path,'http://') !== false ||
			strpos($image_path,'https://') !== false) {
				return false;
			}
			// create a thumb filename
			$file_name  = strrpos($image_path,'/');
			$thumb_name = substr($image_path, $file_name+1);
			$thumb_path = '';
			$thumb_path = 'cache/zt-assets/mod_zt_news_cache_'.md5($moduleId).'_'.$tWidth.'x'.$tHeight.'_'.$thumb_name;
			// check to see if this file exists, if so we don't need to create it
			if($thumb_path !='' && function_exists("gd_info") && !file_exists($thumb_path))
			{
				include_once('zt_thumbnail.php');
				$image_path = JPATH_ROOT.DS.$image_path;
				$thumb 		= new ZTThumbnail($image_path); 
				$thumb->resize_image($tWidth,$tHeight);  
				$thumb->save($thumb_path);
				$thumb->destruct();
			}
			return($thumb_path);
		}
		else {
			return false;
		}
	}

	
	function checkImage($file)
	{
		preg_match("/\<img.+?src=\"(.+?)\".+?\/>/", $file, $matches);
		
		if(count($matches)){
			return $matches[1];
		}
		else {return '';}
	}
	
	
	function FileExists($file)
	{
		if(file_exists($file))
			return true;
		else
			return false;
	}
	
	
	function getImageSizes($file)
	{
		return getimagesize($file);
	}
	
	
	function getAllCategories()
	{
		//Check source
		if($this->source == 'k2_category' && $this->k2)
			return $this->getAllK2Categories();
		//End
		
		$db			= &JFactory::getDBO();
		$categories = (array)$this->params->get('catid', array());
		
		$lists 	= array();
		$i 		= 0;
		
		if(count($categories))
		{
			foreach($categories as $category_id)
			{
				$query = 'SELECT id, parent_id, title FROM #__categories WHERE id = '.$category_id.' AND published = 1';
				$db->setQuery($query);
				$load = $db->loadObject();  
					$lists[$i]->id = $load->id;
					$lists[$i]->parent_id = $load->parent_id;
					$lists[$i]->title = $load->title;
					$lists[$i]->link = JRoute::_(ContentHelperRoute::getCategoryRoute($load->id)); 
				$i = $i+1;
			}	
		}
		return $lists;			
	}
	
	
	function getAllK2Categories()
	{
		$db			= &JFactory::getDBO();
		$categories = (array)$this->params->get('k2_catid', array());
		
		$lists 	= array();
		$i 		= 0;
		
		if(count($categories))
		{
			foreach($categories as $category_id)
			{
				$query = 'SELECT id, parent, name FROM #__k2_categories WHERE id = '.$category_id.' AND published = 1';
				$db->setQuery($query);
				$load = $db->loadObject();  
					$lists[$i]->id 			= $load->id;
					$lists[$i]->parent_id 	= $load->parent;
					$lists[$i]->title 		= $load->name;
					$lists[$i]->link 		= JRoute::_(K2HelperRoute::getCategoryRoute($load->id)); 
				$i = $i+1;
			}	
		}
		return $lists;
	}
	
	
	function getCategoryBySecId($secId)
	{
		$db			= &JFactory::getDBO();
		$categories = (array)$this->params->get('catid',array());
		$aryTmp 	= array();
		
		if(count($categories))
		{
			foreach($categories as $item)
			{
				$sect 		= $secId."_";	
				$sectTmp 	= substr($item,0,(int)strpos($item,'_') + 1);
				if($sect == $sectTmp) {			
					$aryTmp[] = substr($item,(int)strpos($item,"_") + 1,strlen($item));
				}
			}
			
			$strCat = implode(',',$aryTmp);
			$sql ="SELECT id,title,section
					 FROM #__categories AS c 
					 WHERE c.published = 1  AND id IN(".$strCat.") ORDER BY ordering ASC";		
			$db->setQuery($sql);
			
			$results = $db->loadObjectList();
			$rows 	 = array();
			
			if(count($results))
			{
				$i = 0;
				foreach($results as $item1)
				{
					$rows[$i]->id 		= $item1->id;
					$rows[$i]->title 	= $item1->title;
					$rows[$i]->link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item1->id));
					$i++;
				}
			}
		}
		return $rows;	
	}
	

	function getLatestItemByCatId($fcatId)
	{ 
		
		//Check source
		if($this->source == 'k2_category' && $this->k2)
			return $this->getLatestItemByK2CatId($fcatId);
		//End
		
		global $mainframe;
		
		$db         = &JFactory::getDBO();
		$user       = &JFactory::getUser();
		$userId     = (int) $user->get('id');
		$cid 		= $this->params->get('catid', NULL);
		$count 		= (int)$this->params->get('v_no_latest_item') +(int)$this->params->get('v_no_link_item');
		
		$intro_lenght 	= intval($this->params->get('intro_length', 200));
		$aid        	= $user->get('aid', 0);
		$imgWidth 		= $this->params->get('image_width');
		$imgHeight 		= $this->params->get('image_height');
		$contentConfig 	= &JComponentHelper::getParams('com_content');
		$access     	= !$contentConfig->get('shownoauth');
		$nullDate   	= $db->getNullDate();
		$date 			= &JFactory::getDate();
		$now 			= $date->toMySQL();
		$amountCookie 	= 'amount'.$fcatId;
		$amountCookie 	= $this->getCookie($amountCookie);	
		
		if($amountCookie)
		{
			$aryAmount 	= explode(',',$amountCookie);
			$noHeadLine = $aryAmount[0];
			$noLink 	= $aryAmount[1];
			$count 		= (int)$noHeadLine +(int)$noLink;
		}
		
		$where      = 'a.state = 1'
		. ' AND(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')'
		. ' AND(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')'
		;
		
		if(is_array($cid))
		{
			$allChildren = array();  
				
				$categories 	= $this->getCategoryChilds($fcatId, true);
				$categories[] 	= $fcatId;
				$categories 	= @array_unique($categories);  
				$allChildren 	= @array_merge($allChildren, $categories);
				
			$allChildren = @array_unique($allChildren);
			JArrayHelper::toInteger($allChildren);
			$sql    = @implode(',', $allChildren);
			$where .= " AND a.catid IN({$sql})"; 
		}
		else
		{
			$categories 	= $this->getCategoryChilds($cid, true);
			$categories[] 	= $cid;
			$categories 	= @array_unique($categories);
			
			JArrayHelper::toInteger($categories);
			$sql 	= @implode(',', $categories);
			$where .= " AND a.catid IN({$sql})";
		} 
		$ordering       = 'a.created DESC';
		// Content Items only
		$query = 'SELECT a.*, cc.title as cat_title,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' . 
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' . 
			' WHERE '. $where .'' .    
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;		
		$db->setQuery($query, 0, $count);
		
		$rows 	= $db->loadObjectList(); 
		$i      = 0;
		$lists  = array();
		
		foreach($rows as $row)
		{
			$imageurl 	= $this->checkImage($row->introtext);
			$folderImg 	= DS.$row->id;
			
			$this->createdDirThumb();
			
			$lists[$i]->title = $row->title;
			$lists[$i]->alias = $row->alias;
			$lists[$i]->link  = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
			
			$lists[$i]->introtext 	= $this->introContent($row->introtext, $intro_lenght);
			$lists[$i]->created 	= $row->created;
			$lists[$i]->thumb 		= '';
			
			if($this->FileExists($imageurl))
			{
				$lists[$i]->thumb 	= $this->getThumb($row->introtext,$imgWidth,$imgHeight,false,$row->id);
				$images_size 		= $this->getImageSizes($lists[$i]->thumb);
				
				if($images_size[0] != $imgWidth || $images_size[1] != $imgHeight)
				{
					@unlink($lists[$i]->thumb);
					$lists[$i]->thumb = $this->getThumb($row->introtext,$imgWidth,$imgHeight,false,$row->id);
				}			
			} 
			$i++;
		}
		return $lists;
	}
	
	
	function getLatestItemByK2CatId($fcatId)
	{
		global $mainframe;
		
		$db         = &JFactory::getDBO();
		$user       = &JFactory::getUser();
		$userId     = (int) $user->get('id');
		$cid 		= $this->params->get('k2_catid', NULL);
		$count 		= (int)$this->params->get('v_no_latest_item') +(int)$this->params->get('v_no_link_item');
		
		$intro_lenght 	= intval($this->params->get('intro_length', 200));
		$aid        	= $user->get('aid', 0);
		$imgWidth 		= $this->params->get('image_width');
		$imgHeight 		= $this->params->get('image_height');
		$contentConfig 	= &JComponentHelper::getParams('com_content');
		$access     	= !$contentConfig->get('shownoauth');
		$nullDate   	= $db->getNullDate();
		$date 			= &JFactory::getDate();
		$now 			= $date->toMySQL();
		$amountCookie 	= 'amount' . $fcatId;
		$amountCookie 	= $this->getCookie($amountCookie);	
		
		if($amountCookie)
		{
			$aryAmount 	= explode(',',$amountCookie);
			$noHeadLine = $aryAmount[0];
			$noLink 	= $aryAmount[1];
			$count 		= (int)$noHeadLine +(int)$noLink;
		}
		
		$where      = 'a.published = 1'
		. ' AND(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')'
		. ' AND(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')'
		;
		
		if(is_array($cid))
		{
			$allChildren = array();  
				
				$categories 	= $this->getK2CategoryChilds($fcatId, true);
				$categories[] 	= $fcatId;
				$categories 	= @array_unique($categories);  
				$allChildren 	= @array_merge($allChildren, $categories);
				
			$allChildren = @array_unique($allChildren);
			JArrayHelper::toInteger($allChildren);
			$sql    = @implode(',', $allChildren);
			$where .= " AND a.catid IN({$sql})"; 
		}
		else
		{
			$categories 	= $this->getK2CategoryChilds($cid, true);
			$categories[] 	= $cid;
			$categories 	= @array_unique($categories);
			
			JArrayHelper::toInteger($categories);
			$sql 	= @implode(',', $categories);
			$where .= " AND a.catid IN({$sql})";
		} 
		$ordering       = 'a.created DESC';
		// Content Items only
		$query = 'SELECT a.*, cc.name as cat_title' .
			' FROM #__k2_items AS a' . 
			' INNER JOIN #__k2_categories AS cc ON cc.id = a.catid' . 
			' WHERE '. $where .'' .    
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;		
		$db->setQuery($query, 0, $count);
		
		$rows 	= $db->loadObjectList(); 
		$i      = 0;
		$lists  = array();
		
		foreach($rows as $row)
		{
			$imageurl 	= $this->checkImage($row->introtext);
			$folderImg 	= DS.$row->id;
			
			$this->createdDirThumb();
			
			$lists[$i]->title = $row->title;
			$lists[$i]->alias = $row->alias;
			$lists[$i]->link  = JRoute::_(K2HelperRoute::getItemRoute($row->id, $row->catid));
			
			$lists[$i]->introtext 	= $this->introContent($row->introtext, $intro_lenght);
			$lists[$i]->created 	= $row->created;
			$lists[$i]->thumb 		= '';
			
			if($this->FileExists($imageurl))
			{
				$lists[$i]->thumb 	= $this->getThumb($row->introtext, $imgWidth, $imgHeight, false, $row->id);
				$images_size 		= $this->getImageSizes($lists[$i]->thumb);
				
				if($images_size[0] != $imgWidth || $images_size[1] != $imgHeight)
				{
					@unlink($lists[$i]->thumb);
					$lists[$i]->thumb = $this->getThumb($row->introtext, $imgWidth, $imgHeight, false, $row->id);
				}			
			} 
			$i++;
		}
		return $lists;
	}
	
	
	function getCategoryDetail($catId)
	{
		$db	 = &JFactory::getDBO();			
		$sql = "SELECT c.id,c.title
				FROM #__categories AS c ".  
			" WHERE c.published = 1 AND c.id =".$catId.
			" ORDER BY c.title ASC";		
		$db->setQuery($sql);
		$results = $db->loadObject(); 
		return $results;	
	}
	
	
	function getContentByCatId($catId,$noItem)
	{
		$catInfo = $this->getCategoryInfo($catId);
		$linkCat = JRoute::_(ContentHelperRoute::getCategoryRoute($catInfo->id,$catInfo->section));		
		$str = '<div class="items_e_cat">
				<div class="item_header_wrap">
					<div class="item_header"><a class="title" href="'.$linkCat.'">'.$catInfo->title.'</a>
					<div class="cat_addremove"><a id="add_cat'.$catId.'" href="#" class="cat_add">&nbsp;</a><a id="cat_remove'.$catId.'" href="#" class="cat_remove">&nbsp;</a></div>
					</div>';
		$listItem = $this->getAllItemsByCatId($catId);
		if(count($listItem))
		{
			$str .='<ul class="cat_morelink">';
			$i   = 1;			
			
			foreach($listItem as $item)
			{
				if($i<=$noItem) {
					$str.='<li class="active"><a href="'.$item->link.'">'.$item->title.'</a></li>';
				}
				else {
					$str.='<li class="block"><a href="'.$item->link.'">'.$item->title.'</a></li>';
				}
				$i++;
			}	
		}
		$str .= '</div><div class="cls"></div></div>';
		return $str;	
	}
	
	
	function getCategoryInfo($catId)
	{
		$db  = &JFactory::getDBO();
		$sql = "SELECT id,title 
				FROM #__categories AS c 
				WHERE c.published = 1 AND c.id=".$catId;
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	
	function getAllItemsByCatId($catId)
	{
		$db         	= &JFactory::getDBO();
		$intro_lenght 	= intval($this->params->get('intro_length', 200));
		$nullDate   	= $db->getNullDate();
		$date 			= &JFactory::getDate();
		$now 			= $date->toMySQL();
		$count 			= $this->params->get('v_max_item');
		
		$where = 'a.state = 1'
		. ' AND(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')'
		. ' AND(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')';
		// Ordering
		$allChildren 	= array();  
		$categories 	= $this->getCategoryChilds($catId, true);
		$categories[] 	= $catId;
		$categories 	= @array_unique($categories);  
		$allChildren 	= @array_merge($allChildren, $categories);  
		$allChildren 	= @array_unique($allChildren);
		
		JArrayHelper::toInteger($allChildren);
		$sql    = @implode(',', $allChildren);
		$where .= " AND a.catid IN({$sql})";
		$ordering = 'a.created DESC';
		$query = 'SELECT a.*, cc.title as cat_title, ' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
            ' FROM #__content AS a' .             
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' WHERE '. $where .'' .
            ' AND cc.published = 1' .
            ' ORDER BY '. $ordering;		
		$db->setQuery($query, 0, $count);
		
		$rows   = $db->loadObjectList();
		$i      = 0;
		$lists  = array();
		
		foreach($rows as $row)
		{
			$lists[$i]->title = $row->title;				
			$lists[$i]->link  = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug)); 
			$i++;
		}
		return $lists;
	}
	
	
	function getCategoryByVTemp()
	{
		
		//Check source
		if($this->source == 'k2_category' && $this->k2)
			return $this->getK2CategoryByVTemp();
		//End
		
		$db			= &JFactory::getDBO();
		$sections 	= (array)$this->params->get('catid',array());  
		$orderBy 	= $this->params->get('v_section_orderding');
		$ordering 	= " ORDER BY title";
		
		if($orderBy == 1) {
			$ordering = " ORDER BY title ASC";
		}
		else if($orderBy == 2){
			$ordering = " ORDER BY title DESC";
		}
		else if($orderBy == 3){
			$ordering = " ORDER BY ordering DESC";
		}
		else {
			$ordering = " ORDER BY ordering ASC";
		}
		
		$lists 	= array();
		$i 		= 0;
		
		foreach($sections as $item)
		{
			$sql = 'SELECT id, title 
					FROM #__categories 
					WHERE published = 1 AND id ='.$item.''.$ordering;			
			$db->setQuery($sql);
			$results 			= $db->loadObject();  
			$lists[$i]->id 		= $results->id;
			$lists[$i]->title 	= $results->title;
			$lists[$i]->link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($results->id));
			
			$i++;
		}
		return $lists;
	}
	
	function getK2CategoryByVTemp()
	{
		$db			= &JFactory::getDBO();
		$sections 	= (array)$this->params->get('k2_catid', array());  
		$orderBy 	= $this->params->get('v_section_orderding');
		$ordering 	= " ORDER BY name";
		
		if($orderBy == 1) {
			$ordering = " ORDER BY name ASC";
		}
		else if($orderBy == 2){
			$ordering = " ORDER BY name DESC";
		}
		else if($orderBy == 3){
			$ordering = " ORDER BY ordering DESC";
		}
		else {
			$ordering = " ORDER BY ordering ASC";
		}
		
		$lists 	= array();
		$i 		= 0;
		
		foreach($sections as $item)
		{
			$sql = 'SELECT id, name 
					FROM #__k2_categories 
					WHERE published = 1 AND id ='.$item.''.$ordering;			
			$db->setQuery($sql);
			
			$results 			= $db->loadObject();  
			$lists[$i]->id 		= $results->id;
			$lists[$i]->title 	= $results->name;
			$lists[$i]->link 	= JRoute::_(K2HelperRoute::getCategoryRoute($results->id));
			
			$i++;
		}
		return $lists;
	}
}
?>