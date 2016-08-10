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
<div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Auction Listing Locations") ?> </h3>
        <div>
         <?php foreach($this->location->getBreadCrumNode() as $node): ?>
        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'index', 'parent_id' =>$node->getIdentity()), $node->shortTitle(), array()) ?>
        		&raquo;
         <?php endforeach; ?>
          <strong><?php
         if(count($this->location->getBreadCrumNode()) > 0):
            echo $this->location;
          else:
            echo  $this->translate("All Locations");
          endif; ?></strong>
        </div>
        <br />
        
          <?php if(count($this->locations)>0):?>

         <table class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Location Name") ?></th>
              <th><?php echo $this->translate("Number of Times Used") ?></th>
              <th><?php echo $this->translate("Sub-Location") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->locations as $location): ?>
              <tr>
                <td><?php echo $location->title?></td>
                <td><?php echo $location->getUsedCount()?></td>
                <td><?php echo $location->countChildren()?></td>
                <td>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'edit-location', 'id' =>$location->location_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'delete-location', 'id' =>$location->location_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  <?php //check # Two-level location
                  if(count($this->location->getBreadCrumNode()) < 1): ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'add-location', 'parent_id' =>$location->location_id), $this->translate('add sub-location'), array(
                  	'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'index', 'parent_id' =>$location->location_id), $this->translate('view sub-location'), array(
                  )) ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There is no location.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynauction', 'controller' => 'locations', 'action' => 'add-location','parent_id'=>$this->location->getIdentity()), $this->translate('Add Location'), array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
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