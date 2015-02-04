var Filters = new Class({
	Implements: [Events, Options],
	
	countSold: true,
	
	options: {
		request : {
			nbProduct: 12
		},
		collections : ''
	},
	
	initialize: function(options){
		this.setOptions(options);
		this.filtersContainer = $('filters');
		this.labelsContainer = $('showing');
		this.count = $('count_item');
		this.productsContainer = $('products');
		this.loader = $('filters_loading');
		this.loadMore = $('load_more');
		this.filters = this.filtersContainer.getElements('div.filter');
		this.dropdowns = this.filtersContainer.getElements('div.dropdown');
		
		if(this.loader){
			this.loader.set('morph', {duration:300});
		}
		
		this.initDropdowns();
		if ($$('div.filter_special').length > 0)
			this.initHideBtn();
		this.initLoadMore();
		this.initInfiniteScroll();
		this.labelsEvents();
		this.parsePreSelectedOptions();
		this.updateCount();
		this.currentRequest = null;
	},
	
	initDropdowns: function(){
		// Event click on options
		this.filtersContainer.getElements('.option').each(function(option){
			option.addEvent('click', function(e){
				e.stop();
				this.selectOption(option);
			}.bind(this));
		}.bind(this));
		
	},
	
	labelsEvents: function(){
		if(this.labelsContainer){
			this.labelsContainer.addEvent('click:relay(.delete)', function(e){
				e.preventDefault();
				var a = e.target;
				var index = parseInt(a.getParent('div.element').get('id').match(/\d+/gi)[0]);
				var option = this.filtersContainer.getElements('.option')[index];
				this.selectOption(option);
			}.bind(this));
		}
	},
	
	selectOption: function(option){
		this.cancelRequest();
		
		
		if (!option.hasClass('selected'))
		{	
			// Select the option
			option.addClass('selected');
			this.createLabel(option);
		} 
		else 
		{
			//alert('click on selected');
			//if (option.getParents('.filter_product_category').length > 0 && this.filtersContainer.getElements('.filter_product_size .optionGroup')[option.getParent('div.filter').getElements('.option').indexOf(option)].getElements('.selected').length > 0){
			//	this.filtersContainer.getElements('.filter_product_size .optionGroup')[option.getParent('div.filter').getElements('.option').indexOf(option)].getElements('.selected').each(function(optionSub){
			//		this.selectOption(optionSub);
			//	}.bind(this));
			//}
			// Unselect the option
			option.removeClass('selected');
			
			this.deleteLabel(option);
			this.sendRequest();
		}

		var parent = option.getParent('div.filter');
		
		
		var nbSelectedDisplay = parent.getElement('.select .nb_selected');
		var nbSelected = parent.getElements('.option.selected').length;
		if (nbSelected == 0){
			nbSelectedDisplay.set('html', '');
		}
		
		if (option.getParents('.filter_product_category').length > 0){
			this.updateFilterProductCategory();
			this.updateFilterProductSize("filter_product_category");
		}

		if (option.getParents('.filter_product_categorybags').length > 0){
			this.updateFilterProductCategory();
			this.updateFilterProductSize("filter_product_categorybags");
		}

		if (option.getParents('.filter_product_categoryboots').length > 0){
			this.updateFilterProductCategory();
			this.updateFilterProductSize("filter_product_categoryboots");
		}

		if (option.getParents('.filter_product_categoryhome').length > 0){
			this.updateFilterProductCategory();
			this.updateFilterProductSize("filter_product_categoryhome");
		}

		if (option.getParents('.filter_product_looks').length > 0){
			this.updateFilterProductCategory();
		}

		if (option.getParents('.filter_product_eras').length > 0){
			this.updateFilterProductCategory();
		}

		if (option.getParents('.filter_product_accessories').length > 0){
			this.updateFilterProductCategory();
		}



		if (option.hasClass('selected')){
			this.sendRequest();
		}
	},
	
	updateFilterProductSize: function(filterdiv){
		var product_categories = this.filtersContainer.getElements('div.'+filterdiv+' .option.product_category');
		var selector = [];
		product_categories.each(function(element){
			if (element.hasClass('selected')) selector.push('.optionGroup.'+element.get('class').match(/\product\_category\_\d+/gi)[0]);
		});
		
		this.filtersContainer.getElement('div.filter_product_size').getElements('.optionGroup').getParent().setStyles({'display' : 'none'});
		this.filtersContainer.getElement('div.filter_product_size').getElements(selector.join(', ')).getParent().setStyles({'display' : 'block'});
	},
	
	updateFilterProductCategory: function(){
		var product_types = this.filtersContainer.getElements('div.filter_product_type .option.product_type');
		var selector = [];
		product_types.each(function(element){
			if (element.hasClass('selected')) selector.push('.option.'+element.get('class').match(/\product\_type\_\d+/gi)[0]);
		});
		
		if (selector.length > 0){
			this.filtersContainer.getElement('div.filter_product_category').getElements('.option').setStyles({'display' : 'none'});
			this.filtersContainer.getElement('div.filter_product_category').getElements(selector.join(', ')).setStyles({'display' : 'block'});
		} else
			this.filtersContainer.getElement('div.filter_product_category').getElements('.option').setStyles({'display' : 'block'});
		
		this.filtersContainer.getElement('div.filter_product_category').getElements('.option').each(function(option){
			if (option.getStyle('display') == 'none' && option.hasClass('selected'))
				this.selectOption(option);
		}.bind(this));
	},
	
	parsePreSelectedOptions: function(){
		var preSelecteds = this.filtersContainer.getElements('.option.selected');
		preSelecteds.removeClass('selected');
		preSelecteds.each(function(option){
			this.selectOption(option);
		}.bind(this));
	},
	
	loading: function(state){
		if (state)
			this.loader.morph({'height' : 40});	
		else {
			this.loader.morph({'height' : 0});
			this.loadMore.removeClass('loading').set('html', 'See more products !');
		}
	},
	
	loadingMore: function(state, text){
		if (state){
			this.loadMore.addClass('loading').set('html', text);
		} else {
			this.loadMore.removeClass('loading').set('html', text);
		}
	},
	
	sendRequest: function(more){
		if (this.currentRequest == true)
			//return;
		//alert('stoinkers');
		var more = more || false;
		this.cancelRequest();
		var options = this.getSelectedOptions();
		var data = options;
		if (more){
			if (typeOf(data.order_by) == 'array')
				Object.append(data, {start: 0, length: this.productsContainer.getElements('.product').length+this.options.request.nbProduct});
			else
				Object.append(data, {start: this.productsContainer.getElements('.product').length, length: this.options.request.nbProduct});
			this.loadingMore(true, '');
		}
		else
		{
			if (typeOf(data.order_by) == 'array')
				Object.append(data, {start: 0, length: this.productsContainer.getElements('.product').length});
			this.loading(true);
		}
		
		//if(typeof page != undefined && page == 'archive'){
		
			Object.append(data, {archive: 'true'});
			//alert(document.URL);
			if(document.URL.indexOf("search/get_items_by_value/under-35") !== -1){
				var search_term='under35';
				Object.append(data, {search_term: search_term});
			}
			if(document.URL.indexOf("search/get_items_by_search") !== -1){
				var search_term=document.URL.replace('https://www.niftythrifty.com/search/get_items_by_search/','');
				Object.append(data, {search_term: search_term});
			}
			if(document.URL.indexOf("search/get_items_by_sale") !== -1){
				var search_term='WarehouseSale';
				Object.append(data, {search_term: search_term});
			}
			if(document.URL.indexOf("shop/show_tag/") !== -1){
				var tag_id =document.URL.replace('https://www.niftythrifty.com/shop/show_tag/','');
				//Object.append(data, {tag_id: tag_id});
			}
		//}
		
		this.pendingRequest = new Request.HTML({
			//url : 'http://staging.niftythrifty.com/tempgetproducts.php',
			//url : 'https://www.niftythrifty.com/tempgetproducts.php',
			url : 'https://'+window.location.host+'/tempgetproducts.php',
			data : data,
			onRequest : function(){
				this.currentRequest = true;
			}.bind(this),
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){
				var nbProducts = responseElements.filter(function(element){
					if (element.hasClass('product')) return element;
				});
				
				if (nbProducts.length > 0){
					var imgLoader = new ImgLoader(responseHTML);
					imgLoader.addEvent('complete', function(loader){
						if (more){
							if (typeOf(data.order_by) == 'array')
								this.replaceContent(loader);
							else
								this.addContent(loader);
						} else {
							this.replaceContent(loader);
							this.loading(false);
						}
						this.loadingMore(false, 'See more products !');
						
						if ($$('div.filter_special').length > 0)
							this.checkHideBtn();
						else
							this.updateCount();
						
						this.currentRequest = null;
					}.bind(this));
				} else {
					if (more)
						this.loadingMore(false, 'You\'ve reached the end of this sale');
					else {
						this.loading(false);
						this.eraseContent();
					}
				}
				
			}.bind(this)
		}).send();
	},
	
	replaceContent: function(content){
		this.productsContainer.erase('html');
		var children = content.getChildren();
		children.setStyles({'opacity' : 0});
		children.addClass('newItem');
		children.inject(this.productsContainer);
		new Element('div', {'class' : 'clear'}).inject(this.productsContainer);
		var newItems = this.productsContainer.getElements('.newItem');
		newItems.morph({'opacity' : 1});
		newItems.removeClass('newItem');
	},
	
	addContent: function(content){
		var children = content.getChildren();
		children.setStyles({'opacity' : 0});
		children.addClass('newItem');
		children.inject(this.productsContainer.getElements('div.clear').getLast(), 'before');
		var newItems = this.productsContainer.getElements('.newItem');
		newItems.morph({'opacity' : 1});
		newItems.removeClass('newItem');
	},
	
	eraseContent: function(){
		this.productsContainer.erase('html');
		new Element('div', {'class' : 'clear'}).inject(this.productsContainer);
		this.loadingMore(false, 'You\'ve reached the end of this sale');
	},
	
	cancelRequest: function(){
		// Cancel the pending AJAX Request if there is one running
		if (this.pendingRequest && this.pendingRequest.isRunning())
			this.pendingRequest.cancel();
	},
	
	getSelectedOptions: function(){
		var collections = this.options.collections;
		var product_types = this.filtersContainer.getElements('.filter_product_type .option.selected .value').get('html');
		var product_categoriesbags = this.filtersContainer.getElements('.filter_product_categorybags .option.selected .value').get('html');
		var product_categoriesfull = this.filtersContainer.getElements('.filter_product_category .option.selected .value').get('html');
		var product_categoriesboots = this.filtersContainer.getElements('.filter_product_categoryboots .option.selected .value').get('html');
		var product_categorieshome = this.filtersContainer.getElements('.filter_product_categoryhome .option.selected .value').get('html');
		var product_category_sizes = this.filtersContainer.getElements('.filter_product_size .option.selected .value').get('html');
		var designers = this.filtersContainer.getElements('.filter_designer .option.selected .value').get('html');
		var order_by = this.filtersContainer.getElements('.filter_sort .option.selected .value').get('html');
		
		var product_looks = this.filtersContainer.getElements('.filter_product_looks .option.selected .value').get('html');
		var product_eras = this.filtersContainer.getElements('.filter_product_eras .option.selected .value').get('html');
		var product_colors = this.filtersContainer.getElements('.filter_product_colors .option.selected .value').get('html');
		var product_accessories = this.filtersContainer.getElements('.filter_product_accessories .option.selected .value').get('html');
		var product_categories = [];
		var product_tags = [];

		if(typeOf(product_categoriesbags) == 'array'){
			product_categories = product_categories.concat(product_categoriesbags);
		}
		if(typeOf(product_categoriesfull) == 'array'){
			product_categories = product_categories.concat(product_categoriesfull);
		}
		if(typeOf(product_categoriesboots) == 'array'){
			product_categories = product_categories.concat(product_categoriesboots);
		}
		if(typeOf(product_categorieshome) == 'array'){
			product_categories = product_categories.concat(product_categorieshome);
		}
		
		//tags, combine later
		if(typeOf(product_looks) == 'array'){
			product_tags = product_tags.concat(product_looks);
		}
		if(typeOf(product_eras) == 'array'){
			product_tags = product_tags.concat(product_eras);
		}
		if(typeOf(product_colors) == 'array'){
			product_tags = product_tags.concat(product_colors);
		}
		if(typeOf(product_accessories) == 'array'){
			product_tags = product_tags.concat(product_accessories);
		}
		//var product_tags = this.filtersContainer.getElements('.filter_product_colors .option.selected .value').get('html');

		var options = {
			'collection_ids' : (typeOf(collections) == 'array') ? collections : '', 
			'product_type_ids' : (typeOf(product_types) == 'array') ? product_types : '',
			'product_category_ids' : (typeOf(product_categories) == 'array') ? product_categories : '', 
			'product_category_size_ids' : (typeOf(product_category_sizes) == 'array') ? product_category_sizes : '', 
			'designer_ids' : (typeOf(designers) == 'array') ? designers : '', 
			'order_by' : (typeOf(order_by) == 'array') ? order_by : '',
			'product_tags' : (typeOf(product_tags) == 'array') ? product_tags : '' 
		};
		
		return options;
	},
	
	createLabel: function(option){
		var element = new Element('div', {
			'class' : 'element',
			'id' : 'option_label_'+this.filtersContainer.getElements('.option').indexOf(option)
		}).inject((this.labelsContainer.getElements('.element').length > 0) ? this.labelsContainer.getElements('.element').getLast() : this.labelsContainer.getElement('#title_showing'), 'after');
		
		//new Element('div', {'class' : 'left_showing'}).inject(element);
		new Element('div', {'class' : 'element_name', 'html' : ((option.getParents('.optionGroup').length > 0) ? option.getParent('.optionGroup').getPrevious('.optionGroup_title').get('html')+' - ' : '')+option.getElement('.label').get('html')}).inject(element);
		new Element('a', {'class' : 'delete', 'href' : '#'}).inject(element);
		//new Element('div', {'class' : 'right_showing'}).inject(element);
	},
	
	deleteLabel: function(option){
		$('option_label_'+this.filtersContainer.getElements('.option').indexOf(option)).destroy();
	},
	
	updateCount: function(){
		if(this.count){
			var nb = this.count.getElement('#nbr');
			var text = this.count.getElement('#text');
		
			var nbProducts = (this.countSold) ? productSearchCount : productSearchCount - this.productsContainer.getElements('.product_sold').length; 
			//var nbProducts = this.productsContainer.getElements(((this.countSold) ? '.product' : '.product:not(.product_sold)')).length;
		
			if (this.productsContainer.getElements('script').length > 0)
				this.productsContainer.getElements('script').destroy();
		
			nb.set('html', nbProducts);
			text.set('html', (nbProducts > 1) ? 'items' : 'item');
		}
	},
	
	initHideBtn: function(){
		//this.hideBtn = this.filtersContainer.getElement('div.filter_special input');
		this.hideBtn = $('filter_hidecheck').getElement('div.filter_special input');
		
		this.hideBtn.addEvent('change', function(){
			this.checkHideBtn();
		}.bind(this));
	},
	
	checkHideBtn: function(){
		this.countSold = !this.hideBtn.checked;
		this.toggleProductSold();
	},
	
	toggleProductSold: function(){
		this.productsContainer.getElements('.product').each(function(product){
			if (!product.hasClass('product_sold') && product.getElements('.status.sold').length > 0)
				product.addClass('product_sold');
		});
		
		var productsSold = this.productsContainer.getElements('.product_sold');
		
		if (this.countSold)
			productsSold.setStyles({'display' : 'block'});
		else
			productsSold.setStyles({'display' : 'none'});
		
		this.updateCount();
	},
	
	initLoadMore: function(){
		if(this.loadMore){
			this.loadMore.addEvent('click', function(e){
				e.stop();
			
				if (!this.loadMore.hasClass('loading')){
					this.sendRequest(true);
				}
			}.bind(this));
		}
	},
	
	initInfiniteScroll: function(){
		window.addEvent('scroll', function(){
			var top = window.getScrollTop();
			var bottom = top+window.getCoordinates().height;
			
			if (bottom > this.productsContainer.getCoordinates().bottom+200 && (!this.pendingRequest || !this.pendingRequest.isRunning())){
				this.sendRequest(true);
			}
		}.bind(this));
	}
});