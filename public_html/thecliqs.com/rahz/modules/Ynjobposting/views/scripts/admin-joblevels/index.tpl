<h2><?php echo $this->translate("YouNet Job Posting Plugin") ?></h2>

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
        <h3> <?php echo $this->translate("Manage Job Level") ?> </h3>
       <i class="fa fa-plus"></i><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynjobposting', 'controller' => 'joblevels', 'action' => 'add'), $this->translate('Add New Level'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'padding-left: 10px;'
	  )) ?>
		<br />	<br />
          <?php if(count($this->joblevels)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("level") ?></th>
              <th><?php echo $this->translate("Actions") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->joblevels as $level): ?>
                    <tr>
                      <td>
                          <span class="ynjobposting-category-collapse-nocontrol"></span>
                          <?php echo $level->title?>
                      </td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynjobposting', 'controller' => 'joblevels', 'action' => 'edit', 'id' =>$level->joblevel_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynjobposting', 'controller' => 'joblevels', 'action' => 'delete', 'id' =>$level->joblevel_id), $this->translate("delete"), array(
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
      <span><?php echo $this->translate("There are currently no levels.") ?></span>
      </div>
      <?php endif;?>
        <br/>
    </div>
    </form>
    </div>
  </div>
     