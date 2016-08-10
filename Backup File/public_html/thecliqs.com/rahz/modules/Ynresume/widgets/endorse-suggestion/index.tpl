<script type="text/javascript">
	window.addEvent('domready', function(){
		$$("span.close-btn").addEvent('click', function(){
			var id = this.get('id');
			var elm = $('skill-item_'+id);
			if (elm !== null)
			{
				elm.dispose();
			}
		});
		$("ynresume-endorse-btn").addEvent('click', function(){
			var skills = $$("span.close-btn").get('val');
			var myRequest = new Request({
			    url: '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'endorse'), 'ynresume_extended');?>',
			    method: 'post',
			    format: 'json',
			    async : true,
			    data: {
				    'skills': skills,
				    'resume_id': <?php echo $this -> resume -> getIdentity();?>
				},
			    onSuccess: function(responseText, responseXML){
			        $("ynresume-endorse-suggestion").dispose();
			    },
			});
			myRequest.send();
		});
		$("ynresume-skip-btn").addEvent('click', function(){
			var myRequest = new Request({
			    url: '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'skip'), 'ynresume_extended');?>',
			    method: 'post',
			    format: 'json',
			    async : true,
			    data: {
				    'resume_id': <?php echo $this -> resume -> getIdentity();?>
				},
			    onSuccess: function(responseText, responseXML){
			        $("ynresume-endorse-suggestion").dispose();
			    },
			});
			myRequest.send();
		});
	});
</script>

<div id="ynresume-endorse-suggestion">
	<h3>
		<?php echo $this-> translate("Does %s have these skills or expertise?", $this->owner->getTitle());?>
	</h3>
	<div class="ynresume-endorse-skill-list">
	<?php foreach ($this -> userSkills as $skill):?>
		<div class="skill-item" id="skill-item_<?php echo $skill -> skill_id;?>">
			<span>
				<?php echo $skill -> text;?>
			</span>
			<span class="close-btn" id="<?php echo $skill -> skill_id;?>" val="<?php echo $skill -> text;?>">
				<i class="fa fa-close"></i>
			</span>
		</div>
	<?php endforeach;?>
		<div style="margin-top: 20px;">
			<button id="ynresume-endorse-btn"><?php echo $this -> translate("Endorse");?></button>
			<button id="ynresume-skip-btn"><?php echo $this -> translate("Skip");?></button>
		</div>
	</div>
</div>