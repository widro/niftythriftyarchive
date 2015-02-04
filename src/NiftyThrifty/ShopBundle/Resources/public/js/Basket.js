var basket = null;
window.addEvent('domready', function() {
	basket = new Basket();
});


var Basket = new Class({
	initialize : function() {
		this.my_cart = $('open_sub_cart');
		this.products = $$('div#sub_cart.sub table tbody tr.product');
		this.basket_items_list = $$('div#sub_cart.sub table tbody')[0];
		
		$$('div#sub_cart.sub table tbody tr.product').each(function(product, index){
			new BasketItem(product);
		}.bind(this));
		this.setNbProductsInBasket();
		
		this.btn_sale = $$('#sale_item');
		if(this.btn_sale.length > 0){
			this.btn_sale = this.btn_sale[0];
			this.btn_sale.addEvent('click', function(e){
				e.stop();
				this.addBasketItem();
			}.bind(this));
		}
	},
	
	getBasketPrice: function(){
		this.products = $$('div#sub_cart.sub table tbody tr.product');
		var basket_price = 0;
		this.products.each(function(product, index){
			basket_price += product.getElements('div.price_final span')[0].get('text')*1;
		});
		
		return basket_price.format({
				    decimal: ".",
				    decimals: 2
				});
	},
	
	updateBasketPrice: function(){
		$('order_subtotal_value').getElement('span').set('text', this.getBasketPrice());
		scrollbarCart.update();
	},
	
	deleteBasketItem: function(product){
		new Request.JSON({
			url: product.getElements('a.remove').get('href'),
			data: {
				'Ajax':1
			},
			method: 'post',
			onComplete: function(){
				product.getNext().destroy();
				product.destroy();
				
				this.updateBasketPrice();
				this.setNbProductsInBasket();
				
				if ($$('#sub_cart tr.product').length == 0){
					$$('#sub_cart #no_products')[0].setStyles({'display' : 'block'});
				}
				scrollbarCart.update();
			}.bind(this)
		}).send();
	},
	
	expireBasketItem: function(product){
		product.getNext().destroy();
		product.destroy();
		this.updateBasketPrice();
		this.setNbProductsInBasket();
	},
	
	changeButton: function(mode){
		$$('div#add_to_basket').set('class', mode);
	},
	
	addBasketItem: function(){
		new Request.JSON({
			url: this.btn_sale.get('href'),
			data: {
				'Ajax':1
			},
			method: 'post',
			onComplete: function(xhr){
				if(!xhr.error.length){
					var product = this.getBasketItemProductHTML(xhr.product);
					var sepa = this.getBasketItemSepaHtml();
	
					product.inject(this.basket_items_list);
					sepa.inject(this.basket_items_list);
					
					new BasketItem(product);
					this.updateBasketPrice();
					this.setNbProductsInBasket();
					this.changeButton('reserved');
					$('ajax_result').removeClass('error').addClass('add_success').set('text', xhr.success);
				}
				else{
					$('ajax_result').removeClass('success').addClass('add_error').set('text', xhr.error);
				}
				scrollbarCart.update();
			}.bind(this)
		}).send();
		
		$$('#sub_cart #no_products')[0].setStyles({'display' : 'none'});
	},
	
	getBasketItemProductHTML: function(product_datas){
		var html = '';
		html += '	<td><a href="'+product_datas.product_url+'" title=""><img class="cart_pic" src="'+product_datas.product_visual1+'" /></a></td>';
		html += '	<td>';
		html += '		<div class="description_name">'+product_datas.product_name+'</div>';
		html += '		<div class="description_size">'+product_datas.product_category_size_value+'</div>';
		html += '	</td>';
		html += '	<td>';
		html += '		<div class="delivery">'+product_datas.basket_item_delivery+'</div>';
		html += '	</td>';
		html += '	<td>';
		html += '		<div class="time_remaining active"><span>'+product_datas.basket_item_time_remaining+'</span> min.</div>';
		html += '	</td>';
		html += '	<td>';
		html += '		<div class="price_final">$<span>'+product_datas.basket_item_price+'</span></div>';
		
		if(product_datas.basket_item_discount != 0){
			html += '			<div class="price">$<span>'+product_datas.basket_item_final_price+'</span></div>';
		}
	
		html += '	</td>';
		html += '	<td>';
		html += '		<a href="'+product_datas.basket_item_delete_from_basket+'" class="remove"></a>';
		html += '	</td>';
		
		var div = new Element('tr', {
			'class' : 'product',
			'id' : 'basket-item-'+product_datas.product_id,
			'html' : html
		});

		return div;
	},

	getBasketItemSepaHtml: function(){
		var html = '';
		html += '	<td colspan="6"><div class="cart_separator"></div></td>';		
		var div = new Element('tr', {
			'html' : html,
			'class' : 'sepa'
		});
		return div;
	},
	
	setNbProductsInBasket: function(){
		this.products = $$('div#sub_cart.sub table tbody tr.product');
		var nb_products = this.products.length;
		
		if(nb_products == 0){
			this.my_cart.getElement('#in_cart').setStyle('display','none');
		}
		else{
			this.my_cart.getElement('#in_cart').setStyle('display','block');
		}
		
		this.my_cart.getElement('#in_cart').set('text', this.products.length);
		this.centerMyCart();
	},
	
	centerMyCart: function(){
		var children = this.my_cart.getChildren();
		var width = 0;
		children.each(function(el){
			width += el.getSize().x+el.getStyle('margin-left').toInt();
		});
		this.my_cart.setStyle('width', width);
	}
});

var BasketItem = new Class({
	initialize : function(product) {
		this.product = product;
		this.time_remaining = this.product.getElements('div.time_remaining')[0];
		
		if(this.time_remaining.hasClass('active')){
			var tmp = this.time_remaining.getElement('span').get('text').split(':');
			this.time_remaining_min = tmp[0]*1;
			this.time_remaining_sec = tmp[1]*1;
			this.enableCounter();
		}
		
		
		this.product.getElements('a.remove')[0].addEvent('click', function(e){
			e.stop();
			this.disableCounter();
			basket.deleteBasketItem(this.product);
		}.bind(this));	
	},
	
	enableCounter : function (){
		this.updateCounter();
		this.timer = setInterval(function(){
			this.updateCounter();
		}.bind(this), 1000);
	},
	
	disableCounter : function (){
		clearInterval(this.timer);
	},

	updateCounter: function(){
		if(this.time_remaining_sec > 0)
			this.time_remaining_sec--;
		else{
			this.time_remaining_sec = 59;
			this.time_remaining_min--;
		}
		
		if(this.time_remaining_min <= 0){
			this.time_remaining.addClass('red');
		}
		if(this.time_remaining_min == 0 && this.time_remaining_sec == 0)
			basket.expireBasketItem(this.product);
		
		this.setTimeRemaining();
	},
	
	setTimeRemaining: function(){
		this.time_remaining.getElement('span').set('text', this.formatNumber(this.time_remaining_min)+':'+this.formatNumber(this.time_remaining_sec));
	},
	
	formatNumber: function(value){
		return value < 10 ? '0'+value : value;
			
	}
});
