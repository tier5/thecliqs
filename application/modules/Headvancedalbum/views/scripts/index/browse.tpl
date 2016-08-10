
<script type="text/javascript">

  window.filterParams = {
    page: 1,
    category_id: 0,
    search: ''
  };

  window.is_next = <?php echo $this->is_next;?>;

  window.filterLoading = false;

  function filterByCategory(category)
  {
    if (window.filterLoading){
      return ;
    }
    window.filterLoading = true;
    $('hapLoader').addClass('active');

    // prepare filter fields
    window.filterParams.page = 1;
    window.filterParams.category_id = category;

    $('hapNavigation').getElement('.categories').getElements('a').removeClass('active');
    $('category_'+category).addClass('active');

    (new Request.JSON({
      url: en4.core.baseUrl + 'headvancedalbum/index/browse/format/json',
      data: window.filterParams,
      onSuccess: function (res)
      {
        window.filterLoading = false;
        $('hapLoader').removeClass('active');

        window.is_next = res.is_next;

        var $c = $('hapThumbs');
        if (res.item_count){
          $('tipNoResult').hide();
          $c.show();
          $c.empty();
          var $items = (new Element('div', {html: res.body})).getChildren();
          $items.inject($c);
          $items.setStyle('opacity', 0).fade('in');
        } else {
          $('tipNoResult').show();
          $c.hide();
        }
      }

    })).send();
  }

  function filterBySearch(search)
  {
    if (window.filterLoading){
      return ;
    }
    window.filterLoading = true;
    $('hapLoader').addClass('active');

    // prepare filter fields
    window.filterParams.page = 1;
    window.filterParams.search = search;

    (new Request.JSON({
      url: en4.core.baseUrl + 'headvancedalbum/index/browse/format/json',
      data: window.filterParams,
      onSuccess: function (res)
      {
        window.filterLoading = false;
        $('hapLoader').removeClass('active');

        window.is_next = res.is_next;

        var $c = $('hapThumbs');
        if (res.item_count){
          $('tipNoResult').hide();
          $c.show();
          $c.empty();
          var $items = (new Element('div', {html: res.body})).getChildren();
          $items.inject($c);
          $items.setStyle('opacity', 0).fade('in');
        } else {
          $('tipNoResult').show();
          $c.hide();
        }
      }

    })).send();
  }

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
      url: en4.core.baseUrl + 'headvancedalbum/index/browse/format/json',
      data: window.filterParams,
      onSuccess: function (res)
      {
        window.filterLoading = false;
        $('hapLoader').removeClass('active');

        window.is_next = res.is_next;

        var $c = $('hapThumbs');
        if (res.item_count){
          $('tipNoResult').hide();
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
    viewMore();

  });


</script>


<div class="hapLoader" id="hapLoader"></div>


<div class="hapNavigation" id="hapNavigation">
  <div class="title">
    <h2><?php echo $this->translate('HEADVANCEDALBUM_Albums');?></h2>
  </div>
  <ul class="categories">
    <li>
    <a href="javascript:void(0);" onclick="filterByCategory('recent')" id="category_recent" class="active">
      <?php echo $this->translate('HEADVANCEDALBUM_Recent');?>
    </a>
    </li>
    <li>
      <a href="javascript:void(0);" onclick="filterByCategory('popular')" id="category_popular">
      <?php echo $this->translate('HEADVANCEDALBUM_Popular');?>
    </a>
    </li>
    <?php foreach ($this->categories as $key => $title):?>
    <li>
      <a href="javascript:void(0);" onclick="filterByCategory(<?php echo $key;?>)" id="category_<?php echo $key;?>"
        class="<?php if ($this->category_id == $key):?>active<?php endif;?>">
        <?php echo $title;?>
      </a>
    </li>
    <?php endforeach;?>
  </ul>

  <form action="" id="hapSearchForm" class="hapSearchForm" onsubmit="filterBySearch($('search_value').get('value')); return false;">
    <input id="search_value" type="text" name="search" value="" placeholder="<?php echo $this->translate('Search');?>"/>
    <a href="javascript:void(0)" class="hap-btn" onclick="filterBySearch($('search_value').get('value'));"><i class="icon-search"></i></a>
  </form>

</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="hapThumbs" id="hapThumbs">
    <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_albumItems.tpl');?>
  </ul>

  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album yet.');?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif;?>

<div class="tip" style="display: none;" id="tipNoResult">
  <span>
    <?php echo $this->translate('Nobody has created an album with that criteria.');?>
    <?php if( $this->canCreate ): ?>
      <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
    <?php endif; ?>
  </span>
</div>


<br />
<br />
