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

<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>
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
      	
        <h3><?php echo $this->translate("Listing Industries") ?></h3>
        
        <p><?php echo $this->translate("YNRESUME_ADMIN_INDUSTRIES_DESCRIPTION") ?></p>
        <br />  
        <div>
         <?php foreach($this->industry->getBreadCrumNode() as $node): ?>
        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'index', 'parent_id' =>$node->industry_id), $this->translate($node->shortTitle()), array()) ?>
        		&raquo;
         <?php endforeach; ?>
         <strong><?php
         if(count($this->industry->getBreadCrumNode()) > 0):
            echo $this->industry;
          else:
            echo  $this->translate("All Industries");
          endif; ?></strong>
        </div>
        <br />
          <?php if(count($this->industries)>0):?>
         <table style="position: relative;" class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Industry Name") ?></th>
              <th><?php echo $this->translate("Sub-Industry") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody id='demo-list'>
            <?php foreach ($this->industries as $industry): ?>
              <tr id='industry_item_<?php echo $industry->getIdentity() ?>'>
                <td><?php echo $industry->title?></td>
                <td><?php echo $industry->countChildren() ?></td>
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'edit-industry', 'id' =>$industry->industry_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'delete-industry', 'id' =>$industry->industry_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  <?php if($industry->level <= 2) :?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'add-industry', 'parent_id' =>$industry->industry_id), $this->translate('add sub-industry'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'index', 'parent_id' =>$industry->industry_id), $this->translate('view sub-industry'), array(
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
      <span><?php echo $this->translate("There are currently no industries.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries', 'action' => 'add-industry','parent_id'=>$this->industry->getIdentity()), $this->translate('Add Industry'), array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
    	 <?php if(Engine_Api::_() -> hasModuleBootstrap('ynjobposting')) :?>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries-mapping', 'action' => 'index'), $this->translate('Industry Mapping'), array(
          'class' => 'buttonlink',
		  'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);'
		  )); ?>
        <?php endif;?>
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
          url: '<?php echo $this->url(array('controller'=>'industries','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
            'parent_id' : <?php echo $this->industry->getIdentity()?>,
          }
        }).send();
      }
    });
});
</script>