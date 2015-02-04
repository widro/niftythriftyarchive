var tweetCollections = null;
var Tweets = new Class({
	
	position:0,
	
	initialize: function(){
		this.getTweets();
	},
	
	getTweets : function() {
		getTwitters('lastest_tweets', { 
			  id: 'niftythrifty', 
			  count: 10, 
			  enableLinks: true, 
			  ignoreReplies: true, 
			  clearContents: true,
			  template: '<span class="content">"%text%"</span><span class="time">%time%</span></a>',
			  callback : callbackTweets
			});

	},
	
	tweetsLoaded : function() {
		
		this.tweetBox = $('lastest_tweets');
		var ul = this.tweetBox.getFirst('ul');
		var lis = ul.getElements('li');
		ul.setStyle('display', 'none');
		for (var i=0;i<lis.length;i++) {
			var tweet = new Element('div', {
				'class' : 'tweet',
			}).inject(this.tweetBox);
			var content = new Element('div', {
				'class' : 'content',
				'html' : lis[i].getFirst('span.content').get("html")
			}).inject(tweet);
			var time = new Element('div', {
				'class' : 'time',
				'html' : lis[i].getFirst('span.time').get("html")
			}).inject(tweet);
		}
		ul.destroy();
		
		this.tweets = this.tweetBox.getChildren();
		
		if (this.tweets.length > 1){
			this.tweetBox.addEvents({
				'mouseenter' : function(){
					clearInterval(this.timer);
				}.bind(this),
				
				'mouseleave' : function(){
					this.play();
				}.bind(this)
			});
			
			this.initTweets();
		}
	},
	
	initTweets: function(){
		this.tweets.each(function(tweet, index){
			tweet.store('fx', new Fx.Morph(tweet));
			tweet.setStyles({
				'top' : (index == 0) ? 0 : this.tweetBox.getDimensions().height,
				'display' : 'block'
			});
		}.bind(this));
		
		this.play();
	},
	
	play: function(){
		this.timer = (function(){
			this.switchTweet( (this.position+1 == this.tweets.length) ? 0 : this.position+1 );
		}.bind(this)).periodical(3000, this);
	},
	
	switchTweet: function(index){
		var twOut = this.tweets[this.position].retrieve('fx');
		var twIn = this.tweets[index].retrieve('fx');
		
		twOut.start({'top' : this.tweetBox.getDimensions().height, 'opacity' : 0});
		twIn.set({'top' : -this.tweetBox.getDimensions().height, 'opacity' : 0}).start({'top' : 0, 'opacity' : 1});
		
		this.position = index;
	}
});

function callbackTweets () {
	tweetCollections.tweetsLoaded();
}
window.addEvent('domready', function(){
	if ($$('#lastest_tweets').length > 0){
		tweetCollections = new Tweets();
	}
});