<div class="layout_middle">
<?php echo $this->content()->renderWidget('yncontest.main-menu') ?>
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form yncontest_browse_filters">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
      <div style = "margin-bottom:15px">
        <?php echo $this->htmlLink(array(
              'route' => 'yncontest_photo',             
              'action' => 'upload',
              'contestId' => $this->contest_id,
            ), $this->translate('Add More Photos'), array(
              'class' => 'buttonlink icon_yncontest_photo'
          )) ?>
          </div>
      <div class="form-elements">
     <?php if(Count($this->paginator) > 0): ?>
      <?php echo $this->form->contest_id; ?>
      <ul class='yncontest_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <div class="yncontest_editphotos_photo">
              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
            </div>
            <div class="yncontest_editphotos_info">
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class="yncontest_editphotos_cover">
                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
              </div>
              <div class="yncontest_editphotos_label">
                <label><?php echo $this->translate('Main Photo');?></label>
              </div>
            </div>
            <br/>
          </li>
        <?php endforeach; ?>
      </ul>

       <?php echo $this->form->submit->render(); ?>
       <?php echo $this->form->cancel->render(); ?>
       <?php endif;?>
               </div>
    </div>
  </div>
</form>
       <?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
</div>