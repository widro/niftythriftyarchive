var Panels = new Class({

	initialize : function(){
		this.menu = $('left_bar');
		this.linksTab = this.menu.getElements('.open_sub');
		this.arrow = this.menu.getElement('div.tab_selected');
		this.backToTop = $('backtotopbtn');
		
		this.linksEvents();
		this.initTabs();
		this.initBackToTopBtn();
	},
	
	linksEvents: function(){
		this.linksTab.each(function(link){
			link.addEvent('click', function(e){
				e.preventDefault();
				this.switchTab(link);
			}.bind(this));
		}.bind(this));
	},
	
	initTabs: function(){
		$$('.sub[id^="sub_"]').each(function(sub){
			//sub.setStyles({'left' : this.menu.getDimensions().width - sub.getDimensions().width});
			
			sub.addEvents({
				'mouseenter' : function(){
					clearTimeout(this.timer);
				}.bind(this),
				'mouseleave' : function(){
					this.timer = this.close.delay(500, this, [sub, true]);
				}.bind(this)
			});
		}.bind(this));
	},
	
	switchTab: function(link){
		var tabOut = $$('.sub.opened')[0];
		var tabIn = $(link.get('id').replace(/open\_/, ''));
		var offsetX = this.menu.getDimensions().width;
		
		if (tabOut)
			this.close(tabOut);
		this.open(tabIn);	
	},
	
	close: function(tab, last){
		$('cart_wrapper').fade('in');
		$('cart_wrapper').removeClass('cart_wrapper');
		var offsetX = this.menu.getDimensions().width;
		var lastPanel = last || false;
		var offsetY = 60;
		var offsetY2 = -3000;
		
		tab
		.setStyles({'z-index' : 2})
		.morph({'top' : offsetY2})
		.removeClass('opened');
		
		if (lastPanel)
			this.arrow.morph({'right' : -this.arrow.getDimensions().width});
	},
	
	refresh:function(tab){
		$('cart_dyn').set('html', '<img src="https://d2tqxpnkaovy9w.cloudfront.net/Public/Files/images/cartloading.gif">');
		var offsetX = this.menu.getDimensions().width;
		var offsetY = 60;
		$('sub_cart').setStyles({'height' : 'auto'});
	
		tab
		.setStyles({'z-index' : 3})
		.morph({'top' : offsetY})
		.addClass('opened');
	
		this.arrow.morph({'right' : 0, 'top' : $('open_'+tab.get('id')).getCoordinates(this.arrow.getParent()).top});
		
		var data;
		//data.dinkers = 'dinkers';
		new Request.HTML({
		//url : slsBuild.site.protocol+'://'+slsBuild.site.domainName+'/User/Cart.sls',
		url : '/basket/my_basket',
		method: 'get',
		data : data,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){
				$('cart_dyn').set('html', responseHTML);
				$$('div#cart_dyn table tbody tr.product').each(
						function(product, index) {
							new BasketItem(product);
						}.bind(this)
				);
				
			}.bind(this)
		}).send();
	},
	
	open: function(tab){
		if( /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent) ) {
			window.location = '/basket/my_basket';
			return;
		}
		$('cart_wrapper').addClass('cart_wrapper');
		$('cart_wrapper').fade('in');
		this.refresh(tab);
	},
	
	initBackToTopBtn: function(){
		this.backToTop.setStyles({'opacity' : 0, 'display' : 'block', 'top' : (window.getCoordinates().height/3)*2});
		var morph = new Fx.Morph(this.backToTop);
		window.addEvent('scroll', function(){
			var top = window.getScrollTop();
			var windowHeight = window.getCoordinates().height;

			if (top >= (windowHeight/2) && !morph.isRunning() && parseInt(this.backToTop.getStyle('opacity')) != 1){
				morph.start({'opacity' : 1});
			} 
			if (top < (windowHeight/2)) {
				if (morph.isRunning())
					morph.cancel();
				this.backToTop.setStyles({'opacity' : 0});
			}
		}.bind(this));
		
		this.scroll = new Fx.Scroll(window);
		
		this.backToTop.addEvent('click', function(e){
			e.preventDefault();
			this.scroll.toTop();
		}.bind(this));
	}
});

var panels = null;
var backtotop = null;

window.addEvent('domready', function() {
	if ($$('#left_bar').length > 0)
		panels = new Panels();
});
