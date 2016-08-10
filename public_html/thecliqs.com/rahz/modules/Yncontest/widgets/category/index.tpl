<ul class="ynContest_LRH3ULLi">
<?php 
	foreach ($this->categories as $cat) : 
		$cat_id = $cat->category_id;
?>
	<li class="ynContest_mainCat">
		<img onclick="javascript:showSub($(this), <?php echo $cat_id; ?>);" 
			src="application/modules/Yncontest/externals/images/sub-category.png" 
			id = "main-cat-<?php echo $cat_id?>">		
		<?php 
		$category_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'listing','category'=>$cat->category_id), 'yncontest_general', null);
		echo $this->htmlLink($category_url,
	        strlen($this->translate($cat->name))>30?'&nbsp;'.substr($this->translate($cat->name),0,30).'...':''.$this->translate($cat->name),
	        array('class'=>''));
        ?>
        <?php $subcat = $cat->getDescendantCat($cat->category_id); ?>
			<ul>
	        <?php 
	        	foreach ($subcat as $cat):
	        	if ($cat->level == 2) : ?>
	        		<li style="display: none;" class="sub-cat-<?php echo $cat_id?>">
	        		<?php 
						$category_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'listing','category'=>$cat->category_id), 'yncontest_general', null);
						echo $this->htmlLink($category_url,
					        strlen($this->translate($cat->name))>30?'&nbsp;'.substr($this->translate($cat->name),0,30).'...':''.$this->translate($cat->name),
					        array('class'=>''));
				    ?>
                    </li>
            <?php endif; 
				  endforeach;			
			?>
			</ul>
	</li>
<?php endforeach;?>
</ul>

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