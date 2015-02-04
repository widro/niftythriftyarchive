window.addEvent('domready', function(){
	var login_wrapper = $('opa_wrapper');
	var links = $$('body a');
	var body = $$('body')[0];

	links.removeEvents('click');
	links.each(function(link){
		if(!$chk(link.getParent('div#opa_wrapper'))){
			link.addEvent('click', function(e, link){
				var currenturl = document.URL;
				var thislink = link.get('href');
				var tocheck = new Array();
				tocheck.push("content");
				tocheck.push("blog.niftythrifty");
				tocheck.push("twitter.com");
				tocheck.push("facebook.com");
				tocheck.push("instagram.com");
				tocheck.push("pinterest.com");
				
				if(currenturl.indexOf('Collections/Archive')>0){
					tocheck.push("Collections/Product");
				}
				
				for (var i = 0; i < tocheck.length; i++) {
					if(thislink.indexOf(tocheck[i])>0){
						return;
					}
				}
				
				
				if( /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent) ) {
					e.stop();
					window.location = '/login';
					return;
				}
				e.stop();
				openlogin();
				/*
				login_wrapper.setStyles({
					'display' : 'block'
				});
				if ($chk($('login_redirect')))
					$('login_redirect').set('value', link.get('href'));
				new Fx.Scroll(window, {
					'onComplete' : function(){
						login_wrapper.fade('in');
						body.setStyles({
							'overflow' : 'hidden'
						});
						login_wrapper.setStyles({
							'width' : window.getWidth() + 'px'
						});
					}
				}).toTop();
				*/
			}.bindWithEvent(this, [link]));
		}
	});
	/*	
	*/
	
	var close = $('close_login');
	close.addEvent('click', function(e){
		var login_wrapper_inner = $('user_login');
		    login_wrapper_inner.setStyles({
			'display' : 'none'
		    });
		e.stop();
		body.setStyles({
			'overflow' : 'auto',
			'width' : '100%'
		});
		login_wrapper.fade('out');

	});


	var close2 = $('close_register');
	close2.addEvent('click', function(e){
		var login_wrapper_inner = $('user_registration');
		e.stop();
		body.setStyles({
			'overflow' : 'auto',
			'width' : '100%'
		});
		login_wrapper.fade('out');
		login_wrapper_inner.setStyles({
		'display' : 'none'
		});

	});

	var close3 = $('logintoregister');
	close3.addEvent('click', function(e){
		var login_wrapper_inner = $('user_login');
		login_wrapper_inner.setStyles({
		'display' : 'none'
		});
		openregister();
	});






	var hashtag = window.location.hash;

	if(hashtag=="#login"){
		openlogin();
	}

	if(hashtag=="#register"){
		openregister();
	}

	function openlogin(){
	    var body = $$('body')[0];
	    var login_wrapper = $('opa_wrapper');
	    var login_wrapper_inner = $('user_login');
	    login_wrapper.setStyles({
		'display' : 'block'
	    });

	    login_wrapper.setStyles({
		'display' : 'block'
	    });

	    login_wrapper_inner.setStyles({
		'display' : 'block'
	    });

	    new Fx.Scroll(window, {
		'onComplete' : function(){
		    login_wrapper.fade('in');
		    body.setStyles({
			'overflow' : 'hidden'
		    });
		    login_wrapper.setStyles({
			'width' : window.getWidth() + 'px'
		    });
		}
	    }).toTop();

	    var redirect = '<xsl:value-of select="//View/homepage/redirect" />';

	    if (redirect != '') {
		$('login_redirect').set('value', redirect);
	    }


	}

	function openregister(){
		var body = $$('body')[0];
		var login_wrapper = $('opa_wrapper');
		var login_wrapper_inner = $('user_registration');
		var login_wrapper_inner2 = $('user_login');
		    login_wrapper_inner2.setStyles({
			'display' : 'none'
		    });
		login_wrapper.setStyles({
		'display' : 'block'
		});

		login_wrapper.setStyles({
		'display' : 'block'
		});

		login_wrapper_inner.setStyles({
		'display' : 'block'
		});

		new Fx.Scroll(window, {
		'onComplete' : function(){
		    login_wrapper.fade('in');
		    body.setStyles({
			'overflow' : 'hidden'
		    });
		    login_wrapper.setStyles({
			'width' : window.getWidth() + 'px'
		    });
		}
		}).toTop();

		var redirect = '<xsl:value-of select="//View/homepage/redirect" />';

		if (redirect != '') {
		$('login_redirect').set('value', redirect);
		}
	
	}

	if($('login_fake')){
		$('login_fake').addEvent('click', function(){
			openlogin();
		});
	}

	if($('register_fake')){
		$('register_fake').addEvent('click', function(){
			openregister();
		});
	}










});