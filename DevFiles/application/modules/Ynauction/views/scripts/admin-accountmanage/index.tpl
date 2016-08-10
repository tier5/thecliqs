 <h2><?php echo $this->translate(" Auction Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<H3>
    <?php echo $this->translate("Manage Finance Accounts") ?>      
</H3>
<?php if (count($this->accounts)>0):?>
 <table class="admin_table" width="100%">
    <thead>
        <tr>
        <th style="text-align:center" ><?php echo $this->translate('User ID');?></th>
        <th style="text-align:center" ><?php echo $this->translate('User Account');?></th>
        <th style="text-align:center" ><?php echo $this->translate('Payment Account');?></th>
    </tr>  
    </thead>
    <tbody>
        <?php foreach ($this->accounts as $acc):?>
         <tr>
            <td class="stat_number" style="text-align:center" align="center" ><?php echo $acc->user_id; ?> </td>
            <td class="stat_number" style="text-align:center" align="center" >
            <?php echo Engine_Api::_()->getItem('user',$acc->user_id);?>  
                        </td>
           
            <td class="stat_number" style="text-align:center" align="center" > <?php echo $acc->account_username; ?> </td>
        </tr>
       
       <?php endforeach; ?>
        
    </tbody>
    </table>   
<?php echo  $this->paginationControl($this->accounts); ?>
<?php else: ?>
   <?php echo $this->translate("There is no finance accounts.") ?>                 
<?php endif; ?>
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