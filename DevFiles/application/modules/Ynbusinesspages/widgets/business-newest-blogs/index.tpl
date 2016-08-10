<!-- Content -->
<?php if( $this->paginator->getTotalItemCount() > 0 ): 
$business = $this->business;?>
<ul class="ynbusinesspages_blog">           
    <?php foreach ($this->paginator as $blog): 
    	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($blog);?>
    <li>
        <div class="wrap_col3">
            <div class="wrap_col_left">
                <div class="ynblog_entrylist_entry_date">
                    <?php 
                    $creation_date = new Zend_Date(strtotime($blog->creation_date)); 
                    $creation_date->setTimezone($this->timezone);
                    ?>
                    <div class="day">
                        <?php echo $creation_date->get(Zend_Date::DAY)?>
                    </div>
                    <div class="month">
                    <?php echo $creation_date->get(Zend_Date::MONTH_NAME_SHORT)?>
                    </div>
                    <div class="year">
                    <?php echo $creation_date->get(Zend_Date::YEAR)?>
                    </div>
                </div>
            </div>
            <div class="wrap_col_center">
                <div class="yn_title"><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></div>
                <div class="post_by"><?php echo $this->translate('by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?></div>
                <div class="ynblog_entrylist_entry_body"><?php echo $this->string()->truncate($this->string()->stripTags($blog->body), 300) ?></div>
            </div>
        </div>
    </li>       
    <?php endforeach; ?>             
</ul>  
<?php endif; ?>
