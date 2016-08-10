<div id ="business-people-checkin-here">
    <div class="ybo_headline">
        <h3><?php echo $this->translate(array('%s person check in here', '%s people check in here', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></h3>
    </div>
    <div class="people-list">
        <ul>
            <?php foreach ($this->paginator as $user) : ?>
            <li class="people-item" title="<?php echo $user->getTitle()?>">
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array(
                    'class' => 'people-avatar',
                ))?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php if ($this->paginator->getTotalItemCount() > $this->limit) : ?>
    <a class="view-all" href="javascript:void(0);" onclick="viewAll()"><?php echo $this->translate('View all')?></a>
    <?php endif;?>
</div>

<script>
    function viewAll() {
        var url = '<?php echo $this->url(array('action' => 'get-people-checkin', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_specific', true)?>';
        new Request.JSON({
            url: url,
            onSuccess: function(responseJSON) {
                if (responseJSON.success) {
                    var users = responseJSON.json;
                    var div = new Element('div', {
                        'id' : 'people-checkin-full-list'
                    });
                    var ul = new Element('ul', {
                    });
                    for (var i = 0; i < users.length; i ++) {
                        var li = new Element('li', {
                            'class': 'people-item',
                            'title': users[i].title
                        });
                        var link = new Element('a', {
                            'class' : 'people-avatar',
                            'href': users[i].url,
                            'html': users[i].photo
                        }).inject(li);
                        li.inject(ul);
                    }
                    ul.inject(div);
                    var button = new Element('button', {
                        'onclick': 'parent.Smoothbox.close();',
                        'text' : '<?php echo $this->translate('Close')?>'
                    }).inject(div);
                    Smoothbox.open(div);                    
                }
            }
        }).send();
    }
</script>