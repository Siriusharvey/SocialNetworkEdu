
var openidconnect_api_endpoint = 'openidconnect_ajax.php';

function openidconnect_autologin(openid_user_id) {
  // get user pref and :
  // if autologin is on - refresh page
  // if autologin is off - do nothing
  // if autologin is not set - show dialog

  var ajax = new SEMods.Ajax(openidconnect_autologin_onSuccess);
  var params = 'task=autologin&openidservice='+openidconnect_primary_network;
  ajax.post(openidconnect_api_endpoint, params)
  
}


function openidconnect_autologin_onSuccess (obj, responseText) {
  var r = [];
  try {
	r = eval('(' + responseText + ')')
  } catch(e) {
	r.status = 1
  };
  
  if (r.status == 0) {
	
	if(r.autologin == 0) {
	  
	  // show dialog
	  openidconnect_autologin_prompt();
	  
	} else if(r.autologin == 1) {

	  // autologin
	  openidconnect_autologin_complete();
	  
	}
	// otherwise autologin is off by user
	
  } else {

  }
  
}


function openidconnect_autologin_prompt() {
  mooFaceboxExShow("", "#openidconnect_autologin_prompt", 570)  
  
  // reattach events
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_autologin_prompt_confirmed').addEvent('click', function(e) { openidconnect_autologin_confirmed() });
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_autologin_prompt_cancel').addEvent('click', function(e) { openidconnect_autologin_cancel() });
  
}

function openidconnect_autologin_confirmed() {
  
  var checkbox = _mooFaceboxEx.faceboxEl.getElement('.openidconnect_autologin_remember');
  var checked = checkbox.checked;
  
  mooFaceboxExClose();
  
  var complete_login = function() { openidconnect_autologin_complete(); };
  
  if(checked) {
	var ajax = new SEMods.Ajax(complete_login,complete_login);
	var params = 'task=autologinnexttime&openidservice='+openidconnect_primary_network;
	ajax.post(openidconnect_api_endpoint, params);
  } else {
	complete_login();
  }

}

function openidconnect_autologin_complete() {
  window.location = 'login_openid.php?openidservice=' + openidconnect_primary_network + '&next=' + escape(document.location);
}

function openidconnect_autologin_cancel() { 
  var checkbox = _mooFaceboxEx.faceboxEl.getElement('.openidconnect_autologin_remember');
  var checked = checkbox.checked;
  
  mooFaceboxExClose();
  
  var ajax = new SEMods.Ajax();
  var params = 'task=autologinsuppress&openidservice='+openidconnect_primary_network + '&autologinremember=' + (checked ? 1 : 0);
  ajax.post(openidconnect_api_endpoint, params);
  
}


function openidconnect_facebook_require_login() {
  SEMods.B.register_onload( function() { openidconnect_facebook_require_login_onload(); } );
}

var openidconnect_facebook_require_login_current_state = 1;

function openidconnect_facebook_require_login_onload() {

  FB_RequireFeatures(["Connect"], function () {
	FB.ensureInit(function() {
	  FB.Connect.ifUserConnected(
		function(facebook_user_id) {
		  if(openidconnect_facebook_require_login_current_state != 0) {
			if (facebook_user_id == openidconnect_facebook_user_id) {
			  openidconnect_facebook_require_login_loaded(true);
			} else {
			  openidconnect_facebook_require_login_loaded(false);
			}
		  }
		},
		function() {
		  openidconnect_facebook_require_login_current_state = 0;
		  openidconnect_facebook_require_login_loaded(false);
		});
	})
  });

}

function openidconnect_facebook_require_login_loaded(loggedin) {
  if($('openidconnect_facebook_require_login_loading')) {
	SEMods.B.hide('openidconnect_facebook_require_login_loading');
  }
  if (loggedin) {
	SEMods.B.show('openidconnect_facebook_loggedin');
  } else {
	SEMods.B.hide('openidconnect_facebook_loggedin');
	SEMods.B.show('openidconnect_facebook_notloggedin');
  }
}

function openidconnect_register_invite_form() {

  SEMods.B.register_onload( function() { openidconnect_invite_form_onload(); } );
}





function openidconnect_invite_form_invitable(facebook_user_id) {

  if (!facebook_user_id || (openidconnect_facebook_user_id != facebook_user_id)) {
	SEMods.B.hide('openidconnect_facebook_invite_dialog');
	SEMods.B.show('openidconnect_facebook_connect');
  }
}

function openidconnect_invite_form_onload() {
  
  FB_RequireFeatures(["XFBML","Connect"], function () {
	FB.Facebook.init(openidconnect_facebook_api_key, 'xd_receiver.php', {
	  ifUserConnected: function (facebook_user_id) {
		openidconnect_invite_form_invitable(facebook_user_id)
	  },
	  ifUserNotConnected: function () {
		openidconnect_invite_form_invitable()
	  },
	  doNotUseCachedConnectState: true
	});
  });
 
}


function openidconnect_register_facebook_login_button(redirect_url) {
  SEMods.B.register_onload( function() { openidconnect_facebook_login_button_onload(redirect_url); } );
}

function openidconnect_facebook_login_button_onload(redirect_url) {
  
  FB_RequireFeatures(["Connect"], function () {
	openidconnect_facebook_login_button_clickable(redirect_url);
  });
  
}

function openidconnect_facebook_login_button_clickable(redirect_url) {

  $$('.openidconnect_facebook_login_button').each( function(elem) {
	elem.addEvent('click', function() {
	  FB_RequireFeatures(["Connect"], function () {
		FB.Facebook.init(openidconnect_facebook_api_key, "xd_receiver.php", {doNotUseCachedConnectState: true});
		FB.Connect.requireSession();
		FB.Facebook.get_sessionState().waitUntilReady(function (session_object) {
		  window.location = redirect_url;
		})
	  })
	  return false;
	})
  });

}  

function openidconnect_facebook_disconnect(redirect) {
  
  if(typeof redirect == 'undefined') {
	redirect = 'user_logout.php';
  }
  
  FB.ensureInit(function() {
	FB.Connect.get_status().waitUntilReady( function( status ) {
	   switch ( status ) {
		case FB.ConnectState.connected:
		  FB.Connect.logoutAndRedirect( redirect );
		   break;
 
		case FB.ConnectState.appNotAuthorized:
		  window.location = redirect;
	   }
	}) 
  });
  
}

function openidconnect_facebook_authorize_status_update() {
  openidconnect_facebook_prompt_permission('status_update');
}

function openidconnect_facebook_logout() {

  FB_RequireFeatures(["Connect"], function () {
	FB.Facebook.init(openidconnect_facebook_api_key, "xd_receiver.php", null);
	FB.Connect.logoutAndRedirect( 'user_logout.php' );
  });

  return false;

}

function openidconnect_facebook_logout_network() {
  window.location = 'user_logout.php';
}

function openidconnect_facebook_hook_logout_link() {

  $$("A.top_menu_item").each( function(el) {
	if(/user_logout.php/.test(el.href)) {

	  el.href = 'javascript:void(0)';
	  el.innerHTML = "<img style='margin-bottom: -4px' border='0' id='fb_logout_image' src='http://static.ak.fbcdn.net/images/fbconnect/logout-buttons/logout_small.gif' alt='Connect'/>";

	  if (typeof el.addEventListener != 'undefined') {
		el.addEventListener("click", openidconnect_facebook_logout, false);
	  } else if (typeof el.attachEvent != 'undefined') {
		el.attachEvent('onclick', openidconnect_facebook_logout);
	  }

	}
  });

}


function openidconnect_compose_feed_story(story_type,story_params) {
  
  var ajax = new SEMods.Ajax(openidconnect_compose_feed_story_onSuccess, openidconnect_compose_feed_story_onFail);
  var params = 'task=composestory&story_type=' + story_type + '&story_params=' + story_params;
  ajax.post(openidconnect_api_endpoint, params)

}


function openidconnect_compose_feed_story_onSuccess (obj, responseText) {
  var r = [];
  try {
	r = eval('(' + responseText + ')')
  } catch(e) {
	r.status = 1
  };
  
  if (r.status == 0) {
	  
	if(r.openidconnect_feed_story.publish_using == 'stream') {
	  openidconnect_facebook_publish_stream( r.openidconnect_feed_story.story_type,
											 r.openidconnect_feed_story.data,
											 r.openidconnect_feed_story.user_prompt,
											 r.openidconnect_feed_story.user_message
											);
	} else {
	  openidconnect_facebook_publish_feed_story( r.openidconnect_feed_story.story_type,
												 r.openidconnect_feed_story.data,
												 r.openidconnect_feed_story.template_bundle_id,
												 r.openidconnect_feed_story.user_prompt,
												 r.openidconnect_feed_story.user_message
												 );
	}
	
  } else {

  }
  
}

function openidconnect_compose_feed_story_onFail (obj, responseText) {
}

function openidconnect_publish_feed_story_prompt() {
  openidconnect_require_connected( function() { _openidconnect_publish_feed_story_prompt(); } );
}

function _openidconnect_publish_feed_story_prompt() {
  mooFaceboxExShow("", "#openidconnect_publish_feed_story_prompt", 570)  

  // reattach events
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_publish_feed_story_prompt_confirmed').addEvent('click', function(e) { openidconnect_publish_feed_story_prompt_confirmed() });
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_publish_feed_story_prompt_wait').addEvent('click', function(e) { openidconnect_publish_feed_story_prompt_wait() });
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_publish_feed_story_prompt_cancel').addEvent('click', function(e) { openidconnect_publish_feed_story_prompt_cancel() });
}

function openidconnect_publish_feed_story_prompt_confirmed() { 
  mooFaceboxExClose();

  openidconnect_compose_feed_story(openidconnect_facebook_feed_story_type,openidconnect_facebook_feed_story_params);
 
}

function openidconnect_publish_feed_story_prompt_wait() { 
  mooFaceboxExClose();
}

function openidconnect_publish_feed_story_prompt_cancel(story_type) {

  var checkbox = _mooFaceboxEx.faceboxEl.getElement('.openidconnect_publish_feed_story_neveragain');
  var checked = checkbox.checked;
  
  mooFaceboxExClose();
  
  openidconnect_publish_feed_story_completed(openidconnect_facebook_feed_story_type);

  if(checked) {
	var ajax = new SEMods.Ajax();
	var params = 'task=storynopublish&story_type=' + story_type;
	ajax.post(openidconnect_api_endpoint, params);
  }
  
}

function openidconnect_publish_feed_story_completed(story_type, callback) {
  var ajax = new SEMods.Ajax();
  var params = 'task=clearstory&story_type=' + story_type;
  ajax.post(openidconnect_api_endpoint, params)
  
  if((typeof callback != 'undefined') && !callback) {
	callback();
  }
}




var openidconnect_connected = false;
var openidconnect_onconnect = null;

function openidconnect_onconnected(hook_logout) {

  FB.Facebook.get_sessionState().waitUntilReady(function (facebook_user_obj) {
	if (facebook_user_obj && (facebook_user_obj.uid == openidconnect_facebook_user_id)) {

	  openidconnect_connected = true;
	  if(openidconnect_onconnect) {
		openidconnect_onconnect();
	  }
	  if(hook_logout == 1) {
		openidconnect_facebook_hook_logout_link();
	  }
	  
	};
  });
  
}


function openidconnect_register_onconnect(handler) {

  if (openidconnect_onconnect) {
	var original_handler = openidconnect_onconnect;
	openidconnect_onconnect = function() { original_handler(); handler(); };
  } else {
	openidconnect_onconnect = handler;
  }
  
}


function openidconnect_facebook_onload(params) {

  var options = {'request_connect' : false,
				 'callback'		  : null,
				 'hook_logout'	  : true,
				 'user_exists' 	 : false,
				 'autologin'	: true
				};
				
  if(typeof params != 'undefined') {
    for (var param in params) {
	  options[param] = params[param];
	}
  }

  FB_RequireFeatures(["XFBML", "Connect"], function(){
	FB.Facebook.init( openidconnect_facebook_api_key, "xd_receiver.php", {
	  ifUserConnected: function (facebook_user_id) {

		// if user not logged in - auto login
		// if user logged in to SE, but with another user - try autologin
		if(options.autologin == 1) {
		  if((options.user_exists == 0) || ((openidconnect_facebook_user_id != 0) && (facebook_user_id != openidconnect_facebook_user_id)) ) {
			openidconnect_autologin(facebook_user_id);
		  }
		}
	  },
	  ifUserNotConnected: function () {
	  },
	  doNotUseCachedConnectState: true
    });
	
	if(options.user_exists == 1) {
	  FB.Connect.get_status().waitUntilReady( function( status ) {
		 switch ( status ) {
		  case FB.ConnectState.connected:
			  
			  if(options.callback) {
				options.callback();
			  }

			  openidconnect_onconnected(options.hook_logout);
			  
			 break;
   
		  //case FB.ConnectState.appNotAuthorized:
			 
		  case FB.ConnectState.userNotLoggedIn:
			 // some funcs queued
			if(openidconnect_onconnect && (options.request_connect == 1)) {
			  openidconnect_facebook_request_connect();
			}
		 }
	  }) 
	}
  });

}




function openidconnect_facebook_request_connect() {
  mooFaceboxExShow("", "#openidconnect_connect_prompt", 570)  

  // reattach events
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_connect_prompt_confirmed').addEvent('click', function(e) { openidconnect_facebook_request_connect_confirmed() });
  _mooFaceboxEx.faceboxEl.getElement('.openidconnect_connect_prompt_cancel').addEvent('click', function(e) { openidconnect_facebook_request_connect_cancel() });
}

function openidconnect_facebook_request_connect_confirmed() {
  mooFaceboxExClose();

  FB.Connect.requireSession( function() {
	// got some hooks
	if(openidconnect_onconnect) {
	  openidconnect_onconnected();
	} else {
	  openidconnect_refresh_page();
	}
  });  
  
}


function openidconnect_facebook_request_connect_cancel() { 
  mooFaceboxExClose();

  var ajax = new SEMods.Ajax();
  var params = 'task=suppressconnect';
  ajax.post(openidconnect_api_endpoint, params)
  
  openidconnect_publish_feed_story_completed('all');
  
}



function openidconnect_facebook_onlogin_ready() {
  openidconnect_refresh_page();
}


function openidconnect_refresh_page() {
  document.location = document.location;
}


function openidconnect_facebook_prompt_permission(permission, callback) {
  if(typeof callback == 'undefined'){
	callback = null;
  }
  FB.ensureInit(function() {
    FB.Connect.showPermissionDialog(permission,callback);
  });
}


function openidconnect_require_connected( callback ) {
  if(openidconnect_connected) {
	callback();
  } else {
	openidconnect_register_onconnect( function() { callback(); } );
  }
}

function openidconnect_facebook_publish_feed_story(story_type, template_data, form_bundle_id, userprompt, usermessage, callback) {
  openidconnect_require_connected( function() { _openidconnect_facebook_publish_feed_story(story_type, template_data, form_bundle_id, userprompt, usermessage, callback); } );
}

var openidconnect_facebook_load_form_bundle_id_callback;

function openidconnect_facebook_load_form_bundle_id(story_type,callback) {
  
  openidconnect_facebook_load_form_bundle_id_callback = callback;
  
  var ajax = new SEMods.Ajax(openidconnect_facebook_load_form_bundle_id_onSuccess, openidconnect_facebook_load_form_bundle_id_onFail);
  var params = 'task=getbundleid&story_type=' + story_type;
  ajax.post(openidconnect_api_endpoint, params)

}


function openidconnect_facebook_load_form_bundle_id_onSuccess (obj, responseText) {
  var r = [];
  try {
	r = eval('(' + responseText + ')')
  } catch(e) {
	r.status = 1
  };
  
  if (r.status == 0) {
	
	openidconnect_facebook_load_form_bundle_id_callback( r.template_bundle_id ); 
	
  } else {

  }
  
}

function openidconnect_facebook_load_form_bundle_id_onFail (obj, responseText) {
  
}

function _openidconnect_facebook_publish_feed_story(story_type, template_data, form_bundle_id, userprompt, usermessage, callback) {

  if(form_bundle_id == "auto") {
	openidconnect_facebook_load_form_bundle_id( story_type, function(_form_bundle_id) { _openidconnect_facebook_publish_feed_story(story_type, template_data, _form_bundle_id, userprompt, usermessage, callback); } );
	return;
  }
  
  if(typeof userprompt == 'undefined') {
	userprompt = null;
  }

  if(typeof usermessage == 'undefined') {
	usermessage = null;
  } else {
	usermessage = {value: usermessage};
  }
		
  // Load the feed form
  FB.ensureInit(function() {
	feed_callback = function() { openidconnect_publish_feed_story_completed(story_type, callback); };
	FB.Connect.showFeedDialog(form_bundle_id, template_data, null, null, null, FB.RequireConnect.promptConnect, feed_callback, userprompt, usermessage);
  });

}


function openidconnect_facebook_publish_stream(story_type, data, userprompt, usermessage, callback) {
  openidconnect_require_connected( function() { _openidconnect_facebook_publish_stream(story_type, data, userprompt, usermessage, callback); } );
}


function _openidconnect_facebook_publish_stream(story_type, data, userprompt, usermessage, callback) {

	feed_callback = function() { openidconnect_publish_feed_story_completed(story_type, callback); };

    var UserRequestsNoPrompting = 1;
    
    FB.ensureInit(function(){
	  FB.Connect.requireSession(function(){
		  if (UserRequestsNoPrompting) {
			FB.Facebook.apiClient.users_hasAppPermission("publish_stream",function(has){
			  if (has == 0) {
				FB.Connect.showPermissionDialog("publish_stream", function(granted){
				openidconnect_facebook_publish_stream2(data,true,userprompt,usermessage,feed_callback);
			   });
			  }
			  else {
				openidconnect_facebook_publish_stream2(data,true,userprompt,usermessage,feed_callback);
			  }
			});    
		  } else {
			openidconnect_facebook_publish_stream2(data,false,userprompt,usermessage,feed_callback);
		  }
	  });
    });

}

function openidconnect_facebook_publish_stream2(data,auto_publish,userprompt,usermessage,callback) {

  if(typeof callback == 'undefined') {
	callback = null;
  }

  if(typeof userprompt == 'undefined') {
	userprompt = null;
  }

  if(typeof usermessage == 'undefined') {
	usermessage = null;
  }
    
  var attachment = typeof data.attachment != 'undefined' ? data.attachment : null; 
  var links = typeof data.links != 'undefined' ? data.links : null;
  var target_id = typeof data.target_id != 'undefined' ? data.target_id : ''; 

  FB.Connect.streamPublish(usermessage,attachment,links,target_id,userprompt,callback,auto_publish);
	
}
