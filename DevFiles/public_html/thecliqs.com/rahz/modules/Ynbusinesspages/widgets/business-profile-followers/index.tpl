<script type="text/javascript">
	en4.core.runonce.add(function()
	{
		var anchor = $('ynbusinesspages_profile_followers').getParent();
		$('ynbusinesspages_profile_followers_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
		$('ynbusinesspages_profile_followers_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

		$('ynbusinesspages_profile_followers_previous').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
				}
			}), {
				'element' : anchor
			})
		});

		$('ynbusinesspages_profile_followers_next').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
				}
			}), {
				'element' : anchor
			})
		});
	});
</script>
<ul id ='ynbusinesspages_profile_followers' class="ynbusinesspages_members_list">
	<?php foreach ($this->paginator as $member): ?>
		<li id="ynbusinesspages_follower_<?php echo $member->getIdentity() ?>" class="">
			<div class="ynbusinesspages_members_avatar">
	          <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($member); ?>
	        </div>

	        <div class='ynbusinesspages_members_body'>
	          <div>
	            <span class='ynbusinesspages_members_status'>
					<?php echo $this->htmlLink($member->getHref(), $member -> getTitle(), array('class' => 'ynbusinesspages_follower_title')) ?>
	            </span>
	          </div>
	        </div>
		</li>
		<?php endforeach;?>
	</ul>
	<div class="ynbusinesspages-paginator">
		<div id="ynbusinesspages_profile_followers_previous" class="paginator_previous">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
				'onclick' => '',
				'class' => 'buttonlink icon_previous'
				)); ?>
			</div>
			<div id="ynbusinesspages_profile_followers_next" class="paginator_next">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
					'onclick' => '',
					'class' => 'buttonlink_right icon_next'
					)); ?>
				</div>
			</div>