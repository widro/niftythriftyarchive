window.addEvent('domready', function(){
	if($chk($$('div#user_account div.instagram')[0]))
		new AccountInstagram();
});

var AccountInstagram = Class({
	initialize : function(){
		this.products = $$('div#user_account div.instagram div.line div.small_slide');
		
		if(this.products.length > 0)
			this.initializeInstagramProducts();
		
	},
	
	initializeInstagramProducts : function(){
		
		this.products.each(function(product){
			var selected_media_id = product.getElement('input.selected_media_id').get('value');
			var product_id = product.getElement('input.product_id').get('value');
			
			var product_pictures = product.getElements('div.pic');
			
			product_pictures.each(function(picture){
				var media_id = picture.getElement('input.media_id').get('value');
				picture.store('picture-object', new ProductPicture(picture, media_id));
			});
			new ProductSlider(product);
			
		}.bind(this));
		
	}
});

var ProductSlider = Class({
	initialize : function(small_slide){
		this.pics = small_slide.getElements('div.pic');
		if(this.pics.length == 0)
			return;
		
		this.pics[0].setStyles({
			'display' : 'block'
		});
		
		this.selected_media_id = small_slide.getElement('input.selected_media_id');
		
		this.accept = small_slide.getParent('div.line').getElement('div.col.accept span.checkbox');
	
		this.previous = small_slide.getElement('span.previous');
		this.next = small_slide.getElement('span.next');
		
		this.nb_elements = this.pics.length;
		this.current = 0;
		
		this.current_picture = this.pics[this.current].retrieve('picture-object');
		
		this.initializeSlider();
	},
	
	initializeSlider : function(){
		this.previous.addEvent('click', function(){
			if(this.current == 0)
				this.current = (this.nb_elements - 1);
			else
				this.current--;
			this.run();
		}.bind(this));
		
		this.next.addEvent('click', function(){
			if(this.current == (this.nb_elements - 1))
				this.current = 0;
			else
				this.current++;
			this.run();
		}.bind(this));
		
		this.accept.addEvent('click', function(){
			this.updateSelected();
		}.bind(this));
	},
	
	run : function(){
		this.pics.setStyles({
			'display' : 'none'
		});
		this.pics[this.current].setStyles({
			'display' : 'block'
		});
		this.current_picture = this.pics[this.current].retrieve('picture-object');
		if(this.current_picture.isSelected()){
			if(!this.accept.hasClass('selected'))
				this.accept.addClass('selected');
		}
		else{
			if(this.accept.hasClass('selected'))
				this.accept.removeClass('selected');
		}
	},
	
	updateSelected : function(){
		this.unselectAll();
		this.current_picture.setSelected(true);
		if(!this.accept.hasClass('selected'))
			this.selected_media_id.set('value', this.current_picture.getMediaId());
		else
			this.selected_media_id.set('value', '');
		
		this.accept.toggleClass('selected');
	},
	
	unselectAll : function(){
		this.pics.each(function(pic){
			var picture = pic.retrieve('picture-object');
			picture.setSelected(false);
		});
	}
});

var ProductPicture = Class({
	initialize : function(picture, media_id){
		this.picture = picture;
		this.media_id = media_id;
		this.selected = false;
	},
	
	getMediaId : function(){
		return this.media_id;
	},
	
	getPicture : function(){
		return this.picture;
	},
	
	isSelected : function(){
		return this.selected;
	},
	
	setSelected : function(selected){
		this.selected = selected;
	}
	
});