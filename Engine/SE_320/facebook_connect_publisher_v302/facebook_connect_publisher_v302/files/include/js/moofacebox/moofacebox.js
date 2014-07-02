/*
 * mooFacebox
 * version: 0.1 (03/10/2008)
 * @requires MooTools v1.2 or later
 *
 * MODIFIED BY SocialEngineMods http://www.socialenginemods.net/
 *
 * Facebox is a port to mootools of the original 
 * Facebox (http://famspam.com/facebox) written by Chris Wanstrath with some 
 * added features like drag support and titles.
 *
 * Licensed under the MIT:
 *
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 * Copyright 2008 Augusto Becciu [ augusto@becciu.org ]
 *
 * Usage:
 *  
 *  window.addEvent('domready', function() {
 *      var myFacebox = new mooFacebox();
 *  });
 *
 *
 *  <a href="#terms" rel="facebox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facebox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facebox">Terms</a>
 *    Loads the terms.png image in the box
 *
 */


// TODO: on ajax error (404) - ok
var _mooFaceboxEx = null;

function mooFaceboxExShow(caption, url, width, showclosebutton) {
    if(!_mooFaceboxEx) {
        _mooFaceboxEx = new mooFaceboxEx();
    }
    _mooFaceboxEx.show(caption, url, width, showclosebutton);
}

function mooFaceboxExClose() {
    if(!_mooFaceboxEx)
        return;

    _mooFaceboxEx.close();
}


var mooFaceboxEx = new Class({

    Implements: Options,

    options: {
        draggable: true,
        showclosebutton : true,
        width: '370',
        elementsSelector: 'a[rel="facebox"]',
        loading_image : './include/js/moofacebox/images/loading.gif',
        close_image   : './include/js/moofacebox/images/closelabel.gif',
        image_types   : [ 'png', 'jpg', 'jpeg', 'gif' ],
        facebox_html  : '\
    <div class="facebox-popup"> \
      <table class="facebox-table"> \
        <tbody> \
          <tr> \
            <td class="facebox-tl"/><td class="facebox-b"/><td class="facebox-tr"/> \
          </tr> \
          <tr> \
            <td class="facebox-b"/> \
            <td class="facebox-dialog-content"> \
              <div class="facebox-body"> \
                <div class="facebox-content"> \
                </div> \
                <div class="facebox-footer"> \
                  <a href="#" class="facebox-close"> \
                    <img src="./include/js/moofacebox/images/closelabel.gif" title="close" class="facebox-close_image" onerror="this.src=\'../include/js/moofacebox/images/closelabel.gif\'"/> \
                  </a> \
                </div> \
              </div> \
            </td> \
            <td class="facebox-b"/> \
          </tr> \
          <tr> \
            <td class="facebox-bl"/><td class="facebox-b"/><td class="facebox-br"/> \
          </tr> \
        </tbody> \
      </table> \
    </div>'
    },

    loading: function() {
        if (this.faceboxEl.getElement('.facebox-loading')) return true;

        this.faceboxEl.getElement('.facebox-content').empty();

        var bodyEl = this.faceboxEl.getElement('.facebox-body');
        bodyEl.getChildren().setStyle('display', 'none');
        
        var loadingEl = new Element('div', {'class': 'facebox-loading'});
        var loadingImgEl = new Element('img', {'src': this.options.loading_image});
        loadingEl.adopt(loadingImgEl);

        bodyEl.adopt(loadingEl);

        var pageScroll = this.getPageScroll();
        this.faceboxEl.setStyles({
            top: pageScroll[1] + (this.getPageHeight() / 4),
            left: pageScroll[0]
        });

        $(document).addEvent('keydown', this.keydownHdlr);

        this.fadeIn(this.faceboxEl);
    },

    reveal: function(data, klass) {
        if (klass) this.faceboxEl.getElement('.facebox-content').addClass(klass);

        //this.faceboxEl.setStyle('display', 'none');
        //if ($type(data) == 'string') {
        //    this.faceboxEl.getElement('.facebox-content-measure').set('html', data);
        //}
        //else {
        //    this.faceboxEl.getElement('.facebox-content-measure').adopt(data);
        //}

        //var width = parseInt( this.faceboxEl.getElement('.facebox-content-measure').getStyle('clientWidth') );
        //var width = this.faceboxEl.getElement('.facebox-content-measure').offsetWidth;
        //var xx = document.getElementById("asdasdasd");
        //var width = xx.offsetWidth;
        //console.debug("width:" + width);
        //if(width < 370) {
            //this.faceboxEl.getElement('.facebox-content').setStyle('width','370px');
        //}


        if ($type(data) == 'string') {
            this.faceboxEl.getElement('.facebox-content').set('html', data);
        }
        else {
            this.faceboxEl.getElement('.facebox-content').adopt(data);
        }

        this.faceboxEl.getElement('.facebox-loading').destroy();

        //var width = parseInt( this.faceboxEl.getElement('.facebox-content').getStyle('width') );
        //console.debug("width:" + width);
        //if(width < 370) {
        //    this.faceboxEl.getElement('.facebox-content').setStyle('width','370px');
        //}
        //this.faceboxEl.style.width = "370px";
        //width = parseInt( this.faceboxEl.getElement('.facebox-content').getStyle('width') );
        //console.debug("width:" + width);
        //console.debug("width:" + this.faceboxEl.getElement('.facebox-content-measure').offsetWidth);

        //this.faceboxEl.setStyle('display', 'block');

        this.faceboxEl.getElement('.facebox-body').getChildren().each(this.fadeIn);

//        this.faceboxEl.setStyles({
//            top: pageScroll[1] + (this.getPageHeight() / 4),
//            left: pageScroll[0]
//        });

//        $('#facebox').css('left', $(window).width() / 2 - ($('#facebox table').width() / 2))

    },

    fadeIn: function(el) {
        el.set('tween', {
            onStart: function() {
                el.setStyle('display', 'block');
            }
        });
        el.fade('in');
    },

    fadeOut: function(el) {
        el.set('tween', {
            onComplete: function() {
                el.setStyle('display', 'none');
            }
        });
        el.fade('out');
    },

    close: function() {
        $(document).removeEvent('keydown', this.keydownHdlr);

        this.fadeOut(this.faceboxEl);
        var contentEl = this.faceboxEl.getElement('.facebox-content');
        contentEl.set('class', '');
        contentEl.addClass('facebox-content');
        return false;
    },

    //setTitle: function(title) {
    //    var titleEl = this.faceboxEl.getElement('.facebox-title');
    //    title = "adasdasdasdasdasdasdasd";
    //    if (title == "")
    //        titleEl.setStyle('display', 'none');
    //    else
    //        titleEl.setStyle('display', 'block');
    //
    //    //titleEl.getElement('span').setText(title);
    //},

    initialize: function(options) {
        this.setOptions(options);

        this.faceboxEl = new Element('div', {'id': 'facebox', 'style': 'display: none;'});
        this.faceboxEl.fade('hide');
        this.faceboxEl.set('html', this.options.facebox_html);

        $(document.body).adopt(this.faceboxEl);

        // preload images
        var preload = [ new Asset.image(this.options.close_image),
                        new Asset.image(this.options.loading_image) ];

        this.faceboxEl.getElements('.facebox-b:first, .facebox-bl, .facebox-br, .facebox-tl, .facebox-tr').each(function(el) {
            preload.push(new Asset.image(el.getStyle('background-image').replace(/url\((.+)\)/, '$1')));
        });

        this.faceboxEl.getElement('.facebox-close').addEvent('click', this.close.bind(this));
        this.faceboxEl.getElement('.facebox-close_image').set('src', this.options.close_image);

        if (this.options.draggable == true) {
            var dcontentEl = this.faceboxEl.getElement('.facebox-dialog-content');
            this.drag = this.faceboxEl.makeDraggable({handle: dcontentEl});
            dcontentEl.setStyle('cursor', 'move');
        }

        this.keydownHdlr = function(e) {
            e = new Event(e);
            //e.stop();

            if (e.code == 27) this.close();
        }.bind(this);

        var image_types = this.options.image_types.join('|');
        image_types = new RegExp('\.' + image_types + '$', 'i');
        this.image_types = image_types;

        var elements = $$(this.options.elementsSelector);

        elements.addEvent('click', function(e) {
            e = new Event(e);
            e.stop();

            //this.setTitle(e.target.title);

            this.loading();

            // support for rel="facebox[.inline_popup]" syntax, to add a class
            var klass = e.target.rel.match(/facebox\[\.(\w+)\]/);
            if (klass) klass = klass[1];

            // div
            if (e.target.href.match(/#/)) {
                var url = window.location.href.split('#')[0];
                var target = e.target.href.replace(url+'#','');
                this.reveal($(target).clone().setStyle('display','block'), klass);

            // image
            } else if (e.target.href.match(image_types)) {
                var image = new Asset.image(e.target.href, {
                    onload: function() {
                        this.reveal('<div class="facebox-image"><img src="' + image.src + '" /></div>', klass);
                    }.bind(this)
                });

            // ajax
            } else {
                new Request({
                    url: e.target.href,
                    method: 'get',
                    onSuccess: function(responseText, responseXML) {
                        this.reveal(responseText, klass);
                    }.bind(this),
                    onFailure: function(responseText, responseXML) {
                        this.reveal("Error loading content.", klass);
                    }.bind(this)
                }).send();
            }

            return false;
        }.bind(this));

    },
    
    show : function(caption, targeturl, width, showclosebutton) {

        var klass = "";
        //this.width = "370px";
        //if(options) {
            //this.setOptions(options);
        //}

        if(width) {
            this.options.width = width;
        }

        if(showclosebutton) {
            this.options.showclosebutton = showclosebutton;
        }
        
        this.faceboxEl.getElement('.facebox-content').setStyle('width',this.options.width + 'px');

        //if(options) {
        //    if(options.width) {
        //        this.options.width = options.width + 'px';
        //    }
        //}

        this.loading();

        // div
        if (targeturl.match(/^#/)) {
            //var url = window.location.href.split('#')[0];
            //var target = targeturl.replace(url+'#','');
            var target = targeturl.replace('#','');
            this.reveal($(target).clone().setStyle('display','block'), klass);

        // image
        } else if (targeturl.match(this.image_types)) {
            var image = new Asset.image(targeturl, {
                onload: function() {
                    this.reveal('<div class="facebox-image"><img src="' + image.src + '" /></div>', klass);
                }.bind(this)
            });

        // ajax
        } else {
            new Request({
                url: targeturl,
                method: 'get',
                onSuccess: function(responseText, responseXML) {
                    this.reveal(responseText, klass);
                }.bind(this),
                onFailure: function(responseText, responseXML) {
                    this.reveal("Error loading content", klass);
                }.bind(this)
            }).send();
        }

        return false;
    },

    // getPageScroll() by quirksmode.com
    getPageScroll: function() {
        var xScroll, yScroll;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;	
        }

        return new Array(xScroll,yScroll);
    },

    // adapter from getPageSize() by quirksmode.com
    getPageHeight: function() {
        var windowHeight;
        if (self.innerHeight) {	// all except Explorer
            windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
            windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
            windowHeight = document.body.clientHeight;
        }

        return windowHeight;
    }

});

