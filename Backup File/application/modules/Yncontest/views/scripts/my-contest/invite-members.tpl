<script type="text/javascript">
	en4.core.runonce.add(function(){
		document.getElementById('friendselectall').addEvent('click', function(){
			check = this.checked;
			list = document.getElementsByName('friends[]');
			for(var i=0;i<list.length;i++){
				list[i].checked = check;
			}
		});
	});

	en4.core.runonce.add(function(){
		document.getElementById('userselectall').addEvent('click', function(){
			check = this.checked;
			list = document.getElementsByName('users[]');
			for(var i=0;i<list.length;i++){
				list[i].checked = check;
			}
		});
	});

	function tab_switch_type(element,content) {
		if( element.tagName.toLowerCase() == 'a' ) {
			element = element.getParent('li');
		}
		var myContainer = element.getParent('.tabs_parent').getParent();
		myContainer.getElements('ul > li').removeClass('active');
		document.getElementById('friend_content').style.display = 'none';
		document.getElementById('other_content').style.display = 'none';
		element.addClass('active');
		document.getElementById(content).style.display = 'block';
	}

	function updateUserList(event, mode) {
		if (event.keyCode == 13) {
			var search_text = document.getElementById(mode + '_search').value;

			var element = document.getElementById(mode+'-element');
			var content = element.innerHTML;
			element.innerHTML= "<img  src='application/modules/Yncontest/externals/images/loading.gif'></img>";

			new Request.JSON({
				'method': 'get',
				'url' : '<?php echo $this->url(array('controller' => 'member', 'action' => 'ajax'), 'yncontest_mycontest',true) ?>',
				'data' : {
					'format' : 'json',
					'text' : search_text,
					'mode' : mode,
					'contestId' : <?php echo $this->contest->getIdentity();?>
				},
				'onSuccess' : function(json) {

					element.innerHTML = "";

					var input = new Element('INPUT', {
						'type': 'hidden',
						'name': 'users'
					});

					var ul = new Element('UL', {
						'class':'form-options-wrapper'
					});

					element.appendChild(input);
					element.appendChild(ul);

					if(json.total == 0)
					{
						document.getElementById('buttons-wrapper').style.display = 'none';
					}
					else
					{
						document.getElementById('buttons-wrapper').style.display = 'block';
						for(var i=0;i<json.total;i++)
						{
							var item  = json.rows[i];

							var li = document.createElement('li');
							var li = new Element('LI');
							var input = new Element('INPUT',{
								'type': 'checkbox',
								'name': mode+'[]',
								'id': mode+'-'+ item.id,
								'value': item.id
							});

							var label = new Element('LABEL',{
								'html': item.title,
								'for': mode+'-'+ item.id
							});

							li.appendChild(input);
							li.appendChild(label);
							ul.appendChild(li);
						}
					}



				}
			}).send();
		}
	}

	function submitForm(mode)
	{
		var form = document.getElementById('group_form_'+ mode +'_invite');
		form.setAttribute('action', '');
		form.submit();
	}
</script>


	<div id="friend_content">
		<?php if($this->friend_count !=0):?>
			<?php echo $this -> friend_form -> setAttrib('class', 'global_form_popup') -> render($this);?>
		<?php else:?>
			<div class="tip">
				<span>
					<?php echo $this -> translate('Currently, there are no friends that you can invite.');?>
				</span>
			</div>
		<?php endif;?>
		</div>

