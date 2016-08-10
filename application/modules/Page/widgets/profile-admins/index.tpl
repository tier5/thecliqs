<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */

$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Page/externals/scripts/pagination.js');
?>

<script type="text/javascript">
	var internalTips = null;
	en4.core.runonce.add(function() {
		var options = {
			url: '<?php echo $this->url( array("action" => "show-matches"), "like_default" ); ?>',
			delay: 300,
			onShow: function(tip, element) {
				var miniTipsOptions = {
					'htmlElement': '.he-hint-text',
					'delay': 1,
					'className': 'he-tip-mini',
					'id': 'he-mini-tool-tip-id',
					'ajax': false,
					'visibleOnHover': false
				};

				internalTips = new HETips($$('.he-hint-tip-links'), miniTipsOptions);
				Smoothbox.bind();
			}
		};

		var $thumbs = $$('.admin_profile_thumb');
		var $admins_tips = new HETips($thumbs, options);
	});
</script>

<div class="page_team_list">

  <div id="widget_pagination_up_id" class="widget_pagination_up">
    <img id="widget_up_img" class="hidden" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Page/externals/images/up.png">
    <img id="widget_up_loading_img" class="hidden" src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Core/externals/images/loading.gif">
  </div>

  <ul class="page_team_content" id="page_team_content_id">
    <?php foreach ($this->admins as $admin): ?>
    <li class="page_team_list_item">
      <div class="member_photo">
        <?php echo $this->htmlLink(
        $admin->getHref(),
        $this->itemPhoto($admin, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_user')),
        array(
          'class' => 'admin_profile_thumb',
        )
      ); ?>
      </div>

      <div class="member_info">

        <?php echo $this->htmlLink($admin->getHref(), $this->string()->truncate($admin->getTitle(), 15, '...'), array('class' => 'member_title', 'title' => $admin->getTitle())); ?>

        <div class="member_type">

          <?php
          if($admin->title)
            echo $admin->title ;
          elseif( $admin->type == 'ADMIN' )
            echo $this->translate('Admin');
          else
            echo $this->translate('Employer');
          ?>
        </div>

      </div>
    </li>
    <?php endforeach; ?>
  </ul>


  <div id="widget_pagination_down_id" class="widget_pagination_down">
    <img id="widget_down_img" src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Page/externals/images/down.png">
    <img id="widget_down_loading_img" class="hidden" src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Core/externals/images/loading.gif">
  </div>

</div>

<?php if($this->firstRequest) : ?>
<script type="text/javascript">
  window.addEvent('domready', function(){
    new WidgetPagination({
        ajaxUrl: '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity, 'page_id' => $this->page->getIdentity()), 'page_widget', true) ?>',
        totalPage: "<?php echo $this->totalPages;?>",
        perPage: "<?php echo $this->perPage;?>",
        nextElement: $('widget_down_img'),
        previousElement: $('widget_up_img'),
        nextLoadingImg: $('widget_down_loading_img'),
        previousLoadingImg: $('widget_up_loading_img'),
        items: '.page_team_list_item'
    }

    );

    $('widget_pagination_down_id').addEvents({
      mouseenter: function(){
        $('widget_down_img').src = '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Page/externals/images/down_hover.png';
      },

      mouseleave: function(){
        $('widget_down_img').src = '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Page/externals/images/down.png';
      }

    });

    $('widget_pagination_up_id').addEvents({
      mouseenter: function(){
        $('widget_up_img').src = '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Page/externals/images/up_hover.png';
      },
      mouseleave: function(){
        $('widget_up_img').src = '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Page/externals/images/up.png';
      }
    });
  });

</script>
<?php endif;?>