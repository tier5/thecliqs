<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div id="<?php echo $this->tmpId ?>">
</div>

<?php echo $this->action('index', 'index', 'chat', array(
  'tmpId' => $this->tmpId,
  'no-content' => 1,
)) ?>