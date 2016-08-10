<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
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
<?php echo $this->form->render($this);?>
</div>
</div>
<script type="text/javascript">
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
var cal_display_time_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_start_time.calendars[0].start = new Date( $('display_time-date').value );
    // redraw calendar
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', 1);
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', -1);
}
var cal_start_time_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_display_time.calendars[0].start = new Date( $('start_time-date').value );
    // redraw calendar
    cal_display_time.navigate(cal_display_time.calendars[0], 'm', 1);
    cal_display_time.navigate(cal_display_time.calendars[0], 'm', -1);
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
#ynauctions_create
{
    width: 85%;
}
#ynauctions_create #check-wrapper {
    float: left;
    padding-right: 5px;
}
#ynauctions_create #check-label {
    display: none;
}
#ynauctions_create #buttons-element .form-wrapper
{
    border: none;
    padding: 0px;
    min-width: 110px;
}
#ynauctions_create .form-element
{
    float: none;
}
#ynauctions_create .form-wrapper
{
    border: none;     
}
</style>
