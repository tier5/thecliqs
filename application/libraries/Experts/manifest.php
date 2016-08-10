<?php
/**
 * SocialEngine
 *
 * @category   Experts
 * @package    Hecore
 * @copyright  Copyright 2006-2012 Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2012-03-14 16:55:52Z mt.uulu $
 * @author     Mirlan <mt.uulu@gmail.com>
 */
return array(
  'package' => array(
    'type' => 'library',
    'name' => 'experts',
    'version' => '4.0.0',
    'revision' => '$Revision: 1 $',
    'path' => 'application/libraries/Experts',
    'repository' => 'hire-experts.com',
    'title' => 'Experts',
    'author' => 'Hire-Experts LLC Development',
    'license' => 'http://www.hire-experts.com/license/',
    'changeLog' => array(),
    'dependencies' => array(
      array(
        'type' => 'hecore',
        'name' => 'install',
        'required' => true,
        'minVersion' => '4.1.0',
      ),
    ),
    'directories' => array(
      'application/libraries/Experts',
    )
  )
) ?>