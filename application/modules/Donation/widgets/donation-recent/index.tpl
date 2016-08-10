<?php
    /**
     * SocialEngine
     *
     * @category   Application_Extensions
     * @package    ${NAME}
     * @copyright  Copyright Hire-Experts LLC
     * @license    http://www.hire-experts.com
     * @author     adik
     * @date       30.07.12
     * @time       12:08
     */
    ?>

<?php if (count($this->paginator) > 0): ?>
    <ul class='donations_browse'>
        <?php foreach( $this->paginator as $donation ): ?>
        <li>
            <?php
            /**
             * @var $donation Donation_Model_Donation
             */
            ?>
            <div class="donations_photo">
                <?php echo $this->htmlLink($donation->getHref(), $this->itemPhoto($donation, 'thumb.normal')) ?>
            </div>
            <div class="donations_options">
            </div>
            <div class="donations_info">
                <div class="donations_title">
                    <h3><?php echo $this->htmlLink($donation->getHref(), $donation->getTitle()) ?></h3>
                </div>
                <div class="donations_desc">
                    <?php echo $this->viewMore($donation->getDescription()) ?>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('There are no donations yet.') ?>
        </span>
    </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
    'query' => $this->formValues
)); ?>
