var ImgLoader = new Class({
	Implements:  [Events, Options],
	
	options: {
		type: 'html'  // html, imgs (array of paths), img (path)
	},
	
	initialize: function(content, options){
		this.setOptions(options);
		this.content = content;
		this.load();
		window.addEvent('resize', function(){
			if ($$('div.sls_img_loader').length > 0){
				$$('div.sls_img_loader').setStyles({
					'top' : window.getHeight()*2, 
					'left' : window.getWidth()*2
				});
			}
		});
	},
	
	load: function(){
		var loader = this.getLoader();
		loader.innerHTML = this.content;
		var imgs = loader.getElements('img');
		var paths = [];
		
		imgs.each(function(img){
			paths[paths.length] = img.get('src');
			var imgStyles = img.getStyles('display', 'float', 'position', 'top', 'left', 'right', 'bottom', 'width', 'heigh');
			var replacor = new Element('span', {
				'class' : 'sls_img_loader_replacor '+((img.get('class') != null) ? img.get('class') : ''),
				'styles' : {
					'display' : imgStyles.display,
					'float' : imgStyles.float, 
					'position' : imgStyles.position, 
					'top' : imgStyles.top, 
					'left' : imgStyles.left, 
					'right' : imgStyles.right, 
					'bottom' : imgStyles.bottom, 
					'width' : imgStyles.width, 
					'heigh' : imgStyles.height
				}
			}).replaces(img);
		});
		
		var replacors = loader.getElements('span.sls_img_loader_replacor');
		
		if (paths.length > 0){
			var images = new Asset.images(paths, {
				onProgress: function(counter, index, source){
					if (replacors[index].get('class').split(' ').slice(1).length > 0)
						this.addClass(replacors[index].get('class').split(' ').slice(1));
					this.replaces(replacors[index]);
				},
				onComplete: function(){
					this.complete(loader);
				}.bind(this)
			});
		} else
			this.complete(loader);
	},
	
	getLoader: function(){
		var loader = new Element('div', {
			'class' : 'sls_img_loader',
			'id' : 'sls_img_loader_'+new Date().getTime(),
			'styles' : {
				'position' : 'fixed',
				'left' : window.getWidth()*2,
				'top' : window.getHeight()*2
			}
		});
		return loader;
	},
	
	complete: function(loader){
		this.fireEvent('complete', loader);
	}
});