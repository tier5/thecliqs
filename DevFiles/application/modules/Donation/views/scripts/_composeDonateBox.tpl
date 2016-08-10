<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 29.08.12
 * Time: 11:55
 * To change this template use File | Settings | File Templates.
 */
?>

<?php
$background = "background: url(" . $this->base_url . $this->baseUrl() . "/application/modules/Like/externals/images/like_button.png) no-repeat left 0px;";

$donate_button_bg = "background: url(" . $this->base_url . $this->baseUrl() . "/application/modules/Donation/externals/images/donate_button.gif) no-repeat left 0px;";
?>

<style type="text/css">
  .donate_box_container {
    background: none repeat scroll 0 0 white;
    border-color: #AAAAAA #AAAAAA #AAAAAA;
    border-radius: 0 0 0 0;
    border-style: solid;
    border-width: 1px;
    font-family: tahoma, arial, verdana, sans-serif;
    overflow: hidden;
    text-decoration: none;
    width: 240px;
  }

  .donate_box_container .donate_box_header {
    background-color: #EDEFF4;
    border-bottom: 1px solid #C6CEDD;
    color: #1C2A47;
    cursor: default;
    direction: ltr;
    font-size: 13.3333px;
    font-style: normal;
    font-weight: bold;
    letter-spacing: normal;
    line-height: 16px;
    padding: 8px 10px 7px;
    text-align: left;
    text-transform: none;
    vertical-align: baseline;
    word-spacing: normal;
  }

  .donate_box_container .donate_box_header .header_text {
    display: block;
    float: left;
    font-family: tahoma, arial, verdana, sans-serif;
  }

  .donate_box_container .donate_box_header .header_image {
    float: right;
    display: block;
  }

  .donate_box_container .donate_box_content {
    padding: 5px;
  }

  .donate_box_container .donate_box_content .donate_box_info {
    margin: 5px;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_left {
    float: left;
    padding: 5px;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_left .donate_box_photo a {
    font-weight: bold;
    text-decoration: none;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_right {
    float: left;
    margin-top: 5px;
    padding: 5px;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_right .donate_box_details a {
    font-family: tahoma, arial, verdana, sans-serif;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_right .like_button_container {
    -moz-box-shadow: 0 0 1px 0 #888888;
    -webkit-box-shadow: #888 0px 0px 1px 0px;
    float: left;
    position: relative;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_right .like_button_container > a {
    border-color: #999999 #999999 #888888;
    border-style: solid;
    border-width: 1px;
    color: #333333;
    cursor: pointer;
    display: block;
    float: left;
    padding: 4px 5px;
    text-decoration: none;
  }

  .donate_box_container .donate_box_content .donate_box_info .donate_box_right .like_button_container > a > span {
    padding-left: 16px;
    font-weight: bold;
    text-decoration: none;
    font-family: 'lucida grande', tahoma, verdana, arial, sans-serif;
    font-size: 11px;
    color: #748AB7;
  }

  .donate_box_container .donate_box_content .donate_button_container {
    -moz-box-shadow: 0 0 1px 0 #888888;
    -webkit-box-shadow: #888 0px 0px 1px 0px;
    float: right;
    position: relative;
    padding: 5px;
  }

  .donate_box_container .donate_box_content .donate_button_container > a {
    border-color: #999999 #999999 #888888;
    color: #333333;
    cursor: pointer;
    display: block;
    float: left;
    padding: 8px 5px;
    text-decoration: none;
  }

  .donate_box_desc{
    border-top: 1px solid #D8DFEA;
    font-family: tahoma,arial,verdana, sans-serif;
    font-size: 11px;
    padding: 5px 0 4px 5px;
  }

  .supporters{
    padding: 5px;
  }

  .supporters .supporter{
    float: left;
    padding: 2px;
    width: 50px;
  }

</style>

<div id="donate_box_container" class="donate_box_container">
    <div class="donate_box_header">
    <span class="header_text">
      <?php echo $this->translate("like_Find us on"); ?> <?php echo $this->htmlLink($this->base_url . $this->baseUrl(), $this->layout()->siteinfo['title'], array('target' => '_blank', 'style' => 'text-decoration:none;')); ?>
    </span>

        <div style="clear: both;"></div>
    </div>
    <div class="donate_box_content">
        <div class="donate_box_info">
            <div class="donate_box_left">
                <div class="donate_box_photo">
                  <?php echo $this->htmlLink($this->donation->getHref(), $this->itemPhoto($this->donation, 'thumb.icon'), array('target' => '_blank')); ?>
                </div>
            </div>
            <div class="donate_box_right">
                <div class="donate_box_details">
                  <?php echo $this->htmlLink($this->donation->getHref(), $this->string()->truncate($this->donation->getTitle(), 17), array('target' => '_blank')); ?>
                </div>
                <div style="clear: both;"></div>
                <?php if($this->like_button): ?>
                <div class="like_button_container">
                    <a id="_<?php echo $this->donation->getGuid(); ?>"
                       style="background:transparent url(<?php echo $this->baseUrl(); ?>/application/modules/Like/externals/images/like_button_bg.png) repeat scroll 0 0;"
                       class="like_button_link <?php echo $this->actionName; ?>"
                       href="<?php echo $this->donation->getHref(); ?>" target="_blank">
						  <span class="like_button" style="<?php echo (isset($background)) ? $background : ''; ?>">
							  <?php echo $this->translate('like_Like'); ?>
						  </span>
                    </a>
                </div>

                <div style="clear: both;"></div>
                <?php endif; ?>
            </div>
        </div>
        <div style="clear: both;"></div>
        <?php if($this->donate_button): ?>
        <div class="donate_button_container">
            <a href="<?php echo $this->url(array('object' => $this->donation->getType(), 'object_id' => $this->donation->getIdentity()), 'donation_donate', true); ?>"
               target="_blank">
             <img src="<?php echo $this->base_url . $this->baseUrl() . '/application/modules/Donation/externals/images/donate_button.gif'?>" alt="">
            </a>
        </div>
        <div style="clear: both;"></div>
        <?php endif; ?>
        <?php if($this->supporters_count > 0 && $this->show_supporters == 1): ?>
          <div class="donate_box_desc">
            <?php
              echo $this->translate(array("%s supporter:", "%s supporters:", $this->supporters_count), $this->locale()->toNumber($this->supporters_count));
            ?>
          </div>
          <div class="supporters">
            <?php foreach($this->supporters as $item): ?>
              <?php if(null != $supporter = Engine_Api::_()->getItem('user',$item['user_id'])): ?>
              <div class="supporter">
                  <div class="l"><?php echo $this->htmlLink($supporter->getHref(), $this->likePhoto($supporter, 'thumb.icon', '', array('width' => '48px', 'height' => '48px', 'style' => 'text-decoration:none;border:1px solid #DDDDDD;')), array('target' => '_blank', 'border' => 0));  ?></div>
                  <div class="r">
                    <?php echo $this->htmlLink($supporter->getHref(), $supporter->getTitle(), array('target' => '_blank', 'style' => 'text-decoration:none;color: #808080; font-size: 9px; padding-top: 2px; font-family: tahoma,arial,verdana,sans-serif; text-align: center; white-space: normal; display: block; width: 50px; height: 22px; line-height:1;')); ?>
                  </div>
                  <div style="clear: both;"></div>
              </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
    </div>
</div>