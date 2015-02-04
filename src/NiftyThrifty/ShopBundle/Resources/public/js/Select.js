/**
 * Generic class to build custom select
 * author : Kén LANCIEN
/**
 * Generic class to build custom select
 * author : Kén LANCIEN
 * date : 18/04/2012
 */

var Select = new Class({
	Implements: [Events, Options],
	
	options: {
		maxOptions: 13,
		scrollbar: {
			width : 5,
			offsetY : 0,
			offsetX : 2
		}
	},
	
	scrollbarActive : false,
	mouseScrollbar : false,
	keyword: '',
	optionsLiteral: [],
	
	initialize: function(select, options){
		this.setOptions(options);
		this.select = select;
		this.select_width = this.select.getSize().x;
		this.select_height = this.select.getSize().y;
			
		this.build();
		this.handleKeys();
		
		$$('body')[0].addEvent('click', function(){
			this.close();
		}.bind(this));
	},
	
	build: function(){
		this.select.setStyles({
			'display' : 'none'
		});
		
		var padding = 7;
		
		// Select
		this.selectBox = new Element('div', {
			'class' : 'sls_custom_select',
			'styles' : {
				'position' : 'relative',
				'cursor' : 'pointer',
				'width' : this.select_width-2*padding,
				'height' : this.select_height,
				'padding' : '0 '+padding+'px'
			},
			'events' : {
				'click' : function(e){
					e.stop();
					if (this.dropdown.hasClass('closed')){
						$$('body')[0].fireEvent('click');
						this.open();
					} else
						this.close();
				}.bind(this)
			}
		}).inject(this.select.getParent());
		
		this.selectValue = new Element('span', {
			'class' : 'sls_custom_select_value',
			'styles' : {
				'width' : this.select_width-2*padding-7,
				'height' : this.select_height,
				'line-height' : this.select_height
			}
		}).inject(this.selectBox);
		
		new Element('span', {
			'class' : 'sls_custom_select_arrow'
		}).inject(this.selectBox);
		// /Select
		
		// Dropdown
		this.dropdown = new Element('div', {
			'class' : 'sls_custom_select_dropdown closed',
			'styles' : {
				'overflow' : 'hidden',
				'position' : 'absolute',
				'height' : 0,
				'top' : this.selectBox.getCoordinates().height,
				'left' : 0,
				'z-index' : 99999
			},
			'events' : {
				'click' : function(e){
					e.stop();
				}
			}
		}).inject(this.selectBox)
		.set('morph', {link:'cancel', duration:300});
		
		this.dropdownWrapper = new Element('div', {
			'class' : 'sls_custom_select_dropdown_wrapper'
		}).inject(this.dropdown);
		
		this.ulOptions = new Element('ul', {
			'class' : 'sls_custom_select_options'
		}).inject(this.dropdownWrapper);
		
		if (this.select.getElements('option').length > 0){
			this.select.getElements('option').each(function(realOption, index){
				if (realOption.get('html') != ''){
					this.optionsLiteral.push(realOption.get('html').toLowerCase());
					
					if(realOption.get('selected')){
						this.selectValue.set('html', realOption.get('html'));
					}
					
					var option = new Element('li', {
						'class' : (realOption.get('selected')) ? 'sls_custom_select_option selected' : 'sls_custom_select_option',
						'styles' : {
							'cursor' : 'pointer'
						},
						'events' : {
							'click' : function(){
								// Close the dropdown if the select isn't multiple
								if (!this.select.get('multiple')){
									this.setClosed();
									this.ulOptions.getElements('.selected').removeClass('selected');
									option.addClass('selected');
									this.selectValue.set('html', realOption.get('html'));
									realOption.set('selected', 'selected');
								} else {
									if (option.hasClass('selected')){
										option.removeClass('selected');
										realOption.erase('selected');
									} else {
										option.addClass('selected');
										realOption.set('selected','selected');
									}
									var indexValue = 0;
									var value = '';
									
									this.select.getElements('option').each(function(option){
										if (option.get('selected')){
											value += (indexValue == 0) ? option.get('html') : ' / '+option.get('html');
											indexValue++;
										}
									});
									this.selectValue.set('html', value);
								}
							}.bind(this)
						}
					}).inject(this.ulOptions);
					
					var optionLabel = new Element('span', {
						'class' : 'sls_custom_select_option_label',
						'html' : realOption.get('html'),
						'styles' : {
							'white-space' : 'nowrap'
						}
					}).inject(option);
					
					var optionValue = new Element('span', {
						'class' : 'sls_custom_select_option_label display_none',
						'html' : realOption.get('value'),
						'styles' : {
							'display' : 'none'
						}
					}).inject(option);
				}
			}.bind(this));
		}
		
		this.dropdownDimensions = this.ulOptions.measure(function(){
			return this.getDimensions(true);
		});
		
		if (this.selectBox.getDimensions(true).width > this.dropdownDimensions.width){
			this.dropdownDimensions.width = this.selectBox.getDimensions(true).width;
			this.dropdown.setStyles({
				'width' : this.dropdownDimensions.width
			});
		}
		
		// Build scrollbar if too many options
		if (this.ulOptions.getElements('li').length > this.options.maxOptions){
			this.buildScrollbar();
			this.scrollbarActive = true;
		}
		// /Dropdown
	},
	
	open: function(){
		this.dropdown
		.morph({'height' : this.dropdownDimensions.height+1})
		.addClass('open')
		.removeClass('closed');
		
		this.selectBox
		.addClass('open')
		.removeClass('closed');
		
		this.dropdownState = true;
	},
	
	close: function(){
		this.dropdown
		.morph({'height' : 0})
		.addClass('closed')
		.removeClass('open');
		
		this.selectBox
		.addClass('closed')
		.removeClass('open');
		
		if (this.scrollbarActive){
			this.ulOptions.setStyles({'margin-top' : 0});
			this.scrollbar.setStyles({'top' : this.scrollbar.retrieve('offsetY')});
		}
		
		this.ulOptions.getElements('li').removeClass('hover');
		this.dropdownState = false;
		this.optionHover = null;
	},
	
	setClosed: function(){
		this.dropdown
		.setStyles({'height' : 0})
		.addClass('closed')
		.removeClass('open');
		
		this.selectBox
		.addClass('closed')
		.removeClass('open');
		
		if (this.scrollbarActive){
			this.ulOptions.setStyles({'margin-top' : 0});
			this.scrollbar.setStyles({'top' : this.scrollbar.retrieve('offsetY')});
		}
		
		this.ulOptions.getElements('li').removeClass('hover');
		this.dropdownState = false;
		this.optionHover = null;
	},
	
	buildScrollbar: function(){
		var optionHeight = this.dropdown.measure(function(){
			return this.getElements('li')[0].getDimensions(true).height;
		});
		
		var ulOptionsDimensions = this.dropdown.measure(function(){
			return this.getElements('ul')[0].getDimensions(true);
		});
		
		this.dropdownDimensions.height = optionHeight * this.options.maxOptions; 
		this.dropdown.setStyles({
			'padding-right' : this.options.scrollbar.width+this.options.scrollbar.offsetX
		});
		
		var offsetY = parseInt(this.ulOptions.getStyle('padding-top'))+parseInt(this.ulOptions.getStyle('margin-top'));
		var ratio = (this.dropdownDimensions.height / ulOptionsDimensions.height);
		
		this.scrollbar = new Element('div', {
			'class' : 'sls_custom_select_scrollbar',
			'styles' : {
				'width' : this.options.scrollbar.width,
				'height' : ratio * this.dropdownDimensions.height,
				'position' : 'absolute',
				'right' : 8,
				'top' : offsetY + this.options.scrollbar.offsetY,
				'background-color' : '#E2E2E2'
			}
		}).inject(this.dropdown)
		.store('offsetY', offsetY + this.options.scrollbar.offsetY);
		
		this.dropdown.addEvent('mousewheel', function(e){
			e.stop();
			
			if (e.wheel > 0 && parseInt(this.ulOptions.getStyle('margin-top')) < 0){
				this.ulOptions.setStyles({
					'margin-top' : parseInt(this.ulOptions.getStyle('margin-top'))+20
				});
			}
			if (e.wheel < 0 && (Math.abs(parseInt(this.ulOptions.getStyle('margin-top'))) + this.dropdownDimensions.height) <= ulOptionsDimensions.height) {
				this.ulOptions.setStyles({
					'margin-top' : parseInt(this.ulOptions.getStyle('margin-top'))-20
				});
			}
			
			this.scrollbar.setStyles({
				'top' : ratio * Math.abs(parseInt(this.ulOptions.getStyle('margin-top'))) - this.scrollbar.retrieve('offsetY')
			});
		}.bind(this));
		
		Element.Events.drag = {
			base: 'mousemove',
			condition: function(e){
				return this.mouseScrollbar;
			}.bind(this)
		};
		
		this.scrollbar.addEvents({
			'mousedown' : function(e){
				this.mouseScrollbar = true;
				this.dragY = this.scrollbar.getCoordinates().top-e.page.y;
			}.bind(this),
			
			'mouseup' : function(){
				this.mouseScrollbar = false;
			}.bind(this)
		});
		
		window.addEvents({
			mouseup: function(){
				this.mouseScrollbar = false;
			}.bind(this),
			
			drag: function(e){
				e.stop();
				var Y = e.page.y-this.dropdown.getCoordinates().top; 
				var dy = this.dragY;
				var y = Y+dy;
				var H = this.ulOptions.getCoordinates().height;
				var h = this.dropdown.getCoordinates().height;
				var hS = this.scrollbar.getCoordinates().height;
				var Hm = H-h;
				var hmS = h-hS;
				var ratio = y/hmS;
				
				if (y >= 0 && y <= hmS){
					this.scrollbar.setStyles({'top' : y});
					this.ulOptions.setStyles({'margin-top' : -ratio*Hm});
				}
			}.bind(this)
		});
	},
	
	updateScrollbar: function(){
		var content = {
			height 	: this.ulOptions.getCoordinates().height,
			area 	: this.ulOptions.getCoordinates().height - this.dropdown.getCoordinates().height,
			marginTop : parseInt(this.ulOptions.getStyle('margin-top'))
		};
		var wrapper = { height : this.dropdown.getCoordinates().height};
		var scrollbar = {
			height	: this.scrollbar.getCoordinates().height,
			area	: wrapper.height - this.scrollbar.getCoordinates().height
		};
		var ratio = scrollbar.area / content.area;
		
		this.scrollbar.setStyles({
			'top' : Math.abs(content.marginTop) * ratio
		});
	},
	
	handleKeys: function(){
		Element.Events.selectKeydown = {
			base: 'keydown',
			condition: function(e){
				return this.dropdownState;
			}.bind(this)
		};
		
		document.addEvents({
			"selectKeydown": function(e){
				e.stop();
				var options = this.ulOptions.getElements('li');
				var realOptions = this.select.getElements('option').filter(function(option){
					if (option.get('html') != '') return option;
				});

				switch (e.key){
					case 'esc' : 
						this.setClosed();
						break;
					case 'enter': 
						if (this.optionHover != null){
							var realOption = realOptions[this.optionHover];
							
							if (!this.select.get('multiple')){
								this.ulOptions.getElements('.selected').removeClass('selected');
								options[this.optionHover].removeClass('hover').addClass('selected');
								this.selectValue.set('html', realOption.get('html'));
								realOption.set('selected', 'selected');
								this.setClosed();
								
							} else {
								
								if (options[this.optionHover].hasClass('selected')){
									options[this.optionHover].removeClass('hover').removeClass('selected');
									realOption.erase('selected');
									
								} else {
									options[this.optionHover].removeClass('hover').addClass('selected');
									realOption.set('selected', 'selected');
									
								}
								
								var value = "";
								var indexValue = 0;
								this.select.getElements('option').each(function(option){
									if (option.get('selected')){
										value += (indexValue == 0) ? option.get('html') : ' / '+option.get('html');
										indexValue++;
									}
								});
								this.selectValue.set('html', value);
							}
						}
						break;
					case 'up' : 
						if (this.optionHover == null){
							options.getLast().addClass('hover');
							this.optionHover = options.length-1;
						} else {
							options[this.optionHover].removeClass('hover');
							this.optionHover = (this.optionHover-1 < 0) ? options.length-1 : this.optionHover-1;
							options[this.optionHover].addClass('hover');
						}
						this.stayFocused(options[this.optionHover]);
						break;
					case 'down' :
						if (this.optionHover == null){
							options[0].addClass('hover');
							this.optionHover = 0;
						} else {
							options[this.optionHover].removeClass('hover');
							this.optionHover = (this.optionHover+1 >= options.length) ? 0 : this.optionHover+1;
							options[this.optionHover].addClass('hover');
						}
						this.stayFocused(options[this.optionHover]);
						break;
					default:
						if (e.key == 'space') e.key = ' ';
						this.keyword += e.key;
						this.findOptionByString();
						clearTimeout(this.timerKeydown);
						this.timerKeydown = this.clearKeyword.delay(1000, this);
						break;
				}
			}.bind(this)
		});
	},
	
	stayFocused: function(option){
		var li = {
			object : option,
			top : option.getCoordinates(this.ulOptions).top,
			bottom : option.getCoordinates(this.ulOptions).bottom
		};
		
		var conditionTop = li.top < Math.abs(parseInt(this.ulOptions.getStyle('margin-top')));
		var conditionBottom = li.bottom > (this.dropdownDimensions.height + Math.abs(parseInt(this.ulOptions.getStyle('margin-top'))));
		
		if (conditionTop || conditionBottom){
			if (conditionTop)
				this.ulOptions.setStyles({'margin-top' : -li.top});
			else if (conditionBottom)
				this.ulOptions.setStyles({'margin-top' : -(li.bottom - this.dropdownDimensions.height)});
			
			this.updateScrollbar();
		}			
	},
	
	findOptionByString: function(){
		var regex = new RegExp('^'+this.keyword+'\.*', 'gi');
		
		for (var i = 0; i < this.optionsLiteral.length; i++){
			if (regex.test(this.optionsLiteral[i])){
				this.stayFocused(this.ulOptions.getElements('li')[i]);
				this.ulOptions.getElements('li.hover').removeClass('hover');
				this.ulOptions.getElements('li')[i].addClass('hover');
				this.optionHover = i;	
				return true;
			}
		}
	},
	
	clearKeyword: function(){
		this.keyword = '';
	}
});

window.addEvent('domready', function(){
	if ($$('select').length > 0){
		$$('select').each(function(select){
			new Select(select);
		});
	}
	
	$$('.display_none_js').each(function(hide){
		hide.setStyle('display','none');
	});
});
