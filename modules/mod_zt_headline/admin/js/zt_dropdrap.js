/**
 * @package ZT Tabs module for Joomla! 2.5
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
var ZTdrapdrop = new Class({
	Implements: [Events, Options],
	options: {
		handles: false,
		containers: false,
		onStart: Class.empty,
		onComplete: Class.empty,
		sfield: true,
		classmove: '1',
		onDragStart: function(element, sfield){
			sfield.setStyles({'opacity':0.8, 'z-index':10});
			element.getChildren().setStyles({'opacity':0.1, 'z-index':1});
			element.addClass('change');
			this.fieldlists.addClass ('remove-drag');
		},
		onDragComplete: function(element, sfield){
			element.getChildren().setStyle('opacity', 1);
			element.removeClass('change');
			this.fieldlists.removeClass ('remove-drag'); 	
			this.trash.removeChild(sfield);
			this.trash.dispose();
		}
	},
	initialize: function(fieldlists, options){	
		this.setOptions(options);
		this.fieldlists = $$(fieldlists);
		this.elements = [];
		this.handles = [];	
		this.fieldlists.each(function(field){
			var elements = field.getChildren();
			elements.each(function(el,i){
				el.typeList = field; 
				if(this.options.classmove){
					el.order = el.getElement(this.options.classmove);
					if (!el.order) return;
					tmp = el.order.getParent();
					el.order.dispose();
					el.order.injectTop(tmp); 
					el.itemlist = i;
					var handle = el.getElement('.move') 
					this.handles.push(handle);
				}
			},this);
			this.elements.combine(elements);
			this.fieldlists.setStyle('visibility','visible');
			
		},this);
		this.handles = (this.options.handles) ? $$(this.options.handles) : (this.handles.length?this.handles:this.elements);
		this.bound = {
			'start': [],
			'movesfield': this.movesfield.bindWithEvent(this)
		};
		for (var i = 0, l = this.handles.length; i < l; i++){
			this.bound.start[i] = this.start.bindWithEvent(this, this.elements[i]);
			
		}	
									
		this.action();
		
		if (this.options.initialize) this.options.initialize.call(this);
		this.bound.move = this.move.bindWithEvent(this);
		this.bound.complete = this.complete.bind(this);	

	},
	action: function(){
		this.handles.each(function(handle, i){
			handle.addEvent('mousedown', this.bound.start[i]);
			handle.setStyle('cursor','move');
		}, this);
	},
	start: function(event, el){
		this.active = el;
		if (this.options.sfield){
			this.previous = 0;
			var position = el.getPosition();
			this.offsetX = event.page.x - position.x;
			this.offsetY = event.page.y - position.y;
			this.trash = new Element('div', {'class':'sfield'}).inject(document.body);
			this.sfield = el.clone().inject(this.trash).setStyles({
				'position': 'absolute',
				'width': el.offsetWidth
			});		
			document.addEvent('mousemove', this.bound.movesfield);
			this.fireEvent('onDragStart', [el, this.sfield]);
		}
		document.addEvent('mousemove', this.bound.move);
		document.addEvent('mouseup', this.bound.complete);
		this.fireEvent('onStart', el);
		event.stop();
	},
	movesfield: function(event){  
		this.sfield.setStyles({'left': event.page.x-this.offsetX,'top': event.page.y-this.offsetY});
		event.stop();
	},
	move: function(event){
		var numlocation = event.page.y; 
		this.previous = this.previous || numlocation;
		var up = ((this.previous - numlocation) > 0);
		var prev = this.active.getPrevious();
		var next = this.active.getNext();
		if (prev && up && numlocation < prev.getCoordinates().bottom) this.active.injectBefore(prev);
		if (next && !up && numlocation > next.getCoordinates().top) this.active.injectAfter(next);
		this.previous = numlocation;
	},
	complete: function(){
		this.previous = null;
		document.removeEvent('mousemove', this.bound.move);
		document.removeEvent('mouseup', this.bound.complete);
		if (this.options.sfield){
			document.removeEvent('mousemove', this.bound.movesfield);
			this.fireEvent('onDragComplete', [this.active, this.sfield]);
		}
		this.fireEvent('onComplete', this.active); 
		
		var art_id = $('jform_params_text_data');
		var itemorder = '';
		$$('#custom_list input.hiddendata').each(function(item,i){
			itemorder = itemorder +  item.value  + '||'; 
		});
		art_id.innerHTML = itemorder;
	}
});
ZTdrapdrop.implement(new Events, new Options); 