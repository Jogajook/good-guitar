// JavaScript Document
window.addEvent('domready', function() {
	// Anpassung IE6
	if(window.ie6)
		var heightValue = '100%';
	else
		var heightValue = '';
	
	//If Slide = Yes
	if(slide)
	{
		toggler = document.getElements('a[class^=trigger]');
		element	= document.getElements('ul[class^=jv_amenu_items]');
		for(i = 0; i < toggler.length; i++)
		{
			tclass = toggler[i].getProperty('class').split(' ')[1];
			eclass = element[i].getProperty('class').split(' ')[1];
			
			toggler[i].removeClass(tclass);
			element[i].removeClass(eclass);
			
			toggler[i].addClass('jv_menu_toggler_'+i);
			element[i].addClass('jv_menu_content_'+i);
		}
	}
	//End slide
	
	// Selektoren der Container fr Schalter und Inhalt
	var togglerName	= 'a.jv_menu_toggler_';
	var contentName	= 'ul.jv_menu_content_';
	
	// Selektoren setzen
	var counter		= 0;	
	var toggler		= $$(togglerName + counter);
	var element		= $$(contentName + counter);	
	
	while(toggler.length > 0)
	{		
		for(i = 0; i < toggler.length; i++)
		{
			var id = toggler[i].getParent().getParent().getProperty('id');
			if(exppand_active)
			{
				if(active.contains(id))
				{
					new Accordion(toggler, element, {
						opacity: false,
						display: i,
						alwaysHide: true,
						onComplete: function() { 
							var element = $(this.elements[this.previous]);
							if(element && element.offsetHeight>0) element.setStyle('height', heightValue);			
						},
						onActive: function(toggler, element) {
							toggler.addClass('open');
						},
						onBackground: function(toggler, element) {
							toggler.removeClass('open');
						}
					});
					i = toggler.length + 1;
				}
				else
				{
					if(i == (toggler.length -1))
					new Accordion(toggler, element, {
						opacity: false,
						display: -1,
						alwaysHide: true,
						onComplete: function() { 
							var element = $(this.elements[this.previous]);
							if(element && element.offsetHeight>0) element.setStyle('height', heightValue);			
						},
						onActive: function(toggler, element) {
							toggler.addClass('open');
						},
						onBackground: function(toggler, element) {
							toggler.removeClass('open');
						}
					});
				}
			}
			else
			{
				new Accordion(toggler, element, {
					opacity: false,
					display: -1,
					alwaysHide: true,
					onComplete: function() { 
						var element = $(this.elements[this.previous]);
						if(element && element.offsetHeight>0) element.setStyle('height', heightValue);			
					},
					onActive: function(toggler, element) {
						toggler.addClass('open');
					},
					onBackground: function(toggler, element) {
						toggler.removeClass('open');
					}
				});
				i = toggler.length + 1;
			}
		}
		// Selektoren fr nchstes Level setzen
		counter ++;
		toggler = $$(togglerName + counter);
		element = $$(contentName + counter);
	}
});