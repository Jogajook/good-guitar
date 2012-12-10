(function( $ ){ 
	function ZT_AccordionSetting(obj,settings) {		
			
			this.remiSettings = settings;
			this.remiAnimate = animate;			
            var _this = this;
			
			function animate(obj,i){ 
				$.each(obj, function(j) {
					var otherDim = Math.round(  (  (_this.remiSettings.closeDim*obj.length)-(_this.remiSettings.openDim)  )/(obj.length-1)  );			
					var itemDim = otherDim;			
					if ( j == i ) {
						itemDim = _this.remiSettings.openDim;
					}
					if (typeof i == 'undefined') {
						if (_this.remiSettings.openItem == null) itemDim = _this.remiSettings.closeDim;
						else if (_this.remiSettings.openItem == j) itemDim = _this.remiSettings.openDim;
						else itemDim = otherDim;
					}
					
					if (_this.remiSettings.position == 'vertical')
						$(this).animate({'height':itemDim},_this.remiSettings.duration,_this.remiSettings.effect);
					else 
						$(this).animate({'width':itemDim},_this.remiSettings.duration,_this.remiSettings.effect);
						
					var title = $('span',this);
					
					title.stop(true,false);
					
					if (_this.remiSettings.fadeInTitle != null && title.length > 0) {
						if (itemDim == _this.remiSettings.openDim) {
							if (_this.remiSettings.fadeInTitle) title.animate({'opacity':0.7});
							else title.animate({'opacity':0});		
						} else {
							if (_this.remiSettings.fadeInTitle) title.animate({'opacity':0});
							else title.animate({'opacity':0.7});
						}
					}						
				});		
			
			}

			var $this = $('div.item',obj);
			
			_this.remiAnimate($this);
			
			var maxItem = _this.remiSettings.closeDim*$this.length + _this.remiSettings.border*$this.length + 10;
			
			if (_this.remiSettings.position == 'vertical') 
				$(obj).css({'width':_this.remiSettings.width+'px','height':maxItem+'px'});
			else 
				$(obj).css({'height':_this.remiSettings.height+'px'});		 
			
			$.each($this, function(i) {	 
				ImgSrc = $('img',this).attr('src'); 
				var borderBottomValue = 0;
				var borderRightValue = 'solid '+_this.remiSettings.border+'px '+_this.remiSettings.color;
				var aWidth = 'auto';			
				var aHeight = _this.remiSettings.height+'px'; 
				if (_this.remiSettings.position == 'vertical') { 
					borderBottomValue = 'solid '+_this.remiSettings.border+'px '+_this.remiSettings.color;
					borderRightValue = 0;
					aWidth = _this.remiSettings.width+'px';				
					aHeight = 'auto';				
				}	 
				if ( i == ($this.length-1)) {
					borderBottomValue = 0;
					borderRightValue = 0;
				}  	
				$(this).css({  		
				}).mouseenter(function() {
					$this.stop(true,false);
					_this.remiAnimate($this,i);
				});	 
			}); 
			$(obj).mouseleave(function() {
				_this.remiAnimate($this);
			}); 
	}
	
	$.fn.ZT_Accordion = function( options ) {	 
		var settings = { 
		}; 
		return this.each(function() { 	 
			$('br',this).remove();
			if ( options ) $.extend( settings, options );			
			var menu = new ZT_AccordionSetting(this,settings);
		});
	}; 
})( jQuery );