<script type="text/javascript">
function submitFilter(form) {
    var page_id = $('page_id').value;
    var placement = ($('placement')) ? $('placement').value : '';
    if (form == 0) {
        window.location.href = en4.core.baseUrl + 'admin/ynsocialads/ad-blocks/create/page_id/'+page_id+'/placement/'+placement;
    }
    else {
        $('create_adblock').set('action', en4.core.baseUrl + 'admin/ynsocialads/ad-blocks/create/page_id/'+page_id+'/placement/'+placement);
        $('create_adblock').submit();
    }
}

function changePlacement(obj) {
    var value = obj.value;
    var placementArr = value.split('_');
    var imageSrc = en4.core.baseUrl + 'application/modules/Ynsocialads/externals/images/widgets/'+placementArr[0]+'.png';
    $('widget_preview').set('src', imageSrc);
} 
</script>
<h2><?php echo $this->translate("YouNet Social Ads Plugin") ?></h2>
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

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
<?php print_r($this->test) ; ?>