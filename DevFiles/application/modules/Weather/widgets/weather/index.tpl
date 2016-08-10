<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-12-18 00:25 ermek $
 * @author     Ermek
 */
?>

<?php
  $widget_uniq_key = uniqid('weather_');
?>

<script type="text/javascript">
  var edit_weather_location_url = '<?php echo $this->url(array('module' => 'weather', 'controller' => 'index', 'action' => 'edit-location'), 'default'); ?>';

  function show_edit_location_box(node)
  {
    node.blur();

    var $container = $(node).getParent('.weather_cont');
    Smoothbox.open($container.getElement('.weather_edit_location_box'), {mode: 'Inline', width: 350, height: 40});
  }

  function edit_weather_location(node, widget_key)
  {
    var $form = $(node).getParent('.weather_edit_location_box');
    var $weather_box = $('weather_' + widget_key).getElement('.weather_box');
    var $loading_box = $('weather_' + widget_key).getElement('.weather_loading');
    var $location_input = $('weather_' + widget_key).getElement('.weather_location_input');

    var location = $form.getElement('.weather_location_input').value;
    var object_type = $form.getElement('input[name="object_type"]').value;
    var object_id = $form.getElement('input[name="object_id"]').value;

    $weather_box.addClass('display_none');
    $loading_box.removeClass('display_none');

    en4.core.request.send(new Request.JSON({
      url: edit_weather_location_url,
      data: {format: 'json', location: location, object_type: object_type, object_id:object_id},
      onSuccess: function(response){
        Smoothbox.close();

        if (response && response.error) {
          he_show_message(response.message, 'error');
        } else {
          $weather_box.set('html', response.html);
          $location_input.value = response.weather.location;
        }

        $weather_box.removeClass('display_none');
        $loading_box.addClass('display_none');
      }
    }));
  };
</script>

<div class="weather_cont he_active_theme_<?php echo $this->activeTheme(); ?>" id="weather_<?php echo $widget_uniq_key; ?>">

  <div class="weather_box">
    <?php echo $this->render('_weather.tpl'); ?>
  </div>

  <div class="weather_loading display_none"></div>

  <div class="display_none">
    <div class="weather_edit_location_box">
      <div class="weather_edit_location_desc"><?php echo $this->translate("WEATHER_EDIT_LOCATION_DESC"); ?></div>
      <div class="weather_edit_location_desc">
        <input name="weather_location" class="text weather_location_input" value="<?php echo $this->weather['location'] ?>"/>
        <input type="hidden" name="object_type" value="<?php echo $this->object_type; ?>"/>
        <input type="hidden" name="object_id" value="<?php echo $this->object_id; ?>"/>
      </div>

      <button type="submit" name="submit" onclick="edit_weather_location(this, '<?php echo $widget_uniq_key; ?>');"><?php echo $this->translate('Save'); ?></button>&nbsp;
      <button type="submit" name="submit" onclick="Smoothbox.close();"><?php echo $this->translate('Cancel'); ?></button>
    </div>
  </div>

</div>