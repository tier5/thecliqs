<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Chat Box',
    'description' => 'Displays the chat box.',
    'category' => 'Chat',
    'type' => 'widget',
    'name' => 'chat.chat',
    'defaultParams' => array(
      'title' => 'Chat',
    ),
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),
) ?>