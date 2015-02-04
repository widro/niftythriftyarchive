window.addEvent('domready', function(){
	new Press();
});
var Press = new Class({
	initialize :function() {
		if (!$chk($$('#main_content div.press')[0]))
			return;
		
		$$('div.publication').each(function(element, index){
			var lis = element.getElements('ul.actions li');
			
			if (lis.length > 0) {
				element.getElements('ul.actions li')[lis.length-1].addClass('last');
			}
			/*var more = element.getElements('div.more a')[0];
			more.addEvent('click', function(e, el){
				this.showMore(el);
				e.stop();
			}.bindWithEvent(this, [more]))*/
		}.bind(this));
		
	},
	
	showMore : function(el) {
		var content = el.getNext('span').get('html');
		var wrapper = el.getParent().getPrevious('div.pub_content');
		el.destroy();
		wrapper.set('html', content);
		
	}
});