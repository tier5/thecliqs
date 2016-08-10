<?php echo $this->form->render($this);?>



 <script type="text/javascript">
  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_start_date.calendars[0].end = new Date( $('end_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }
</script>