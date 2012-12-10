/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
var ZTSplitTwitter = new Class({
	initialize: function(urlAjax, moduleId, wrapAccordId, noTwitter, isAccord, twitterWrap, isMerge){
		var request 	= new Request.JSON({url:urlAjax, onSuccess: function(jsonObj){
			if(jsonObj.twitter)
			{
				var divWrap = $(twitterWrap);
				divWrap.removeClass('twitter-ajax-loading');
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
			}
		}}).get({'moduleId' : moduleId, 'isMerge' : isMerge, 'isAccord' : isAccord});
	}
});