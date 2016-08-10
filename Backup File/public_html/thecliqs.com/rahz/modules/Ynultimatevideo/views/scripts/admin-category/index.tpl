<?php $maxCategoryLevel = 2; ?>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>   

<h2><?php echo $this->translate("Ultimate Video Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
      	
        <h3><?php echo $this->translate("Video Categories") ?></h3>
        <p><?php echo $this->translate("YNULTIMATEVIDEO_ADMIN_CATEGORY_DESCRIPTION") ?></p>
        <br />  
        <div>
         <?php foreach($this->category->getBreadCrumNode() as $node): ?>
        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'index', 'parent_id' =>$node->category_id), $this->translate($node->shortTitle()), array()) ?>
        		&raquo;
         <?php endforeach; ?>
         <strong><?php
         if(count($this->category->getBreadCrumNode()) > 0):
            echo $this->category;
          else:
            echo  $this->translate("All Categories");
          endif; ?></strong>
        </div>
        <br />
          <?php if(count($this->categories)>0):?>
         <table style="position: relative;" class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Sub-Category") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody id='demo-list'>
            <?php foreach ($this->categories as $category): ?>
              <tr id='category_item_<?php echo $category->getIdentity() ?>'>
                <td><?php echo $category->title?></td>
                <td><?php echo $category->countChildren() ?></td>
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                    <a href='<?php echo $this->baseUrl();?>/admin/ynultimatevideo/category-fields?option_id=<?php echo $category->option_id ?>&id=<?php echo $category->category_id ?>'><?php echo $this->translate('manage custom fields') ?></a>
                    <?php if($category->level <= $maxCategoryLevel) :?>
                  |
	                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'add-category', 'parent_id' =>$category->category_id), $this->translate('add sub-category'), array(
	                    'class' => 'smoothbox',
	                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'index', 'parent_id' =>$category->category_id), $this->translate('view sub-category'), array(
                  )) ?>
				  <?php endif;?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'category', 'action' => 'add-category','parent_id'=>$this->category->getIdentity()), $this->translate('Add Category'), array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     

<script type="text/javascript">
en4.core.runonce.add(function(){
    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'category','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
            'parent_id' : <?php echo $this->category->getIdentity()?>,
          }
        }).send();
      }
    });
});
</script>