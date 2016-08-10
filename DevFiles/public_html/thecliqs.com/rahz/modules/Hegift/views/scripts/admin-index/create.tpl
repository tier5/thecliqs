<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: create.tpl  04.02.12 12:47 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {

    var updateTextFields = window.updateTextFields = function() {
      var gift_element = document.getElementById("type");
      var photo_element = document.getElementById("photo-wrapper");
      var audio_element = document.getElementById("audio-wrapper");
      var video_element = document.getElementById("video-wrapper");
      var upload_element = document.getElementById("upload-wrapper");
      upload_element.style.display = "none";

      if (gift_element.value == 0) {
        photo_element.style.display = "none";
        audio_element.style.display = "none";
        video_element.style.display = "none";
        return ;
      } else if (gift_element.value == 1) {
        photo_element.style.display = "block";
        audio_element.style.display = "none";
        video_element.style.display = "none";
        return ;
      } else if (gift_element.value == 2) {
        photo_element.style.display = "none";
        audio_element.style.display = "block";
        video_element.style.display = "none";
        return ;
      } else if (gift_element.value == 3) {
        photo_element.style.display = "none";
        audio_element.style.display = "none";
        video_element.style.display = "block";
        return ;
      }
    }

    var updateAmountField = window.updateAmountField = function() {
      var limit_element = document.getElementById("limit");
      var amount_element = document.getElementById("amount-wrapper");
      amount_element.style.display = "none";

      if (limit_element.value == 0) {
        amount_element.style.display = "none";
        return ;
      } else if (limit_element.value == 1) {
        amount_element.style.display = "block";
        return ;
      }
    }

    updateTextFields();
    updateAmountField();
  });
</script>


<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMININDEX_CREATE_DESCRIPTION") ?>
</p>
<br />

<div class="settings">
  <?php echo $this->form->render($this)?>
</div>

<script type="text/javascript">
  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
</script>
