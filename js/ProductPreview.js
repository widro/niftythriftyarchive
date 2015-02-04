var ProductPreview = new Class({
	
	current: 0,
	running: false,
	
	initialize: function(){
		this.preview = $$('#pictures .main_img')[0];
		this.otherImgs = $$('#pictures .others_img')[0];
		
		this.initImgs();
	},
	
	initImgs: function(){
		this.primaryImgs = this.preview.getElements('img');
		this.secondaryImgs = this.otherImgs.getElements('.img');
		
		this.primaryImgs.each(function(img, index){
			img.setStyles({
				'top' : 0,
				'left' : 0,
				'opacity' : (index == 0) ? 1 : 0
			});
			
			img.addEvent('click', function(){
				this.switchImg( (this.current+1 == this.primaryImgs.length) ? 0 : this.current+1 );
			}.bind(this));
			img.store('fx', new Fx.Morph(img));
		}.bind(this));
		
		this.secondaryImgs.each(function(img, index){
			img.setStyles({
				'display' : 'block',
				'width' : 137
			});
			
			img.addEvent('click', function(){
				if (index != this.current)
					this.switchImg(index);
			}.bind(this));
		}.bind(this));
	},
	
	switchImg: function(index){
		if (!this.running){
			this.running = true;
			var primaryOut 		= this.primaryImgs[this.current].retrieve('fx');
			var primaryIn 		= this.primaryImgs[index].retrieve('fx');
			
			primaryOut
			.set({'z-index' : 1})
			.start({'opacity' : 0});
			
			primaryIn
			.set({'z-index' : 2, 'opacity' : 0})
			.start({'opacity' : 1});
			
			this.current = index;
			(function(){
				this.running = false;
			}.bind(this)).delay(600, this);
		}
	}
});

window.addEvent('domready', function(){
	if ($$('#pictures .others_img').length > 0)
		new ProductPreview();
});