<h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate("Music Categories") ?></h3>
  <table class='admin_table' width="500px" id="category">
    <thead>
      <tr>
        <th width="420px"><?php echo $this->translate("Title") ?></th>
        <th width="80px"><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->catPaginator as $item): ?>
        <tr id="category_item_<?php echo $item->getIdentity() ?>" class="file file-success">
          <td> <span style="font-weight: bold" class="file-name"> <?php echo $item->title ?> </span></td>
          <td>
            <a href="javascript:void(0)" class="category_action_rename file-rename"><?php echo $this->translate('Rename') ?></a>
            |
            <a href="javascript:void(0)" class="category_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
            |
            <a href="javascript:void(0)" onclick="return addSubCategory(<?php echo $item->getIdentity() ?>)" class="category_action_add_sub file-addsub"><?php echo $this->translate('Add sub category') ?></a>
          </td>
        </tr>
        <?php $sub_categories = Engine_Api::_()->mp3music()->getCatPaginator(array('parent_cat' => $item->getIdentity()));
			foreach($sub_categories as $sub_categorie):
        ?>
        <tr id="category_item_<?php echo $sub_categorie->getIdentity() ?>" class="file file-success">
          <td style = "padding-left: 30px"> <span class="file-name"> <?php echo $sub_categorie->title ?> </span></td>
          <td style="text-align: right">
            <a href="javascript:void(0)" class="category_action_rename file-rename"><?php echo $this->translate('Rename') ?></a>
            |
            <a href="javascript:void(0)" class="category_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
          </td>
        </tr>
        <?php endforeach;?>
		<tr id = "subcategory_item_<?php echo $item->getIdentity() ?>"> <td></td>
         <td></td></tr>
    <?php endforeach; ?>
     <tr >
    <td colspan = "2"><a href="javascript:void(0)" class="category_action_add file-add"><?php echo $this->translate('Add new category') ?></a> </td></tr>
    </tbody>
  </table>
  <br />
  <div>
    <?php echo $this->paginationControl($this->catPaginator); ?>
  </div>
  <!-- Manage aritsts -->
  <?php $allow_artist = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.artist', 1);
  if(!$allow_artist):?>
	<h3><?php echo $this->translate("Music Artist") ?></h3>
      <table class='admin_table' width="600px">
        <thead>
          <tr>
            <th width="420px"><?php echo $this->translate("Name") ?></th>
            <th width="120px"><?php echo $this->translate("Photo") ?></th>
            <th width="80px"><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (Mp3music_Api_Core::getArtistRows() as $item): ?>
            <tr id="artist_item_<?php echo $item->getIdentity() ?>" class="file file-success">
             
              <td> <span class="file-name"> <?php echo $item->title ?> </span></td>
              <td> <?php echo $this->itemPhoto($item, 'thumb.normal')?> </td>
              <td>
                <a href="<?php echo 
                    $this->url(array('artist_id'=>$item->artist_id), 'mp3music_edit_artist') ?>" class = 'smoothbox mp3music_player_tracks_add'><?php echo $this->translate('Edit') ?> </a>
                |
                <a href="javascript:void(0)" class="artist_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
              </td>
            </tr>
        <?php endforeach; ?>
         <td></td>
         <td></td>

        <td><a href="<?php echo 
        $this->url(array(), 'mp3music_create_artist') ?>" class = 'smoothbox mp3music_player_tracks_add'><?php echo $this->translate('Create new artist') ?> </a> </td>
        </tbody>
      </table>         
 <?php endif; ?>
  <!-- Quan ly singer -->
<h3><?php echo $this->translate("Music Singers") ?></h3>
<table class='admin_table' width="600px" id='singerType'>
<tbody>
  <?php foreach (Mp3music_Model_SingerType::getSingerTypes() as $singerType): ?>
   <tr id="singerType_item_<?php echo $singerType->getIdentity() ?>" class="file file-success">
  <td> <span style="font-weight: bold;" class="file-name"> <?php echo $singerType->title ?> </span> 

    <a href="javascript:void(0)" class="singerType_action_rename file-rename"><?php echo $this->translate('Rename') ?></a>
    |
    <a href="javascript:void(0)" class="singerType_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
  </td>
 </tr>
 <tr id="singerType_items_<?php echo $singerType->getIdentity() ?>">

 <td>
      <table class='admin_table' width="600px">
        <thead>
          <tr>
            <th width="420px"><?php echo $this->translate("Name") ?></th>
            <th width="120px"><?php echo $this->translate("Photo") ?></th>
            <th width="80px"><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($singerType->getSingers() as $item): ?>
            <tr id="singer_item_<?php echo $item->getIdentity() ?>" class="file file-success">
             
              <td> <span class="file-name"> <?php echo $item->title ?> </span></td>
              <td> <?php echo $this->itemPhoto($item, 'thumb.normal')?> </td>
              <td>
                <a href="<?php echo 
        $this->url(array('singertype_id'=>$item->singer_type,'singer_id'=>$item->singer_id), 'mp3music_edit_singer') ?>" class = 'smoothbox music_player_tracks_add'><?php echo $this->translate('Edit') ?> </a>
                |
                <a href="javascript:void(0)" class="singer_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
              </td>
            </tr>
        <?php endforeach; ?>
         <tr id="singerType_item_<?php echo $singerType->getIdentity() ?>">
         <td></td>
         <td></td>

        <td><a href="<?php echo 
        $this->url(array('singertype_id'=>$singerType->getIdentity()), 'mp3music_create_singer') ?>" class = 'smoothbox music_player_tracks_add'><?php echo $this->translate('Create new singer') ?> </a> </td>
        </tbody>
      </table>         
  </td></tr>
  <?php endforeach; ?>
   <tr>
        <td>
        <a href="javascript:void(0)" class="singerType_action_add file-add"><?php echo $this->translate('Add New Singer Category') ?></a> </td>
        </tbody>
      </table>
 <?php if ($this->success): 
 endif; ?> 
<script type="text/javascript"> 
    // CREATE SUB CATEGORY
    var addSubCategory = function(parent_id)
    {
    	var newTitle  = prompt('<?php echo $this->translate('What is the title of this sub category?') ?>', '');
        newTitle = newTitle.replace(/^\s+|\s+$/g,'');
        if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);   
        var lastRow = $('subcategory_item_'+parent_id);
        lastRow.innerHTML = "<td style = 'padding-left: 30px'>" + newTitle + "</td><td></td>";  
        new Request({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'add-cat'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'title': newTitle,
            'parent_cat' : parent_id
          },
          onComplete: function(){location.reload(true);}
        }).send();
      }
      return false;
    }
  en4.core.runonce.add(function(){
    // RENAME CATEGORY
    $$('a.category_action_rename').addEvent('click', function(){ 
      var origTitle = $(this).getParent('tr').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this category?') ?>', origTitle);
      newTitle = newTitle.replace(/^\s+|\s+$/g,'');
      var category_id   = $(this).getParent('tr').id.split(/_/);
          category_id   = category_id[category_id.length-1];
      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        $(this).getParent('tr').getElement('.file-name').set('text', newTitle);  
        new Request({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'rename-cat'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'cat_id': parseInt(category_id),
            'title': newTitle
          }
        }).send();
      }
      return false;
    });
    // CREATE CATEGORY
    $$('a.category_action_add').addEvent('click', function(){
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this category?') ?>', '');
        newTitle = newTitle.replace(/^\s+|\s+$/g,'');
        if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        var lastRow = $('category').rows.length - 1;
        var iteration = lastRow;
        var row = $('category').insertRow(lastRow);
        var cellLeft = row.insertCell(0);
        cellLeft.innerHTML = newTitle;  
        new Request({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'add-cat'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'title': newTitle
          },
          onComplete: function(){location.reload(true);}
        }).send();
      }
      return false;
    });
    // REMOVE/DELETE CATEGORY
    $$('a.category_action_remove').addEvent('click', function(){
     var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this category?') ?>');
      if(flag == true)
      {
      var category_id  = $(this).getParent('tr').id.split(/_/);
          category_id  = category_id[ category_id.length-1 ];
     
      $(this).getParent('tr').destroy(); 
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'remove-cat'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'cat_id': category_id
          }
      }).send();
      }
      return false;
    });
    
    // RENAME SINGER TYPE
    $$('a.singerType_action_rename').addEvent('click', function(){
      var origTitle = $(this).getParent('tr').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this singer type?') ?>', origTitle);
      newTitle = newTitle.replace(/^\s+|\s+$/g,'');
      var singertype_id   = $(this).getParent('tr').id.split(/_/);
          singertype_id   = singertype_id[singertype_id.length-1];

      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        $(this).getParent('tr').getElement('.file-name').set('text', newTitle);
        new Request({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'rename-singer-type'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'singertype_id': singertype_id,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

     // CREATE SINGER TYPE
    $$('a.singerType_action_add').addEvent('click', function(){
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this singer type?') ?>', '');
        newTitle = newTitle.replace(/^\s+|\s+$/g,'');
        if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        var lastRow = $('singerType').rows.length - 1;
        var iteration = lastRow;
        var row = $('singerType').insertRow(lastRow);
        var cellLeft = row.insertCell(0);
        cellLeft.innerHTML = "<h3>"+newTitle+ "</h3>";
        new Request({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'add-singer-type'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'title': newTitle
          },
           onComplete: function(){location.reload(true);}
        }).send();
      }
      return false;
    });


    // REMOVE/DELETE SINGER TYPE
    $$('a.singerType_action_remove').addEvent('click', function(){
         var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this singer category?') ?>');
      if(flag == true)
      {
      var singertype_id  = $(this).getParent('tr').id.split(/_/);
          singertype_id  = singertype_id[ singertype_id.length-1 ];

      
      $('singerType_item_'+ singertype_id).destroy();
      $('singerType_items_'+ singertype_id).destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'remove-singer-type'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'singertype_id': singertype_id
          }
      }).send();
      }
      return false;
    });
    
     // REMOVE/DELETE SINGER 
    $$('a.singer_action_remove').addEvent('click', function(){
         var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this singer?') ?>');
      if(flag == true)
      {
      var singer_id  = $(this).getParent('tr').id.split(/_/);
          singer_id  = singer_id[ singer_id.length-1 ];

      
      $('singer_item_'+ singer_id).destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'remove-singer'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'singer_id': singer_id
          }
      }).send();
      }
      return false;
    });
     // REMOVE/DELETE ARTIST 
    $$('a.artist_action_remove').addEvent('click', function(){
         var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this artist?') ?>');
      if(flag == true)
      {
      var artist_id  = $(this).getParent('tr').id.split(/_/);
          artist_id  = artist_id[ artist_id.length-1 ];

      
      $('artist_item_'+ artist_id).destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'category','action'=>'remove-artist'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'artist_id': artist_id
          }
      }).send();
      }
      return false;
    });
});
</script>
<style type="text/css">
 table.admin_table thead tr th {
text-align: left;
}
 </style>