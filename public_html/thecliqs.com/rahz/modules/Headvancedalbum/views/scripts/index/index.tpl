
<script type="text/javascript">

  window.filterParams = {
    page: 1,
    category_id: 0,
    search: ''
  };

  window.filterLoading = false;
  window.is_next = <?php echo $this->is_next;?>;

  function filterByCategory(category)
  {
    // prepare filter fields
    window.filterParams.page = 1;
    window.filterParams.category_id = category;
    hapPhotos.options.request_params = $merge(hapPhotos.options.request_params, window.filterParams);

    $('hapNavigation').getElement('.categories').getElements('a').removeClass('active');
    $('category_'+category).addClass('active');

    hapPhotos.loadContent(en4.core.baseUrl + 'headvancedalbum/index/index/format/json', window.filterParams, function (res){
      var $c = $('hapPhotos');
      if (res.item_count){
        $('tipNoResult').hide();
        $c.show();
      } else {
        $('tipNoResult').show();
        $c.hide();
      }
    });
  }

  function filterBySearch(search)
  {
    // prepare filter fields
    window.filterParams.page = 1;
    window.filterParams.search = search;
    hapPhotos.options.request_params = $merge(hapPhotos.options.request_params, window.filterParams);

    hapPhotos.loadContent(en4.core.baseUrl + 'headvancedalbum/index/index/format/json', window.filterParams, function (res){
      var $c = $('hapPhotos');
      if (res.item_count){
        $('tipNoResult').hide();
        $c.show();
      } else {
        $('tipNoResult').show();
        $c.hide();
      }
    });
  }

</script>

<div class="hapLoader" id="hapLoader"></div>
<div class="hapLoader" id="hapBuildLoader"></div>


<div class="hapNavigation" id="hapNavigation">
  <div class="title">
    <h2><?php echo $this->translate('HEADVANCEDALBUM_Photos');?></h2>
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


<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Headvancedalbum/externals/scripts/hap.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function (){
    (new HapInstance({
      request_url: '<?php echo $this->url();?>?format=json',
      max_width: 220
    }));
  });
</script>

<div class="layout_middle">



<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

 <ul class="hapPhotos" id="hapPhotos">
   <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');?>
 </ul>

   <?php else: ?>
   <div class="tip">
     <span>
       <?php echo $this->translate('Nobody has created an photo yet.');?>
       <?php if( $this->canCreate ): ?>
         <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
       <?php endif; ?>
     </span>
   </div>
 <?php endif;?>

 <div class="tip" style="display: none;" id="tipNoResult">
   <span>
     <?php echo $this->translate('Nobody has created an photo with that criteria.');?>
     <?php if( $this->canCreate ): ?>
       <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
     <?php endif; ?>
   </span>
 </div>


</div>



