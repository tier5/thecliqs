<?php
if ($this->error != '')
{
    echo $this->error;
    return;
}
?>
<div class="user-tooltip-container">
    <div style="background-color: white; border: 1px solid #D0E2EC; border-radius: 3px">
        <div class="user-photo">
            <?php
            if ($this->type == 'user')
                echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile'));
            if ($this->type == 'group')
                echo $this->htmlLink($this->group->getHref(), $this->itemPhoto($this->group, 'thumb.profile'));
            ?>
        </div>
        <div class="user-info">
            <div class="user-name">
                <?php
                if ($this->type == 'user'){
                    echo $this->htmlLink($this->user->getHref(), $this->user->getTitle());
                }
                    
                if ($this->type == 'group'){
                    echo $this->htmlLink($this->group->getHref(), $this->group->getTitle());
                }
                ?>
            </div>

            <div class="user-friends">
                <?php
                if ($this->type == 'user')
                {
                    $count = count($this->friends);
                    echo $count . ' ';
                    echo ($count == 1) ? $this->translate('friend') : $this->translate('friends');
                }
                ?>

                <?php
                if ($this->type == 'group')
                {
                    echo $this->group->member_count . ' ';
                    echo ($this->group->member_count == 1) ? $this->translate('member') : $this->translate('members');
                }
                ?>
                <div class="user-friends-count">
                    <?php
                    if ($this->type == 'user')
                    {
                        $i = 0;
                        foreach ($this->friends as $friend)
                        {
                            $i++;
                            if ($i > 5)
                                break;
                            echo '<a title="' . $friend['name'] . '" href="' . $this->baseUrL() . '/' . $friend['l'] . '"><img src="' . $this->baseUrL() . '/' . $friend['photo'] . '"/></a>';
                        }
                    }
                    
                    if ($this->type == 'group')
                    {
                        foreach ($this->groupMembers as $member){
                            echo '<a title="'.$member->getTitle().'" href="'.$member->getHref().'">'.$this->itemPhoto($member,'thumb.icon').'</a>';
                        }
                    }
                    ?>    
                </div>
            </div>
        </div>
        <div class="user-links">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                    <?php if ($this->type == 'user'): ?>
                        <span class="user-links-friendship">
                            <?php
                            if ($this->viewer()->getIdentity())
                            {
                                echo $this->userFriendship($this->user);
                            }
                            ?>
                        </span>
                        <span class="user-links-friendship">
                            <?php
                            if ($this->viewer()->getIdentity() != $this->idMessage)
                            {
                                $url = $this->url(array('action' => 'compose', 'to' => $this->idMessage), 'messages_general');
                                echo '<a href="' . $url . '" style="background-image: url(application/modules/Messages/externals/images/send.png)" class="buttonlink">'.$this->translate('Send Message').'</a></li>';
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($this->type == 'group'): ?>
                        <span class="user-links-friendship">
                            <?php
                            if ($this->type == 'group')
                            {
                                switch($this->groupAction){
                                    case 'request': 
                                        $url = $this->url(array('controller'=>'member', 'action' => 'request', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/join.png)" class="buttonlink smoothbox">'.$this->translate('Request Membership').'</a></li>';
                                        break;
                                    case 'join':
                                        $url = $this->url(array('controller'=>'member', 'action' => 'join', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/join.png)" class="buttonlink smoothbox">'.$this->translate('Join Group').'</a></li>';
                                        break;
                                    case 'leave':
                                        $url = $this->url(array('controller'=>'member', 'action' => 'leave', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/leave.png)" class="buttonlink smoothbox">'.$this->translate('Leave Group').'</a></li>';
                                        break;
                                    case 'delete':
                                        $url = $this->url(array('controller'=>'group', 'action' => 'delete', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/delete.png)" class="buttonlink smoothbox">'.$this->translate('Delete Group').'</a></li>';
                                        break;
                                    case 'cancel':
                                        $url = $this->url(array('controller'=>'member', 'action' => 'cancel', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/cancel.png)" class="buttonlink smoothbox">'.$this->translate('Cancel Membership Request').'</a></li>';
                                        break;
                                    case 'accept-or-reject':
                                        $url = $this->url(array('controller'=>'member', 'action' => 'accept', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/accept.png)" class="buttonlink smoothbox">'.$this->translate('Accept Membership Request').'</a></li>';
                                        $url = $this->url(array('controller'=>'member', 'action' => 'reject', 'group_id' => $this->group->group_id), 'group_extended');
                                        echo '<a href="' . $url . '" style="background-image: url(application/modules/Group/externals/images/member/reject.png)" class="buttonlink smoothbox">'.$this->translate('Ignore Membership Request').'</a></li>';
                                        break;
                                }
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
        </div>
    </div>
    <div class="arrow">
        <img src="application/modules/Wall/externals/images/arrows.png"/>
    </div>
</div>
<script type="text/javascript">
    $$('ul#activity-feed .smoothbox').each(function(el){
        el.addEvent('click', function(e){
            e.stop();
            Smoothbox.open(el.get('href')); 
        });
    });
</script>