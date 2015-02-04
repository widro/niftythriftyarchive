var __InvitationsList;

var UserInvite = Class({
	initialize : function(){
		this.multiple_mails = $$('div.invite.multiple_mails a.submit')[0];
		this.multiple_mails.store('type', 'multiple_mails');
	
		this.invite_user_link = $('invite_user_link');
		
		this.social_fb = $('social_fb');
		
		this.initializeEvents();
	},
	
	initializeEvents : function(){
		this.multiple_mails.addEvent('click', function(e){
			new MultipleMails();
			e.stop();
		});
		
		/*
		this.invite_user_link.addEvent('keydown', function(e){
			if(e.code == '17' || e.code == '67' || e.code == '65'){
			}else{
				e.stop();
			}
		});
		*/
		
		this.social_fb.addEvent('click', function(e){ 
			
			/*
			var popup = window.open(null, '', 'width=100, height=100');
			try { 
				popup.close(); 
			}
			catch(e) { 
			}
			
			
			return;
			*/
			
			new SocialFb();				
			e.stop();
		}.bind(this));
	}
});

var NiftyBox = Class({
	options : {
		opacity : 0.5,
		duration : 500
	},
	initialize : function(mode, title){
		this.body = $$('body')[0];
		this.mode = mode;
		this.title = title;
		
		this.buildBox();
	},
	
	buildBox : function(){
		this.body.setStyles({
			'overflow' : 'hidden'
		});
		
		this.box_wrapper = new Element('div', {
			'id' : 'nifty_box'
		}).inject(this.body);
		this.box_wrapper.setStyles({
			'height' : window.getHeight() + 'px',
			'width' : window.getWidth() + 'px',
			'opacity' : '0'
		});
		
		this.content = new Element('div', {
			'class' : 'content'
		}).inject(this.box_wrapper);
		
		this.title = new Element('h1', {
			'html' : this.title
		}).inject(this.content);
		
		new Element('div',{
			'class' : 'clear'
		}).inject(this.content);
		
		this.overflow = new Element('div',{
			'class' : 'overflow'
		}).inject(this.content);
		
		this.container = new Element('div',{
			'class' : 'container'
		}).inject(this.overflow);
		
		this.close = new Element('a', {
			'class' : 'close',
			'href' : '#',
			'html' : 'Close',
			'styles' : {
				'opacity' : '0',
				'visibility' : 'hidden'
			}
		}).inject(this.content);
		
		new Element('div',{
			'class' : 'clear'
		}).inject(this.content);
		
		this.initializeEvents();
		this.showBox();
	},
	
	initializeEvents : function(){
		this.close.addEvent('click', function(e){
			this.closeBox();
			__InvitationsList.retrieveList();
			e.stop();
		}.bind(this));
	},
	
	showBox : function(){
		new Fx.Morph(this.box_wrapper, {
			'link' : 'cancel',
			'duration' : this.options.duration
		}).start({
			'opacity' : '1'
		});
	},
	
	closeBox : function(){
		new Fx.Morph(this.box_wrapper, {
			'link' : 'cancel',
			'duration' : this.options.duration,
			'onComplete' : function(){
				this.body.setStyles({
					'overflow' : 'auto'
				});
				this.box_wrapper.destroy();
			}.bind(this)
		}).start({
			'opacity' : '0'
		});
	},
	
	updateContent : function(){
		
		new Fx.Morph(this.overflow, {
			'duration' : '500',
			'link' : 'cancel',
			'onComplete' : function(){
				new Fx.Morph(this.container,{
					'link' : 'cancel',
					'duration' : '200'
				}).start({
					'opacity' : '1'
				});
			}.bind(this)
		}).start({
			'height' : this.container.getHeight() + 'px'
		});
	},
	
	toggleClose : function(visible){
		var opacity = 0;
		if(visible)
			opacity = 1;
		
		new Fx.Morph(this.close, {
			'duration' : this.options.duration,
			'link' : 'cancel'
		}).inject({
			'opacity' : opacity
		});
	},
	
	showResult : function(text){
		var result = new Element('div',{
			'class' : 'result',
			'html' : text
		}).inject(this.container);
		
		new Fx.Morph(this.overflow, {
			'duration' : '500',
			'link' : 'cancel',
			'onComplete' : function(){
				this.close.fade('in');
			}.bind(this)
		}).start({
			'height' : this.container.getHeight() + 'px'
		});
	},
	
	buildSteps : function(){
		this.steps_container = new Element('div', {
			'class' : 'steps'
		}).inject(this.container);
	},
	
	buildStep : function(_class, title){
		if(!$chk(this.steps_container))
			this.buildSteps();
		
		var step = new Element('div',{
			'class' : _class
		}).inject(this.steps_container);
		
		var span_status = new Element('span',{
			'class' : 'status'
		}).inject(step);
		
		new Element('img', {
			'src' : '/Public/Style/Img/small_grey_loader.gif',
			'alt' : 'Nifty Loader',
			'title' : 'Nifty Loader'
		}).inject(span_status);
		
		new Element('span',{
			'class' : 'title',
			'html' : title
		}).inject(step);
		
		new Element('span',{
			'class' : 'value'
		}).inject(step);
	},
	
	getContainer : function(){
		return this.container;
	}
	
});

var MultipleMails = Class({
	initialize : function(){
		// Nifty Box
		this.NiftyBox = new NiftyBox('multiple_mails', 'Invite Friends via email');
		this.box_wrapper = $('nifty_box');
		
		// Class vars 
		this.emails = $('invite_multiple_mails').get('value');
		this.content = $('invite_multiple_mails_content').get('value');
		this.current_step = 0;
		
		this.prepare();
	},

	
	prepare : function(){
		this.emails = this.emails.split(',');
		
		var emails = [];
		var errors = 0;
		
		this.NiftyBox.buildStep('step current', 'Checking e-mails');
		
		this.emails.each(function(email){
			this.NiftyBox.buildStep('step email', email);
		}.bind(this));
		this.NiftyBox.buildStep('step', 'Sending');
		
		this.NiftyBox.updateContent();
		this.check.delay(1500, this);
	},
	
	check : function(){
		this.steps = $$('div#nifty_box div.steps div.step');
		this.nextStep('done');
		
		var errors = 0;
		var emails = [];
		
		this.emails_steps = $$('div#nifty_box div.steps div.step.email');
		
		this.emails_steps.each(function(email_step){
			var email = email_step.getElement('span.title').get('html').clean().toLowerCase();
			var pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (pattern.test(email)){
				if(!_invitation_email.contains(email)){
					this.nextStep('valid');
					emails.push(email);
				}
				else
					this.nextStep('Invitation already sent.');
			}
			else{
				this.nextStep('invalid');
				errors++;
			}
		}.bind(this));
		
		if(errors > 0){
			this.nextStep('failed');
			this.NiftyBox.showResult('We are unable to send invitations because one or more e-mail(s) are invalid.');
		}else{
			this.send(emails);
		}
	},
	
	nextStep : function(value_content){
		this.steps_length = this.steps.length;
		
		this.steps[this.current_step].removeClass('current');
		var value = this.steps[this.current_step].getElement('span.value');
		value.set('html', value_content);
		
		if(this.current_step < (this.steps_length - 1)){
			this.current_step++;
			this.steps[this.current_step].addClass('current');
		}
	},
	
	send : function(emails){
		if(emails.length > 0){
			new Request.JSON({
				url: _json_url,
				data: {
					'action' : 'invitation_multiple_emails',
					'emails' : emails,
					'content' : this.content
				},
				method: 'post',
				onComplete: function(){
					this.updateSendInvitations(emails);
					this.nextStep('done');
					this.NiftyBox.showResult('We have sent all your invitations. <br /> If no email address is attached to an account the invitation will not be sent.');
				}.bind(this)
			}).send();
		}else{
			this.nextStep('not sent');
			this.NiftyBox.showResult('');
		}
	},
	
	updateSendInvitations : function(emails){
		emails.each(function(email){
			_invitation_email.push(email);
		});
	}
});

var SocialFb = Class({
	fb_feed : {
		invitation_msg : 'I\'m inviting you to join NiftyThrifty, where expert curators deliver rare vintage finds, everyday. Membership is free, so join now! Please click here and use the link to join.',
		invitation_description : 'ThriftyNifty.com - Vintage finds',
		invitation_url : _user_invitation_url,
		invitation_photo : slsBuild.site.protocol + '://'+slsBuild.site.domainName + '/' + slsBuild.paths.img + 'fb_logo.png',
		invitation_name : 'Invitation to ThriftyNifty',
		invitation_action_title : 'Accept invitation'
	},
	
	initialize : function(){
		this.NiftyBox = new NiftyBox('social_fb', 'Send invites with Facebook');
		
		// Vars
		this.current_step = 0;
		
		this.prepare();
	},
	
	prepare : function(){
		this.NiftyBox.buildStep('step current', 'Connecting to Facebook');
		this.NiftyBox.buildStep('step', 'Retrieving your friend list');
		this.NiftyBox.buildStep('step', 'Choosing friends');
		this.NiftyBox.buildStep('step', 'Sending invitations');
		
		// FB Friends List
		this.container = this.NiftyBox.getContainer();
		this.fb_overflow = new Element('div',{
			'class' : 'fb_overflow',
			'styles' : {
				'height' : '0px'
			}
		}).inject(this.container);
		
		this.fb_container = new Element('div',{
			'class' : 'fb_container'
		}).inject(this.fb_overflow);
		
		this.NiftyBox.updateContent();
		
		
		this.steps = $$('div#nifty_box div.steps div.step');
		this.fbPrepare();
	},
	
	fbPrepare : function(){
		if (window.FB)
            this.fbReady();
	},
	
	fbReady : function() {
		
		FB.init({
			appId      : _fbAppId,
			channelUrl : _fbChannelUrl,
			status     : true,
			cookie     : true,
			xfbml      : false
		});

		this.fbLogin();
	},
	
	fbLogin : function(){
		var that = this;
		FB.getLoginStatus(function(response){ 
			// The dude has already accepted Nifty App, just retrieve his fb infos
			if(response.status == 'connected')
				that.fbRetrieveFriendsList();
			else
				that.fbAcceptApp();
		}, true);
	},
	
	fbAcceptApp : function(){
		var that = this;
		FB.login(function(response){
			if(response.status == 'connected')
				that.fbLogin();
		}, {
            scope: 'email, user_birthday, user_location, user_hometown, user_about_me, publish_stream, friends_status, user_status'
        });
	},
	
	fbRetrieveFriendsList : function(){
		this.nextStep('connected');
		var that = this;
		FB.api({
            method: 'fql.query',
            query: 'SELECT uid, first_name, last_name, name, pic_square  FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())'
        },
        function (response) {
        	that.fbBuildFriendList(response);
        });
	},
	
	nextStep : function(value_content){
		this.steps_length = this.steps.length;
		
		this.steps[this.current_step].removeClass('current');
		var value = this.steps[this.current_step].getElement('span.value');
		value.set('html', value_content);
		
		if(this.current_step < (this.steps_length - 1)){
			this.current_step++;
			this.steps[this.current_step].addClass('current');
		}
	},
	
	fbBuildFriendList : function(friends){
		this.nextStep('done');
		this.fb_overflow.setStyles({
			'height' : '260px'
		});
		friends.each(function(friend){
			this.fbBuildFriend(friend);
		}.bind(this));
		
		// new Scrollbar(this.fb_overflow, {'class' : 'bright', fillParent : true});
		
		// Valid button
		this.fb_valid = new Element('a',{
			'class' : 'fb_valid',
			'html' : 'Valid'
		}).inject(this.fb_container, 'after');
		
		this.fb_valid.addEvent('click', function(e){
			this.fbGetSelectedFriends();
			e.stop();
		}.bind(this));
		
		this.NiftyBox.updateContent();
	},
	
	fbBuildFriend : function(user){
		var friend = new Element('div',{
			'class' : 'friend'
		}).inject(this.fb_container);
		
		var input = new Element('input',{
			'type' : 'checkbox',
			'class' : 'select_friend',
			'value' : user.uid
		}).inject(friend);
		input.store('user', user);
		
		var mask = new Element('div',{
			'class' : 'mask'
		}).inject(friend);
		
		new Element('img',{
			'src' : user.pic_square,
			'alt' : user.name,
			'title' : user.name
		}).inject(mask);
		
		new Element('div', {
			'class' : 'name',
			'html' : user.name
		}).inject(friend);
	},
	
	fbGetSelectedFriends : function(){
		this.selectedFbFriends = this.fb_container.getElements('input.select_friend:checked');
		new Fx.Morph(this.fb_overflow,{
			'link' : 'cancel',
			'duration' : '150',
			'onComplete' : function(){
				// this.fb_overflow.destroy();
				this.NiftyBox.updateContent();
				this.nextStep(this.selectedFbFriends.length + ' friends(s) selected');
				this.fbSendInvitations();
			}.bind(this)
		}).start({
			'height' : '0px',
			'opacity' : '0'
		});
	},
	
	fbSendInvitations : function(){
		var that = this;
		if(this.selectedFbFriends.length == 0){
			this.nextStep('not sent');
			this.NiftyBox.showResult('You haven\'t selected friends.');
			this.NiftyBox.updateContent();
		}
		else
		{
			var users_invited = [];
			this.selectedFbFriends.each(function(selected){
				var user = selected.retrieve('user');
				this.saveAndSend(user);				
			}.bind(this));
			this.nextStep('send');
			this.NiftyBox.showResult('Facebook invitations send.');
		}
	},
	
	saveAndSend : function(user){
		var that = this;
		new Request.JSON({
			url: _json_url,
			data: {
				'action' : 'invitation_fb',
				'user' : user
			},
			method: 'post',
			onComplete: function(xhr){
				if(xhr.status){
					//console.log(xhr);
					var url = this.fb_feed.invitation_url.replace('#', xhr.user_invitation_id);
					//console.log(url);
					FB.api('/' + user.uid + '/feed', 'post', {
			    		'message' : that.fb_feed.invitation_msg,
			    		'picture' : that.fb_feed.invitation_photo, 
			    		'description' : that.fb_feed.invitation_description,
			    		'name' : that.fb_feed.invitation_name,
			    		'link' : url,
			    		'actions' : [{name: that.fb_feed.invitation_action_title, link: url}]
			    	}, 
			    	function(response){
			    		// No response if published !!!
			    	});
				}
				
			}.bind(this)
		}).send();
	}
	
});

var InvitationsList = Class({
	options : {
		duration : 500
	},
	
	initialize : function(){
		this.overflow = $$('div#invitations_list div.overflow')[0];
		this.container = $$('div#invitations_list div.overflow div.list')[0];
	},
	
	retrieveList : function(){
		new Request.JSON({
			url: _json_url,
			data: {
				'action' : 'invitation_list'
			},
			method: 'post',
			onComplete: function(xhr){
				new Fx.Morph(this.overflow, {
					'link' : 'cancel',
					'duration' : this.options.duration,
					'onComplete' : function(){
						this.container.empty();
						this.buildInvitations(xhr);
					}.bind(this)
				}).start({
					'height' : '0px'
				});
			}.bind(this)
		}).send();
	},
	
	buildInvitations : function(data){
		if(data.length > 0)
		{
			for(var i=0; i<data.length; i++){
				var invitation = data[i];
				
				var line = new Element('div',{
					'class' : 'line'
				}).inject(this.container);
				
				new Element('div',{
					'class' : 'col name',
					'html' : invitation.user_invitation_last_name + ' ' + invitation.user_invitation_first_name
				}).inject(line);
				
				new Element('div',{
					'class' : 'col email',
					'html' : invitation.user_invitation_email
				}).inject(line);
				
				new Element('div',{
					'class' : 'col invited',
					'html' : invitation.user_invitation_date
				}).inject(line);
				
				new Element('div',{
					'class' : 'col status ' + invitation.user_invitation_status,
					'html' : invitation.user_invitation_status
				}).inject(line);
			}
			
			new Fx.Morph(this.overflow, {
				'link' : 'cancel',
				'duration' : this.options.duration,
				'onComplete' : function(){
				}.bind(this)
			}).start({
				'height' : this.container.getHeight() + 'px'
			});
		}
		else{
			var line = new Element('div',{
				'class' : 'none',
				'html' : 'You don\'t have send invitations.'
			}).inject(this.container);
		}
		
	}
});

window.addEvent('domready', function(){
	new UserInvite();
	__InvitationsList = new InvitationsList();
	__InvitationsList.retrieveList();
});