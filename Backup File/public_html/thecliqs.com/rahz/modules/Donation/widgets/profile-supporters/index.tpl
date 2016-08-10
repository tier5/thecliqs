<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:54
 */?>

<script type="text/javascript">
    en4.core.runonce.add(function(){

        var miniTipsOptions = {
            'htmlElement': '.like_tip_text',
            'delay': 1,
            'className': 'he-tip-mini',
            'id': 'he-mini-tool-tip-id',
            'ajax': false,
            'visibleOnHover': false
        };

        var $likesTips = new HETips($$('.like_tool_tip_links'), miniTipsOptions);
    });
</script>

<h3> <?php echo $this->translate('DONATION_Supporters');?></h3>
<div class="he_like_cont">
    <div class="like_list likes_all active_list">
        <?php $total_items = $this->likes->getTotalItemCount(); ?>
        <?php if ($total_items == 0) : ?>
            <div class="he_like_no_content">
                <?php echo $this->translate('There are no content.'); ?>
            </div>
        <?php else : ?>
        <?php $href = "javascript:like.see_all('" . $this->subject->getType()."', ". $this->subject->getIdentity() . ", 'all')";?>
        <div class="see_all_container" style="margin-left: 12px;">
            <a href="<?php echo $href;?>">
                <?php echo $this->translate(array("%s DONATION_supporter", "%s supporters", $this->likes->getTotalItemCount()), ($this->likes->getTotalItemCount())); ?>
            </a>
        </div>

        <div class="clr"></div>

        <div class="list">
            <?php $counter = 0; ?>
            <?php foreach ($this->likes as $like): ?>
            <div class="item">
                <a class="like_tool_tip_links" href="<?php echo $like->getHref(); ?>">
                    <?php echo $this->itemPhoto($like, 'thumb.icon'); ?>
                </a>

                <div class="like_tip_title hidden"></div>
                <div class="like_tip_text hidden"><?php echo $like->getTitle(); ?></div>
            </div>
            <?php $counter++; ?>

            <?php if ($counter % 3 == 0): ?>
                <div class="clr">
                </div><?php endif; ?>

            <?php endforeach; ?>

            <div class="clr"></div>
        </div>

        <div class="clr" style="margin-bottom:10px;"></div>
        <?php endif; ?>
    </div>
</div>