<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BannerContent.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Widget_BannerContent extends Engine_Form {

    public function init() {

        $this
                ->setAttrib('id', 'form-upload');

        $this->addElement('Radio', 'showBanners', array(
            'label' => "Select the banners which do you want to show in this widget?",
            'multiOptions' => array(
                1 => 'Show All Banners.',
                0 => 'Select the Banners.'
            ),
            'value' => 1,
            'onclick' => 'showMultiCheckboxBannerOptions()'
        ));

        $listBanner = Engine_Api::_()->getItemTable('spectacular_banner')->getBanners(array('enabled' => 1));
        $listArray = array();
        foreach ($listBanner->toArray() as $banners) {
            $listArray[$banners['banner_id']] = $banners['title'];
        }

        $this->addElement('MultiCheckbox', 'selectedBanners', array(
            'multiOptions' => $listArray,
            'label' => 'Please select the banner.',
        ));

        $this->addElement('Text', 'width', array(
            'label' => 'Enter width for the Banners (If left blank banners will have 100% width)',
            'value' => '',
        ));

        $this->addElement('Text', 'height', array(
            'label' => 'Enter the height for Banners.',
            'value' => 200,
        ));

        $this->addElement('Text', 'speed', array(
            'label' => 'Enter the duration in milliseconds (ms) after which images in the banner should rotate.',
            'value' => 5000,
        ));

        $this->addElement('Radio', 'order', array(
            'label' => 'How do you want the images in this banner to rotate?',
            'multiOptions' => array(
                2 => 'Random Order',
                1 => 'Descending Order',
                0 => 'Ascending Order'
            ),
            'value' => 2,
        ));

        $this->addElement('Text', 'spectacularHtmlTitle', array(
            'label' => 'Enter the title that you want to display on this banner.',
            'value' => "Events & Groups that you'd love"
        ));

        $this->addElement('Text', 'spectacularHtmlDescription', array(
            'label' => 'Enter the description that you want to display on this banner.',
            'value' => 'Discover new events in your town, interact with other party-goers and share the fun!'
        ));
    }

}
?>

<script type="text/javascript">
    var form = document.getElementById("form-upload");
    window.addEvent('domready', function () {
        showMultiCheckboxBannerOptions();
    });

    function showMultiCheckboxBannerOptions() {
        if (form.elements["showBanners"].value == 1) {
            $('selectedBanners-wrapper').style.display = 'none';
        } else {
            $('selectedBanners-wrapper').style.display = 'block';
        }
    }
</script>