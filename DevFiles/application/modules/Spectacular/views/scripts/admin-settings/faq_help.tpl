<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
<?php if ($this->faq_id): ?>
        window.addEvent('domready', function () {
            faq_show('<?php echo $this->faq_id; ?>');
        });
<?php endif; ?>
</script>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo "I want to change the color scheme of this theme. Is it possible with this Theme?"; ?></a>
            <div class='faq' style='display: none;' id='faq_1'>
                <?php echo "A - Yes to do so, please go to the 'Theme Customization' tab from the Admin panel of this theme. Now choose color scheme for your theme by selecting the given radio buttons. You can also select the 'Custom Colors' option to customize your theme according to your site. By this you can choose color according to your site from various available options."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo "My site is not coming fine after installing this theme. What might be the problem?"; ?></a>
            <div class='faq' style='display: none;' id='faq_2'>
                <?php
                $url = $this->url(array('module' => 'spectacular', 'controller' => 'settings', 'action' => 'place-customization-file'), 'admin_default', true);
                ?>
                <?php
                echo "A - It might be possible that Spectacular Theme directory is missing ‘customization.css’ file.  For resolving this, you need to create customization.css file over here: '/application/themes/spectacular/'. Please <a href='javascript:void(0)' onclick='Smoothbox.open(\"$url\");'>click here</a> if you want to create ‘customization.css’ file.";
                ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo "Can I add my custom CSS in this theme? If yes then how I can add it so that my changes do not get lost in case of theme up-gradations?"; ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo "A - Yes, you can add your custom CSS in this theme. We have created a new file customization.css for you in this theme, which enables you to add your customization changes for your website, you can write your CSS code over here and get your site look just the way you want it to. It will also not get lost in case of theme up-gradation.You can find this file by following the below steps :<br />
- Go to the 'Layout' >> 'Theme Editor' section from the Admin panel of your site.<br />
- Now choose 'customization.css' from the ‘editing file’ dropdown. You may add the changes here which you want to do for your website.<br />
[If you are unable to find this file in the ‘editing file’ dropdown then please read the above FAQ.]"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo "I do not want mini menu to be visible on the landing page for the logged out user. Can I do so?"; ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php echo "A - Yes, please go to the “Layout Editor” >> “Site Header”, Now configure the settings of Advanced Mini Menu by clicking on the edit link. Select “No” for “Do you want to show this widget to non-logged-in users?” setting. [Dependent on Advanced Menus Plugin - Interactive and Attractive Navigation]"; ?>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_44');"><?php echo "When any client wants advanced main menu widget in header instead of Spectacular main menu then please do the below changes?"; ?></a>
            <div class='faq' style='display: none;' id='faq_44'>
                <?php echo "1) Open layout editor >> site header. Then remove “Spectacular main menu widget” and “ Spectacular navigation tabs widget”. After that place “advanced main menu” widget after “advanced mini menu widget”"; ?><br />
                <?php echo "2) Open theme editor >> customization.css . Then add the below code in this file:";?><br />
                <?php echo 'div#global_wrapper {padding-top: 105px;}<br />
div.headline { background-color: transparent;}<br />
div.headline h2, div.headline h2 a, div.headline .tabs > ul > li > a {color: $theme_font_color;}';?>
            </div>
        </li>
        
        <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_141');"><?php echo "Fonts are not coming fine on my site. What might be the problem? How can I resolve this?";?></a>
          <div class='faq' style='display: none;' id='faq_141'>
           <?php 
             $url = $this->url(array('module' => 'spectacular', 'controller' => 'settings', 'action' => 'place-htaccess-file'), 'admin_default', true); 
             $genralSettingUrl = $this->url(array('module' => 'core', 'controller' => 'settings', 'action' => 'general'), 'admin_default', true); 
             ?>
           <?php 
             echo "It is happening because you are using the 'Static File Base URL' setting in ‘<a href='$genralSettingUrl'>General Settings</a>’ section of admin panel. For resolving this, you need to create .htacces file over here: '/application/themes/spectacular/'. Please <a href='javascript:void(0)' onclick='Smoothbox.open(\"$url\");'>click here</a> if you want to create .htaccess file.";
           ?>
       </div>
         </li> 
        <?php if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) : ?>
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo "Can I disable browse navigation from header ?"; ?></a>
                <div class='faq' style='display: none;' id='faq_5'>
                    <?php echo "A - Please follow the below steps to do so:<br />
- Go to the 'Layout' >> 'Language Manager' >> 'Site Header' from the admin panel of your site.<br />
- Now remove the widget 'Responsive Spectacular Theme - Main Menu' from here 
You will now not see the Browse dropdown in the header. [In this case you will not have main menu on your website so you are recommended to place main menu widget from layout editor.]"; ?>
                </div>
            </li>
        <?php endif; ?>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo "I have changed color of sign in and sign up buttons on my site, now transparency is not coming in colors as it was before. What should I do?"; ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                <?php echo "A : If you will change the sign in and sign up buttons color from admin side using theme customization tab then transparency in colors will not be visible in buttons.<br />
For this you need to do some changes mentioned below:<br />
- Choose color from the color picker which you want for the sign in and sign up button for your site and copy the code generated in the text box.<br />
- Now go to http://hex2rgba.devoth.com/ and enter the color code in “HEX value” box and click on “HEX 2 RGB(angel)” button.<br />
- You will now get the RGB and RGBA format color in “RGB for CSS” box and “RGBA for CSS” box. <br />
- Now, choose the color code from “RGBA for CSS” and put this color code in file at below mentioned path:<br />
Directory_Name >> public/seaocore_themes >> spectacularThemeConstants.css
Now, search for this code: landingpage_signinbtn
You will see the code like this: landingpage_signinbtn: #ff5f3f; landingpage_signupbtn: #ff5f3f; [Values may be change, its just for your reference]
- Replace it with the code like this: landingpage_signinbtn: “Paste RGBA color code”;landingpage_signupbtn: “Paste RGBA color code”; [It will look like this: landingpage_signinbtn: rgba(255, 95, 63, 0.5); landingpage_signupbtn: rgba(255, 95, 63, 0.5); ]<br />
[Note: You can configure the transparency of the buttons accordingly by changing the last value of RGBA code, for eg: rgba(255, 95, 63, 1) => rgba(255, 95, 63, 0.5)]"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo "Can I change the images rotating in the background on the landing page?"; ?></a>
            <div class='faq' style='display: none;' id='faq_7'>
                <?php echo "A - Yes, you can do so by following the below steps:
- Go to the Admin panel of this theme.<br />
- Now from the “Images” section, upload the images you want for your landing page.<br />
- Now, these images will be visible on your Landing Page when you will place our widget “Landing Page Images” from the Layout Editor.<br />
[You can upload multiple images to display them one after another as slideshow.]"; ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo "I am getting blur images for larger resolution on my 'Landing Page' under the 'Landing Page Images' widget. What should I do?"; ?></a>
            <div class='faq' style='display: none;' id='faq_8'>
                <?php echo "A - Go to the 'Layout' >> 'Theme Editor' section from the Admin Panel of your site. Now choose 'theme.css' from the ‘editing file’ dropdown.<br />
Now add the below code and click on 'Save Changes':<br />
@media only screen and (min-width: 1360px) {div#slide-images{width:1200px; 
margin:0 auto;}"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo "I want to change the text for the blocks shown on clicking the Get Started/How It Works Button, on the landing page: 'Create Events', 'Sell Tickets' & 'Share Events'. How will I be able to do this?"; ?></a>
            <div class='faq' style='display: none;' id='faq_9'>
                <?php echo "A - Please go to the Global Settings section of this theme, please change the text accordingly from the tinymce editor placed there.
"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo "I want to display an image rotator on inner pages of my website. Is it possible with this theme?"; ?></a>
            <div class='faq' style='display: none;' id='faq_10'>
                <?php echo "A - Yes, you can easily do so by using our ‘Responsive Spectacular Theme - Banner Images’ widget, you can upload / delete / manage all your banner images. You can also set the sequence of banner images by dragging-and-dropping them vertically. Multiple banner images can be added to display them in a circular manner, i.e one after another.<br />
You will be able to configure your Banner Images from 'Banners' section available in the admin panel of this theme."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo "Can I disable the uploaded Landing Page Images from admin panel?"; ?></a>
            <div class='faq' style='display: none;' id='faq_11'>
                <?php echo "A - Please do the following steps to do so:<br />
- Go to the 'Images' section available in the admin panel of this plugin. Here you can see the list of all the images uploaded by you.<br />
- Now, use the green button available along the images by clicking it to enable/disable the images as per your requirement."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo "How to change the ordering of image displaying?"; ?></a>
            <div class='faq' style='display: none;' id='faq_12'>
                <?php echo "A - Please do the following steps to do so:<br />
1. Go to the 'Images' section available in the admin panel of this plugin. Here you can see the list of all the images uploaded by you.<br />
2. Now, drag and drop the images vertically to re-order them in sequence they should appear to members on the landing page of your website."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo "How I can hide Browse, Sign in/Sign up and other available links on image?"; ?></a>
            <div class='faq' style='display: none;' id='faq_13'>
                <?php echo "A - Yes please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now open the widgetized page where you want to disable Browse, Sign in/Sign up and other available links on image.<br />
- Now use ‘edit’ link to configure the ‘Responsive Spectacular Theme - Landing Page Images’ widget.<br />
- Here set ‘No’ for various settings you want to disable for image and save your changes.<br />
You will now not see the above disabled option on your image."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo 'How I can change the below text displaying on image rotator?<br />
"1. BRING PEOPLE TOGETHER"<br />
"2. Create an event. Sell tickets online."<br />
'; ?></a>
            <div class='faq' style='display: none;' id='faq_14'>
                <?php echo "A - Please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now open the widgetized page where ‘Responsive Spectacular Theme - Landing Page Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />
- Now Here, configure the settings below:<br />
Enter the title that you want to display on this image rotator.<br />
Enter the description that you want to display on this image rotator.<br />
Now, enter the text you want to display in place of available text and save your changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo "How I can hide 'Get Started' button displaying on image ?"; ?></a>
            <div class='faq' style='display: none;' id='faq_15'>
                <?php echo "A - Yes please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now open the widgetized page where ‘Responsive Spectacular Theme - Landing Page Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />
- Here set ‘No’ for below setting:<br />
‘Do you want to display an action button like 'Get Started', 'How It Works', etc on the image rotator? (You can configure this button from the administration of Spectacular Theme, and can also configure the slide-down content that comes after clicking of this button.)’<br />
You will now not see the 'Get Started' button on your image."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo "How can I display logo in ‘Responsive Spectacular Theme - Landing Page Images’ widget?"; ?></a>
            <div class='faq' style='display: none;' id='faq_16'>
                <?php echo "A - You can do so by following the below steps:<br />
- Please go to the  ‘Layout’  >> ‘File and Media Manager’ section available under the admin panel of your website and upload your website logo here.<br />
- Now, go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now open the widgetized page where ‘Responsive Spectacular Theme - Landing Page Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />
- Here, choose ‘Yes’ for ‘Do you want to display your website's logo on the top-left side of the images rotator?’<br /> 
You can now see your website logo on ‘Responsive Spectacular Theme - Landing Page Images widget."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo "I want to change the URL on 'Important Link' showing on image. How I can do so ?"; ?></a>
            <div class='faq' style='display: none;' id='faq_17'>
                <?php echo "A - Yes please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- open the widgetized page where ‘Responsive Spectacular Theme - Landing Page Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />
- Here, configure the ‘Url’ setting and replace the URL with the one you want to have as your important link."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo "I have placed Landing Page Images widget on widgetized page, but I want to display this page without header like your demo. How Can I do this?"; ?></a>
            <div class='faq' style='display: none;' id='faq_18'>
                <?php echo "A - Yes please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now go to the widgetized page where you have placed the Landing Page Images widget.<br />
- Here, disable header. Please <a href='application/modules/Spectacular/externals/images/without_header.png' target='_blank'>click here</a> to see the screenshot for this.<br />
You will now be able to get the look of this page exactly like our demo."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo "How can choose distinct banner images for my various widgetized pages?"; ?></a>
            <div class='faq' style='display: none;' id='faq_19'>
                <?php echo "A - You can easily do this by following the below steps:<br />        
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />    
- Now open the widgetized page where ‘Responsive Spectacular Theme - Banner Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />       
- Here configure the setting ‘Select the banners which do you want to show in this widget?’<br />       
[You can upload banners from ‘banners’ section available in the admin panel of this plugin to make these image available in this widget.]"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo 'I want to change the below given texts, shown on the banner image. How can I do so?<br />
"Events & Groups that you\'d love"<br />
"Discover new events in your town, interact with other party-goers and share the fun!"
'; ?></a>
            <div class='faq' style='display: none;' id='faq_20'>
                <?php echo "A - Please follow the below steps to do so:<br />
- Go to Layout >> Layout Editor available in the admin panel of your site.<br />
- Now open the widgetized page where ‘Responsive Spectacular Theme - Banner Images’ widget is placed. Use ‘edit’ link to configure the widget.<br />
- Now Here, configure the settings below:<br />
Enter the title that you want to display on this banner.<br />
Enter the description that you want to display on this banner.<br />
Now, enter the text you want to display in place of available text and save your changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo "Q: I have placed Banner Images widget on widgetized page, but it is not coming in full length. What might be the reason?"; ?></a>
            <div class='faq' style='display: none;' id='faq_21'>
                <?php echo "A - It is happening because you have not placed the widget in the top container, please <a target='_blank' href='application/modules/Spectacular/externals/images/banner_images.png'>click here</a> to see the screenshot to view the placement of this widget."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo 'Q: I want to display the below text coming in footer menu in French language. Is it possible to do so?<br />
"1. Join the Media Community"<br />
"2. Stay tuned with upcoming events, organize your own parties, see what\'s being shared, and lots more exciting stuff!"
'; ?></a>
            <div class='faq' style='display: none;' id='faq_22'>
                <?php echo "A - Please follow the below steps to do so:<br />
- Go to the 'Layout' >> 'Language Manager' from the Admin panel of your site.<br />
- Now, click on 'Edit Phrases' link and search for the text showing on image currently.<br />
- Now add your phrase (In French) which you want to replace with the above text and click on 'Save Changes'.<br />
You will now be able to see the text you have added in place of the text showing currently."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo "I want to change the footer text: 'Join the Media Community…...'. How can I do so?"; ?></a>
            <div class='faq' style='display: none;' id='faq_23'>
                <?php echo "A - Please follow the below steps to do so:<br />
- Go to the 'Layout' >> 'Language Manager' from the Admin panel of your site.<br />
- Now, click on 'Edit Phrases' link and search for the phrase 'Join the Media Community…...'.<br />
- Now add your phrase here which you want to replace with the above text and click on 'Save Changes'.<br />
You will now be able to see the text you have added in place of the above text."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo "Can I add one more column in footer menu?"; ?></a>
            <div class='faq' style='display: none;' id='faq_24'>
                <?php echo "A - Yes you can do it easily by following the below steps:<br />
- <a href='admin/menus/index?name=spectacular_footer' target='_blank'>Click here</a> to go to the 'Responsive Spectacular Theme - Footer Menu'.<br />
- Click on 'Add Item' link.<br />
- Now fill the required details. Here while adding the URL, please add: javascript:void in place URL. You will now have another column added in the footer menu.
"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo "How can I add menu in the Spectacular - Footer Menu?"; ?></a>
            <div class='faq' style='display: none;' id='faq_25'>
                <?php echo "A - To do so, please follow the steps below:<br />
- <a href='admin/menus/index?name=spectacular_footer' target='_blank'>Click here</a> to go to the 'Responsive Spectacular Theme - Footer Menu'.<br />
- Now, click on 'Add Item' and enter the 'Label' of the item.<br />
- Now enter URL for your added menu in the URL field."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo 'The CSS of this Theme is not coming on my site. What should I do ?'; ?></a>
            <div class='faq' style='display: none;' id='faq_26'>
                <?php echo "A - Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."; ?>
            </div>
        </li>

    </ul>
</div>