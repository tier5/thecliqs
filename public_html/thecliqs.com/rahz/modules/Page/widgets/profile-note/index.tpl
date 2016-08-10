<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->isAdmin): ?>
<script type="text/javascript">
page.ajax_url = '<?php echo $this->url(array( 'action' => 'post-note', 'page_id' => $this->subject->getIdentity()), 'page_team'); ?>';
page.note = <?php echo Zend_Json_Encoder::encode($this->subject->note); ?>;
page.empty_note = <?php echo Zend_Json_Encoder::encode($this->translate('Write something about %s page.', $this->subject->getTitle())); ?>;
en4.core.runonce.add(function(){
	$$('#profile_note_textarea textarea')[0].autogrow();
	$$('#profile_note_textarea textarea')[0].setStyle('padding', '0px');
});
</script>
<?php endif; ?>

<?php if($this->isAdmin): ?>
	<a href="javascript://" class="edit_page_note" id="profile_note_link" onclick="page.prepare_post(<?php echo (int)$this->subject->getIdentity(); ?>)">&nbsp;</a>
<?php endif; ?>

<div class="profile_note">	
	<div class="profile_note_text" id="profile_note_text"><?php if ($this->subject->note): ?><?php echo nl2br($this->subject->note); ?><?php else: ?><?php if($this->isAdmin): ?><?php echo $this->translate('Write something about %s page.', $this->subject->getTitle()); ?><?php endif; ?><?php endif; ?></div>
	<?php if ($this->isAdmin): ?>
    <div class="profile_note_textarea" id="profile_note_textarea" style="display: none;">
      <textarea onblur="page.post_note(this.value);" ></textarea>
    </div>
	<?php endif; ?>
</div>