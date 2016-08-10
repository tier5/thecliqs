<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _weather.tpl 2010-12-21 17:53 ermek $
 * @author     Ermek
 */
?>
<?php  if (empty($this->weather['information'])) : ?>
  <div class="weather_city">
    <?php if ($this->can_edit_location) : ?>
      <a href="javascript://" onclick="show_edit_location_box(this);" class="edit_location_data_btn" title="<?php echo $this->translate('weather_edit location'); ?>"></a>
    <?php endif; ?>
    <?php echo ($this->weather['location']) ?  $this->weather['location'] : $this->translate('weather_No location'); ?>
  </div>
  <div class="weather_forecast_body"><?php echo $this->translate('weather_No data found'); ?></div>

<?php else : ?>

  <div class="weather_city">
    <?php if ($this->can_edit_location) : ?>
      <a href="javascript://" onclick="show_edit_location_box(this);" class="edit_location_data_btn" title="<?php echo $this->translate('weather_edit location'); ?>"></a>
    <?php endif; ?>
    <?php echo $this->weather['information']['city']; ?>
  </div>

  <div class="weather_today_weather">
    <div class="weather_forecast_title"><?php echo $this->translate("weather_Today's weather"); ?></div>
    <div class="weather_forecast_body">
      <div class="weather_icon float_right_rtl"><img src="application/modules/Weather/externals/images/yahoo_code/<?php echo strtolower(str_replace(array(' ', '/'), '_', $this->weather['current']['code']));?>.gif" alt="weather"/></div>
      <div class="condition float_right_rtl">
          <?php
            $weather=0; $unit_system = ($this->unit_system == 'us') ? $this->translate('weather_F') : $this->translate('weather_C');
            $weather = $this->weather['current']['temp'];
          ?>
          <b><?php echo $weather; ?>&deg; <?php echo $unit_system; ?></b>
          <span>
            <?php echo $this->translate(strtolower($this->weather['current']['text'])) ?>
          </span>
          <span>
            <?php
              $wind_direction = $this->translate($this->weather['current']['wind_direction']);
              $wind_speed = $this->weather['current']['wind_speed'];
              if( !$wind_speed or $wind_speed < 0 ) {
                $wind_speed = 0;
              }
              $unit_speed = ($this->unit_system == 'us') ? 'mph' : 'm/s';
              $wind_condition = $this->translate('Wind: %1$s at %2$s ' . $unit_speed, $wind_direction, $wind_speed);
              $humidity = $this->translate('Humidity: ') . $this->weather['current']['humidity'] . '%';
              echo ($wind_condition) ? $humidity : '';
            ?>
          </span>
          <span><?php echo $wind_condition ?></span>
          <div class="clr"></div>
        </div>
        <div class="clr"></div>
    </div>
  </div>

  <div class="weather_forecast_weather">
    <div class="weather_forecast_title"><?php echo $this->translate("weather_Forecast"); ?></div>

    <?php foreach ($this->weather['forecast_list'] as $forecast) : ?>
<?php
$forecast_condition = str_replace(array('AM ', 'PM '), '', $forecast['text']);

if(strpos($forecast_condition, '/')){
  $i = 0;
  $conditions = explode('/', $forecast_condition);
  foreach($conditions as $condition) {
    $forecast_condition = ($i > 0) ? $forecast_condition . '/' . $this->translate(strtolower($condition)) : '' . $this->translate(strtolower($condition));
    $i++;
  }
} else {
   $forecast_condition = $this->translate( $forecast_condition == 'Clear' ? 'WEATHER_' . strtolower($forecast_condition) : strtolower($forecast_condition) );
}
?>
    <div class="weather_forecast_body">
      <div class="weather_icon float_right_rtl"><img src="application/modules/Weather/externals/images/yahoo_code/<?php echo $forecast['code'];?>.gif" alt="weather" title="<?php echo $forecast_condition;?>"/></div>
      <div class="condition float_right_rtl">
        <div class="day_of_week"><?php echo $this->translate('WEATHER_' . $forecast['day']) ?></div>
        <span style="display:block">
          <?php
            $high = $forecast['high'];
            $low = $forecast['low'];
          ?>
          <?php echo $low; ?>&deg;<?php echo $unit_system; ?> | <?php echo $high; ?>&deg;<?php echo $unit_system; ?>
        </span>
        <span>
    <?php
      echo $forecast_condition;
    ?>
        </span>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </div>
    <?php endforeach ?>
  </div>

<?php endif; ?>