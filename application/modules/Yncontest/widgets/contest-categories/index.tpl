<ul class="yncontest_quicklinks_menu">

<?php foreach ($this->category as $cat) : 
		$cat_id = $cat->category_id;
?>
	<li class="main-cat">
		<img onclick="javascript:showSub($(this), <?php echo $cat_id; ?>);" 
			style="cursor: pointer; vertical-align: middle;" src="application/modules/Yncontest/externals/images/sub-category.png" 
			id = "main-cat-<?php echo $cat_id?>">		
		<?php echo $this->htmlLink($this->url(array('action'=>'listing', 'category_id'=>$cat_id), 'yncontest_general'),
                    ($this->translate($cat->name)),
                    array('class'=>''))
                    ?>
        <?php $subcat = $cat->getDescendantIds(); ?>
	        <?php 
	        	foreach ($subcat as $cat):	        		
	        		if ($cat->level == 2) : ?>
	        		<li style="border: 0; background-color:#FFF; display: none; padding-left:17px" class = "sub-cat-<?php echo $cat_id?>">
	        		<?php 	echo $this->htmlLink($this->url(array('action'=>'listing', 'category_id'=>$cat->category_id), 'yncontest_general'),
                    ($this->translate($cat->name)),
                    array('class'=>'')); ?>
                    </li>
                    <?php endif;
	        	endforeach;
	        ?>
	</li>

<?php endforeach;?>
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
	for (i = 0;i < ele_array.length; i++) {
		if (ele_array[i].getStyle('display') == 'none') {
			ele_array[i].setStyle('display', 'block');
			ele.src = "application/modules/Yncontest/externals/images/main-cat.png";
		}
		else if (ele_array[i].getStyle('display') == 'block') {
			ele_array[i].setStyle('display','none');
			ele.src = "application/modules/Yncontest/externals/images/sub-category.png";
		}
	}
}
</script>