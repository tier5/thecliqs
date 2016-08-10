<li class="business-review">
    <div class="business-review-count">
        <?php echo $this->translate(array('%s review', '%s reviews', $this->reviewCount), $this->reviewCount);?>
    </div>
    <?php if (!is_null($this->latestReview)) : ?>
    <div class="business-latest-review">
        <span class="review-title"><?php echo $this->latestReview->getTitle()?></span>
        <span class="review-date"><?php echo '- '.$this->translate('at').' '.$this->locale()->toDateTime($this->latestReview->getCreationTime())?></span>
        <?php $reviewer = $this->latestReview->getCreator()?>
        <span class="review-by"><?php echo '- '.$this->translate('by').' '.$this->htmlLink($reviewer->getHref(), $reviewer->getTitle())?></span>
    </div>
    <?php endif; ?>
</li>