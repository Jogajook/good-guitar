<?php
/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport( 'joomla.methods' );

require_once 'json.php';

class modZTTwitter
{	
	function accessTwitter($params,$api_url)
	{
		$credentials = sprintf("%s:%s", $params->get('user_name',''),$params->get('pass_word',''));
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $api_url);
		curl_setopt($curl_handle, CURLOPT_USERPWD, $credentials);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
		$twitter_data = curl_exec($curl_handle);
		$http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		curl_close($curl_handle);
		if( intval( $http_status ) == 200 ){
			return $twitter_data;
		} else {
			return false;
		}
	}	
	/*
	 * Function render users information
	 * @Created by ZooTemplate
	 */
	function getUserTwittersInfo(&$params,&$listUserId = array(),$moduleId = '',&$aryItemIds= array()){
		$html = "";
		$users = $params->get('usernames','');
		$no_following = $params->get('following_icons_count','');
		if($users !=''){
			$aryUser = split(',',$users);
			foreach($aryUser as $item){
				$urlTwitter = "http://twitter.com/users/show/".$item.".xml";
				$remainHits = modZTTwitter::getRemainingHits();
				if($remainHits > 0 ){
					$xml = modZTTwitter::writeBackupXmlUserInfo($item,$params);
				} else {
					$api_url = JURI::base().'/modules/mod_zt_twitter/backup/user_'.$item.'.xml';
					$xml = modZTTwitter::getNormalTwitter($api_url,$http_code);
				}
				if($xml){
					$xml = simplexml_load_string($xml);
					//Get friendlist of each username
					$listUserId[] = (string)$xml->id;
					$divItemTwitter = 'jv_item_twitter'.$moduleId.'_user'.(string)$xml->id;
					$divTwitterTitle = 'jv_twitter_sub_title'.$moduleId.'_user'.(string)$xml->id;
					$friendList= '';
					$remainHits = modZTTwitter::getRemainingHits();
					if($remainHits > 0 ){
						$xmlFriend = modZTTwitter::writeBackupXmlUserFriend($item,$params);
						if(!$xmlFriend) {
							$api_url = JURI::base().'/modules/mod_zt_twitter/backup/userfriend_'.$item.'.xml';
							$xmlFriend = modZTTwitter::getNormalTwitter($api_url);
						}
					} else {
						$api_url = JURI::base().'/modules/mod_zt_twitter/backup/userfriend_'.$item.'.xml';
						$xmlFriend = modZTTwitter::getNormalTwitter($api_url);
					}
					if($xmlFriend){
						$xmlFriend = simplexml_load_string($xmlFriend);
						$i=0;
						foreach($xmlFriend->user as $itemFriend){
							if($i>=$no_following) break;
							$friendList .= '<span class="vcard">
                <a href="http://twitter.com/'.$itemFriend->screen_name.'" title="'.$itemFriend->name.'"><img src="'.$itemFriend->profile_image_url.'" width="24" height="24" alt="'.$itemFriend->name.'" /></a></span>'; 
							$i++;
						}
						//End get friendlist
						$html.='<div class="jv_twitter_user">';
						if($params->get('show_avatar_header') ==1 || $params->get('show_username_header') == 1){
							$html.='<div class="jv_header_wrapper"><div class="jv_twitter_header">';
							if($params->get('show_avatar_header') == 1) $html.='<div class="show_avatar"><img alt="" width="48" height="48" src="'.(string)$xml->profile_image_url.'" title="'.(string)$xml->name.'" /></div>';

							if($params->get('show_username_header') == 1)$html.='<div class="info"><span class="name">'.(string)$xml->name.'</span><span class="nick">'.(string)$xml->screen_name.'</span></div>';
							$html.="</div></div>";
						}
						$html.='<div class="twitter_content">';
						$strTmp = '';
						if($params->get('show_feed'))$strTmp.='<li class="twitter_feed"><div class="title">'.JText::_('FEED_TITLE').'</div>
                  <div class="content_feed"><a href="https://twitter.com/statuses/user_timeline/'.(string)$xml->screen_name.'.rss" target="_blank">&nbsp;</a></div>
                  </li>';               
						if($params->get('show_bio')) $strTmp.='<li class="twitter_bio">
                        <div class="title">'.JText::_('BIO_TITLE').'</div>
                        <div class="content">'.(string)$xml->description.'</div>
                    </li>';
						if($params->get('show_web')) $strTmp.='<li class="twitter_web">
                        <div class="title">'.JText::_('WEB_TITLE').'</div>
                        <div class="content"><a href="'.(string)$xml->url.'">'.(string)$xml->url.'</a></div>
                    </li>';
						if($params->get('show_location')) $strTmp .='<li class="twitter_location">
                        <div class="title">'.JText::_('LOCATION_TITLE').'</div>
                        <div class="content">'.(string)$xml->location.'</div>
                    </li>';                    
						if($params->get('show_updates')) $strTmp.='<li class="twitter_update">
                        <div class="title">'.JText::_('UPDATES_TITLE').'</div>
                        <div class="content">'.(string)$xml->statuses_count.'</div>
                    </li>';
						if($params->get('show_followers')) $strTmp.='<li class="twitter_follwers">
                        <div class="title">'.JText::_('FOLLOWERS_TITLE').'</div>
                        <div class="content">'.(string)$xml->followers_count.'</div>
                    </li>';
						if($params->get('show_following')) $strTmp.='<li class="twitter_follwers">
                        <div class="title">'.JText::_('FOLLOWING_TITLE').'</div>
                        <div class="content">'.(string)$xml->friends_count.'</div>
                    </li>';
						$_html ='';
						if($strTmp !='') $_html.='<ul class="stas">'.$strTmp.'</ul>';
						$html.=$_html;
						if($params->get('show_follow_updates')){
							$strUpdate = (string)$xml->screen_name;
							$html.='<div class="jv_twit_follow">';
							if($params->get('is_follow_img') == 1){
								$html.='<div class="jv_twit_follow_cont">'.JText::_('FOLLOW_TITLE').'&nbsp;'.'<a href="http://twitter.com/'.(string)$xml->screen_name.'">'.$strUpdate.'</a> on twitter</div></div>';
							} else{
								$html.='<a class="twit_fol_upimage" href="http://twitter.com/'.(string)$xml->screen_name.'">
                        <img src="'.JURI::base().'modules/mod_zt_twitter/assets/images/twitter.gif" alt="twitter" /> 
                        </a></div>';
							}
						}
						if($params->get('show_friendlist_icon')){
							$html.='<div class="jv_twitter_friendlist"><div class="friendlist_title">'.JText::_('FOLLOWING_ICONS_MORE').'</div>'.
							$friendList;
							if($params->get('show_viewall')) $html.='<div class="viewall"><a href="http://twitter.com/'.$item.'/friends" target="_blank">'.JText::_('VIEWALL').'</a></div>';
							$html.="</div>";
						}
						if($params->get('merge_twitter') ==  1){
							$html .="</div></div>";
						} else {
							$aryTwitter = modZTTwitter::getSplitTwitter($params,$moduleId,$item,$itemIds);
							$aryItemIds[] = $itemIds;
							if($params->get('show_title_twitter') == 1){
								$html .='<div class="tweets-title-surround"><p class="twitter_title" id="'.$divTwitterTitle.'">'.$aryTwitter['titleTwitter'].'</p></div><div id="'.$divItemTwitter.'" class="twit_content_euser">'.$aryTwitter['html'].'</div></div></div>';
							} else {
								$html .='<div id="'.$divItemTwitter.'">'.$aryTwitter['html'].'</div></div></div>';
							}
						}
					}
				}
			}
		}
		return $html;
	}
	//End get users information
	
	/*
	 * Function get result of search twitter
	 * @Created by ZooTemplate
	 */
	function getSearchTwitter($params){
		$html ="";
		$countResult = $params->get('search_count_size');
		$searchText = $params->get('search');
		if($searchText !=''){
			$arySearchTwitter = split(',',$searchText);
			$html ="";
			foreach($arySearchTwitter as $search){
				$search = trim($search);
				$remainHits = modZTTwitter::getRemainingHits();
				if($remainHits > 0){
					$resultJson = modZTTwitter::writeBackupTwiSearch($search);	
					if(!$resultJson){
						$urlSearch = "http://search.twitter.com/search.json?q=".urlencode($search);				
						$resultJson = modZTTwitter::getNormalTwitter($urlSearch);
					}
				} else {					
					$urlSearch = "http://search.twitter.com/search.json?q=".urlencode($search);				
					$resultJson = modZTTwitter::getNormalTwitter($urlSearch);
				}				
				if($resultJson){
					$result = json_decode($resultJson);					
					$arySearch = $result->results;
					array_splice($arySearch,$countResult);
					foreach($arySearch as $item){
						$twitterSource = '';
						if($params->get('show_source') == 1){
							$twitterSource = (string)$item->source;
							$twitterSource = modZTTwitter::renderFromWeb($twitterSource);
						}
						$content = (string)$item->text;
						$content = modZTTwitter::addLinkTwitter($content);
						$twitterTime = modZTTwitter::renderHourSearch((string)$item->created_at,(string)$item->id,(string)$item->from_user);
							
						if($params->get('enable_search_avatar')){
							$width = $params->get('size_avatar_search');
							$html .='<div class="search_item"><a class="thumb_author" href="http://twitter.com/'.(string)$item->from_user.'"><img width="'.$width.'" height="'.$width.'" src="'.(string)$item->profile_image_url.'" title="'.(string)$item->from_user.'" /></a>';
						}
						$html.='<p class="status_body">';
						$_html ='';
						if($twitterSource !=''){
							$_html .='<span class="jv_twitter_infos">&nbsp;'.$twitterSource.'
                </span>';
						}
						$html.=$_html.'<span class="twitter_content"><strong class="author"><a class="twitter_author" href="http://twitter.com/'.(string)$item->from_user.'">'.(string)$item->from_user.'</a></strong>:&nbsp;'.$content.'
                 &nbsp;</span>
                 <span class="jv_twitter_infos">'.$twitterTime.'&nbsp;</span>  
            </p></div>';
					}
				}
			}
		}
		return $html;
	}
	//End result of search twitter
	function replace_url_link($text){
		$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" rel=\"nofollow\">\\0</a>", $text);
		return $text;
	}
	function getNormalTwitter($api_url,&$http_status=''){
		$curl_handle = curl_init();
		$headers=array('X-Twitter-Client: PHPTwitterSearch','X-Twitter-Client-Version: 0.1','X-Twitter-Client-URL: http://ryanfaerman.com/twittersearch');
		curl_setopt($curl_handle, CURLOPT_URL, $api_url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		$twitter_data = curl_exec($curl_handle);
		$http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		curl_close($curl_handle);
		if( intval( $http_status ) == 200 ){
			return $twitter_data;
		} else {
			return false;
		}
	}

	function niceTime($time) {
		$delta = time() - $time;
		if ($delta < 60) {
			return 'less than a minute ago.';
		} else if ($delta < 120) {
			return 'about a minute ago.';
		} else if ($delta < (45 * 60)) {
			return floor($delta / 60) . ' minutes ago.';
		} else if ($delta < (90 * 60)) {
			return 'about an hour ago.';
		} else if ($delta < (24 * 60 * 60)) {
			return 'about ' . floor($delta / 3600) . ' hours ago.';
		} else if ($delta < (48 * 60 * 60)) {
			return '1 day ago.';
		} else {
			return floor($delta / 86400) . ' days ago.';
		}
	}
	function renderFromWeb($text){
		$text = htmlspecialchars_decode($text);
		$regex_pattern = "/<a href=\"(.*)\">(.*)<\/a>/";
		preg_match_all($regex_pattern,$text,$matches);
		if(!count($matches[1])){
			$html = '<span class="source">From &nbsp;<a href="http://twitter.com/" target="_blank" class="jv_twitter_source">'.$text.'</a></span>';
		}else {
			$html ='<span class="source">From &nbsp;<a class="jv_twitter_source" target="_blank" href="'.$matches[1][0].'">'.$matches[2][0]."</a></span>";
		}
		return $html;
	}
	function renderHourSearch($time,$twitterId,$userId){
		$time = strtotime($time);
		$niceTime = modZTTwitter::niceTime($time);
		$html ='<a class="jv_twitter_date" target="_blank" href="http://twitter.com/'.$userId.'/statuses/'.$twitterId.'">'.$niceTime.'</a>';
		return $html;
	}
	function addLinkTwitter($tweet){
		$regex = '/http([s]?):\/\/([^\ \)$]*)/';
		$link_pattern = '<a href="http$1://$2" rel="nofollow" class="jv_twitter_link" title="$2">http$1://$2</a>';
		$tweet = preg_replace($regex,$link_pattern,$tweet);
		$regex = '/@([a-zA-Z0-9_]*)/';
		$link_pattern = '<a href="http://twitter.com/$1" class="jv_twitter_nofollow" title="$1 profile on Twitter" rel="nofollow">@$1</a>';
		$tweet = preg_replace($regex,$link_pattern,$tweet);
		$regex = '/\#([a-zA-Z0-9_]*)/';
		$link_pattern = '<a href="http://search.twitter.com/search?q=%23$1" class="jv_twitter_search" title="search for $1 on Twitter" rel="nofollow">#$1</a>';
		$tweet = preg_replace($regex,$link_pattern,$tweet);
		return $tweet;
	}

	/*
	 * Function get twitter content when selecting merge twitter
	 * @Created by ZooTemplate
	 */
	function getTwitByMerge($params,$moduleId,$isExceed=1){
		$numDays = $params->get('no_day_twitter');
		$aryTwitter = array();
		$countItem = $params->get('no_twitter');
		$count = ($countItem != '') ? "&count=$countItem" : '';
		$users = ($params->get('usernames') != '') ? $params->get('usernames') : '';
		$aryUser = split(',',$users);
		foreach($aryUser as $item){
			$remainHits = modZTTwitter::getRemainingHits();	//Get request exceed rate limit
			if($remainHits > 0) {
				$userTimelineData = modZTTwitter::writeBackupXmlFile($item,$countItem,$params);
				if(!$userTimelineData){
					$api_url = JURI::base().'/modules/mod_zt_twitter/backup/timeline_'.$item.'.xml';
					$userTimelineData = modZTTwitter::getNormalTwitter($api_url,$http_code);
				}
			} else {
				$api_url = JURI::base().'/modules/mod_zt_twitter/backup/timeline_'.$item.'.xml';
				$userTimelineData = modZTTwitter::getNormalTwitter($api_url,$http_code);
			}
			if($userTimelineData){
				$userTimelineData = simplexml_load_string($userTimelineData);
				modZTTwitter::renderHtmlMergeTwitter($userTimelineData,$params,$moduleId,$aryTwitter);
			}
		}
		return $aryTwitter;
	}
	//End get twitter content

	/*
	 * Function render twitter when selecting merge twitter
	 * @Created by ZooTemplate
	 */
	function renderTwitByMerge($params,$moduleId,&$strTitle,&$aryItemIds){
		$no_twitter = $params->get('no_twitter');
		$aryItemIds = array();
		$html = '';
		$aryTwitter = array();
		$aryTwitter = modZTTwitter::getTwitByMerge($params,$moduleId);
		$aryItems = modZTTwitter::msort($aryTwitter,'id',false);
		array_splice($aryItems,$no_twitter);
		foreach($aryItems as $item){
			$aryItemIds[] = $item['id'];
			$html .='<li class="jv_twitter_item" id="item'.$moduleId.'_'.$item['id'].'">'.$item['content'].'</li>';
		}
		$numTwitter = count($aryItems);
		$noDays = ($params->get('no_day_twitter') !=0) ? $params->get('no_day_twitter') : 'all';
		$strTitle = "Last $numTwitter tweets in past ".$noDays." days from ".$params->get('usernames').":";
		return $html;
	}
	//End render twitter

	//Function sort array by des direction
	function msort($array, $id="id", $sort_ascending=true) {
		$temp_array = array();
		while(count($array)>0) {
			$lowest_id = 0;
			$index=0;
			foreach ($array as $item) {
				if (isset($item[$id])) {
					if ($array[$lowest_id][$id]) {
						if ($item[$id]<$array[$lowest_id][$id]) {
							$lowest_id = $index;
						}
					}
				}
				$index++;
			}
			$temp_array[] = $array[$lowest_id];
			$array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
		}
		if ($sort_ascending) {
			return $temp_array;
		} else {
			return array_reverse($temp_array);
		}
	}
	//End sort array
	/*
	* Function get number days between two dates
	* @Created by ZooTemplate
	*/
	function getdays($beginDay,$endDay){
		return round((strtotime($endDay)-strtotime($beginDay))/(24*60*60),0);
	}
	//End get number days
	function renderHour($time,$twitterId,$userId,$moduleId,$showFromWeb,&$niceTime){
		$time = strtotime($time);
		$niceTime = modZTTwitter::niceTime($time);
		if($showFromWeb == 1){
			$html ='<a id="timeago'.$moduleId.'_'.$twitterId.'" class="jv_twitter_date" target="_blank" href="http://twitter.com/'.$userId.'/statuses/'.$twitterId.'">'.$niceTime.'</a>';
		} else {
			$html ='<a id="timeago'.$moduleId.'_'.$twitterId.'" class="date_no_from_web" target="_blank" href="http://twitter.com/'.$userId.'/statuses/'.$twitterId.'">'.$niceTime.'</a>';
		}
		return $html;
	}

	/*
	 * Function get twitter when selecting split twitter
	 * @Created by ZooTemplate
	 */
	function getSplitTwitter($params,$moduleId,$userName,&$aryItemIds){
		$aryTwitter = array();
		$countItem = ($params->get('no_twitter')) ? $params->get('no_twitter') : 5;
		$count = ($countItem != '') ? "&count=$countItem" : '';
		$aryItemIds = array();
		$numDays = $params->get('no_day_twitter');
		$remainHits = modZTTwitter::getRemainingHits();
		if($remainHits > 0){
			$userTimelineData = modZTTwitter::writeBackupXmlFile($userName,$countItem,$params);
		} else {
			$api_url = JURI::base().'/modules/mod_zt_twitter/backup/timeline_'.$userName.'.xml';
			$userTimelineData = modZTTwitter::getNormalTwitter($api_url,$http_code);
		}
		if($userTimelineData){
			$userTimelineData = simplexml_load_string($userTimelineData);
			$wrapHtml = '<ul class="jv_twitter">';
			$i=0;
			foreach($userTimelineData->status as $timeline){
				$html = "";
				$author = $timeline->user;
				$userId = (string)$author->id;
				$content = (string)$timeline->text;
				$content = modZTTwitter::addLinkTwitter($content);
				$nowDay = date('Y/m/d');
				$createdDay = date('Y/m/d',strtotime((string)$timeline->created_at));
				if(($numDays == 0) || (modZTTwitter::getdays($createdDay,$nowDay) <=$numDays)){
					if($params->get('show_avatar')){
						$width= $params->get('size_avatar_twitter');
						$html ='<a class="thumb_author" href="http://twitter.com/'.$author->screen_name.'"><img alt="" width="'.$width.'" height="'.$width.'" src="'.$author->profile_image_url.'" title="'.$author->name.'" /></a>';
					}
					$twitterSource = '';
					if($params->get('show_source') == 1){
						$twitterSource = (string)$timeline->source;
						$twitterSource = modZTTwitter::renderFromWeb($twitterSource);
					}
					$twitterTime = modZTTwitter::renderHour((string)$timeline->created_at,(string)$timeline->id,$author->screen_name,$moduleId,$params->get('show_source'),$niceTime);
					$html.='<p class="status_body">';
					$_html ='';
					if($twitterSource !='') $_html ='<span class="jv_twitter_infos">'.$twitterSource.'</span>';
					$html.=$_html.'<span class="twitter_content">&nbsp;';
					if($params->get('show_username')==1){
						$html.='<strong class="author"><a class="twitter_author" href="http://twitter.com/'.$author->screen_name.'">'.$author->screen_name.'</a></strong>:&nbsp;';
					}
					$html .=$content.'</span><span class="jv_twitter_infos">'.$twitterTime.'</span></p>';
					$wrapHtml.='<li class="jv_twitter_item" id="item'.$moduleId.'_'.(float)$timeline->id.'">'.$html.'</li>';
				}
				$aryItemIds[] = (float)$timeline->id;
				$i++;
			}
			$numTwitter = $i;
			if($params->get('show_title_twitter') == 1){
				$noDays = ($params->get('no_day_twitter') !=0) ? $params->get('no_day_twitter'):'all';
				$strTitle = "Last $numTwitter tweets in past ".$noDays." days from ".$author->screen_name.":";
				$aryTwitter['titleTwitter'] = $strTitle;
			}
			$aryTwitter['html'] = $wrapHtml."</ul>";
		}
		return  $aryTwitter;
	}
	//End split twitter
	/*
	* Function get hits remain twitter
	* @Created by ZooTemplate
	*/
	function getRemainingHits(){
		$urlRateLimit = "http://api.twitter.com/1/account/rate_limit_status.xml";
		$strData = file_get_contents($urlRateLimit);
		$strReplace = str_replace('remaining-hits','remaininghits',$strData);
		$xmlObject = simplexml_load_string($strReplace);
		return((int)$xmlObject->remaininghits);
	}
	//End get hits remain

	/*
	 * Function render html of default merge twitter
	 * @Created by ZooTemplate
	 */
	function renderHtmlMergeTwitter($userTimelineData,$params,$moduleId,&$aryTwitter){
		$numDays = $params->get('no_day_twitter');
		foreach($userTimelineData->status as $item){
			$author = $item->user;
			$content = (string)$item->text;
			$content = modZTTwitter::addLinkTwitter($content);
			$nowDay = date('Y/m/d');
			$createdDay = date('Y/m/d',strtotime((string)$item->created_at));
			if(($numDays == 0) || (modZTTwitter::getdays($createdDay,$nowDay) <=$numDays)){
				$html = "";
				$twitterSource = '';
				if($params->get('show_source') == 1){
					$twitterSource = (string)$item->source;
					$twitterSource = modZTTwitter::renderFromWeb($twitterSource);
				}
				$twitterTime = modZTTwitter::renderHour((string)$item->created_at,(string)$item->id,$author->screen_name,$moduleId,$params->get('show_source'),$niceTime);
				if($params->get('show_avatar')){
					$width = $params->get('size_avatar_twitter');
					$html ='<a class="thumb_author" href="http://twitter.com/'.$author->screen_name.'"><img alt="" width="'.$width.'" height="'.$width.'" src="'.$author->profile_image_url.'" title="'.$author->name.'" /></a>';
				}
				$_html ='';
				if($twitterSource !='') $_html.='<span class="jv_twitter_infos">'.$twitterSource.'</span>';
				$html.='<p class="status_body">'.$_html.'<span class="twitter_content">';
				if($params->get('show_username')==1){
					$html.='<strong class="author"><a class="twitter_author" href="http://twitter.com/'.$author->screen_name.'">'.$author->screen_name.'</a></strong>:&nbsp;';
				}
				$html .=$content.'</span><span class="jv_twitter_infos">'.$twitterTime.'</span></p>';
				$twitterItem['id'] = (float)$item->id;
				$twitterItem['content'] = $html;
				array_push($aryTwitter,$twitterItem);
			}
		}
	}
	//End function

	/*
	 * Function create and write backup xml user timline file
	 * @Created by ZooTemplate
	 */
	function writeBackupXmlFile($item,$countItem,$params){
		$count = ($countItem != '') ? "&count=$countItem" : '';
		$xmlFileBackup = 'modules/mod_zt_twitter/backup/timeline_'.$item.'.xml';
		$api_url = "http://twitter.com/statuses/user_timeline.xml?screen_name=$item".$count;
		$userTimelineData = modZTTwitter::getNormalTwitter($api_url,$http_code);
		if($userTimelineData == false && $http_code == 401) $userTimelineData = modZTTwitter::accessTwitter($params,$api_url);
		//modZTTwitter::writeBackupXmlFile($item);
		//$data = modZTTwitter::getNormalTwitter($api_url);
		if(!file_exists($xmlFileBackup)) {
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		} else {
			$xmlUnLinkHandle = unlink($xmlFileBackup);
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		}
		$writeHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
		fwrite($writeHandle,$userTimelineData);
		return $userTimelineData;
	}
	//End create backup xml file
	
	/*
	 * Function get data user timeline from twitter
	 * @Created by ZooTemplate 
	 */
	function getDataFromTwitter($apiUrl,$param){
		$twitterData = modZTTwitter::getNormalTwitter($apiUrl,$http_code);
		if($twitterData == false && $http_code == 401) $twitterData = modZTTwitter::accessTwitter($params,$apiUrl);
		return $twitterData;
	}
	//End
		
	/*
	 * Function create and write backup user information file
	 * @Created by ZooTemplate
	 */
	function writeBackupXmlUserInfo($item,$params){
		$urlTwitter = "http://twitter.com/users/show/".$item.".xml";
		$xmlFileBackup = 'modules/mod_zt_twitter/backup/user_'.$item.'.xml';
		$userTimelineData = modZTTwitter::getNormalTwitter($urlTwitter);
		if(!file_exists($xmlFileBackup)) {
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		} else {
			$xmlUnLinkHandle = unlink($xmlFileBackup);
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		}
		$writeHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
		fwrite($writeHandle,$userTimelineData);
		return $userTimelineData;
	}
	//End
	/*
	* Function create and write back up user friend xml
	* @Created by ZooTemplate
	*/
	function writeBackupXmlUserFriend($item,$params){
		$urlFriend = "http://twitter.com/statuses/friends.xml?screen_name=".$item;
		$xmlFileBackup = 'modules/mod_zt_twitter/backup/userfriend_'.$item.'.xml';
		$userTimelineData = modZTTwitter::accessTwitter($params,$urlFriend);
		if(!file_exists($xmlFileBackup)) {
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		} else {
			$xmlUnLinkHandle = unlink($xmlFileBackup);
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		}
		$writeHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
		fwrite($writeHandle,$userTimelineData);
		return $userTimelineData;
	}
	//End
	/*
	* Function create and write backup search twitter
	* @Created by ZooTemplate
	*/
	function writeBackupTwiSearch($search){
		$encryptSearch = md5($search);
		$urlSearch = "http://search.twitter.com/search.json?q=".urlencode($search);
		$xmlFileBackup = 'modules/mod_zt_twitter/backup/jvtwittersearch_'.$encryptSearch.'.json';
		$userSearchData = modZTTwitter::getNormalTwitter($urlSearch);
		if(!file_exists($xmlFileBackup)) {
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		} else {
			$xmlUnLinkHandle = unlink($xmlFileBackup);
			$xmlFileHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
			fclose($xmlFileHandle);
		}
		$writeHandle = fopen($xmlFileBackup,'w') or die('Cannot open file');
		fwrite($writeHandle,$userSearchData);
		return $userSearchData;
	}
	//End
	
	
}
?>