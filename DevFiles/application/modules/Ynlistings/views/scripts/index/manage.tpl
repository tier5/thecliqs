<script type="text/javascript">
	var pageAction =function(page){
		$('page').value = page;
		$('filter_form').submit();
	}
</script>

<?php if ($this->can_import) :?>
<div id="import_listings" class='import_listings'>
	<?php echo $this->htmlLink(
	array('route' => 'ynlistings_general', 'action' => 'import'),
	$this->translate('Import Listings'), 
	array('class' => 'buttonlink icon_listings_import')) ?>
</div>
<?php endif; ?>

<?php if( count($this->paginator) > 0 && $this->can_export): ?>
<div id="export_listings" class='export_listings'>
	<?php echo $this->htmlLink(
	array('route' => 'ynlistings_general', 'action' => 'export'),
	$this->translate('Export Listings'), 
	array('class'=>'smoothbox buttonlink icon_listings_export')) ?>
</div>
<?php endif;?>

<div class='layout_middle'>
<?php if( count($this->paginator) > 0 ): ?>
<ul class='listing_browse'>
    <?php foreach( $this->paginator as $listing): ?>
    <li>
        <div class="listing_photo">
        <?php echo $this->htmlLink($listing->getHref(), $this->itemPhoto($listing, 'thumb.normal')) ?>
        </div>
        <div class="listing_options">
        <?php if( $listing->isOwner($this->viewer())): ?>
            <?php if ($listing->isEditable()) : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general', 'action' => 'edit', 'id' => $listing->getIdentity()), 
            $this->translate('Edit Listing'), 
            array('class' => 'buttonlink icon_listings_edit')) ?>
            <?php endif; ?>
            
            <?php 
            $category = $listing->getCategory();
            if ($this->can_select_theme): 
            ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general','controller' => 'index','action' => 'select-theme', 'listing_id' => $listing->getIdentity()), 
            $this->translate('Select Theme'), 
            array('class' => 'smoothbox buttonlink icon_listings_select_theme')) ?>
            <?php endif; ?>
                  
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_extended','controller' => 'photo','action' => 'index', 'listing_id' => $listing->getIdentity()), 
            $this->translate('Add Photos'), 
            array('class' => 'buttonlink icon_listings_add_photos')) ?>
            
            <?php if(Engine_Api::_()->hasItemType('video')): ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_extended','controller' => 'video','action' => 'list', 'listing_id' => $listing->getIdentity()), 
            $this->translate('Add Videos'), 
            array('class' => 'buttonlink icon_listings_add_videos')) ?>
            <?php endif;?>
            
            <?php if ($listing->status == 'open' && $listing->approved_status == 'approved') : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynlistings_general', 'action' => 'close', 'id' => $listing->getIdentity()), 
                $this->translate('Close Listing'), 
                array('class' => 'buttonlink smoothbox icon_listings_close')) ?>
            <?php endif; ?>
            <?php if ($listing->status == 'draft') : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynlistings_general', 'action' => 'place-order', 'id' => $listing->getIdentity()), 
                $this->translate('Publish Listing'), 
                array('class' => 'buttonlink icon_listings_publish')) ?>
            <?php endif; ?>
            <?php if ($listing->status == 'closed' || $listing->status == 'expired') : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynlistings_general', 'action' => 're-open', 'id' => $listing->getIdentity()), 
                $this->translate('Re-open Listing'), 
                array('class' => 'buttonlink smoothbox icon_listings_open')) ?>
            <?php endif; ?>
            
            <?php if ($listing->isDeletable()) : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general', 'action' => 'delete', 'id' => $listing->getIdentity()), 
            $this->translate('Delete Listing'), 
            array('class' => 'buttonlink smoothbox icon_listings_delete')) ?>
            <?php endif; ?>
        <?php endif ;?>
        </div>
        <div class="listing_info">
            <div class="listing_title">
                <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
            </div>
            <div class="listing_creation">
                <?php 
                    $creation_date = new Zend_Date(strtotime($listing->creation_date));
                    $creation_date->setTimezone($this->timezone);
                ?>
                <span class="small_description"><?php echo $this->locale()->toDateTime($creation_date)?></span>
                <span class="small_description"><?php echo $this->translate(' - by ')?></span>
                <span><?php echo $listing->getOwner()?></span>
            </div>
            <div class="listing_price">
            <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
            </div>
            <div class="listing_rating">
            <?php 
                echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing));
            ?>
            </div>
            <div class="listing_activity small_description">
            <?php 
            $activity = array (
                $this -> translate(array("%s review", "%s reviews" ,$listing->ratingCount()), $listing->ratingCount()),	
                $this -> translate(array("%s view", "%s views" ,$listing->view_count), $listing->view_count),
                $this -> translate(array("%s like", "%s likes" ,$listing->like_count), $listing->like_count),
            );
            echo implode(", ", $activity);
            ?>
            </div>
            <div class="category">
            <span class="small_description"><?php echo $this->translate('Category: ')?></span>
            <span><?php echo $this->translate($listing->getCategoryTitle())?></span>
            </div>
            <div class="location">
                <span class="small_description"><?php echo $this->translate('Location: ')?></span>
                <span><?php echo ($listing->location) ? $listing->location : $this->translate('unknown')?></span>
                <?php if(!(($listing -> longitude == 0) && ($listing -> latitude == 0))): ?>
                <span class="small_description"><?php echo $this->translate(' - ')?></span>	
	                <span>
	                <?php echo $this->htmlLink(
	                array('route' => 'ynlistings_specific', 'action' => 'direction', 'id' => $listing->getIdentity()), 
	                $this->translate('<span class="fa fa-map-marker"></span> Get Direction'), 
	                array('class' => 'smoothbox get_direction')) ?>
	                </span>
	            <?php endif;?>
            </div>
            <div class="status">
            <span class="small_description"><?php echo $this->translate('Listing Status: ')?></span>
            <span><?php echo $this->translate($listing->status)?></span>
            </div>
            <div class="approved_status">
            <span class="small_description"><?php echo $this->translate('Approved Status: ')?></span>
            <span><?php echo (($listing->approved_status) ? $this->translate($listing->approved_status) : 'N/A')?></span>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
        )); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('No listings found.') ?></span>
    </div>
<?php endif; ?>
</div>
