/*
 * SocialEngineMods Javascript Library v0.4
 * http://www.SocialEngineMods.Net
 * CRC: 8f2dd94788f2e3840afd58f988cf863d
 *
 * Copyright SocialEngineMods.Net
 * This code is licensed GPL for use exclusively on SocialEngine sites
 *
 */


/* Extensions */


Function.prototype.bind = function(obj) {
  var method = this, temp = function() {
    return method.apply(obj, arguments)
  };
  return(temp);
}; 

__super_class = function (obj) {
  this.__super=obj;
  this.__parent=obj.prototype.parent;
};

__super_class.prototype = {
  __super_method : function(method, pointer) {
    var __pointer=pointer;
    this[method]=function() {
      var __parent=this.__context.parent;
      this.__context.parent=__parent ? __parent.parent : null;
      var __ret=__pointer.apply(this.__context, arguments);
      this.__context.parent=__parent;
      __parent=null;
      return __ret;
    };
  },
  
  construct : function(context) {
    this.__context=context;
    var a=new Array();
    for (var i=1; i<arguments.length; i++) {
      a.push(arguments[i]);
    }
    this.__context.parent=this.__parent;
    var __ret=this.__super.apply(context, a);
    this.__context.parent=this;
    return __ret;
  }
};

Function.prototype.extend=function(obj) {
  this.prototype.parent=new __super_class(obj);
  for (var i in obj.prototype) {
    if (typeof obj.prototype[i]=='function') {
      this.prototype[i]=obj.prototype[i];
      this.prototype.parent.__super_method(i, obj.prototype[i]);
    }
    else if (i!='parent') {
      this.prototype[i]=obj.prototype[i];
    }
  }
};


/* SEMods */


if (typeof SEMods == 'undefined') {
  SEMods = function () {};
}


/* SEMods Utils */


SEMods.Utils = function () {};
SEMods.Utils = {
  dbgFunc : null,
  
  htmlspecialchars : function (text) {
	  return text ? text.toString().replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
  },

  toHtml : function (text) {
	  return SEMods.Utils.htmlspecialchars(text).replace(/\n/g, '<br />');
  },
  
  debug : function (message) {
	if(SEMods.debug)
	  this.dbgFunc ? this.dbgFunc(message) : alert(message);
  },
  
  setDebugger : function(dbgFunc) {
	this.dbgFunc = dbgFunc;
  },
  
  unescapeQuotes : function (word) {

	// jsesq is a key/acronym for javascript escaped single quote
	// jsdsq is a key/acronym for javascript escaped double quote
  
	escaped = word.replace(/:jsesq:/g, "'"); 
	escaped = escaped.replace(/:jsedq:/g, '"'); 
	escaped = escaped.replace(/:jselb:/g, '\['); 
	escaped = escaped.replace(/:jserb:/g, '\]'); 
	escaped = escaped.replace(/:jsebs:/g, '\\'); 
  
	return escaped;
  },
  
  arrayToQueryString : function (queryArray)  {
	var query = '';
	
	for( var key in queryArray ) {
	  query += encodeURIComponent(key) + '=' + encodeURIComponent(queryArray[key]) + '&';
	}
	
	return query.slice(0, -1);
  }

};


/* SEMods Browser */


SEMods.Browser = function () {};
SEMods.Browser = {
    isIE : (/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent) ),
    isFireFox : (/FireFox/i.test(navigator.userAgent)),
    isOpera : (/Opera/i.test(navigator.userAgent)),

    addEvent : function (obj, type, func) {
        if (obj.addEventListener) {
            obj.addEventListener(type, func, 0);
        } else if (obj.attachEvent) {
            obj.attachEvent("on" + type, func);
        }
    },
    
	register_onload : function (handler) {
	  if (window.onload) {
	    var original_handler=window.onload;
	    window.onload=function() { original_handler(); handler(); };
	  }
	  else {
	    window.onload=handler;
	  }
	},
	
	ge : function(element) {
      var elem;
      if( typeof element == 'string' ) {
        elem = document.getElementById(element);
        
        // try by name, first in array
        if(!elem)
          elem = this.geByName(element);
      } else {
        elem = element;
      }
      return elem;
	},
    
	geByName : function(element) {
		var elems = document.getElementsByName(element);
        if(elems.length == 1)
          return elems[0];
        return null;
	},
    
    show : function () {
      for( var i = 0; i < arguments.length; i++ ) {
        var element = SEMods.B.ge(arguments[i]);
        if (element && element.style) element.style.display = 'block';
      }
    },
    
    hide : function () {
      for( var i = 0; i < arguments.length; i++ ) {
        var element = SEMods.B.ge(arguments[i]);
        if (element && element.style) element.style.display = 'none';
      }
    },

    toggle : function () {
      for( var i = 0; i < arguments.length; i++ ) {
        var element = SEMods.B.ge(arguments[i]);
	    element.style.display = (element.style.display == 'block') ? 'none' : 'block';
      }
    },

    // Get Absolute X Position of HTML Element
    findX : function(obj) {
      var curleft = 0;
      if (obj.offsetParent) {
        while (obj.offsetParent) {
          curleft += obj.offsetLeft
          obj = obj.offsetParent;
        }
      }
      else if (obj.x)
        curleft += obj.x;
      return curleft;
    },
    
  
    // Get Absolute Y Position of HTML Element
    findY : function (obj) {
      var curtop = 0;
      if(obj.offsetParent) {
        while (obj.offsetParent) {
          curtop += obj.offsetTop
          obj = obj.offsetParent;
        }
      }
      else if (obj.y)
        curtop += obj.y;
      return curtop;
    },
    
    mousePosX : function (e) {
      var posx = 0;
      if (!e) var e = window.event;
      if (e.pageX)
        posx = e.pageX;
      else if (e.clientX && document.body.scrollLeft)
        posx = e.clientX + document.body.scrollLeft;
      else if (e.clientX && document.documentElement.scrollLeft)
        posx = e.clientX + document.documentElement.scrollLeft;
      else if (e.clientX)
        posx = e.clientX;
      return posx;
    },
    
    mousePosY : function (e) {
      var posy = 0;
      if (!e) var e = window.event;
      if (e.pageY)
        posy = e.pageY;
      else if (e.clientY && document.body.scrollTop)
        posy = e.clientY + document.body.scrollTop;
      else if (e.clientY && document.documentElement.scrollTop)
        posy = e.clientY + document.documentElement.scrollTop;
      else if (e.clientY)
        posy = e.clientY;
      return posy;
    },
    
    getStyle : function(obj, property) {
        if (window.getComputedStyle) {
            return window.getComputedStyle(obj, null).getPropertyValue(property);
        }
        if (document.defaultView && document.defaultView.getComputedStyle) {
            var computedStyle = document.defaultView.getComputedStyle(obj, null);
            if (computedStyle) return computedStyle.getPropertyValue(property);
        }
        if (obj.currentStyle) {
            return obj.currentStyle[property];
        }
        return obj.style[property];
    },
    
    getStyleName : function(stylename) {
      return SEMods.Browser.isIE ? stylename : stylename.replace(/[A-Z]/g, function(a){return'-'+a.toLowerCase();} );
    },
    
    // em's not supported for now
    getPXMetrics : function(metric, defvalue) {
      var metricBase = parseFloat(metric);
      if(isNaN(metricBase)) return defvalue!=null ? defvalue : metricBase;
      return /px/i.test(metric) ? metricBase : /pt/i.test(metric) ? 1.3333*metricBase  : metricBase;
    },
    
    createDiv : function( parent, id, cname ) {
      var div = document.createElement("div");
      if(id) div.id = id;
      if(cname) div.className = cname;
      parent.appendChild( div );
      return div;
  }
  
};



/* Shortcuts */



SEMods.B = SEMods.Browser;
SEMods.U = SEMods.Utils;



/* SEMods TextAreaControl */



SEMods.TextAreaControl = function(object) {
    this.obj = object;
    this.obj.style['overflow'] = 'hidden';
    this.originalHeight = this.obj.offsetHeight;
    var updater = this.update.bind(this);
    SEMods.Browser.addEvent(object, "focus", this.onFocus.bind(this));
    SEMods.Browser.addEvent(object, "blur", this.onBlur.bind(this));
    this.update();
};

SEMods.TextAreaControl.prototype = {
    obj : null,
    updating : false,
    autoGrow : false,
    originalHeight : null,
    shadowElement : null,
    increment : 0,
    timer : null,
    lastLength : 0,
    fontFamily : SEMods.Browser.getStyleName('fontFamily'),
    fontSize : SEMods.Browser.getStyleName('fontSize'),
    paddingLeft : SEMods.Browser.getStyleName('paddingLeft'),
    paddingRight : SEMods.Browser.getStyleName('paddingRight'),
    lineHeight : SEMods.Browser.getStyleName('lineHeight'),
    
    setAutoGrow : function(autoGrow) {
        this.autoGrow = autoGrow;
        this.createShadowElement();
        this.update();
    },
    
    onUpdate : function() {
        if(this.autoGrow && this.lastLength != this.obj.value.length) {
            this.lastLength = this.obj.value.length;
            this.updateShadowElement();
            this.obj.style.height = Math.max(this.originalHeight, this.shadowElement.offsetHeight + this.increment) + 'px';
        }
    },
    
    beginUpdate : function() {
        if(this.updating)
            return false;
        this.updating = true;
        return true;
    },
    
    endUpdate : function() {
        this.updating = false;
    },
    
    update : function() {
        if(!this.beginUpdate())
            return;
        
        this.onUpdate();
        this.endUpdate();
    },
    
    createShadowElement : function() {
        if(this.shadowElement)
            return;
        
        this.shadowElement = document.createElement("DIV");
        this.shadowElement.style.position = "absolute";
        this.shadowElement.style.top = "-99999px";
        this.shadowElement.style.left = "-99999px";
        
        document.body.appendChild(this.shadowElement);
    },
    
    updateShadowElement : function () {
        if(this.shadowElement) {
            this.shadowElement.innerHTML = SEMods.Utils.toHtml(this.obj.value + '<br>');
            var fontSize = SEMods.Browser.getPXMetrics( SEMods.Browser.getStyle(this.obj, this.fontSize), 10);
            var lineHeight = SEMods.Browser.getStyle(this.obj, this.lineHeight);
            // Opera misses on line-height
            if(SEMods.Browser.isOpera) 
              lineHeight = SEMods.Browser.getPXMetrics( lineHeight, 0) + 3 + 'px';
              
            this.increment = fontSize + 10;
        
            this.shadowElement.style['width'] = this.obj.offsetWidth + 'px';
            this.shadowElement.style['lineHeight'] = lineHeight;
            this.shadowElement.style['fontSize'] = SEMods.Browser.getStyle(this.obj, this.fontSize);

            this.shadowElement.style['fontFamily'] = SEMods.Browser.getStyle(this.obj, this.fontFamily);
            this.shadowElement.style['paddingLeft'] = SEMods.Browser.getStyle(this.obj, this.paddingLeft);
            this.shadowElement.style['paddingRight'] = SEMods.Browser.getStyle(this.obj, this.paddingRight);
            
        } 
    },
    
    onFocus : function() {
      this.timer = setInterval(this.update.bind(this), 500);
    },
    
    onBlur : function() {
      if(this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    }
    
};



/* SEMods TextAreaControl */



SEMods.Ajax = function (doneHandler, failHandler)
{
  this.onDone = doneHandler;
  this.onFail = failHandler;
  this.transport = this.getTransport();
  this.transport.onreadystatechange = this.stateDispatch.bind(this);
};

SEMods.Ajax.prototype = {
  
  get : function (uri, query, force_sync)  {
    // Firefox doesn't call onDone and onFail handlers if you force_sync
    force_sync = force_sync || false;
    if( typeof query != 'string' )
      query = SEMods.U.arrayToQueryString(query);
    fullURI = uri+(query ? ('?'+query) : '');
    this.transport.open('GET', fullURI, !force_sync );
    this.transport.send('');
  },

  post : function (uri, data, force_sync) {
    force_sync = force_sync || false;
    if( typeof data != 'string' )
      data = SEMods.U.arrayToQueryString(data);
    this.transport.open('POST', uri, !force_sync);
    this.transport.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    this.transport.send(data);
  },

  stateDispatch : function () {

    if( this.transport.readyState == 4 ) {
      if( this.transport.status >= 200 &&
          this.transport.status < 300 &&
          this.transport.responseText.length > 0 ) {
        if( this.onDone ) this.onDone(this, this.transport.responseText);
      } else {
        if( this.onFail ) this.onFail(this);
      }
    }
  },

  getTransport : function () {
    var ajax = null;
    
    try { ajax = new XMLHttpRequest(); }
    catch(e) { ajax = null; }
    
    try { if(!ajax) ajax = new ActiveXObject("Msxml2.XMLHTTP"); }
    catch(e) { ajax = null; }
    
    try { if(!ajax) ajax = new ActiveXObject("Microsoft.XMLHTTP")}
    catch(e) { ajax = null; }
    
    return ajax;
  }
};


/* Global namespace helper functions */

if (typeof textarea_autogrow == 'undefined') {
textarea_autogrow = function (elementid) {
    var el = SEMods.Browser.ge(elementid);
    if(!el) SEMods.Utils.debug("textarea_autogrow(): element not found");
    if(el && !el._controlled) {
        el._controlled = true;
        new SEMods.TextAreaControl(el).setAutoGrow(true);
    }
};
}

