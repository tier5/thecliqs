<script type="text/javascript">

  window.filterParams = {
    page: 1,
    category_id: 0,
    search: ''
  };

  window.filterLoading = false;
  window.is_next = <?php echo $this->is_next;?>;

  function viewMore()
  {
    if (!window.is_next){
      return ;
    }
    if (window.filterLoading){
      return ;
    }
    window.filterLoading = true;
    $('hapLoader').addClass('active');

    // prepare filter fields
    window.filterParams.page++;

    (new Request.JSON({
      url: en4.core.baseUrl + 'headvancedalbum/index/browse/format/json/owner/<?php echo $this->subject()->getGuid();?>',
      data: window.filterParams,
      onSuccess: function (res)
      {
        window.filterLoading = false;
        $('hapLoader').removeClass('active');

        window.is_next = res.is_next;

        var $c = $('hapThumbs');
        if (res.item_count){
          $c.show();
          var $items = (new Element('div', {html: res.body})).getChildren();
          $items.inject($c);
          $items.setStyle('opacity', 0).fade('in');
        } else {
        }

      }

    })).send();
  }


    // loading on scroll down
  window.addEvent('scroll', function () {

    // check bottom
    var is_bottom = (window.getScrollTop() >= window.getScrollSize().y - window.getSize().y);
    if (!is_bottom) {
      return;
    }
    if (!$('hapThumbs').isVisible()){
      return ;
    }
    viewMore();

  });


</script>

<ul class="hapThumbs" id="hapThumbs">
  <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_albumItems.tpl');?>
</ul>

