<h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>
<script type="text/javascript">
	function activePackage(package_id, obj)
	{
		var element = document.getElementById('yncredit_package_'+ package_id);
        var status = 0;
        if(obj.checked == true) 
        	status = 1;
        else 
        	status = 0;
        var content = element.innerHTML;
        element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Yncredit/externals/images/loading.gif'></img>";
        new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'yncredit', 'controller' => 'manage-package', 'action' => 'active'), 'admin_default') ?>',
          'data' : {
            'format' : 'json',
            'package_id' : package_id,
            'status' : status
          },
          'onRequest' : function(){
          },
          'onSuccess' : function(responseJSON, responseText)
          {
            element.innerHTML = content;
            obj = document.getElementById('active_package_'+ package_id);
            if( status == 1) 
            	obj.checked = true;
            else 
            	obj.checked = false;
          }
        }).send();
            
    }
</script>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("YNCREDIT_VIEWS_SCRIPTS_ADMINPACKAGES_INDEX_DESCRIPTION") ?>
</p>
<br />

<div style="padding: 5px">  
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yncredit', 'controller' => 'manage-package', 'action' => 'create'), $this->translate('Add New Package'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Yncredit/externals/images/add_package.png);')) ?>
</div>

<div style="clear: both;"></div>
<br />
<?php if( count($this->packages) ): ?>
  <div class="admin_table_form">
    <form>
      <table class='admin_table'>
        <thead>
          <tr>
            <th><?php echo $this->translate("Package") ?></th>
            <th><?php echo $this->translate("Active") ?></th>
            <th class='admin_table_options'><?php echo $this->translate("Option") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach( $this->packages as $package ): ?>
            <?php $bonus = ((100*$package->credit)/($package->price*$this->credits_for_one_unit) - 100); 
            $bonus = ($bonus) ? '<span class="credit_payment_bonus">'.round($bonus, 2) . $this->translate('% bonus').'</span>' : ''?>
            <?php $caption = '<span style="font-weight:bold">'.$this->translate(array("%s Credit for ", "%s Credits for ", $package->credit), $this->locale()->toNumber((int)$package->credit) ).'</span>' . $this->locale()->toCurrency($package->price, $this->currency) . ' ' . $bonus?>
            <tr>
              <td><?php echo $caption; ?></td>
              <td>
		          <div id='yncredit_package_<?php echo $package->package_id; ?>' style ="text-align: center;" >
		             <input type="checkbox" id='active_package_<?php echo $package->package_id; ?>' onclick="activePackage(<?php echo $package->package_id; ?>,this)" <?php if($package -> active): ?> checked <?php endif;?> />
		          </div>
	          </td>
              <td class='admin_table_options'>
                <?php echo $this->htmlLink(
                  $this->url(
                    array(
                      'module' => 'yncredit',
                      'controller' => 'manage-package',
                      'action' => 'delete',
                      'package_id' => $package->package_id
                    ), 'admin_default', true
                  ), $this->translate('delete'), array('class' => 'smoothbox')) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>
<?php else:?>
	<div class="tip">
	    <span>
	        <?php echo $this->translate("There are no packages.") ?>
	    </span>
	</div>
<?php endif; ?>