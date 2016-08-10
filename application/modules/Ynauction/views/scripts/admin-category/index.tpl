<h2><?php echo $this->translate("Auction Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
  <!-- Quan ly category -->
<h3><?php echo $this->translate("Categories") ?></h3>
<table class='admin_table' width="640px" id='category'>
<tbody>
  <?php foreach (Engine_Api::_()->ynauction()->getCategories(0) as $category): ?>
 <tr id="category_items_<?php echo $category->getIdentity() ?>">
 <td>
      <table class='admin_table' width="640px" id='subCategory'>
        <thead>
          <tr id="category_item_<?php echo $category->getIdentity() ?>" class="file file-success">
            <th width="400px">
			<span style="font-weight: bold;" class="file-name"> <?php echo $category->title ?> </span> 
			</th>
             <th width="80px">
            <span style="font-weight: bold;" id="count_<?php echo $category->getIdentity()?>"  class="file-name"><?php $auc =  $category->getParentCountYnauction();  echo $auc; ?></span> <?php if($auc == 1): echo $this->translate('auction'); else:  echo $this->translate(' auctions'); endif; ?>
            </th>
            <th width="160px">
			<a href="javascript:void(0)" class="category_action_rename file-rename"><?php echo $this->translate('Rename') ?></a>
			|
			<a href="javascript:void(0)" class="category_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
			</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (Engine_Api::_()->ynauction()->getCategories($category->category_id) as $item): ?>
            <tr id="subCategory_item_<?php echo $item->getIdentity() ?>" class="file file-success">
             
              <td> <span class="file-name"> <?php echo $item->title ?> </span></td>
              <td> <span class="file-name" id="count_<?php echo $item->getIdentity() ?>"><?php $auc =  $item->getCountYnauction(); echo $auc[0]['count']; ?></span><?php   if($auc[0]['count'] == 1): echo $this->translate(' auction'); else:  echo $this->translate(' auctions'); endif; ?></td>                                     
              <td>
                <a href="javascript:void(0)" class="subCategory_action_rename file-remove"><?php echo $this->translate('Rename') ?></a>
                |
                <a href="javascript:void(0)" class="subCategory_action_remove file-remove"><?php echo $this->translate('Delete') ?></a>
              </td>
            </tr>
        <?php endforeach; ?>
         <tr id="category_item_<?php echo $category->getIdentity() ?>">
         <td></td>
         <td></td>
        <td><a href="javascript:void(0)" class="subCategory_action_add file-add"><?php echo $this->translate('Add New Sub Category') ?></a> </td>
        </tbody>
      </table>         
  </td></tr>
  <?php endforeach; ?>
   <tr>
        <td>
        <a href="javascript:void(0)" class="category_action_add file-add"><?php echo $this->translate('Add New Category') ?></a> </td>
        </tbody>
      </table>
 
<style type="text/css">
 table.admin_table thead tr th {
text-align: left;
}
 </style>
 <script type="text/javascript"> 
  en4.core.runonce.add(function(){
    // RENAME CATEGORY
    $$('a.category_action_rename').addEvent('click', function(){;
      var origTitle = $(this).getParent('tr').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this category?') ?>', origTitle);
      var category_id   = $(this).getParent('tr').id.split(/_/);
      		category_id   = category_id[category_id.length-1];
      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 128);
        $(this).getParent('tr').getElement('.file-name').set('text', newTitle);
        new Request({
          url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'rename-category'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'cat_id': category_id,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

     // CREATE CATEGORY
    $$('a.category_action_add').addEvent('click', function(){
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this category?') ?>', '');
        if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 128);
        var lastRow = $('category').rows.length - 1;
        var iteration = lastRow;
        var row = $('category').insertRow(lastRow);
        var cellLeft = row.insertCell(0);
        cellLeft.innerHTML = "<h3>"+newTitle+ "</h3>";
        new Request({
          url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'add-category'), 'admin_default') ?>',
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
    var category_id  = $(this).getParent('tr').id.split(/_/); 
    category_id  = category_id[category_id.length-1];
    var count = $('count_'+category_id).innerHTML;   
    var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this category?\nThis category have');?> ' + count +'<?php echo $this->translate(" auction(s)"); ?>');
      if(flag == true)
      {
      $('category_item_'+ category_id).destroy();
      $('category_items_'+ category_id).destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'remove-category'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'cat_id': category_id
          }
      }).send();
      }
      return false;
    });
    // RENAME SUBCATEGORY
    $$('a.subCategory_action_rename').addEvent('click', function(){
      var origTitle = $(this).getParent('tr').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length);
      var newTitle  = prompt('<?php echo $this->translate("What is the title of this subcategory?") ?>', origTitle);
      var category_id   = $(this).getParent('tr').id.split(/_/);
      		category_id   = category_id[category_id.length-1];

      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 128);
        $(this).getParent('tr').getElement('.file-name').set('text', newTitle);
        new Request({
          url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'rename-category'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'cat_id': category_id,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

     // CREATE SUBCATEGORY
    $$('a.subCategory_action_add').addEvent('click', function(){
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this subcategory?') ?>', '');
        if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 128);
        var lastRow = $('subCategory').rows.length - 1;
        var iteration = lastRow;
        var row = $('subCategory').insertRow(lastRow);
        var cellLeft = row.insertCell(0);
        cellLeft.innerHTML = "<h3>"+newTitle+ "</h3>";
		 var category_id   = $(this).getParent('tr').getParent('tr').id.split(/_/);
      		category_id   = category_id[category_id.length-1];
        new Request({
          url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'add-category'), 'admin_default') ?>',
          data: {
            'format': 'json',
            'title': newTitle,
			'cat_id': category_id
          },
           onComplete: function(){location.reload(true);}
        }).send();
      }
      return false;
    });
    
    
     // REMOVE/DELETE SUBCATEGORY 
    $$('a.subCategory_action_remove').addEvent('click', function(){
         var subcat_id  = $(this).getParent('tr').id.split(/_/);
         subcat_id  = subcat_id[subcat_id.length-1];
          var count = $('count_'+subcat_id).innerHTML;
         var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this sub category?\nThis sub category have');?> ' + count +'<?php echo $this->translate(" auction(s)"); ?>');                                                                                                        
      if(flag == true)
      {
      $('subCategory_item_'+ subcat_id).destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'ynauction','controller'=>'category','action'=>'remove-category'), 'admin_default') ?>',
        data: {
          'format': 'json',
          'cat_id': subcat_id,
          'subcat_id':subcat_id
          }
      }).send();
      }
      return false;
    });
});

</script>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>