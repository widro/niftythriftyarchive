window.addEvent('domready', function(){
	new NiftyFeed();
	new FeedOptions();
});
var NiftyFeed = new Class({
	Implements:[Options],
	options:{},
	
	initialize : function(options){
		this.setOptions(options);
		
		// heck fake container
		if ($chk($('product_container')))
			this.product_container = $('product_container');
		else
			return;
		
		// check elements
		if (this.product_container.getElements('div').length != 0)
			this.elements = this.product_container.getChildren(['div']);
		else
			return;
		
		// check columns
		this.columns = [];
		if ($chk(this.product_container.getNext('div.first_column')))
			this.columns.push(this.product_container.getNext('div.first_column'));
		else
			return;
		if ($chk(this.product_container.getNext('div.second_column')))
			this.columns.push(this.product_container.getNext('div.second_column'));
		else
			return;
		if ($chk(this.product_container.getNext('div.third_column')))
			this.columns.push(this.product_container.getNext('div.third_column'));
		else
			return;
		
		this.placeElements();
		
	},
	
	placeElements : function(ajax) {
		this.delay = 0
		this.elements.each(function(element, index){
			element.setStyles({
				'display' : 'block',
				'visibility' : 'hidden'
			});
			
			var size = this.columns[0].getSize().y;
			var el = 0;
			
			// check smaller column
			for (var i=1;i<this.columns.length;i++) {
				var height = this.columns[i].getSize().y;
				if (size > height) {
					size = height;
					el = i;
				}
			}
			//insert element
			this.columns[el].grab(element);
			this.showEl.delay(this.delay, this, [element]);
			
			this.delay += 200;
		}.bind(this));
		
		if (ajax == null)
			this.product_container.destroy();
	},
	
	showEl : function(element) {
		element.setStyles({
			'opacity' : 0,
			'visibility' : 'visible'
		});
		var morhp = new Fx.Morph(element).start({
			'opacity' : 1 
		});
	},
	
	getMore : function() {
		
		// fill this.elements and call :
		this.placeElements(true);
	}
});
var FeedOptions = new Class({
	initialize : function(){
		this.container = $$('div#feed_options')[0];
		this.options_box = $$('div#feed_options ul')[0];
		
		this.options_box.setStyles({
			'display' : 'none'
		});
		
		this.initSelect();
		this.initEvents();
	},
	
	initSelect : function(){
		this.options_box.setStyles({
			'top' : this.container.getHeight() + 'px',
			'width' : (this.container.getComputedSize().totalWidth + this.container.getComputedSize()["border-right-width"]) + 'px'
		});
	},
	
	initEvents : function(){
		this.container.addEvent('click', function(){
			this.toggleSelect();
		}.bind(this));
	},
	
	toggleSelect : function(){
		if(this.container.hasClass('opened')){
			this.container.removeClass('opened');
			this.options_box.setStyles({
				'display' : 'none'
			});
		}else{
			this.container.addClass('opened');
			this.options_box.setStyles({
				'display' : 'block'
			});
		}
	}
});