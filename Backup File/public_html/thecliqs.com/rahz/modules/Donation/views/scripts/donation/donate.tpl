<?php $this->headTranslate(array('DONATION_Anonym')); ?>

<script type="text/javascript">
  var anon = false;
  var name =  en4.core.language.translate('DONATION_Anonym');
  var email = '';
  var amount = null;
  var donation_text = '';
  var min_amount = <?php echo $this->donation->min_amount; ?>;
  var object_id = <?php echo $this->donation->getIdentity(); ?>;
  var checkout = function (gateway_id) {

    var self = this;
    if ($('anon')) {
      self.anon = $('anon').checked;
      if(self.anon){
        if($('name')){
        self.name = $('name').value;
        }
      }
      if($('email')){
        self.email = $('email').value;
      }
    }

    if(Donate.selected_id && Donate.selected_id!='own'){
      self.amount = Donate.selected_id;
    }
    else{
      var $active = $('own');
      if($('inputOwn') && $active.hasClass('active')){
        self.amount = $('inputOwn').value;
      }
    }
    self.donation_text = $('donation_text').value;

    $('submit').set('disabled',true);

    new Request.JSON({
      'method':'post',
      'data':{
        'format':'json',
        'anon':self.anon,
        'name':self.name,
        'email':self.email,
        'amount':self.amount,
        'min_amount': self.min_amount,
        'donation_text': self.donation_text,
        'object_id': self.object_id,
        'gateway_id':gateway_id
      },
      'url':"<?php echo $this->url(array(
        'action'     => 'checkout'), 'donation_donate', true); ?>",
      'onSuccess':function ($response) {
        if ($response.status) {
          location.href = $response.link;
        } else if ($response.errorMessage) {
          he_show_message($response.errorMessage, 'error');
        }
        window.setTimeout(function(){
          $('submit').set('disabled',false);
        },3000);

      }
    }).send();

  };
</script>

<div class="donation_profile_details he_like_cont">
  <div class="layout_donation_profile_photo">
    <div id="donation_photo">
      <?php echo $this->htmlLink($this->donation->getHref(),$this->itemPhoto($this->donation, 'thumb.icon')); ?>
    </div>
  </div>
  <div class="title"><?php echo $this->htmlLink($this->donation->getHref(),$this->donation->getTitle()); ?></div>
  <div class="details">
    <?php
    if ($this->donation->type == 'charity') {
      echo $this->translate("DONATION_This charity raised %s.", $this->locale()->toCurrency((double)$this->donation->raised_sum, $this->currency));
    }
    else {
      echo $this->translate('DONATION_This project needs to raise a further %1$s to reach its funding target of %2$s',
        $this->locale()->toCurrency((double)$this->donation->target_sum - $this->donation->raised_sum, $this->currency),
        $this->locale()->toCurrency((double)$this->donation->target_sum, $this->currency));
    }
    ?>
  </div>
</div>
<div class="donation_select">
  <?php if (count($this->predefine_list)): ?>
  <div class="title"><?php echo $this->translate('DONATION_Choose the amount you would like to donate'); ?> </div>
  <ul>
    <?php foreach ($this->predefine_list as $list): ?>
    <li id="<?php echo $list; ?>">
      <span><?php echo $this->locale()->toCurrency((double)$list, $this->currency);  ?></span></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php if (!$this->donation->can_choose_amount): ?>
    <?php if (count($this->predefine_list)): ?>
      <span><?php echo $this->translate('DONATION_Or choose your own amount');?></span>
    <?php else: ?>
      <span><?php echo $this->translate('DONATION_Amount');?></span>
    <?php endif; ?>
    <ul>
      <li id="own" class="own"><input id="inputOwn" type="text" size="5"/></li>
    </ul>
  <?php endif; ?>
</div>
<br/>
<br/>
<div class="for_message">
  <label for="donation_text"><?php echo $this->translate('DONATION_Your message'); ?></label>
  <textarea id="donation_text"></textarea>
</div>
<?php if(!$this->viewer->getIdentity()): ?>
   <div class="for_guests">
     <label for="name"><?php echo $this->translate('DONATION_Full Name');?></label>
     <input id="name" type="text" value="<?php echo $this->translate('DONATION_Anonym'); ?>"/><br/>
     <label for="email"><?php echo $this->translate('DONATION_Email'); ?></label>
     <input id="email" type="text"/>
   </div>
  <br/>
  <br/>
<?php endif; ?>
<input id="anon" type="checkbox">
<label for="anon" class="anon"><?php echo $this->translate('DONATION_I would like to donate anonymously'); ?></label>
<input id = "submit" class="btn" type="image"
       src="<?php echo $this->baseUrl() . '/application/modules/Donation/externals/images/buttons/paypal.png'; ?>"
       onclick="checkout(2);">

<script type="text/javascript">
  en4.core.runonce.add(function () {
    Donate.init(<?php echo $this->viewer->getIdentity(); ?>);
  });
</script>

