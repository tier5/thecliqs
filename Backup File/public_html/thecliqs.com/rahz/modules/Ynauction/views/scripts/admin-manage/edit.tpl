<h2><?php echo $this->translate("Auction Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>


<div class='clear'>
  <div class='settings'>
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form groupbuy_browse_filters">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
       <p>
       <?php echo $this->translate($this->form->getDescription()) ?>
       </p>
      <div class="form-elements">
        <?php echo @$this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
        <?php echo $this->form->cat_id; ?>
        <?php echo $this->form->cat1_id; ?>
        <?php echo $this->form->title; ?>
        <?php echo $this->form->location_id; ?>
        <?php echo $this->form->currency_symbol; ?>
        <?php echo $this->form->price; ?>
        <?php echo $this->form->starting_bidprice; ?>
        <?php echo $this->form->minimum_increment; ?>
        <?php echo $this->form->maximum_increment; ?>
        <?php echo $this->form->start_time; ?>
        <?php echo $this->form->end_time; ?>
        <?php echo $this->form->description; ?>
        <?php echo $this->form->description1; ?>
        <?php echo $this->form->getSubForm('fields'); ?>
        <?php if($this->form->auth_view)echo $this->form->auth_view; ?>
        <?php if($this->form->auth_comment)echo $this->form->auth_comment; ?>
        <?php echo $this->form->shipping_delivery; ?> 
        <?php echo $this->form->local_only; ?> 
        <?php echo $this->form->international; ?> 
        <?php echo $this->form->payment_method; ?> 
     <?php if(Count($this->paginator) > 0): ?>
      <?php echo $this->form->deal_id; ?>
      <ul class='ynauction_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <div class="ynauction_editphotos_photo" style="float: left; margin-right: 15px;;">
              <?php echo $this->itemPhoto($photo, 'thumb.profile')  ?>
            </div>
            <div class="ynauction_editphotos_info">
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class="ynauction_editphotos_cover">
                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" 
                <?php if( $this->product->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
              </div>
              <div class="ynauction_editphotos_label">
                <label><?php echo $this->translate('Main Photo');?></label>
              </div>
            </div>
            <br/>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php echo $this->form->submit->render(); ?>
       <?php echo $this->form->cancel; ?>
      <?php else: ?>
      <div class="form-wrapper">
      <div class="form-label" id="buttons-label">&nbsp;</div>
      <?php echo $this->form->submit->render(); ?>
       <?php echo $this->form->cancel; ?>
       </div>
      <?php endif; ?>
        </div>
      
    </div>
  </div>
</form>
</div>
</div>
<script type="text/javascript">
if($('cat_id').selectedIndex <= 0)
	$('cat1_id-wrapper').hide();
function subCategories() {
  if ($('cat_id').selectedIndex > 0) 
  { 
	var cat_id = $('cat_id').options[$('cat_id').selectedIndex].value ;
	document.getElementById('cat1_id').innerHTML = '<img src="./application/modules/Ynauction/externals/images/ajax-loader.gif"/>';
    var makeRequest = new Request(
			{
				url: "ynauction/index/subcategories/cat_id/"+cat_id,
				onComplete: function (respone){	
				document.getElementById('cat1_id-element').innerHTML = '<select title="Sub category which product belongs to" id="cat1_id" name="cat1_id"><option value="0" label="" selected= "selected"></option>' + respone + '</select>';
				if(respone != "")  
				  	$('cat1_id-wrapper').show();
				else
					$('cat1_id-wrapper').hide();
				}
			}
	)
	makeRequest.send();
  } else {
    $('cat1_id-wrapper').hide();
  }
}
function removeSubmit()
{
   $('submit-wrapper').hide(); 
}
var cal_start_time_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_time.calendars[0].start = new Date( $('start_time-date').value );
    // redraw calendar
    cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
    cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);
}
var cal_end_time_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_start_time.calendars[0].end = new Date( $('end_time-date').value );
    // redraw calendar
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', 1);
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', -1);
}
</script>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
ul.ynauction_editphotos .ynauction_editphotos_photo
{
  float: left;
  margin-right: 15px;
  overflow: hidden;
}
ul.ynauction_editphotos .ynauction_editphotos_photo img
{
  display: block;
  margin: 3px;
  width: 150px;
  max-height: 150px;
}
ul.ynauction_editphotos .ynauction_editphotos_info
{
  overflow: hidden;
  height: 140px;
}
ul.ynauction_editphotos .ynauction_editphotos_title_input input
{
  margin: 2px 0px 6px 0px;
  width: 25em;
}
ul.ynauction_editphotos .ynauction_editphotos_caption_input text
{
  margin: 2px 0px 6px 0px;
  width: 25em;
  height: 3em;
  min-height: 0px;
}
ul.ynauction_editphotos .ynauction_editphotos_label
{
  float: left;
}
ul.ynauction_editphotos .ynauction_editphotos_cover
{
  float: left;
}
ul.ynauction_editphotos .photo-delete-wrapper
{
  float: left;
  margin-right: 15px;
}
</style>
