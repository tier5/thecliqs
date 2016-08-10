<script type="text/javascript">
function selectAll(elm)
{
	  var i;
	  var inputs = $$("input[name=member_ids]");
	  for (i = 0; i < inputs.length; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = elm.checked;
	    }
	  }
}

function setMassAction()
{
	member_id = $$("input[name=member_ids]:checked").get('value');
	action = $$("select[name=mass_action]").get('value')[0];
	if (member_id.length == 0 || action == '')
	{
		return false;
	}
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format'  : 'html',
	        'subject' : en4.core.subject.guid,
	        'waiting' : true,
	        'member_id' : member_id,
			'mass_action' : action
	      }
    }), {
      	'element' :	 $('ynbusinesspages_profile_members_anchor').getParent()
    });
}
</script>
<a id="ynbusinesspages_profile_members_anchor"></a>
<script type="text/javascript">
  var businessMemberSearch = <?php echo Zend_Json::encode($this->search) ?>;
  var businessMemberPage = Number('<?php echo $this->members->getCurrentPageNumber() ?>');
  var waiting = '<?php echo $this->waiting ?>';
  en4.core.runonce.add(function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    $('ynbusinesspages_members_search_input').addEvent('keypress', function(e) {
      if( e.key != 'enter' ) return;

      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : this.value
        }
      }), {
        'element' : $('ynbusinesspages_profile_members_anchor').getParent()
      });
    });
  });

  var paginateBusinessMembers = function(page) {
    //var url = '<?php echo $this->url(array('module' => 'ynbusinesspages', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'search' : businessMemberSearch,
        'page' : page,
        'waiting' : waiting
      }
    }), {
      'element' : $('ynbusinesspages_profile_members_anchor').getParent()
    });
  }
</script>

<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
  var showWaitingMembers = function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format'  : 'html',
        'subject' : en4.core.subject.guid,
        'waiting' : true
      }
    }), {
      'element' : $('ynbusinesspages_profile_members_anchor').getParent()
    });
  }
  
  var showRegisteredMembers = function() {
    //var url = '<?php echo $this->url(array('module' => 'ynbusinesspages', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format'  : 'html',
        'subject' : en4.core.subject.guid,
      }
    }), {
      'element' : $('ynbusinesspages_profile_members_anchor').getParent()
    });
  }
</script>
<?php endif; ?>

<?php if( !$this->waiting ): ?>
  <div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages_members_search">
      <input id="ynbusinesspages_members_search_input" type="text" value="<?php echo $this->translate('Search members');?>" onfocus="$(this).store('over', this.value);this.value = '';" onblur="this.value = $(this).retrieve('over');">
    </div>
    <div class="ynbusinesspages_members_total ynbusinesspages-profile-header-content">
      <?php if( '' == $this->search ): ?>
        <span class="ynbusinesspages-numeric"><?php echo $this->members->getTotalItemCount(); ?></span>
        <?php echo $this->translate(array('member.', 'members.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
      <?php else: ?>
        <?php echo $this->translate(array('%1$s member that matched the query "%2$s".', '%1$s members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->escape($this->search)) ?>
      <?php endif; ?>
    </div>
    <?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
      <div class="ynbusinesspages_members_total">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Invited Users and User Waiting For Approval'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages_members_total">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View all approved members'), array('onclick' => 'showRegisteredMembers(); return false;'))  ?>
    </div>
  </div>
<?php endif; ?>

<?php if( $this->members->getTotalItemCount() > 0 ): ?>
  <ul class='ynbusinesspages_members_list'>
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->business->membership()->getMemberInfo($member);
      }
      ?>
      <li id="ynbusinesspages_member_<?php echo $member->getIdentity() ?>">
        <div class="ynbusinesspages_members_avatar">
          <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($member); ?>

          <?php if ($this->waiting):?>
            <div class="ynbusinesspages_members_input"><input id='member_<?php echo $member->getIdentity(); ?>' type='checkbox' class='checkbox' name='member_ids' value="<?php echo $member->getIdentity(); ?>" /></div>
          <?php endif;?>

          <?php if (!$this->waiting && $this->isAdmin):?>
            <div class="ynbusinesspages_members_input">
              <?php if (!$this -> business -> isOwner($member)):?>
                <a class="smoothbox" title="<?php echo $this->translate('Remove This Member');?>" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'remove', 'business_id' => $this->business->getIdentity(), 'user_id' => $member->getIdentity()), 'ynbusinesspages_extended')?>">
                  <i class="fa fa-remove"></i>
                </a>
              <?php endif;?>
            </div>
          <?php endif; ?>
          
          <?php if (!$this->waiting && $this->isAdmin):?>
            <div class="ynbusinesspages_members_action">
            	<?php if (!$this -> business -> isOwner($member)):?>
               		<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'business', 'action' => 'change-role', 'business_id' => $this->business->getIdentity(), 'user_id' => $member->getIdentity(), 'tab' => $this->identity ),'ynbusinesspages_extended');?>">
                		<?php echo $this->translate('Change Role')?>
              		</a>
              	<?php endif;?>
            </div>
          <?php endif; ?>

          <div class="ynbusinesspages_members_status">
            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
            <div>
              <?php echo $this->translate("Waiting for approval");?>
            </div>
            <?php endif;?>

            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
            <div>
              <?php echo $this->translate("Be Invited");?>
            </div>
            <?php endif;?>
          </div>
        </div>

        <div class='ynbusinesspages_members_body'>
          <div>
            <span class='ynbusinesspages_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>

              <?php /* <?php if( $this->business->getParent()->getGuid() == ($member->getGuid())): ?>
                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
              <?php endif; ?> */ ?>
            </span>
          </div>

          <div>
          	<span><?php echo $this->translate("Role:");?></span>
            <?php echo $member->role_name; ?>
          </div>
        </div>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if ( ($this->isOwner || $this->isAdmin) && $this->waiting == true) :?>
  <div style="margin-top: 29px;">
		<span class="checkbox-all">
			<input onclick='selectAll(this);' type='checkbox' class='checkbox' value=0 />
		</span>
		<span class="mass-action">
			<select name="mass_action">
				<option value="approve"><?php echo $this->translate("Approve Request");?></option>
				<option value="reject"><?php echo $this->translate("Reject Request");?></option>
				<option value="cancel"><?php echo $this->translate("Cancel Membership Request");?></option>
			</select>
		</span>
		<span class='buttons'>
		  <button onclick="setMassAction()"><?php echo $this->translate("Submit") ?></button>
		</span>
	</div>
  <?php endif;?>


  <?php if( $this->members->count() > 1 ): ?>
    <div class="ynbusinesspages-paginator">
      <?php if( $this->members->getCurrentPageNumber() > 1 ): ?>
        <div id="user_ynbusinesspages_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateBusinessMembers(businessMemberPage - 1)',
            'class' => 'buttonlink icon_previous',
            'style' => '',
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->members->getCurrentPageNumber() < $this->members->count() ): ?>
        <div id="user_ynbusinesspages_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => 'paginateBusinessMembers(businessMemberPage + 1)',
            'class' => 'buttonlink icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

<?php endif; ?>