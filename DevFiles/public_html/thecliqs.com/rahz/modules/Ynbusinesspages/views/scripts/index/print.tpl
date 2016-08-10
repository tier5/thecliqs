<?php 
    $this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
 ?>
<div id="business-print">
    <div class="options">
        <a href="javascript:void(0)" onclick="window.print();"><span class="fa fa-print"></span><?php echo $this->translate('Print')?></a>
        <a href="<?php echo $this->business->getHref()?>" ><span class="fa fa-chevron-left"></span><?php echo $this->translate('Back')?></a>
    </div>
    <div class="print-business-header">
        <div class="business-photo">
            <?php echo $this->htmlLink($this->business->getHref(), $this->itemPhoto($this->business, 'thumb.profile'))?>
        </div>
        <div class="business-title">
            <?php echo $this->htmlLink($this->business->getHref(), $this->business->getTitle())?>
        </div>
    </div>
    <div class="print-business-content">
    <?php 
        echo $this->content()->renderWidget('ynbusinesspages.business-profile-overview');
    ?>
    </div>
    <script>
        //for printing
        document.getElement('head').getElement('link[rel=stylesheet]').set('media', 'all');
    </script>
</div>