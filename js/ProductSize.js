window.addEvent('domready', function(){
	var activeimage = 1;
	
	if($chk($('nifty_box')))
		new ProductSize();
	if($chk($('picture_full'))){
		new ProductFull();
	}
});
var ProductFull = new Class({
	initialize : function(){
		this.box = $('picture_full');
		this.panes_container = $$('div.product_sizes div.panes')[0];
		this.panes = $$('div.product_sizes div.panes div.pane');
		this.categories = $$('div.product_sizes div.categories a');
		this.close = $$('div.product_full_images a.close_btn')[0];
		this.current = $$('div.product_sizes div.panes div.pane')[0];
		this.open_links = $$('body a.show_full');
		this.thumbs = $$('img.sub_picture2');
		
		if(this.panes.length != this.categories.length)
			return;
		
		this.initSizesBox();
		this.initEvents();
	},
	
	initSizesBox : function(){
		this.box.inject($$('body')[0], 'top');
		
		this.box.setStyles({
			'width' : window.getWidth() + 'px',
			'height' : '2000px',
			'opacity' : '1'
		}); 
		
	},
	
	initEvents : function(){
		this.categories.each(function(category){
			category.addEvent('click', function(e){
				$$('div.product_sizes div.categories a.selected')[0].removeClass('selected');
				category.addClass('selected');
				this.showPane(category.retrieve('pane'));
				e.stop();
			}.bind(this));
		}.bind(this));
		
		this.open_links.each(function(link){
			link.addEvent('click', function(e){
				this.box.setStyles({
					'display' : 'block',
					'opacity' : '0'
				});
				new Fx.Morph(this.box,{
					'link' : 'cancel'
				}).start({
					'opacity' : '1'
				});
				e.stop();
			}.bind(this));
		}.bind(this));
		
		this.thumbs.addEvent('click', function(e){
		
			var jdzoomurl = $('jd_zoomed_in_image_bgs').getStyle('background-image');
			
		
		
			var activeimage = $('active_thumb').get("html");
			var clickedthumb = e.target.id.substring(10, 11);
			
			$('active_thumb').set("html", clickedthumb);
			
			if(e.target.id.indexOf("full")== -1){
				var clickedimage_1 = $('page_image'+clickedthumb);
				var activeimage_1 = $('page_image'+activeimage);

				var clickedimage_2 = $('full_image'+clickedthumb);
				
				var activeimage_2 = $('full_image'+activeimage);
				var clickedimage_url_full = clickedimage_1.getParent('a').get('href');
			}
			else{
				var clickedimage_1 = $('full_image'+clickedthumb);
				var activeimage_1 = $('full_image'+activeimage);

				var clickedimage_2 = $('page_image'+clickedthumb);
				var activeimage_2 = $('page_image'+activeimage);
				var clickedimage_url_full = clickedimage_2.getParent('a').get('href');
			}
			var jdzoomurl = $('jd_zoomed_in_image_bgs').setStyles({
						'background-image' : 'url('+clickedimage_url_full+')'
					});
			
			new Fx.Morph(activeimage_1,{
				'link' : 'cancel',
				'onComplete' : function(){
					activeimage_1.setStyles({
						'display' : 'none'
					});
					clickedimage_1.setStyles({
						'display' : 'inline'
					});
					new Fx.Morph(clickedimage_1,{
						'link' : 'cancel'
					}).start({
						'opacity' : '1'
					});
				}.bind(this)
			}).start({
				'opacity' : '0'
			});

			//also make full image show properly
			activeimage_2.setStyles({
				'display' : 'none',
				'opacity' : '0'
			});
			clickedimage_2.setStyles({
				'display' : 'inline',
				'opacity' : '1'
			});

			
			
			//handle thumbs borders
			$('page_thumb'+clickedthumb).parentNode.addClass('img_product_thumb_active');
			$('page_thumb'+activeimage).parentNode.removeClass('img_product_thumb_active');

			$('full_thumb'+clickedthumb).parentNode.addClass('img_product_thumb_active');
			$('full_thumb'+activeimage).parentNode.removeClass('img_product_thumb_active');
			





						
		}.bind(this));
		
		this.close.addEvent('click', function(e){
			new Fx.Morph(this.box,{
				'link' : 'cancel',
				'onComplete' : function(){
					this.box.setStyles({
						'display' : 'none'
					});
				}.bind(this)
			}).start({
				'opacity' : '0'
			});
			e.stop();
		}.bind(this));
	},
	
});
var ProductSize = new Class({
	initialize : function(){
		this.box = $('nifty_box');
		this.panes_container = $$('div.product_sizes div.panes')[0];
		this.panes = $$('div.product_sizes div.panes div.pane');
		this.categories = $$('div.product_sizes div.categories a');
		this.close = $$('div.product_sizes a.close_btn')[0];
		this.current = $$('div.product_sizes div.panes div.pane')[0];
		this.open_links = $$('body a.show_sizes');
		
		if(this.panes.length != this.categories.length)
			return;
		
		this.initSizesBox();
		this.initEvents();
	},
	
	initSizesBox : function(){
		this.box.inject($$('body')[0], 'top');
		
		this.box.setStyles({
			'width' : window.getWidth() + 'px',
			'height' : window.getHeight() + 'px',
			'opacity' : '1'
		}); 
		
		this.categories.each(function(category, index){
			category.store('pane', this.panes[index]);
		}.bind(this));
	},
	
	initEvents : function(){
		this.categories.each(function(category){
			category.addEvent('click', function(e){
				$$('div.product_sizes div.categories a.selected')[0].removeClass('selected');
				category.addClass('selected');
				this.showPane(category.retrieve('pane'));
				e.stop();
			}.bind(this));
		}.bind(this));
		
		this.open_links.each(function(link){
			link.addEvent('click', function(e){
				this.box.setStyles({
					'display' : 'block',
					'opacity' : '0'
				});
				new Fx.Morph(this.box,{
					'link' : 'cancel'
				}).start({
					'opacity' : '1'
				});
				e.stop();
			}.bind(this));
		}.bind(this));
		
		this.close.addEvent('click', function(e){
			new Fx.Morph(this.box,{
				'link' : 'cancel',
				'onComplete' : function(){
					this.box.setStyles({
						'display' : 'none'
					});
				}.bind(this)
			}).start({
				'opacity' : '0'
			});
			e.stop();
		}.bind(this));
	},
	
	showPane : function(pane){
		new Fx.Morph(this.current, {
			'link' : 'cancel',
			'duration' : 150,
			'onComplete' : function(){
				this.current.setStyles({
					'visibility' : 'hidden',
					'display' : 'none'
				});
				pane.setStyles({
					'visibility' : 'visible',
					'display' : 'block'
				});
				
				new Fx.Morph(pane, {
					'link' : 'cancel',
					'duration' : 150,
					'onComplete' : function(){
						this.current = pane;
					}.bind(this)
				}).start({
					'opacity' : '1'
				});
				
			}.bind(this)
		}).start({
			'opacity' : '0'
		});
	}
});
