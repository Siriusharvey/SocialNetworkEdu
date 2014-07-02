
/* $Id: class_music.js 11 2009-01-11 06:03:58Z john $ */

SocialEngineAPI.Music = new Class({
  
  Base : {},
  
  
  options: {
    'ajaxURL' : 'music_ajax.php'
  },
  
  
  sortablesEffect: null,
  
  
  currentConfirmDeleteID: 0,
  
  
  
  initialize: function()
  {
    var bind = this;
    window.addEvent('domready', function()
    {
      if( !$$('.seMusicRow').length ) return;
      
      bind.sortablesEffect = new Sortables($$('.userMusicList'),
      {
        constrain: true,
        clone: false,
        revert: true,
        handle: '.seMusicMoveHandle',
        opacity: 0.6
      });
      
      bind.sortablesEffect.addEvent('complete', function()
      {
        bind.sendFullMusicOrder();
      });
      
    });
  },
  
  
  // Move Up
  moveUpMusic: function(musicID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'moveupsong',
        'music_id' : musicID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('There was an error processing your move request.');
        }
      }
    });
    
    request.send();
    
    // Switch the element's order.
    var musicContainer = $('seMusic_' + musicID);
    var previousContainer = musicContainer.getPrevious();
    musicContainer.inject(previousContainer, 'before');
  },
  
  
  sendFullMusicOrder: function()
  {
    var isFirst = true;
    var order = '';
    $$('.seMusicRow').each(function(musicRowElement)
    {
      var musicID = musicRowElement.getElement('.seMusicID').getProperty('html');
      if( !isFirst ) order += ',';
      order += musicID;
      isFirst = false;
    });
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'reordermusic',
        'music_order' : order
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('There was an error processing your move request.');
        }
      }
    });
    
    request.send();
  },
  
  
  
  // Delete
  deleteMusic: function(musicID)
  {
    // Display
    this.currentConfirmDeleteID = musicID;
    TB_show(this.Base.Language.Translate(4000038), '#TB_inline?height=100&width=300&inlineId=confirmmusicdelete', '', '../images/trans.gif');
  },
  
  deleteMusicConfirm: function(musicID)
  {
    // Destroy
    if( this.sortablesEffect )
      this.sortablesEffect.removeItems($('seMusic_' + musicID));
    
    $('seMusic_' + musicID).destroy();
    
    if( !$$('.seMusicRow').length )
    {
      $('seMusicListContainer').destroy();
      $('musicnullmessage').style.display = 'block';
    }
    
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'deletesong',
        'music_id' : musicID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('There was an error processing your delete request.');
        }
      }
    });
    
    request.send();
  },
  
  
  
  // Editing
  editMusicTitle: function(musicID)
  {
    // Get title
    var musicTitleContainer = $('seMusic_' + musicID);
    var musicTitle = musicTitleContainer.getElement('.seMusicTitle').getProperty('html');
    
    // Set title
    var musicTitleInput = musicTitleContainer.getElement('.seMusicTitleEditor').getElement('input');
    musicTitleInput.setProperty('value', musicTitle);
    
    // Display
    this.showMusicTitleEditor(musicID);
    
    // Focus
    musicTitleInput.focus();
    musicTitleInput.select();
  },
  
  saveMusicTitle: function(musicID)
  {
    // Get title
    var musicTitleContainer = $('seMusic_' + musicID);
    var musicTitle = musicTitleContainer.getElement('.seMusicTitleEditor').getElement('input').getProperty('value');
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'editsongtitle',
        'music_id' : musicID,
        'music_title' : musicTitle
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('There was an error processing your edit request.');
        }
      }
    });
    
    request.send();
    
    // Set title
    musicTitleContainer.getElement('.seMusicTitle').setProperty('html', musicTitle);
    
    // Display
    this.hideMusicTitleEditor(musicID);
  },
  
  cancelMusicTitle: function(musicID)
  {
    // Display
    this.hideMusicTitleEditor(musicID);
  },
  
  showMusicTitleEditor: function(musicID)
  {
    var musicTitleContainer = $('seMusic_' + musicID);
    
    musicTitleContainer.getElement('.seMusicTitle').style.display = 'none';
    musicTitleContainer.getElement('.seMusicTitleEdit').style.display = 'none';
    
    musicTitleContainer.getElement('.seMusicTitleEditor').style.display = '';
    musicTitleContainer.getElement('.seMusicTitleSave').style.display = '';
    musicTitleContainer.getElement('.seMusicTitleCancel').style.display = '';
  },
  
  hideMusicTitleEditor: function(musicID)
  {
    var musicTitleContainer = $('seMusic_' + musicID);
    
    musicTitleContainer.getElement('.seMusicTitle').style.display = '';
    musicTitleContainer.getElement('.seMusicTitleEdit').style.display = '';
    
    musicTitleContainer.getElement('.seMusicTitleEditor').style.display = 'none';
    musicTitleContainer.getElement('.seMusicTitleSave').style.display = 'none';
    musicTitleContainer.getElement('.seMusicTitleCancel').style.display = 'none';
  }

});