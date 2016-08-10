<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: print.tpl  08.11.11 15:39 TeaJay $
 * @author     Taalay
 */
?>

<?php
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Page/externals/styles/page_print.css');
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Page/externals/scripts/core.js');
?>
<script type="text/javascript">
  function print_page() {
    $('print_page').setStyle('display', 'none');
    window.print();
    setTimeout("show_button()", 60000);
  }
  function show_button() {
    $('print_page').setStyle('display', 'block');
  }
</script>
<link href="<?php echo $this->baseUrl().'/application/modules/Page/externals/styles/page_print.css'?>" type="text/css" rel="stylesheet" media="print">
<div class="print_page_preview">
  <div id="print_page" class="print_page_button">
    <a href="javascript:void(0);" style="background-image: url('./application/modules/Page/externals/images/page_print.png'); width: 100px;" class="buttonlink" onclick="print_page()" align="right"><?php echo $this->translate('Take Print') ?></a>
  </div>
  <div class="print_page_title">
    <?php echo $this->page->getTitle()?>
  </div>
  <div class="print_page_body">
    <div class="print_page_photo">
      <?php echo $this->itemPhoto($this->page, 'thumb.profile', '', array('align'=>'left')); ?>
    </div>
    <div class="print_page_details">
      <ul>
        <?php if ($this->page->getDescription()): ?>
          <li>
            <?php echo $this->page->getDescription(); ?>
          </li>
        <?php endif; ?>
      </ul>
      <div class="print_page_fields">
        <h4><span><?php echo $this->translate("Page Details"); ?></span></h4>
        <ul>
          <li><span><?php echo $this->translate("Posted by"); ?></span><span><?php echo $this->page->owner->getTitle(); ?></span></li>
        </ul>
        <?php echo $this->fieldValueLoop($this->page, $this->fieldStructure); ?>
      </div>
      <h4><span><?php echo $this->translate("Page Contacts"); ?></span></h4>
      <ul>
        <?php if ($this->page->isAddress()): ?>
          <li>
            <span><?php echo $this->translate("Address"); ?></span><span><?php echo $this->page->getAddress(); ?></span>
          </li>
        <?php endif; ?>
        <?php if ($this->page->website): ?>
          <li>
            <span><?php echo $this->translate("Website"); ?></span><span><?php echo $this->page->getWebsite(); ?></span>
          </li>
        <?php endif; ?>
        <?php if ($this->page->phone): ?>
          <li>
            <span><?php echo $this->translate("Phone"); ?></span><span><?php echo $this->page->phone; ?></span>
          </li>
        <?php endif; ?>
        <?php if (isset($this->markers)): ?>
          <li>
            <span><?php echo $this->translate("Map"); ?></span>
            <span class="print_page_map">
              <?php echo Engine_Api::_()->getApi('gmap', 'page')->getMapJS(); ?>
              <script type="text/javascript">
                window.addEvent('domready', function(){
                  pages_map.construct( null, <?php echo $this->markers; ?>, 2, <?php echo $this->bounds; ?> );
                });
              </script>
              <div id="map_canvas" class="page_map"></div>
            </span>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>