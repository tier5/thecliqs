<?php ?>

<script type="text/javascript" >
	function refineGroups() {
	    $('form_minify_admin_groups')._submit.disable = true;
	    $('hloading').setStyles({display:'block'});
	    function done(json){
	       $('form_minify_admin_groups')._submit.disable = false;
	       $('hloading').setStyles({display:'none'});
	       //
	       for(var i =0; i<json.keys.length; ++i){
	           var k = json.keys[i];
	           if(json.groups[k] !== undefined && $(k) != undefined){
	               $(k).value =  json.groups[k].join('\n');
	           }
	       }
	    }
	    function caculate(){
            (new Request.JSON({
                url : en4.core.baseUrl + '?m=lite&module=minify&name=caculate',
                method: 'GET',
                data : {
                    format: 'json'
                },
                onComplete: done
            })).send();
        }
		function check_home() {
			(new Request({
				url : en4.core.baseUrl + '?m=lite&module=minify&name=readhome',
				method: 'GET',
				data : {
					link : 'http://' + document.location.host + en4.core.baseUrl + '?minify=off&config_name=home'
				},
				onComplete: caculate
			})).send();
		}
		function check_member_profile() {
			(new Request({
				url : en4.core.baseUrl + 'profile/' + en4.user.viewer.id,
				method: 'GET',
				data : {
					minify : 'off',
					'config_name' : 'member_profile'
				},
				onComplete : check_home
			})).send();
		}
		function check_member_home() {
			(new Request({
				url : en4.core.baseUrl + 'members/home',
				method: 'GET',
				data : {
					minify : 'off',
					'config_name' : 'member_home'
				},
				onComplete : check_member_profile
			})).send();
		}
		
		check_member_home();
    }
</script>

<h2>
  <?php echo $this->translate('Minify Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>


<div class='clear'>
  <div class='settings'>
    <?php if(APPLICATION_ENV !== 'production'): ?>
    <p>You can not change groups settings in development mode, please switch to production mode.</p>
    <?php else: ?>
    <?php echo $this->form->render($this); ?>
   <div id="hloading" style="position:absolute;width:100%;height:2000px;display:none;top:0;left:0;">
        <div style="position:absolute;left:45%;top:40%;opacity:1;position:fixed">
        
        <img src="./application/modules/Minify/externals/images/loading.gif"  style = "max-width:370px;max-height:300px" />
        
        </div>
    </div>
    <?php endif; ?> 
  </div>
</div>
