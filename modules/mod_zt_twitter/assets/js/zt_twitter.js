/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
var ZTTwitter = new Class({
	initialize: function(urlAjax, moduleId, wrapTwitter, statusId, isAccord, isMerge){
		var request 	= new Request.JSON({url:urlAjax, onSuccess: function(jsonObj){
			var divWrap = $(wrapTwitter);
			divWrap.removeClass('twitter-ajax-loading');
			//alert(urlAjax+'?moduleId='+moduleId+'&isMerge='+isMerge);
			if(jsonObj.twitter)
			{
				divWrap.innerHTML = jsonObj.twitter;
				if(isAccord == 1)
				{			
					new Accordion($$('div.jv_header_wrapper'), $$('div.twitter_content'), {
						opacity: false,
						display: 0,
						alwaysHide: true,
						onActive: function(toggler, element) {
							toggler.setStyle('opacity','1');
						},
						onBackground: function(toggler, element) {
							toggler.setStyle('opacity','0.5');
						}						
					});
				}
				$('jv_twitter_follow'+moduleId).setStyle('display','none');
			}
		}}).get({'moduleId' : moduleId, 'isMerge' : isMerge, 'isAccord' : isAccord});
	}
});