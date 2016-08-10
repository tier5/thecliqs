<div class="page-search-tag-cloud">
  <?php foreach ($this->cloud as $item): ?><a class="he_tag<?php echo $item['class']; ?>" href="javascript:void(0)" onclick="this.blur(); page_search.search_by_tag(<?php echo (int)$item['tag_id']; ?>);"><?php echo $item['text']; ?></a> <?php endforeach; ?>
  <div class="clr"></div>
</div>