

<?php
/**
 * Integration4us
 *
 * @category   Application_Widget
 * @package    Who Viewed Me Widget
 * @copyright  Copyright 2009-2010 Integration4us
 * @license    http://www.integration4us.com/terms
 * @author     Jomar
 */
 ?>
 
<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9979 2013-03-19 22:07:33Z john $
 * @author     John
 */
?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<div class='browsemembers_results' id='browsemembers_results'>
     <h3>
       <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
     </h3>
	  <?php 
		$arr_all = $this->paginator;
		if(!empty($arr_all))
		 {  
		?>   
		 <ul id="browsemembers_ul">
		 <?php  foreach( $arr_all as $key =>$arr ){ ?>
		  	<?php $viewed = Engine_Api::_()->getItem('user', $arr->user_id) ?>		
				     <li class="zone_eventimage">
					      <div class="events_photo">
					      <div class="user_detail">
					         <?php echo $this->htmlLink($viewed->getHref(), $this->itemPhoto($viewed, 'thumb.icon')) ?> 
					      </div>
					         <div class='browsemembers_results_info user_detail'>
					          <?php echo $this->htmlLink($viewed->getHref(), $viewed->getTitle()) ?>
					          <?php echo $viewed->status; ?>
              				   <div>
                			  	<?php echo $this->timestamp($arr->datetime) ?> 
              			    	</div>
              			    	<div class='newestmembers_date'>
					              <span><?php echo $this->translate("View Count:"); ?></span><span><?php echo $arr->count; ?></span>
					            </div>
            				    </div>
					          <div>
					           <?php 
					        $table = Engine_Api::_()->getDbtable('block', 'user');
					        $select = $table->select()
					          ->where('user_id = ?', $viewed->getIdentity())
					          ->where('blocked_user_id = ?', $viewer->getIdentity())
					          ->limit(1);
					        $row = $table->fetchRow($select);
					        ?>
					        <?php if( $row == NULL ): ?>
					          <?php if( $this->viewer()->getIdentity() ): ?>
					          <div class='browsemembers_results_links'>
					            <?php echo $this->userFriendship($viewed) ?>
					          </div>
					        <?php endif; ?>
					        <?php endif; ?>
					          </div>
					          <div class="clear"></div>
					       </div>
				    </li>
			   
		<?php  } ?> <!-- ending loop -->
		</ul>
	<?php } ?>	
</div>
<div>
    <?php echo $this->paginationControl($this->pagination); ?>
  </div>

  


<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
</script>
				  	 