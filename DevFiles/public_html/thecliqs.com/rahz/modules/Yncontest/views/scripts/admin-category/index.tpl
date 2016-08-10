<h2><?php echo $this->translate("Contest Plugin") ?></h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

    <form class="global_form">
      <div>      	
        <h3><?php echo $this->translate("Manage Categories") ?></h3>
        <br />
        <p>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'create','pid'=>0), $this->translate('+ Add Root Category'), array(
          'class' => 'smoothbox',
          )) ?>        
          
          <?php if(is_object($this->category)): ?>
       | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'create','pid'=>$this->category->getIdentity()), $this->translate('+ Add Category'), array(
          'class' => 'smoothbox',
	)) ?>
          <?php endif; ?>
        </p>
        <div style = "margin-top: 20px" >
        <?php if(is_object($this->category)): ?>
         <?php  
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'index'), $this->translate('Home'), array()); ?> 
        &raquo; 
	<?php foreach($this->category->getAscendant() as $node): ?>
        		<?php 
        		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'index', 'pid' =>$node->getId()), $node->getTitle(), array()) ?>
        		&raquo;
         <?php endforeach; ?><strong><?php echo $this->category->name ?></strong>
         <?php
		endif; 
         ?>
        </div>
        <br />
          <?php if(count($this->categories)>0):?>
         <table class='yncontest_admin_table admin_table'>
          <thead>

            <tr>
              <th style = "text-align: left;"><?php echo $this->translate("Category Name") ?></th>
              <th style = "text-align: right;"><?php echo $this->translate("Sub-Categories") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
              <tr>
                <td style = "text-align: left;"><?php echo $this->translate($category->getTitle()); ?></td>
                <td style = "text-align: right;"><?php echo (count($category->getDescendantIds()) - 1);?></td>
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'edit', 'id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'delete', 'id' =>$category->getIdentity()), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'create', 'pid' =>$category->getIdentity()), $this->translate('add sub-category'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncontest', 'controller' => 'category', 'action' => 'index', 'pid' =>$category->getIdentity()), $this->translate('view sub-categories'), array(
                    
                  )) ?>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories. Click here to <a href='%s' class='smoothbox'>post</a> a new one",
	  $this->url(array('action'=>'create','pid'=>$this->pid))) ?></span>
      </div>
      <?php endif;?>
      
    <br />  
	<?php echo $this->paginationControl($this->categories, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
    </div>
    </form>
