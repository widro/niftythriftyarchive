var Scrollbar = new Class({
	Implements: [Events, Options],
	
	scrollEnabled : false,
	
	options: {
		'offsetY' : 0,
		'wrapped' : 'inside',
		'class' : '',
		'padding' : '0',
		'fillParent' : true,
		'height:' : 0
	},
	
	initialize: function(container, options){
		this.setOptions(options);
		this.container = container;
		
		if(container.retrieve('construct'))
			this.destroy();		
		container.store('construct', true);		

		// Create a new event to catch the drag on the scrollbar
		Element.Events.drag = {
			base: 'mousemove',
			condition: function(e){
				return this.mouseScrollbar;
			}.bind(this)
		};
		
		document.addEvents({
			mouseup: function(){
				this.mouseScrollbar = false;
			}.bind(this),
			
			drag: function(e){
				this.stopDefault(e);
				var offsetParent = 0;
				if (this.scroller.getOffsetParent())
					offsetParent = this.scroller.getCoordinates().top - window.getScrollTop();
				else
					offsetParent = this.scroller.getCoordinates().top;
				
				var Y = e.client.y-offsetParent-(e.page.y-e.client.y);
				var dy = this.dragY;
				var y = Y+dy;
				var H = this.scrollable.getDimensions().height;
				var h = this.scroller.getDimensions().height;
				var hS = this.knob.getDimensions().height;
				var Hm = H-h;
				var hmS = h-hS;
				var ratio = y/hmS;
				
				if (y >= 0 && y <= hmS){
					this.knob.setStyles({'top' : y});
					this.scrollable.setStyles({'margin-top' : -ratio*Hm});
				}
			}.bind(this)
		});
		
		//this.initWrapper();
		//this.buildScrollbar();
		//this.update();
	},
	
	initWrapper: function(){
		this.wrapper = new Element('div', {
			'styles' : {
				'width' : this.container.getStyle('width').toInt()-10
			}
		});
		
		switch (this.options.wrapped){
			case 'outside':
				this.scroller = this.wrapper;
				this.scrollable = this.container;
				this.scroller.wraps(this.scrollable);
				break;
			case 'inside':
				this.scroller = this.container;
				this.scrollable = this.wrapper;
				var children = this.scroller.getChildren();
				this.scrollable.inject(this.scroller);
				this.scrollable.adopt(children);
				this.scrollable.setStyles({'padding' : this.scroller.getStyle('padding')});
				
				var height = null;
				if(this.options.height)
					height = this.options.height;
				else
					height = parseInt(this.scroller.getStyle('height'))+parseInt(this.scroller.getStyle('padding-top'))+parseInt(this.scroller.getStyle('padding-bottom'));
				
				this.scroller.setStyles({
					'width' : parseInt(this.scroller.getStyle('width'))+parseInt(this.scroller.getStyle('padding-left'))+parseInt(this.scroller.getStyle('padding-right')),
					'height' : height,
					'padding' : 0
				});
				break;
			default:
				break;
		}
		
		if (this.scroller.getStyle('position') != 'absolute' && this.scroller.getStyle('position') != 'fixed')
			this.scroller.setStyles({'position' : 'relative'});
		this.scrollable.setStyles({'position' : 'relative'});
		//this.scroller.setStyles({'overflow' : 'hidden'});
		
		this.scroller.addEvent('mousewheel', function(e){
			e.stop();
			if (this.scrollEnabled){
				if (e.wheel > 0 && parseInt(this.scrollable.getStyle('margin-top')) < 0){
					var top = parseInt(this.scrollable.getStyle('margin-top'))+20;
					this.scrollable.setStyles({
						'margin-top' : (top <= 0) ? top : 0
					});
				}
				if (e.wheel < 0) {
					var top = parseInt(this.scrollable.getStyle('margin-top'))-20;
					this.scrollable.setStyles({
						'margin-top' : (Math.abs(top) <= (this.scrollable.getDimensions().height-this.scroller.getDimensions().height)) ? top : -(this.scrollable.getDimensions().height-this.scroller.getDimensions().height)
					});
				}
				
				this.knob.setStyles({
					'top' : ( this.scroller.getDimensions().height * Math.abs(parseInt(this.scrollable.getStyle('margin-top'))) ) / this.scrollable.getDimensions().height
				});
			}
		}.bind(this));
		
		window.addEvent('resize', function(){
			this.update();
		}.bind(this));
	},
	
	buildScrollbar: function(){
		this.scrollbar = new Element('div', {
			'class' : 'scrollbar '+this.options['class'],
			'styles' : {
				'height' : this.scroller.getDimensions().height,
				'z-index' : 1
			}
		}).inject(this.scroller);
		
		this.knob = new Element('div', {
			'class' : 'knob',
			'styles' : {
				'height' : (this.scroller.getDimensions().height * this.scroller.getDimensions().height ) / this.scrollable.getDimensions().height
			},
			'events' : {
				'mousedown' : function(e){
					e.stop();
					this.mouseScrollbar = true;
					this.dragY = this.knob.getCoordinates().top-e.client.y;
				}.bind(this)
			}
		}).inject(this.scrollbar);
	},
	
	enableScrollbar: function(){
		this.scrollEnabled = true;
		this.scrollbar.setStyles({'display' : 'block'}).morph({'opacity' : 1});
	},
	
	disableScrollbar: function(){
		this.scrollEnabled = false;
		this.scrollbar.setStyles({'display' : 'none', 'opacity' : 0});
	},
	
	stopDefault: function(e){
		if (e && e.preventDefault) {
	        e.preventDefault();
	    } else {
	        window.event.returnValue = false;
	    }
	    return false;
	},
	
	update: function(){
		if (this.options.fillParent){
			var newHeight = this.scroller.getParent().getCoordinates().bottom - this.scroller.getCoordinates().top;
		} else {
			var maxHeight = window.getCoordinates().height - this.options.offsetY;
			var newHeight = (this.scrollable.getDimensions().height > maxHeight) ? maxHeight : this.scrollable.getDimensions().height;
		}

		this.scroller.setStyles({'height' : newHeight});
		this.scrollbar.setStyles({'height' : newHeight});       
		
		this.knob.setStyles({
			'height' : ( newHeight * newHeight ) / this.scrollable.getDimensions().height,
			'top' : ( newHeight * Math.abs(parseInt(this.scrollable.getStyle('margin-top'))) ) / this.scrollable.getDimensions().height
		});
		
		if (newHeight < this.scrollable.getDimensions().height){
			this.enableScrollbar();
		}
		else
			this.disableScrollbar();
		
		if ( parseInt(this.scrollable.getStyle('margin-top')) + this.scrollable.getDimensions().height < this.scroller.getDimensions().height ){
			var heightDiff = Math.abs(parseInt(this.scrollable.getStyle('margin-top')) + this.scrollable.getDimensions().height - this.scroller.getDimensions().height);
			var marginTop = 0;
			if (parseInt(this.scrollable.getStyle('margin-top')) + heightDiff < 0 )
				marginTop = parseInt(this.scrollable.getStyle('margin-top')) + heightDiff;
			this.scrollable.setStyles({'margin-top' : marginTop});
		}
	},
	
	destroy: function(){
		var to_delete = this.container.getElement('div:first-child');
		var to_inject = to_delete.getElements('> *');

		to_inject.each(function(el){
			el.inject(this.container);
		}.bind(this));
		
		this.container.setStyle('width', to_delete.getStyle('width'));
		this.container.setStyle('padding', to_delete.getStyle('padding'));
			
		this.container.getElement('div.scrollbar').destroy();
		to_delete.destroy();
	}
});


var scrollbarMenu, scrollbarSubMenu, scrollbarCart, scrollbarToday;

window.addEvent('domready', function(){
	if ($$('#left_bar').length > 0){
		scrollbarMenu = new Scrollbar($('left_bar'), {'class' : 'dark'});
	}
	
	if ($$('#sub_cart').length > 0){
		scrollbarCart = new Scrollbar($('sub_cart'), {'class' : 'bright', offsetY: $('sub_cart').getCoordinates().top, fillParent : false});
	}
	
	if($$('#sub_left_bar').length > 0){
		scrollbarToday = new Scrollbar($('sub_left_bar'), {'class' : 'bright', fillParent : true});
	}	
});