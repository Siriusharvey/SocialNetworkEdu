
/* $Id: class_classified.js 7 2009-01-11 06:01:49Z john $ */

// Required language vars: 5555121,5555123,5555142

SocialEngineAPI.gstore = new Class({
  
  Base: {},
  
  
  options: {
    'ajaxURL' : 'gstore_ajax.php'
  },
  
  
  currentConfirmDeleteID: 0,
  
  imagePreviewAttached: false,
  
  

  // Delete
  deletegstore: function(gstoreID)
  {
    // Display
    this.currentConfirmDeleteID = gstoreID;
    TB_show(this.Base.Language.Translate(5555121), '#TB_inline?height=100&width=300&inlineId=confirmgstoredelete', '', '../images/trans.gif');
  },
  
  deletegstoreConfirm: function()
  {
    gstoreID = this.currentConfirmDeleteID;
    
    $('segstore_'+gstoreID).destroy();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'deletegstore',
        'gstore_id' : gstoreID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(5555123));
        }
        
        // Display no gstore message
        if( !$$('.segstore').length )
        {
          $('segstoreNullMessage').style.display = 'block';
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmDeleteID = 0;
  },
  
  

  // Preview
  imagePreviewgstore: function(imageSource, imageWidth, imageHeight)
  {
    var imageElement = $('segstoreImageFull');
    var bind = this;
    
    // Try event (or timeout?)
    imageElement.removeEvents('load');
    imageElement.addEvent('load', function()
    {
      bind.imagePreviewgstoreComplete();
    });
    
    // Set src attrib
    if( imageElement.src!=imageSource )
      imageElement.src = imageSource;
  },
  
  

  // Preview
  imagePreviewgstoreComplete: function()
  {
    var imageElement = $('segstoreImageFull');
    
    var imageWidth  = imageElement.getSize().x;
    var imageHeight = imageElement.getSize().y;
    
    var popupWidth  = imageWidth  + 20;
    var popupHeight = imageHeight + 20;
    
    var windowWidth  = window.getSize().x - 50;
    var windowHeight = window.getSize().y - 75;
    
    if( popupWidth>windowWidth )
      popupWidth  = windowWidth;
      
    if( popupHeight>windowHeight )
      popupHeight = windowHeight;
    
    /*
    var popupWidth  = 400;
    var popupHeight = 300;
    
    imageWidth  = parseInt(imageWidth);
    imageHeight = parseInt(imageHeight);
    
    // User default size
    if( !imageWidth || !imageHeight )
    {
      imageWidth = 400;
      imageHeight = 300;
    }
    
    // Calculate size
    else
    {
      var reductionRatioX = 1, reductionRatioY = 1;
      var windowWidth  = window.getSize().x - 50;
      var windowHeight = window.getSize().y - 75;
      
      if( imageWidth>windowWidth )
        reductionRatioX = (windowWidth / imageWidth);
      if( imageHeight>windowHeight )
        reductionRatioY = (windowHeight / imageHeight);
      
      if( reductionRatioX!=1 && reductionRatioX<reductionRatioY )
        reductionRatioY = reductionRatioX;
      else if( reductionRatioY!=1 && reductionRatioY<reductionRatioX )
        reductionRatioX = reductionRatioY;
      
      imageWidth  = Math.round(imageWidth  * reductionRatioX);
      imageHeight = Math.round(imageHeight * reductionRatioY);
      
      $('segstoreImageFull').style.width  = imageWidth.toString() + 'px';
      $('segstoreImageFull').style.height = imageHeight.toString() + 'px';
      
      popupWidth  = imageWidth  + 10;
      popupHeight = imageHeight + 20;
    }
    */
    
    // Display
    TB_show(this.Base.Language.Translate(5555142), '#TB_inline?height='+popupHeight+'&width='+popupWidth+'&inlineId=segstoreImagePreview', '', '../images/trans.gif');
  }
  
  
});
