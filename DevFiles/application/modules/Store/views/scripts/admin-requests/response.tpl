<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: response.tpl  5/10/12 2:53 PM mt.uulu $
 * @author     Mirlan
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
<div class='store_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class="admin_home_right store_admin_response">
  <?php echo $this->htmlLink($this->url(array(
  'module'     => 'store',
  'controller' => 'requests',
  'action'     => 'index'), 'admin_default', true), $this->translate('Back'), array(
  'class' => 'buttonlink',
  'style' => 'background-image:url(application/modules/Core/externals/images/back.png)',
)) ?>
  <br/>
  <br/>
  <?php if (isset($this->form) && $this->request->status == 'waiting'): ?>
  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>
  <?php endif; ?>
</div>


<div class="admin_home_middle store-request-information">
  <div class="admin_home_news">
    <h3 class="sep">
      <span><?php echo $this->translate('Information'); ?></span>
    </h3>
    <ul>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('STORE_Store Name'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->page->__toString(); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <?php if (null != ($owner = $this->page->getOwner())): ?>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('STORE_Owner Name'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $owner->__toString(); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <?php endif; ?>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Status'); ?>
        </div>
        <div class="admin_home_news_info" style="font-weight: bold">
          <?php echo ucfirst($this->request->status); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Requested Amount'); ?>
        </div>
        <div class="admin_home_news_info store-price">
          <?php echo $this->toCurrency($this->request->amt); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Requested Date'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->timestamp($this->request->request_date); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Requested Message'); ?>
        </div>
        <div class="admin_home_news_info">
          <span class="admin_home_news_blurb">
            <?php echo Engine_String::strip_tags($this->request->request_message); ?>
          </span>
        </div>
      </li>

      <?php if ($this->request->response_date && $this->request->status != 'waiting'): ?>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Response Date'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->timestamp($this->request->response_date); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Response Message'); ?>
        </div>
        <div class="admin_home_news_info">
          <span class="admin_home_news_blurb">
            <?php echo nl2br($this->request->response_message); ?>
          </span>
        </div>
      </li>
      <?php endif; ?>
    </ul>

    <?php if (isset($this->gateway)): ?>
    <h3 class="sep">
      <span><?php echo $this->translate('Payment'); ?></span>
    </h3>
    <ul>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Member'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->user->__toString(); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Date'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->timestamp($this->order->payment_date) . ' (' . $this->locale()->toDateTime($this->order->payment_date) . ')'; ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Gateway'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->gateway->getTitle(); ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
      <li>
        <div class="admin_home_news_date">
          <?php echo $this->translate('Currency'); ?>
        </div>
        <div class="admin_home_news_info">
          <?php echo $this->order->currency; ?>
          <span class="admin_home_news_blurb"></span>
        </div>
      </li>
    </ul>
    <?php endif; ?>
  </div>
</div>
