<?php echo Engine_Api::_()->ynresume()->getPhotoSpan($this -> resume, 'thumb.main'); ?>
<a href="javascript:void(0)" class="ynresume-edit-cover-photo" onclick="checkOpenPopup('<?php echo $this -> url(array('action' => 'photo', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true);?>')"><i class="fa fa-pencil"></i> <?php echo $this -> translate('Change Photo');?></a>

