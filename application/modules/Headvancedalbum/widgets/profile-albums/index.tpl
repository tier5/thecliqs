<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2013-01-21 16:48:00 ratbek $
 * @author     Ratbek
 */
?>

<script type="text/javascript">


    en4.core.runonce.add(function () {

        var $navigation = $$('.he_advancedalbum_profile_albums_navigaton').getElements('li');
        for (var cnt = 0; cnt < $navigation.length; cnt++) {
            $navigation[cnt].addEvent('click', function () {
                $$('.active_item').removeClass('active_item');
                this.addClass('active_item');

                $$('.widget').removeClass('active');
                $$('.widget').set('style', 'display: none;');
                $$('.widget').tween('opacity', 0);// set('style', 'display: none;');

                $$('.'+this.get('id')).set('style', 'display: block;');
                $$('.'+this.get('id')).tween('opacity', 1);//.set('style', 'display: block;');



              setTimeout(function (){
                if (window.hapPhotos){
                  hapPhotos.bindRebuildOnLoadImages();
                  hapPhotos.bindRebuildOnTimeout();
                  hapPhotos.rebuild();
                }
                if (window.hapTaggedPhotos){
                  hapTaggedPhotos.bindRebuildOnLoadImages();
                  hapTaggedPhotos.bindRebuildOnTimeout();
                  hapTaggedPhotos.rebuild();
                }

              }, 100);

            });
        }

        $$('.tab_layout_headvancedalbum_profile_albums, .headvancedalbum-profile-albums').addEvent('click', function (){
          setTimeout(function (){
            if (window.hapTaggedPhotos){
               hapTaggedPhotos.bindRebuildOnLoadImages();
               hapTaggedPhotos.bindRebuildOnTimeout();
               hapTaggedPhotos.rebuild();
             }
          }, 100);
        });

    });
</script>

<ul class="he_advancedalbum_profile_albums_navigaton active_item">
    <li id="he_adv_tagged_photos">
        <i class="icon-user"></i>
        <span><?php echo $this->tagged_count; ?></span>
        <label><?php echo $this->translate('HEADVANCEDALBUM_Photos of %s', $this->subject->getTitle()); ?></label>
    </li>
    <li id="he_adv_photos">
        <i class="icon-picture"></i>
        <span><?php echo $this->photos_count; ?></span>
        <label><?php echo $this->translate('HEADVANCEDALBUM_Photos'); ?></label>
    </li>
    <li id="he_adv_albums">
        <i class="icon-book"></i>
        <span><?php echo $this->albums_count; ?></span>
        <label><?php echo $this->translate('HEADVANCEDALBUM_Albums'); ?></label>
    </li>
</ul>


<div class="he_advanced_albums_content">
    <div class="he_adv_tagged_photos widget active">
        <?php if($this->tagged_count > 0 ) : ?>
            <?php echo $this->content()->renderWidget('headvancedalbum.tagged-photos'); ?>
        <?php else: ?>
            <?php echo $this->translate('HEADVANCEDALBUM_There are no photos'); ?>
        <?php endif; ?>
    </div>
    <div class="he_adv_photos widget">

      <?php if($this->photos_count > 0 ) : ?>
      <?php echo $this->content()->renderWidget('headvancedalbum.browse-photos'); ?>
      <?php else: ?>
          <?php echo $this->translate('HEADVANCEDALBUM_There are no photos'); ?>
      <?php endif; ?>
    </div>
    <div class="he_adv_albums widget">

      <?php if($this->albums_count > 0 ) : ?>
      <?php echo $this->content()->renderWidget('headvancedalbum.member-albums'); ?>
      <?php else: ?>
          <?php echo $this->translate('HEADVANCEDALBUM_There are no albums'); ?>
      <?php endif; ?>
    </div>
</div>