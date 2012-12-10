//JS script for Joomla template
var siteurl = '';

window.addEvent('load', function(){

	var StyleCookie = new Hash.Cookie('ZTHyla25StyleCookieSite');
	var settings = { colors: '' };
	var style_1, style_2, style_3, style_4;
	if(StyleCookie.get('colors')) {
		new Asset.css(StyleCookie.get('colors'));
	}

	/* Style 1 */
	if($('ztcolor1')){$('ztcolor1').addEvent('click', function(e) {
		e = new Event(e).stop();
		if (style_1) style_1.empty();
		new Asset.css(ztpathcolor + 'green.css', {id: 'green'});
		style_1 = $('green');
		settings['colors'] = ztpathcolor + 'green.css';
		StyleCookie.empty();
		StyleCookie.extend(settings);
	});}

	/* Style 2 */
	if($('ztcolor2')){$('ztcolor2').addEvent('click', function(e) {
		e = new Event(e).stop();
		if (style_2) style_2.empty();
		new Asset.css(ztpathcolor + 'blue.css', {id: 'blue'});
		style_2 = $('blue');
		settings['blue'] = ztpathcolor + 'blue.css';
		StyleCookie.empty();
		StyleCookie.extend(settings);
	});}

	/* Style 3 */
	if($('ztcolor3')){$('ztcolor3').addEvent('click', function(e) {
		e = new Event(e).stop();
		if (style_3) style_3.empty();
		new Asset.css(ztpathcolor + 'violet.css', {id: 'violet'});
		style_3 = $('violet');
		settings['colors'] = ztpathcolor + 'violet.css';
		StyleCookie.empty();
		StyleCookie.extend(settings);
	});}
	
	/* Style 4 */
	if($('ztcolor4')){$('ztcolor4').addEvent('click', function(e) {
		e = new Event(e).stop();
		if (style_4) style_4.empty();
		new Asset.css(ztpathcolor + 'red.css', {id: 'red'});
		style_4 = $('red');
		settings['colors'] = ztpathcolor + 'red.css';
		StyleCookie.empty();
		StyleCookie.extend(settings);
	});}

});
