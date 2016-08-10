<?php

class Ynrestapi_Helper_ItemPhoto extends Engine_View_Helper_HtmlImage
{
    /**
     * @var mixed
     */
    static $instance;

    /**
     * @var mixed
     */
    protected $_noPhotos;

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param  $item
     * @param  $type
     * @return mixed
     */
    public function itemPhoto($item, $type = 'thumb.profile')
    {
        // Whoops
        if (!($item instanceof Core_Model_Item_Abstract)) {
            throw new Zend_View_Exception('Item must be a valid item');
        }

        // Get url
        $src = $item->getPhotoUrl($type);
        $safeName = ($type ? str_replace('.', '_', $type) : 'main');

        if (!$src) {
            // Default image
            $src = $this->getNoPhoto($item, $safeName);
        }

        return $src;
    }

    /**
     * @param  $item
     * @param  $type
     * @return mixed
     */
    public function getNoPhoto($item, $type)
    {
        $type = ($type ? str_replace('.', '_', $type) : 'main');

        if (($item instanceof Core_Model_Item_Abstract)) {
            $item = $item->getType();
        } else if (!is_string($item)) {
            return '';
        }

        if (!Engine_Api::_()->hasItemType($item)) {
            return '';
        }

        // Load from registry
        if (null === $this->_noPhotos) {
            // Process active themes
            $themesInfo = Zend_Registry::get('Themes');
            foreach ($themesInfo as $themeName => $themeInfo) {
                if (!empty($themeInfo['nophoto'])) {
                    foreach ((array) @$themeInfo['nophoto'] as $itemType => $moreInfo) {
                        if (!is_array($moreInfo)) {
                            continue;
                        }

                        $this->_noPhotos[$itemType] = array_merge((array) @$this->_noPhotos[$itemType], $moreInfo);
                    }
                }
            }
        }

        // Use default
        if (!isset($this->_noPhotos[$item][$type])) {
            $shortType = $item;
            if (strpos($shortType, '_') !== false) {
                list($null, $shortType) = explode('_', $shortType, 2);
            }
            $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
            $this->_noPhotos[$item][$type] = 
            Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/' .
                $module .
                '/externals/images/nophoto_' .
                $shortType . '_'
                . $type . '.png';
        }

        return $this->_noPhotos[$item][$type];
    }
}
