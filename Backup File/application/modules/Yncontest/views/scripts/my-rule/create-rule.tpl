<?php echo $this->form->render($this);?>


<style type="text/css">

</style>
<script type = "text/javascript">

var cal_creationdate_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_enddate.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_enddate.navigate(cal_enddate.calendars[0], 'm', 1);
    cal_enddate.navigate(cal_enddate.calendars[0], 'm', -1);
  }
  var cal_enddate_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_creationdate.calendars[0].end = new Date( $('end_date-date').value );
    // redraw calendar
    cal_creationdate.navigate(cal_creationdate.calendars[0], 'm', 1);
    cal_creationdate.navigate(cal_creationdate.calendars[0], 'm', -1);
  }



window.addEvent('domready',function(){

	
		
});

</script>