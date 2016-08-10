<style>
.ynresume-list-endorsers, h3{
    margin: 10px 15px;
}
.ynresume-list-endorsers a.endorser-photo{
    display: inline-block;
    vertical-align: middle;
}
.ynresume-list-endorsers a.endorser-title{
    display: inline-block;
    height: 50px;
    line-height: 50px;
    vertical-align: middle;
    font-weight: bold;
}
</style>

<?php if (count($this -> endorses)):?>
    <h3><?php echo $this -> translate("Endorsers");?></h3>
    <div class="ynresume-list-endorsers">
        <?php foreach ($this -> endorses as $endorse) : ?>
            <div>
                <?php if ($endorse -> user_id == $this->resume->user_id) continue;?>
                <?php $user = Engine_Api::_()->user()->getUser($endorse -> user_id);?>
                <?php $userHref = Engine_Api::_()->ynresume()->getHref($user);?>
                <?php echo $this -> htmlLink($userHref, $this->itemPhoto($user, 'thumb.icon'), array('class'=>'endorser-photo', 'target' => '_blank'));?>
                <?php echo $this->htmlLink($userHref, $user->getTitle(), array('class'=>'endorser-title', 'target' => '_blank'));?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this -> translate("No endorsers to list"); ?></span>
    </div>
<?php endif; ?>