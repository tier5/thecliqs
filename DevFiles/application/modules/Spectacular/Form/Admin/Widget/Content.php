<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Widget_Content extends Engine_Form {

    public function init() {

        $this
                ->setAttrib('id', 'form-upload');
        $this->addElement('Radio', 'showImages', array(
            'label' => "Select the images that you want to show in this image rotator? (you can upload new images and manage existing ones from the 'Images' tab in the administration of Responsive Spectacular Theme)",
            'multiOptions' => array(
                1 => 'Show All Images.',
                0 => 'Select the images.'
            ),
            'value' => 1,
            'onclick' => 'showMultiCheckboxImageOptions()'
        ));

        $listImage = Engine_Api::_()->getItemTable('spectacular_image')->getImages(array('enabled' => 1));
        $listArray = array();
        foreach ($listImage->toArray() as $images) {
            $listArray[$images['image_id']] = $images['title'];
        }

        $this->addElement('MultiCheckbox', 'selectedImages', array(
            'multiOptions' => $listArray,
            'label' => 'Please select the images.',
                //'value' => 1,
        ));

        $this->addElement('Text', 'width', array(
            'label' => 'Enter width for the Landing Page Images (If left blank images will have 100% width)',
            'value' => '',
        ));

        $this->addElement('Text', 'height', array(
            'label' => 'Enter the height for Landing Page Images.',
            'value' => 583,
        ));

        $this->addElement('Text', 'speed', array(
            'label' => 'Enter the duration in milliseconds (ms) after which images in the landing page images rotator should rotate.',
            'value' => 5000,
        ));

        $this->addElement('Radio', 'order', array(
            'label' => 'How do you want the images in this rotator to rotate?',
            'multiOptions' => array(
                2 => 'Random Order',
                1 => 'Descending Order',
                0 => 'Ascending Order'
            ),
            'value' => 2,
        ));

        // Get available files
        $logoOptions = array('' => 'Text-only (No logo)');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach ($it as $file) {
            if ($file->isDot() || !$file->isFile())
                continue;
            $basename = basename($file->getFilename());
            if (!($pos = strrpos($basename, '.')))
                continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if (!in_array($ext, $imageExtensions))
                continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Radio', 'showLogo', array(
            'label' => "Do you want to display your website's logo on the top-left side of the images rotator?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
            'onclick' => 'showLogoOptions()'
        ));

        $this->addElement('Select', 'logo', array(
            'label' => 'Choose the site logo file (you can upload a new file from: "Layout" > "File & Media Manager")',
            'multiOptions' => $logoOptions,
        ));


        $this->addElement('Radio', 'spectacularBrowseMenus', array(
            // 'label' => 'Browse Menus',
            'label' => "Do you want to show the 'Browse' dropdown in the header? [If enabled, all the main menu links of your website will show up in the 'Browse' dropdown.]",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
            'onclick' => 'showBrowseMenusOptions()'
        ));

        $this->addElement('Text', 'max', array(
            'label' => "How many menus do you want to show under the 'Browse' drop-down?",
            'value' => 20
        ));

        $this->addElement('Radio', 'spectacularSignupLoginLink', array(
            // 'label' => 'Sign Up / Sign In Link',
            'label' => "Do you want to show Sign Up / Sign In links in the top header area?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));

        $this->addElement('Radio', 'spectacularFirstImprotantLink', array(
            //'label' => 'First Important link',
            'label' => "Do you want to show a button in the header area to display an important link of your website? [Configure the Title and URL for this button below.]",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
            'onclick' => 'showFirstLinksOptions()'
        ));

        $this->addElement('Text', 'spectacularFirstTitle', array(
            'label' => 'Header Button Title',
            'value' => 'Important Title & Link',
            'style' => 'width:350px;',
        ));

        $this->addElement('Text', 'spectacularFirstUrl', array(
            'label' => 'Header Button URL',
            'value' => '#',
            'style' => 'width:350px;',
        ));

        $this->addElement('Text', 'spectacularHtmlTitle', array(
            'label' => 'Enter the title that you want to display on this image rotator.',
            'value' => 'BRING PEOPLE TOGETHER',
            'style' => 'width:350px;',
        ));

        $this->addElement('Text', 'spectacularHtmlDescription', array(
            'label' => 'Enter the description that you want to display on this image rotator.',
            'value' => 'Create an event. Sell tickets online.',
            'style' => 'width:350px;',
        ));

        $this->addElement('Radio', 'spectacularHowItWorks', array(
            'label' => "Do you want to display an action button like 'Get Started', 'How It Works', etc on the image rotator? (You can configure this button from the administration of Spectacular Theme, and can also configure the slide-down content that comes after clicking of this button.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1
        ));

        $this->addElement('Radio', 'spectacularSignupLoginButton', array(
            'label' => "Do you want to show the Sign In and Sign Up buttons on this image rotator? [If enabled, they will get displayed at the bottom of the rotator.]",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));

        if (Engine_Api::_()->hasModuleBootstrap('sitecitycontent') && Engine_Api::_()->hasModuleBootstrap('siteadvsearch')) {
            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in the bottom part of this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search (from "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-events-plugin\'>Advanced Events Plugin</a>")',
                        1 => 'Advanced Search [Dependent on "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-events-plugin\'>Advanced Search Plugin</a>"] / Global Search',
                        0 => 'None'
                    ),
                    'escape' => false,
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in the bottom part of this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-search-plugin\'>Advanced Search Plugin</a>"] / Global Search',
                        0 => 'None'
                    ),
                    'escape' => false,
                    'value' => 1,
                ));
            }

            $this->addElement('Radio', 'showLocationBasedContent', array(
                'label' => 'Show results based on the location, saved in user’s browser cookie.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));

            $this->addElement('Radio', 'showLocationSearch', array(
                'label' => 'Do you want to enable location based searching?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));
        } else {

            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in the bottom part of this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search (from "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-events-plugin\'>Advanced Events Plugin</a>")',
                        1 => 'Advanced Search [Dependent on "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-search-plugin\'>Advanced Search Plugin</a>"] / Global Search',
                        0 => 'None'
                    ),
                    'escape' => false,
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in the bottom part of this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on "<a target=\'_blank\' href=\'http://www.socialengineaddons.com/socialengine-advanced-search-plugin\'>Advanced Search Plugin</a>"] / Global Search',
                        0 => 'None'
                    ),
                    'escape' => false,
                    'value' => 1,
                ));
            }
        }
    }

}
?>

<script type="text/javascript">
    var form = document.getElementById("form-upload");
    window.addEvent('domready', function () {
        showFirstLinksOptions();
        showLogoOptions();
        showBrowseMenusOptions();
        showMultiCheckboxImageOptions();
    });

    function showMultiCheckboxImageOptions() {
        if (form.elements["showImages"].value == 1) {
            $('selectedImages-wrapper').style.display = 'none';
        } else {
            $('selectedImages-wrapper').style.display = 'block';
        }
    }
    function showBrowseMenusOptions() {
        if (form.elements["spectacularBrowseMenus"].value == 1) {
            $('max-wrapper').style.display = 'block';
        } else {
            $('max-wrapper').style.display = 'none';
        }
    }

    function showLogoOptions() {
        if (form.elements["showLogo"].value == 1) {
            $('logo-wrapper').style.display = 'block';
        } else {
            $('logo-wrapper').style.display = 'none';
        }
    }
    function showFirstLinksOptions() {
        if (form.elements["spectacularFirstImprotantLink"].value == 1) {
            $('spectacularFirstTitle-wrapper').style.display = 'block';
            $('spectacularFirstUrl-wrapper').style.display = 'block';
        } else {
            $('spectacularFirstTitle-wrapper').style.display = 'none';
            $('spectacularFirstUrl-wrapper').style.display = 'none';
        }
    }

</script>