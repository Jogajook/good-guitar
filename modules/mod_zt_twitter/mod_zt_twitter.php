<?php
/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
defined('_JEXEC') or die('Restricted access');
if($params->get('usernames') == "")
{
	echo JText::_("Please specific twitter username in backend or you can not see anything!");
}
else
{
	require_once(dirname(__FILE__).DS.'helper.php');
	$url 			= "http://twitter.com/users/show/".$params->get('user_name');
	
	$checkConnect 	= modZTTwitter::getNormalTwitter($url);
	if($checkConnect)
	{ 
  		if($params->get('merge_twitter') == 1)
		{       
    		$path 	= JModuleHelper::getLayoutPath('mod_zt_twitter','default');   
    		if(file_exists($path))
			{
     			require($path);
    		}
  		}
  		else
		{           
			$path 	= JModuleHelper::getLayoutPath('mod_zt_twitter','split_twitter');
    		if(file_exists($path))
			{
      			require($path);
    		}
  		}
	}
	else
	{
  		$users 		= $params->get('usernames', '');
  		$aryUser 	= explode(',', $users);
  		$path 		= JModuleHelper::getLayoutPath('mod_zt_twitter','twitter_error');
  		if(file_exists($path))
		{
    		require($path);
  		}  
	}
}