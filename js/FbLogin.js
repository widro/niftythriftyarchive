var FbLogin = Class({
	initialize : function(){
		var that = this;
		
		this.fb_login = $('fb-login');
		this.fb_register = $('fb-register');
		this.user_lastname = $('registration_userLastName');
		this.user_firstname = $('registration_userFirstName');
		this.user_email = $('registration_userEmail');
		this.user_fb_id = $('registration_userFbId');
		
		// If FB JS API is loaded initialize Fb Nifty App
		if (window.FB)
            this.fbReady();
        else
        	window.fbAsyncInit = that.fbReady();
	},
	
	fbReady : function() {
		FB.init({
			appId      : '374731519293179',
			channelUrl : 'https://www.niftythrifty.com/channel.html',
			status     : true,
			cookie     : true,
			xfbml      : false
		});
		if(this.fb_login){
			this.fb_login.addEvent('click', function(e){
				var whichtype = this.fb_login.get("name");
				this.fbLogin(whichtype);
				e.stop();
			}.bind(this));
		}
		if(this.fb_register){
			this.fb_register.addEvent('click', function(e){
				var whichtype = this.fb_register.get("name");
				this.fbLogin(whichtype);
				e.stop();
			}.bind(this));
		}
	},
	
	fbLogin : function(whichtype){
		var that = this;
		FB.getLoginStatus(function(response){ 
			// The dude has already accepted Nifty App, just retrieve his fb infos
			if(response.status == 'connected')
				that.fbRetrieveUserInfos(whichtype);
			// The dude had never accepted the Nifty App, let's go login him
			else
				that.fbAcceptApp(whichtype);
		}, true);
	},
     
	fbRetrieveUserInfos : function(whichtype){
		var that = this;
		FB.api({
            method : 'fql.query',
            query : 'select uid, name, email, first_name, last_name, username, hometown_location, pic_big from user where uid=me()'
        },
        function (response) {
        	user_infos = response[0];
        	if(whichtype=="fb_login_btn"){
			$('login_facebook').set('value', 'true');
			$('userFbId').set('value', user_infos.uid);
			$('username').set('value', user_infos.email);
			$('login_form').submit();
        	}
        	else if(whichtype == "fb_register_btn"){
        		//change first name to hidden and change label
			that.user_firstname.set('value', user_infos.first_name);
			that.user_firstname.set('type', 'hidden');
			$('user_first_name_div3').setStyle('display','none');
			$('user_first_name_label').set("html", "First Name: <b>" + user_infos.first_name + "</b>");


        		//change last name to hidden and change label
			that.user_lastname.set('value', user_infos.last_name);
			that.user_lastname.set('type', 'hidden');
			$('user_last_name_div3').setStyle('display','none');
			$('user_last_name_label').set("html", "Last Name: <b>" + user_infos.last_name + "</b>");

			that.user_email.set('value', user_infos.email);
			that.user_email.set('type', 'hidden');
			$('user_email_div3').setStyle('display','none');
			$('user_email_label').set("html", "Email: <b>" + user_infos.email + "</b>");
			
			
			//assign facebook id in form
			that.user_fb_id.set('value', user_infos.uid);
			
			//change text of register form h1
			$('reg_form_h1').set("html", "Choose Password & Complete Your Registration!");
			
			//hide facebook btn
			$('fb-login').setStyle('display','none');
			
			//change submit text
			$('register_form_submit_btn').set("value", "Complete Your Registration!");
			$('register_form_submit_btn').setStyle('width','300px');

        	}
        	else if(whichtype == "fb_account_btn"){
			$('user_fb_id').set('value', user_infos.uid);
			$('accountinfo_form').submit();
        	}
        	
        });
	},
	
	fbAcceptApp : function(whichtype){
		var that = this;
		FB.login(function(response){
			if(response.status == 'connected')
				that.fbRetrieveUserInfos(whichtype);
		}, {
            scope: 'email, user_birthday, user_location, user_hometown, user_about_me, publish_stream, friends_status, user_status'
        });
	},
	
	sendFbUserInfos : function(fbUserInfos){
		new Request.JSON({
			url: _jsonUrl,
			data: {
				'action' : 'fb_user_login',
				'fb_user_infos' : fbUserInfos
			},
			method: 'post',
			onComplete: function(xhr){
				if(xhr.connected == 'true'){
					window.location = xhr.forward_url
				}
			}.bind(this)
		}).send();
	}
	
});
window.addEvent('domready', function(){
	new FbLogin();
});
