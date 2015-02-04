var HomeCollection = new Class({
	
	position:0,
	
	initialize: function(){
		this.running = false;
		this.slider = $$('div.collection_ending_hl div.collection_container')[0];
		this.imgs = this.slider.getElements('img');
		this.band = $$('div.collection_ending_hl div.band')[0];
		
		if(this.imgs.length > 1){
			this.slider.setStyles({'position' : 'relative', 'overflow' : 'hidden', 'width' : this.slider.getParent().getDimensions().width, 'height' : this.slider.getParent().getDimensions().height});
			this.initVisuals();
		}
		else{
			var height = this.band.getElements('div.infos')[0].getStyle('height');
			
			this.band.getElements('div.collection_name')[0].setStyle('line-height', height);
		}
	},
	
	initVisuals: function(){
		counter = 0;
		this.imgs.each(function(img, index){
			img.setStyles({
				'position' : 'absolute',
				'top' : 0,
				'left' : (index == 0) ? 0 : -this.slider.getDimensions().width
			});
			img.store('fx', new Fx.Morph(img));
		}.bind(this));
		
		var paths = [];
		this.imgs.each(function(img){
			paths[paths.length] = img.src;
		});
		
		new Asset.images(paths, {
			onComplete: function(){
				if(paths.length > 1)
					{
						this.enable();
					}
				
			}.bind(this)
		});
	},
	
	enable: function(){
		this.bulletBox = new Element('div', {
			'class' : 'bullet_box'
		}).inject(this.band);
		
		
		
		this.bulletBox.setStyle('width', this.imgs.length*15);
		
		for (i = 0; i < this.imgs.length; i++){
			var bullet = new Element('span', {
				'class' : 'bullet'+ ( (i == 0) ? ' selected' : '' )
			}).inject(this.bulletBox);
		}
		
		this.bullets = this.bulletBox.getElements('.bullet');
		this.bullets.each(function(bullet, index){
			bullet.addEvent('click', function(){
				if (this.position != index){
					this.switchVisual(index);
					clearInterval(this.timer);
				}
			}.bind(this));
		}.bind(this));
		
		this.play();
	},
	
	play: function(){
		this.timer = (function(){
			this.switchVisual( (this.position+1 == this.imgs.length) ? 0 : this.position+1 );
		}.bind(this)).periodical(7000, this);
	},
	
	switchVisual: function(index){
		if(this.running)
			return;
		this.running = true;
		
		var imgOut = this.imgs[this.position].retrieve('fx');
		var imgIn = this.imgs[index].retrieve('fx');
		
		imgOut
		.start({'left' : this.slider.getDimensions().width});
		
		imgIn
		.set({'left' : -this.slider.getDimensions().width})
		.start({'left' : 0}).chain(function(){
			this.running = false;
		}.bind(this));
		
		this.bullets[this.position].removeClass('selected');
		this.bullets[index].addClass('selected');
		this.position = index;
	}
});

window.addEvent('domready', function(){
	if ($$('div.collection_ending_hl').length > 0){
		new HomeCollection();
	}
});