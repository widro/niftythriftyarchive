window.addEvent('domready',function() {

	var hashtag = window.location.hash;
	if(hashtag=="#registerbonus"){
		openregisterbonus();
	}




	function openregisterbonus(){
		var body = $$('body')[0];
		var login_wrapper = $('opa_wrapper');
		var login_wrapper_inner = $('registerbonus');
		var login_wrapper_inner2 = $('user_login');
		    login_wrapper_inner2.setStyles({
			'display' : 'none'
		    });
		login_wrapper.setStyles({
		'display' : 'block'
		});

		login_wrapper.setStyles({
		'display' : 'block'
		});

		login_wrapper_inner.setStyles({
		'display' : 'block'
		});

		new Fx.Scroll(window, {
		'onComplete' : function(){
		    login_wrapper.fade('in');
		    body.setStyles({
			'overflow' : 'hidden'
		    });
		    login_wrapper.setStyles({
			'width' : window.getWidth() + 'px'
		    });
		}
		}).toTop();

		var redirect = '<xsl:value-of select="//View/homepage/redirect" />';

		if (redirect != '') {
		$('login_redirect').set('value', redirect);
		}
	
	}
if(document.id("products")||document.id("suggest")){




	if(document.id("suggest")){
		var activearea = document.id("suggest");
	}
	else if(document.id("products")){
		var activearea = document.id("products");
	}

	activearea.addEvent("click:relay(span.addtocarthover)", function(e){
	
		//console.log(e);
		var clickedid = e.target.id;
		var activeelement = $(clickedid);
		//var urltouse = "/User/AddItem/Id/+"+clickedid;
		var urltouse = "/app_dev.php/basket/add_item_to_basket/"+clickedid;
		

		new Request.JSON({
			url : urltouse,
			data : {
				'Ajax' : 1
			},
			method : 'get',
			onComplete : function(xhr) {
				activeelement.addClass('addtocarthover_reserved');
				activeelement.set('html', 'RESERVED');

				var stink = activeelement.parentNode.parentNode;
				//$(stink).addClass("product_sold");
				var flag  = new Element("div"); 
				$(flag).addClass("status");
				$(flag).addClass("reserved");
				var addtoprod = new Elements([flag]); 
				$(stink).adopt(addtoprod);

				var currentitems = $('items_in_cart').get('html');
				newitems = parseInt(currentitems)+1;
				$('items_in_cart').set('html', newitems);

				//if (!xhr.error.length) {
					panels.open($('sub_cart'));
				//} 
			}.bind(this)
		}).send();
	}.bind(this));

	activearea.addEvent("mouseover:relay(div.product)", function(event,node){
		//alert('dinkerssss');
		$(this).getElementById('addtocarthoverdiv').setStyle('display','inline');
	});
	activearea.addEvent("mouseout:relay(div.product)", function(event,node){
		//alert('dinkerssss');
		$(this).getElementById('addtocarthoverdiv').setStyle('display','none');
	});

}

if($$('.add_to_cart_btn')){
	$$('.add_to_cart_btn').addEvent("click", function(e){
		var clickedid = e.target.id;
		var activeelement = $(clickedid);
		//var urltouse = "/User/AddItem/Id/+"+clickedid;
		var urltouse = "/app_dev.php/basket/add_item_to_basket/"+clickedid;
		new Request.JSON({
			url : urltouse,
			data : {
				'Ajax' : 1
			},
			method : 'get',
			onComplete : function(xhr) {
				activeelement.setStyles({'display' : 'none'});
				$('reserved_item').setStyles({'display' : 'inline'});
				$('reserved_item').setStyles({'background-color' : '#B6B6B6'});
				
				var currentitems = $('items_in_cart').get('html');
				newitems = parseInt(currentitems)+1;
				$('items_in_cart').set('html', newitems);

				//if (!xhr.error.length) {
					panels.open($('sub_cart'));
				//} 
			}.bind(this)
		}).send();
	}.bind(this));
}


//gallery
if($$('.addtolovehover')){
	document.addEvent("click:relay(span.addtolovehover)", function(e){
	//$$('.addtolovehover').addEvent("click", function(e){
		var clickediddiv = e.target.id;
		var clickedid = clickediddiv.substr(4);
		var activeelement = $(clickediddiv);
	
		var urltouse = "/app_dev.php/user/love_item/"+clickedid;
		new Request.JSON({
			url : urltouse,
			data : {
				'Ajax' : 1
			},
			method : 'get',
			onComplete : function(xhr) {
				if($$('div.loveheartongallery')){
					var doubleparent = activeelement.getParent().getParent();
					var doublechild = doubleparent.getElements('div.img');
					var doublechild2 = doubleparent.getElements('div.loveheartongallery');
					if(xhr.action=="loved"){
						doublechild2.addClass('loveheartongallery_pink');
						activeelement.addClass('addtolovehover_pink');
					}
					else if(xhr.action=="unloved"){
						doublechild2.removeClass('loveheartongallery_pink');
						activeelement.removeClass('addtolovehover_pink');
					}
				}
			}.bind(this)
		}).send();
		
		
	}.bind(this));
}


//prod page
if($$('.addtolovehover_page')){
	$$('.addtolovehover_page').addEvent("click", function(e){
		var clickediddiv = e.target.id;
		var clickedid = clickediddiv.substr(4);
		var activeelement = $(clickediddiv);
	
		var urltouse = "/app_dev.php/user/love_item/"+clickedid;
		new Request.JSON({
			url : urltouse,
			data : {
				'Ajax' : 1
			},
			method : 'get',
			onComplete : function(xhr) {
				if(xhr.action=="loved"){
					activeelement.addClass('addtolovehover_page_pink');
				}
				else if(xhr.action=="unloved"){
					activeelement.removeClass('addtolovehover_page_pink');
				}

			}.bind(this)
		}).send();
		
		
	}.bind(this));
}




	document.id("cart_dyn").addEvent("click:relay(span.remove)", function(e){
		//alert('NEW delete');
		var clickedid = e.target.id;
		var activeelement = $(clickedid);
		//var urltouse = "/User/DeleteItem/Id/+"+clickedid;
		var urltouse = "/app_dev.php/basket/remove_item_from_basket/"+clickedid;
		new Request.JSON({
			url : urltouse,
			data : {
				'Ajax' : 1
			},
			method : 'get',
			onComplete : function(xhr) {
				var currentitems = $('items_in_cart').get('html');
				newitems = parseInt(currentitems)-1;
				$('items_in_cart').set('html', newitems);

				if (!xhr.error) {
					panels.refresh($('sub_cart'));
				}
			}.bind(this)
		}).send();
	}.bind(this));

	//getElementsByClassName

	//var productsdiv = $('products');

	/*
	var product_hover = $$('.product');
	if (product_hover.length > 0) {
		$$('.product').addEvent('mouseover', function(){
			$(this).getElementById('addtocarthoverdiv').setStyle('display','inline');
		});
	
		$$('.product').addEvent('mouseout', function(){	
			$(this).getElementById('addtocarthoverdiv').setStyle('display','none');
		});
	}
	*/


	if($('detail_condition')){

		$('detail_condition').addEvent('click', function(){
			expandcollapse('detail_condition');
		});
		$('detail_fabric').addEvent('click', function(){
			expandcollapse('detail_fabric');
		});
		$('detail_measurements').addEvent('click', function(){
			expandcollapse('detail_measurements');
		});
		$('detail_shipping').addEvent('click', function(){
			expandcollapse('detail_shipping');
		});
	}	

	function expandcollapse(divname){
		if($(divname).hasClass('expanded')){
			$(divname).removeClass('expanded');
			$(divname).getElementById('bottom').setStyle('display','none');
			$(divname).getElementById('top').getElementById('productplus').set('html','+');
		}
		else{
			$(divname).addClass('expanded');
			$(divname).getElementById('bottom').setStyle('display','inline');
			$(divname).getElementById('top').getElementById('productplus').set('html','-');
		}
	}

/*
	$('hovernav').addEvent('mouseover', function(){
		$('hovernav').setStyle('display','inline');
		$('hovernav').setStyle('height','auto');
		$('topnav_shops').addClass('topnavarrow');
	});
	$('hovernav').addEvent('mouseout', function(){
		$('hovernav').setStyle('display','none');
		$('hovernav').setStyle('height','0px');
		$('topnav_shops').removeClass('topnavarrow');
	});

	$('topnav_shops').addEvent('mouseover', function(){
		$('hovernav').setStyle('display','inline');
		$('hovernav').setStyle('height','auto');
	});

	$('topnav_shops').addEvent('mouseout', function(){
		$('hovernav').setStyle('display','none');
		$('hovernav').setStyle('height','0px');
	});

	$('hovernav2').addEvent('mouseover', function(){
		$('hovernav2').setStyle('display','inline');
		$('hovernav2').setStyle('height','auto');
		$('topnav_sales').addClass('topnavarrow');
	});
	$('hovernav2').addEvent('mouseout', function(){
		$('hovernav2').setStyle('display','none');
		$('hovernav2').setStyle('height','0px');
		$('topnav_sales').removeClass('topnavarrow');
	});

	$('topnav_sales').addEvent('mouseover', function(){
		$('hovernav2').setStyle('display','inline');
		$('hovernav2').setStyle('height','auto');
	});

	$('topnav_sales').addEvent('mouseout', function(){
		$('hovernav2').setStyle('display','none');
		$('hovernav2').setStyle('height','0px');
	});
*/

	if($('gridviewlinkimg')){
		$('gridviewlinkimg').addEvent('click', function(){
			if(this.hasClass("list")){
				$('grid_grid_view1').setStyle('display','');
				$('grid_grid_view2').setStyle('display','');
				$('grid_list_view').setStyle('display','none');
				$('grid_header2').setStyle('display','none');
				$('grid_header3').setStyle('display','none');
				this.removeClass("list");
				this.src = "https://d2tqxpnkaovy9w.cloudfront.net/Public/Files/images/grid_icons_grid.png";
			}
			else{
				$('grid_grid_view1').setStyle('display','none');
				$('grid_grid_view2').setStyle('display','none');
				$('grid_list_view').setStyle('display','');
				$('grid_header2').setStyle('display','');
				$('grid_header3').setStyle('display','');
				this.addClass("list");
				this.src = "https://d2tqxpnkaovy9w.cloudfront.net/Public/Files/images/grid_icons_list.png";
			}
		});
	}	
	
	
	if($('search_button_topnav')){
		$('search_button_topnav').addEvent('click', function(){
			
			var searchtext = $('search_text_topnav').value;
		    window.location = '/search/get_items_by_search/'+searchtext;
	
		});
	}	
	
if($('slideshow-container')){
  /* settings */
  var showDuration = 3000;
  var container = $('slideshow-container');
  var images = container.getElements('img');
  var currentIndex = 0;
  var interval;
  /* opacity and fade */
  images.each(function(img,i){ 
    if(i > 0) {
      img.set('opacity',0);
    }
  });
  /* worker */
  var show = function() {
    images[currentIndex].fade('out');
    images[currentIndex = currentIndex < images.length - 1 ? currentIndex+1 : 0].fade('in');
    var nextIndex = currentIndex < images.length - 1 ? currentIndex+1 : 0;
    
    if($('featured_left_cell1')){
		$('featured_left_cell'+currentIndex).setStyle('background','#000000');
		$('featured_left_cell'+nextIndex).setStyle('background','#282828');
    }
    
  };
  /* start once the page is finished loading */
  window.addEvent('load',function(){
    interval = show.periodical(showDuration);
  });
}	



});
