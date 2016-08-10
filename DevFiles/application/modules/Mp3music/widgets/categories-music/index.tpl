<ul class="global_form_box" style="margin-bottom: 15px;">
<div class = 'avd_music'>
<?php $cats = Mp3music_Model_Cat::getCats(0);
      foreach($cats as $cat): ?>
            <li class="mp3_title_link" title="<?php echo $this->translate($cat->title); ?>">
            	<img onclick="javascript:showSub($(this), <?php echo $cat->getIdentity(); ?>);" 
				style="cursor: pointer; vertical-align: middle;" src="application/modules/Mp3music/externals/images/sub-category.png" 
				id = "main-cat-<?php echo $cat->getIdentity()?>">		
            <?php echo $this->htmlLink($this->url(array('search'=>'categories','id'=>$cat->cat_id,'title'=>null), 'mp3music_search'),
                $this->string()->truncate($this->translate($cat->title), 30),
                array('class'=>'')); ?>
                <?php $subCategories = Mp3music_Model_Cat::getCats($cat->getIdentity());?>
                <?php foreach ($subCategories as $category):?>
                	<li title="<?php echo $this->translate($category->title); ?>" style="border: 0; display: none; padding-left:17px" class = "sub-cat-<?php echo $cat->getIdentity()?>">
			            <?php echo $this->htmlLink($this->url(array('search'=>'categories','id'=>$category->cat_id,'title'=>null), 'mp3music_search'),
			                $this->string()->truncate($this->translate($category->title), 30),
			                array('class'=>'')); ?>
			         </li>
                <?php endforeach;?>
            </li>
      <?php endforeach;?>
            <li class="mp3_title_link" title="<?php echo $this->translate('Others') ?>">
                <img onclick="javascript:showSub($(this), 0);" 
				style="cursor: pointer; vertical-align: middle;" src="application/modules/Mp3music/externals/images/sub-category.png" 
				id = "main-cat-0">
                <?php echo $this->htmlLink($this->url(array('search'=>'categories','title'=>null,'id'=>null), 'mp3music_search'),
                $this->translate('Others'),
                array('class'=>'')); ?>
            </li>  
</div>          
</ul>

<style type="text/css">
.main-cat {
	font-weight: bold;
}
</style>

<script type="text/javascript">
var showSub = function(ele, cat_id){
	var ele_array = $$('.sub-cat-' + cat_id);
	var img_ele = ele;
	for (i = 0;i < ele_array.length; i++) 
	{
		if (ele_array[i].getStyle('display') == 'none') 
		{
			ele_array[i].setStyle('display', 'block');
			ele.src = "application/modules/Mp3music/externals/images/main-cat.png";
		}
		else if (ele_array[i].getStyle('display') == 'block') 
		{
			ele_array[i].setStyle('display','none');
			ele.src = "application/modules/Mp3music/externals/images/sub-category.png";
		}
	}
}
</script>