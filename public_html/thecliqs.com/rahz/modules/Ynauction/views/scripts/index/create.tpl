<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
<?php
function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }      
?>

       <?php
       $viewer = Engine_Api::_()->user()->getViewer(); 
         $account = Ynauction_Api_Cart::getFinanceAccount($viewer->getIdentity());
         if ($account['account_username'] == ''):  ?>
          <div class="tip">
            <span>
          <?php echo $this->translate('You do not have any finance account yet. '); ?><a href="<?php echo selfURL(); ?>auction/account/create"><?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate('  to add your account.'); ?>
          </span>
          </div>
       <?php elseif(!Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity())): ?>
         <div class="tip">
            <span>
          <?php echo $this->translate('You do not have become auction seller yet. '); ?><a href="<?php echo selfURL(); ?>auction/become"><?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate(' to become auction seller.'); ?>
          </span>
          </div>
       <?php else: ?>
      <?php echo $this->form->render($this); endif;?>
      <script type="text/javascript">
      $('cat1_id-wrapper').hide();
      function subCategories() {
        if ($('cat_id').selectedIndex > 0) 
        { 
      	var cat_id = $('cat_id').options[$('cat_id').selectedIndex].value ;
          var makeRequest = new Request(
      			{
      				url: "ynauction/index/subcategories/cat_id/"+cat_id,
      				onComplete: function (respone){
      					respone  = respone.trim();
                      if(respone != "")
                      {  
                            $('cat1_id-wrapper').show();
      				      document.getElementById('cat1_id-element').innerHTML = '<select id= "cat1_id" name = "cat1_id"><option value="0" label="" selected= "selected"></option>' + respone + '</select>';
      				}
      				else
      					$('cat1_id-wrapper').hide();
      				}
      			}
      	)
      	makeRequest.send();
        } else {
          $('cat1_id-wrapper').hide();
        }
      }
      function setCheck()
      {
          if($('check').checked == true)
          {
               $('submit-wrapper').show();
          }
          else
             $('submit-wrapper').hide(); 
      }
      function removeSubmit()
      {
         $('submit-wrapper').hide(); 
      }
      var cal_start_time_onHideStart = function(){
          // check end date and make it the same date if it's too
          cal_end_time.calendars[0].start = new Date( $('start_time-date').value );
          // redraw calendar
          cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
          cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);
      }
      var cal_end_time_onHideStart = function(){
          // check start date and make it the same date if it's too
          cal_start_time.calendars[0].end = new Date( $('end_time-date').value );
          // redraw calendar
          cal_start_time.navigate(cal_start_time.calendars[0], 'm', 1);
          cal_start_time.navigate(cal_start_time.calendars[0], 'm', -1);
      }
      </script>
