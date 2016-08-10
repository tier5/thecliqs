[WILL COPY TO PARTIAL VIEW - LATER]
<style>
.add-endorse-btn
{
	cursor: pointer;
}
</style>

<script type="text/javascript">
	window.addEvent('domready', function(){
		$$("span.add-endorse-btn").addEvent('click', function(){
			var skill = $$("span.add-endorse-btn").get('val')[0];
			var currentElm = this;
			var myRequest = new Request({
			    url: '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'endorse-one'), 'ynresume_extended');?>',
			    method: 'post',
			    format: 'json',
			    async : true,
			    data: {
				    'skill': skill,
				    'resume_id': <?php echo $this -> resume -> getIdentity();?>
				},
			    onSuccess: function(responseText, responseXML){
			    	currentElm.dispose();
			    },
			});
			myRequest.send();
		});
	});
</script>

<?php foreach($this -> skills as $skill):?>
<div>
	<span><?php echo count($skill['endorses'])?></span>
	<span><?php echo $skill['text']?></span>
	<?php if (!in_array($this->viewer()->getIdentity(), $skill['endorsed_user_ids'])):?>
	<span class="add-endorse-btn" val="<?php echo $skill['text']?>">+</span>
	<?php endif;?>
	<?php foreach ($skill['endorses'] as $endorse):?>
		<?php $user = Engine_Api::_()->user()->getUser($endorse -> user_id);?>
		<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?>
	<?php endforeach;?>
</div>
<?php endforeach;?>