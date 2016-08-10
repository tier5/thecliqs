<div style="height:500px; width:450px;">
  <h3>
    <?php 
        echo $this->translate('Bid history');
    ?>
  </h3>

  <table cellpadding="0" cellspacing="0" width="100%" style="padding-bottom: 30px;">
        <tr>
             <td  style="border: 1px solid black;text-align:center; font-weight: bold" align="center"><?php echo $this->translate('Bidder');?>  </td>
             <td  style="border: 1px solid black;text-align:center; font-weight: bold" align="center"><?php echo $this->translate('Date');?>  </td>   
        </tr>
     <?php foreach($this->history as $track):?>
        <tr style="border:1px solid">
            <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php
             $bider = Engine_Api::_()->getItem('user', $track->ynauction_user_id);  
             echo $bider->getTitle(); ?> </td>
            <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $this->locale()->toDateTime($track->bid_time)?> </td>
        </tr>
   
   <?php endforeach; ?>
    </table>  
</div>
 