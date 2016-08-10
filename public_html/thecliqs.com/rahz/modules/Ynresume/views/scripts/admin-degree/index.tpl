<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("Manage Education Degree") ?> </h3>
       <i class="fa fa-plus"></i><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'degree', 'action' => 'add'), $this->translate('Add New Degree'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'padding-left: 10px;'
	  )) ?>
		<br />	<br />
          <?php if(count($this->degrees)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Degree") ?></th>
              <th><?php echo $this->translate("Actions") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->degrees as $degree): ?>
                    <tr>
                      <td>
                          <span class="ynresume-category-collapse-nocontrol"></span>
                          <?php echo $degree->name?>
                      </td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'ynresume', 'controller' => 'admin-degree', 'action' => 'edit', 'id' =>$degree->degree_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'ynresume', 'controller' => 'admin-degree', 'action' => 'delete', 'id' =>$degree->degree_id), $this->translate("delete"), array(
                          'class' => 'smoothbox',
                        )) ?>

                      </td>
                    </tr>                  
                   <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no degrees.") ?></span>
      </div>
      <?php endif;?>
        <br/>
    </div>
    </form>
    </div>
  </div>
     