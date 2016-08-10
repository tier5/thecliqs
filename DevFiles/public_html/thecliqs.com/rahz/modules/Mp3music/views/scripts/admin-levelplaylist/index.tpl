<h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
</div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
       
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    $('level_id').addEvent('change', function(){
        window.location.href = en4.core.baseUrl + 'admin/mp3-music/levelplaylist/'+this.get('value');
    });
    /*
    $('view-0').addEvent('click', function(){
        $('create-0').click();
    });
    if ($type($('moderator-1'))) {
        $('moderator-1').addEvent('click', function(){
            $('create-1').click();
            $('view-1').click();
        });
    }
 	*/
    //]]>
    function checkIt(evt) {
        evt = (evt) ? evt : window.event
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            status = "This field accepts numbers only."
            return false
        }
        status = ""
        return true
    }
</script>