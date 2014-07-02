
/* $Id: class_classified.js 7 2009-01-11 06:01:49Z john $ */

// Required language vars: 4500121,4500123,4500142

SocialEngineAPI.File = new Class({
  
  Base: {},
  
  
  options: {
    'ajaxURL' : 'file_ajax.php'
  },
  
  
  currentConfirmDeleteID: 0,
  
  // Delete
  deleteFileupload: function(fileID)
  {
    // Display
    this.currentConfirmDeleteID = fileID;
    TB_show(this.Base.Language.Translate(7800121), '#TB_inline?height=100&width=300&inlineId=confirmfiledelete', '', '../images/trans.gif');
  },
  
  deleteFileConfirm: function()
  {
    fileID = this.currentConfirmDeleteID;
    
    $('seClassified_'+fileID).destroy();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'deletefileupload',
        'fileupload_id' : fileID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(4500123));
        }
        
        // Display no classified message
        if( !$$('.seClassified').length )
        {
          $('seClassifiedNullMessage').style.display = 'block';
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmDeleteID = 0;
  }
});