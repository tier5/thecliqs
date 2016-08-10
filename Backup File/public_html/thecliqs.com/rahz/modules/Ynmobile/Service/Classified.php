<?php
/**
 * file summary
 */

/**
 * Mobile APIs for Classified module
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @version    $Version$
 * @author     $Author$
 * @copyright  $Copyright$
 * @license    $License$
 */
class Ynmobile_Service_Classified extends Ynmobile_Service_Base
{
    
    protected $module =  'classified';
    
    protected $mainItemType =  'classified';
    
    const IMAGE_WIDTH = 720;
    const IMAGE_HEIGHT = 720;

    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 160;
    
    public function __construct(){
        $locale = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        $language = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');

        $locale = 'en_US';
        
        $localeObject = new Zend_Locale($locale);
        Zend_Registry::set('Locale', $localeObject);
    
    }

    public function fetch($aData){
        if(isset($aData['sView']) && $aData['sView'] == 'my'){
            return $this->my($aData);
        }else{
            return $this->all($aData);
        }
    }
    
    /**
     *
     * Fetch|Search listings
     *
     * Request params:
     * - sSearchText    :   string, optional. searching by title on field: `engine4_classified_classifieds`.`title`
     * - iCategoryId    :   int, optional. searching by category on field:  `engine4_classified_classifieds`.`category_id`
     * - iStatus        :   int, optional. values:
     *  - null          :   list all
     *  - 0             :   only Open Listings
     *  - 1             :   only Closed Listings
     * - sOrderBy       :   string, optional. values:
     *  - most_recent   :   most recent
     *  - most_viewed   :   most viewed
     * - iHasPhoto      :   int, searching by having photos
     * - CUSTOM FIELDS  :   custom fields value
     *
     * Responsed params:
     * - iListingId     :   int. Listidentity
     * - sTitle         :   string. title
     * - sBody          :   string. title
     * - sListImageUrl  :   string. image url
     * - iOwnerId       :   int. owner id
     * - sOwnerName     :   string. owner name
     * - sOwnerImageUrl :   string. owner image url
     * - iTimestamp     :   int. creation data in timestamp format
     * - iTotalView     :   int. total view
     * - iTotalComment  :   int. total comment
     * - bIsClosed      :   boolean. owner closed this list or NOT
     * - bCanEdit       :   boolean. viewer can edit this list or NOT
     * - bCanDelete     :   boolean. viewer can delete this list or NOT
     * - bCanUploadPhoto:   boolean. viewer can upload photo to this list or NOT
     * 
     * @param array $aData
     * @return array $aResult
     * @access public
     */
    public function all($aData)
    {
        $view = Zend_Registry::get("Zend_View");
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        
        
        try 
        {
            extract($aData);
            if (!isset($iPage))
            {
                $iPage = 1;
            }
            if ($iPage == 0)
            {
                return array();
            }
            if (!isset($iLimit))
            {
                $iLimit = 10;
            }
            $viewer = Engine_Api::_()->user()->getViewer();
            $params = $aData;
            if (isset($iCategory))
            {
                $params['category'] = $iCategory;
            }
            if (isset($iStatus))
            {
                $params['closed'] = $iStatus;
            }
            if (isset($sOrderBy) && $sOrderBy != "")
            {
                if ($sOrderBy == 'most_recent')
                    $params['orderby'] = 'creation_date';
                if ($sOrderBy == 'most_viewed')
                    $params['orderby'] = 'view_count';
            }
            if (isset($sSearchText) && $sSearchText != "")
            {
                $params['search'] = $sSearchText;
            }
            if (isset($iHasPhoto))
            {
                $params['has_photo'] = $iHasPhoto;
            }
    
            // Process with custom fields
            $form = new Classified_Form_Search();
            $customFieldValues = array_intersect_key($params, $form->getFieldElements());
                
            // Process options
            $tmp = array();
            foreach( $customFieldValues as $k => $v ) {
                if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
                    continue;
                } else if( false !== strpos($k, '_field_') ) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if( false !== strpos($k, '_alias_') ) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $customFieldValues = $tmp;
            if(!count($customFieldValues))
            {
                $customFieldValues = null;
            }
            $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($params, $customFieldValues);
            
            if(empty($fields)) $fields = 'listing';
            
            $fields = explode(',',$fields);
            
            return Ynmobile_AppMeta::_exports_by_page($paginator, $iPage, $iLimit, $fields);
            
        }
        catch (Exception $e)
        {
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    

    protected function parseList($paginator, $once = false)
    {
        $result = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $view = Zend_Registry::get("Zend_View");
        foreach($paginator as $list)
        {
            $sListImageUrl = $list -> getPhotoUrl(null);
            if ($sListImageUrl != "")
            {
                $sListImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sListImageUrl);
            }
            else
            {
                $sListImageUrl = NO_LIST_ICON;
            }

            $owner = $list -> getOwner();
            $sOwnerImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
            if ($sOwnerImageUrl != "")
            {
                $sOwnerImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sOwnerImageUrl);
            }
            else
            {
                $sOwnerImageUrl = NO_USER_ICON;
            }
            $body = $list->body;
            if( strip_tags($body) == $body ) {
                $body = nl2br($body);
            }
            $tempArr = array(
                'iListingId' => $list -> getIdentity(),
                'sTitle' => $list -> getTitle(),
                'sBody' => $body,
                'sListImageUrl' => $sListImageUrl,
                'iOwnerId'  => $list -> owner_id,
                'sOwnerName' => $owner -> getTitle(),
                'sOwnerImageUrl' => $sOwnerImageUrl,
                'iTimestamp' => strtotime($list->creation_date),
                'iTotalView' => $list -> view_count,
                'iTotalComment' => $list -> comment_count,
                'bIsClosed' => ($list -> closed) ? true : false,
                'bCanEdit' => ($list->authorization()->isAllowed(null, 'edit')) ? true : false,
                'bCanDelete' => ($list->authorization()->isAllowed(null, 'delete')) ? true : false,
                'bCanUploadPhoto' => ($list->authorization()->isAllowed(null, 'photo')) ? true : false,
                'sHref'=> Engine_Api::_()->ynmobile()->finalizeUrl($list->getHref()),
            );
            $fieldStructure = Engine_Api::_()->fields()->getFieldsStructureFull($list);
            foreach ($fieldStructure as $index => $map)
            {
                $field = $map -> getChild();
                $value = $field -> getValue($list);
                if ($field -> type == 'location')
                {
                    $tempArr['sLocation'] = ($value->value) ? $value->value : '';
                }
                else if ($field -> type == 'currency')
                {
                    $tempArr['fPrice'] = ($value->value) ? $value->value : '0.00';
                    if ($value->value != ''){
                        $tempArr['sFullPrice'] = $view->currency($value->value,'usd');
                    }
                        
                    else{
                        $tempArr['sFullPrice'] = $view->currency(0,'usd');
                    } 
                        
                }
            }
            $result[] = $tempArr;
        }
        if ($once === true)
        {
            return $result[0];
        }
        return $result;
    }
   

    public function getAliasFields()
    {
        $structure = Engine_Api::_()->getApi('core', 'fields')->getFieldsStructureSearch("classified", NULL, NULL);
        $result = array();
        // Start firing away
        foreach( $structure as $map )
        {
            $field = $map->getChild();
            // Get search key
            $uKey = $key = $map->getKey();
            $name = null;
            if( !empty($field->alias) ) {
                $name = sprintf('alias_%s', $field->alias);
            } else {
                $name = sprintf('field_%d', $field->field_id);
            }
            $key .= '_' . $name;
            if ($field->type == "currency")
            {
                /*
                $result[] = array(
                    'name' => $field->alias,
                    'alias_field' => $key . "[min]"
                    );
                    $result[] = array(
                    'name' => $field->alias,
                    'alias_field' => $key . "[max]"
                    );
                */  
                $result['price_field']['min'] = $key . "[min]";
                $result['price_field']['max'] = $key . "[max]";
            }
            else
            {
                /*
                $result[] = array(
                    'name' => $field->alias,
                    'alias_field' => $key
                );
                */
                $result['location_field'] = $key;
            }

        }
        return $result;
    }


    public function getCustomFields($subject = null)
    {
        $struct = Engine_Api::_()->fields()->getFieldsStructureFull("classified", NULL, NULL);
        $result = array();
        foreach( $struct as $fskey => $map )
        {
            $field = $map->getChild();
            $key = $map->getKey();
            $valueObj = ($subject !== null) ? $field->getValue($subject) : null;

            if ($field->type == 'currency' && 
                ($field->alias == 'price' || $field->alias == 'currency'))
            {
                
                $result['price'] = array(
                    'label' => $field->label,
                    'name' => $field->alias,
                    'alias_field' => "field__{$key}",
                    'required' => ($field->required) ? true : false,
                    'value' =>  (is_object($valueObj)) ? $valueObj->getValue() : ""
                );
            }
            elseif ($field->type == 'location')
            {
                $result['location'] = array(
                    'label' => $field->label,
                    'name' => $field->alias,
                    'alias_field' => "field__{$key}",
                    'required' => ($field->required) ? true : false,
                    'value' =>  (is_object($valueObj)) ? $valueObj->getValue() : ""
                );
            }
            
        }
        
        return $result;
    }

    public function getPermissions($subject = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $result['bCanView'] = (bool)Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'view');
        $result['bCanCreate'] = (bool)Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'create');
        $result['bCanUploadPhoto'] = (bool)Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'photo');
        return $result;
    }


    /**
     *
     * Get my listing
     *
     * Request params:
     * - sSearchText    :   string, optional. searching by title on field: `engine4_classified_classifieds`.`title`
     * - iCategoryId    :   int, optional. searching by category on field:  `engine4_classified_classifieds`.`category_id`
     * - iStatus        :   int, optional. values:
     *  - null          :   list all
     *  - 0             :   only Open Listings
     *  - 1             :   only Closed Listings
     * - sOrderBy       :   string, optional. values:
     *  - most_recent   :   most recent
     *  - most_viewed   :   most viewed
     * - iHasPhoto      :   int, searching by having photos
     * - CUSTOM FIELDS  :   custom fields value
     *
     * Response params:
     * - iListingId     :   int. Listidentity
     * - sTitle         :   string. title
     * - sBody          :   string. title
     * - sListImageUrl  :   string. image url
     * - iOwnerId       :   int. owner id
     * - sOwnerName     :   string. owner name
     * - sOwnerImageUrl :   string. owner image url
     * - iTimestamp     :   int. creation data in timestamp format
     * - iTotalView     :   int. total view
     * - iTotalComment  :   int. total comment
     * - bIsClosed      :   boolean. owner closed this list or NOT
     * - bCanEdit       :   boolean. viewer can edit this list or NOT
     * - bCanDelete     :   boolean. viewer can delete this list or NOT
     * - bCanUploadPhoto:   boolean. viewer can upload photo to this list or NOT
     *
     * @param array $aData
     * @return array $aResult
     */
    public function my($aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        $aData['user_id'] = $viewer->getIdentity();
        return $this->all($aData);
    }

    /**
     *
     * Create a new listing
     * Request params:
     * - sTitle         : string, required. listing title
     * - sTag           : string, optional. tags for this listing
     * - iCategoryId    : int, optional. listing category
     * - sBody          : string, required, listing body
     * - sViewPrivacy   : string, optional, default "everyone". View privacy
     * - sCommentPrivacy: string, optional, default "everyone". Comment privacy
     * - photo          : FILE, optional. main photo for this listing.
     * - ...            : cutomfields, optional. these field name are got from core/setting service
     *
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     * - iListingId     : int. listing id
     *
     * @param array $aData
     * @return array $aResult
     *
     */
    public function create($aData)
    {
        extract($aData);
        if (!isset($sTitle) || empty($sTitle))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing sTitle")
            );
        }
        if (!isset($sBody) || empty($sBody))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing sBody")
            );
        }
        if (!isset($iCategoryId) || empty($iCategoryId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iCategoryId")
            );
        }
        // get Field price
        $struct = Engine_Api::_()->fields()->getFieldsStructureFull("classified", NULL, NULL);
        $fieldPrice = "";
        foreach( $struct as $fskey => $map )
        {
            $field = $map->getChild();
            $key = $map->getKey();

            if ($field->type == 'currency' && 
                ($field->alias == 'price' || $field->alias == 'currency'))
            {
                $fieldPrice = "field__{$key}";
            }
        }
        // Add fields
        $fValues = array();
        foreach ($aData as $k => $v)
        {
            if($k == $fieldPrice)
            {
                // check price invalid
                if(!is_numeric($v))
                {
                    return array(
                        'error_code' => 1,
                        'error_message' => Zend_Registry::get("Zend_Translate")->_("Price is not valid!")
                    );
                }
            }
        }
        
        // set up data needed to check quota
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values);
        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
        $current_count = $paginator->getTotalItemCount();
        if (($current_count >= $quota) && !empty($quota))
        {
            return array(
                'error_code' => 2,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("You have already created the maximum number of classified listings allowed.")
            );
        }

        // Process
        $table = Engine_Api::_()->getItemTable('classified');
        $db = $table->getAdapter();
        $classified = $table->createRow();
        $saveTask = $this->saveValues($classified, $aData);
        if ($saveTask)
        {
            $db->beginTransaction();
            try {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $classified, 'classified_new');
                if( $action != null ) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $classified);
                }
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                return array(
                    'error_code' => 3,
                    'error_message' => $e->getMessage(),
                );
            }
            return array(
                    'error_code' => 0,
                    'error_message' => '',
                    'message' => Zend_Registry::get("Zend_Translate")->_("Created listing successfully."),
                    'iListingId' => $classified->getIdentity()
            );
        }
        else
        {
            return array(
                    'error_code' => 3,
                    'error_message' => Zend_Registry::get("Zend_Translate")->_("Created listing unsuccessfully.")
            );
        }

    }

    /**
     *
     * Update data for a listing
     * 
     * Request params:
     * - iListingId     : int, required. Listing id
     * - sTitle         : string, required. listing title
     * - sTag           : string, optional. tags for this listing
     * - iCategoryId    : int, optional. listing category
     * - sBody          : string, required, listing body
     * - sViewPrivacy   : string, optional, default "everyone". View privacy
     * - sCommentPrivacy: string, optional, default "everyone". Comment privacy
     * - photo          : FILE, optional. main photo for this listing.
     * - ...            : cutomfields, optional. these field name are got from core/setting service
     *
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     *
     * @param array $aData
     * @return array $aResult
     *
     */
    public function update($aData)
    {
        extract($aData);
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId")
            );
        }
        if (!isset($sTitle) || empty($sTitle))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing sTitle")
            );
        }
        if (!isset($sBody) || empty($sBody))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing sBody")
            );
        }
        if (!isset($iCategoryId) || empty($iCategoryId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iCategoryId")
            );
        }
        // get Field price
        $struct = Engine_Api::_()->fields()->getFieldsStructureFull("classified", NULL, NULL);
        $fieldPrice = "";
        foreach( $struct as $fskey => $map )
        {
            $field = $map->getChild();
            $key = $map->getKey();

            if ($field->type == 'currency' && 
                ($field->alias == 'price' || $field->alias == 'currency'))
            {
                $fieldPrice = "field__{$key}";
            }
        }
        // Add fields
        $fValues = array();
        foreach ($aData as $k => $v)
        {
            if($k == $fieldPrice)
            {
                // check price invalid
                if(!is_numeric($v))
                {
                    return array(
                        'error_code' => 1,
                        'error_message' => Zend_Registry::get("Zend_Translate")->_("Price is not valid!")
                    );
                }
                
            }
        }
        
        $classified = Engine_Api::_()->getItem("classified", $iListingId);
        $saveTask = $this->saveValues($classified, $aData);
        if (isset($iMainPhotoId))
        {
            $photo = Engine_Api::_()->getItem('classified_photo', $iMainPhotoId);
            if ($photo)
            {
                $classified->photo_id = $photo->file_id;
                $classified->save();    
            }
        }
        if ($saveTask)
        {
            return array(
                    'error_code' => 0,
                    'error_message' => '',
                    'message' => Zend_Registry::get("Zend_Translate")->_("Edited listing successfully."),
            );
        }
        else
        {
            return array(
                    'error_code' => 3,
                    'error_message' => Zend_Registry::get("Zend_Translate")->_("Edited listing unsuccessfully.")
            );
        }
    }

    protected function saveValues($classified, $aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('classified');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try
        {
            // Create classified
            $sTitle = html_entity_decode($sTitle, ENT_QUOTES, 'UTF-8');
            $values = array(
                'title' => strip_tags($sTitle),
                'tags' => $sTags,
                'category_id' => $iCategoryId,
                'body' => html_entity_decode($sBody, ENT_QUOTES, 'UTF-8'),
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            );

            $classified->setFromArray($values);
            $classified->save();

            // Set photo
            if(!empty($_FILES['image']))
            {
                //$classified->setPhoto($_FILES['image']);
                $classified = Engine_Api::_()->ynmobile()->setClassifiedPhoto($classified, $_FILES['image']);
            }
            
            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));
            $classified->tags()->addTagMaps($viewer, $tags);

            // Add fields
            $fValues = array();
            foreach ($aData as $k => $v)
            {
                if (strpos($k, "field__") !== false)
                {
                    $k_temp = str_replace("field__", "", $k);
                    $fValues[$k_temp] = $v;
                }
            }
            if (count($fValues))
            {
                $this->saveCustomValues($classified, $fValues);
            }

            // Set privacy
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (!in_array($sViewPrivacy, $roles) || empty($sViewPrivacy) || !isset($sViewPrivacy))
            {
                $sViewPrivacy = "everyone";
            }
            if (!in_array($sCommentPrivacy, $roles) || empty($sCommentPrivacy) || !isset($sCommentPrivacy))
            {
                $sCommentPrivacy = "everyone";
            }
            
            $viewMax = array_search($sViewPrivacy, $roles);
            $commentMax = array_search($sCommentPrivacy, $roles);

            foreach( $roles as $i => $role )
            {
                $auth->setAllowed($classified, $role, 'view',    ($i <= $viewMax));
                $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
            }
            $classified->save();
            // Commit
            $db->commit();
            return true;
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return false;
        }
    }

    protected function saveCustomValues($classified, $fVals)
    {
        // Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($classified);
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();

        foreach( $fVals as $key => $value )
        {
            $parts = explode('_', $key);
            if( count($parts) != 3 ) continue;
            list($parent_id, $option_id, $field_id) = $parts;

            // Array mode
            if( is_array($value))
            {
                // Lookup
                $valueRows = $values->getRowsMatching(array(
                  'field_id' => $field_id,
                  'item_id' => $classified->getIdentity()
                ));

                // Delete all
                $prevPrivacy = null;
                foreach( $valueRows as $valueRow )
                {
                    if( !empty($valueRow->privacy) )
                    {
                        $prevPrivacy = $valueRow->privacy;
                    }
                    $valueRow->delete();
                }

                // Insert all
                $indexIndex = 0;
                if( is_array($value) || !empty($value) )
                {
                    foreach( (array) $value as $singleValue )
                    {
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $classified->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                }
                else
                {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $classified->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

            // Scalar mode
            else
            {
                // Lookup
                $valueRow = $values->getRowMatching(array(
                      'field_id' => $field_id,
                      'item_id' => $classified->getIdentity(),
                      'index' => 0
                ));

                // Create if missing
                $isNew = false;
                if( !$valueRow )
                {
                    $isNew = true;
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $classified->getIdentity();
                }
                $valueRow->value = htmlspecialchars($value);
                $valueRow->save();
            }
        }

        // Update search table
        Engine_Api::_()->getApi('core', 'fields')->updateSearch($classified, $values);

        // Fire on save hook
        Engine_Hooks_Dispatcher::getInstance()->callEvent('onFieldsValuesSave', array(
              'item' => $classified,
              'values' => $values
        ));
    }

    /**
     *
     * Delete listing
     * Request params:
     * - iListingId     :       int, required. Listing Id
     *
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     *
     * @param array $aData
     * @return array $aResult
     */
    public function delete($aData)
    {
        extract($aData);
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId.")
            );
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $classified = Engine_Api::_()->getItem('classified', $iListingId);
        if( !$classified )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Classified listing doesn't exist or not authorized to delete.")
            );
        }
        if (!$classified->authorization()->isAllowed(null, 'delete'))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have permission to delete this listing.")
            );
        }
        $db = $classified->getTable()->getAdapter();
        $db->beginTransaction();
        try
        {
            $classified->delete();
            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get("Zend_Translate")->_("Your classified listing has been deleted.")
            );
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    /**
     *
     * Getting information for form edit classified
     * Request params: 
     * - iListingId             :   int, required. listing id
     * 
     * Response params: 
     * - category_options       :   array. category options
     *  - iCategoryId           :   int. category id
     *  - sCategoryTitle        :   string. category title
     *  
     * - custom_fields          :   array. custom fields
     *  - name                  :   string. field name
     *  - alias_field           :   string. alias field name 
     *  - value                 :   string. field value
     * 
     * - comment_options        :   array. comment options
     *  - sValue                :   string. option value
     *  - sPhrase               :   string. option phrase
     * 
     * - view_options           :   array. view options
     *  - sValue                :   string. option value
     *  - sPhrase               :   string. option phrase
     * 
     * 
     * @param array $aData
     * @return array $aResult
     */
    public function form_edit($aData)
    {
        extract($aData);
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId")
            );
        }
        $classified = Engine_Api::_()->getItem("classified", $iListingId);
        $privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
        $response  =  array(
            'view_options'=> $privacyApi->allowedPrivacy('classified', 'auth_view'),
            'comment_options'=> $privacyApi->allowedPrivacy('classified', 'auth_comment'),
            'category_options'=> $this->categories(),
            'custom_fields' => ($classified) 
                ? $this->getCustomFields($classified)
                : $this->getCustomFields()
        );
        return array_merge(
        $this->detail($aData),
        $response
        );
    }

    /**
     *
     * Getting information for form create classified
     * Request params: N/A
     * 
     * Response params: 
     * - category_options       :   array. category options
     *  - iCategoryId           :   int. category id
     *  - sCategoryTitle        :   string. category title
     *  
     * - custom_fields          :   array. custom fields
     *  - name                  :   string. field name
     *  - alias_field           :   string. alias field name 
     * 
     * - comment_options        :   array. comment options
     *  - sValue                :   string. option value
     *  - sPhrase               :   string. option phrase
     * 
     * - view_options           :   array. view options
     *  - sValue                :   string. option value
     *  - sPhrase               :   string. option phrase
     * 
     * - permissions            :   array. permissions
     *  - bValue                :   bool. permission value
     *  - sPhrase               :   string. permission phrase
     * 
     * @param array $aData
     * @return array $aResult
     */
    public function form_create($aData)
    {
        extract($aData);
        $privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
        $response  =  array(
            'view_options'=> $privacyApi->allowedPrivacy('classified', 'auth_view'),
            'comment_options'=> $privacyApi->allowedPrivacy('classified', 'auth_comment'),
            'category_options'=> $this->categories(),
            'custom_fields' => $this->getCustomFields(),
            'permissions' => $this -> getPermissions(),
        );
        return $response;
    }
    
    /**
     *
     * Getting classified permission
     * Request params: N/A
     * 
     * Response params: 
     * - permissions            :   array. permissions
     *  - bValue                :   bool. permission value
     *  - sPhrase               :   string. permission phrase
     * 
     * @param array $aData
     * @return array $aResult
     */
    public function permissions($aData)
    {
        extract($aData);
        $response  =  array(
            'permissions' => $this -> getPermissions(),
        );
        return $response;
    }

    /**
     *
     * Getting information for form search classified
     * Request params: N/A
     * 
     * Response params: 
     * - category_options       :   array. category options
     *  - iCategoryId           :   int. category id
     *  - sCategoryTitle        :   string. category title
     *  
     * - custom_fields          :   array. custom fields
     *  - name                  :   string. field name
     *  - alias_field           :   string. alias field name 
     * 
     * 
     * @param array $aData
     * @return array $aResult
     */
    public function form_search($aData)
    {
        extract($aData);
        $privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
        $response  =  array(
            'category_options'=> $this->categories(),
            'custom_fields' => $this->getAliasFields()
        );
        return $response;
    }
    
    
    /**
     *
     * view a classified detail.
     * Request params:
     * - iListingId     :   int, required. Listing identity
     *
     * Response params:
     * - iListingId     :   int. Listidentity
     * - sTitle         :   string. title
     * - sBody          :   string. title
     * - sListImageUrl  :   string. image url
     * - iOwnerId       :   int. owner id
     * - sOwnerName     :   string. owner name
     * - sOwnerImageUrl :   string. owner image url
     * - iTimestamp     :   int. creation data in timestamp format
     * - iTotalView     :   int. total view
     * - iTotalComment  :   int. total comment
     * - bIsClosed      :   boolean. this listing is closed or NOT
     * - bCanEdit       :   boolean. viewer can edit this listing or NOT
     * - bCanDelete     :   boolean. viewer can delete this listing or NOT
     * - bCanUploadPhoto:   boolean. viewer can upload photo to this listing or NOT
     * - sCustomContent :   string. Custom Fields content
     * - aPhotos        :   array. photos in this listing
     *
     * @param array $aData
     * @return array $aResult
     */
    public function detail($aData)
    {
        extract($aData);
        
        if(empty($fields)) $fields='detail';
        
        $fields =  explode(',', $fields);
        
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId")
            );
        }
        $classified = Engine_Api::_()->getItem("classified", $iListingId);
        if( !$classified )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Classified listing doesn't exist!")
            );
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $classified->getOwner();
        if( !$owner->isSelf($viewer) )
        {
            $classified->view_count++;
            $classified->save();
        }
        
        $view = Zend_Registry::get("Zend_View");
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        
        return Ynmobile_AppMeta::_export_one($classified, $fields);
        /**
         * Get photos
         */
        // album material
        $album = $classified->getSingletonAlbum();
        $PhotoPaginator = $album->getCollectiblesPaginator();
        $PhotoPaginator -> setCurrentPageNumber(1);
        $PhotoPaginator -> setItemCountPerPage(999);
      
        /**
         * Get custome fields value
         */
        
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified);
        $result =  $this->parseList(array($classified), true);
        $canComment = $classified->authorization()->isAllowed($viewer, 'comment');
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        $result['sCustomContent'] = $view->fieldValueLoop($classified, $fieldStructure);
        $result['bCanEdit'] = ($classified->authorization()->isAllowed(null, 'edit')) ? true : false;
        $result['bCanView'] = ($classified->authorization()->isAllowed(null, 'view')) ? true : false;
        $result['bCanDelete'] = ($classified->authorization()->isAllowed(null, 'delete')) ? true : false;
        $result['bCanUploadPhoto'] = ($classified->authorization()->isAllowed(null, 'photo')) ? true : false;
        $result['bCanComment'] = ($canComment) ? true : false;
        $result['bCanLike'] = ($canComment) ? true : false;
        $result['bIsLiked'] = $classified -> likes() -> isLike($viewer);
        $result['iTotalLike'] = $classified -> likes() -> getLikePaginator() -> getTotalItemCount();
        $result['iTotalComment'] = $classified -> comments() -> getCommentPaginator() -> getTotalItemCount();
        $result['iCategoryId'] = $classified->category_id;
        $result['sCategory'] = (isset($categories[$classified->category_id])) ? $categories[$classified->category_id] : '';
        $result['sHref']= Engine_Api::_()->ynmobile()->finalizeUrl($classified->getHref());
        $result['aLikes'] = Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($classified);
        $result['aPhotos'] = array();
        foreach( $PhotoPaginator as $photo )
        {
            $sPhotoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl());
            $result['aPhotos'][] = array(
                    'sDescription' => $view->string()->chunk($photo->getDescription(), 100),
                    'sTitle' => $photo->getTitle(),
                    'sPhotoUrl' => $sPhotoUrl,
                    'iPhotoId' => $photo -> photo_id,
                    'bIsMainPhoto' => ($classified->photo_id == $photo->file_id) ?  true : false
            );
        }
        $auth = Engine_Api::_() -> authorization() -> context;
        $roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );
        foreach ($roles as $role)
        {
            if (1 === $auth -> isAllowed($classified, $role, 'view'))
            {
                $sViewPrivacy = $role;
            }
            if (1 === $auth -> isAllowed($classified, $role, 'comment'))
            {
                $sCommentPrivacy = $role;
            }
        }
        $result['sViewPrivacy'] = $sViewPrivacy;
        $result['sCommentPrivacy'] = $sCommentPrivacy;
        return $result;
    }

    /**
     *
     * Close a listing
     *
     * Request params:
     * - iListingId     :   int, required. Listing identity
     *
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     *
     * @param array $aData
     * @return array $aResult
     */
    public function close($aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId.")
            );
        }
        $classified = Engine_Api::_()->getItem('classified', $iListingId);
        if( !$classified )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Classified listing doesn't exist or not authorized to delete.")
            );
        }
        if (!$classified->authorization()->isAllowed($viewer, 'edit'))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have any permission to close this listing.")
            );
        }

        $table = $classified->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try
        {
            if ( isset($iStatus) && $iStatus === 0 )
                $classified->closed = $iStatus;
            else
                $classified->closed = 1;
            $classified->save();
            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => ( isset($iStatus) && $iStatus === 0 )
                    ? Zend_Registry::get("Zend_Translate")->_("Opened this listing successfully.")
                    : Zend_Registry::get("Zend_Translate")->_("Closed this listing successfully.")
            );      
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }
    
    /**
     *
     * Open a listing
     *
     * Request params:
     * - iListingId     :   int, required. Listing identity
     *
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     *
     * @param array $aData
     * @return array $aResult
     */
    public function open($aData)
    {
        $aData['iStatus'] = 0;
        return $this->close($aData);
    }
    
    /**
     * Upload photo to listing
     * 
     * Request params:
     * - iListingId     :   int, required. Listing ID
     * - photo          :   FILE data, required. photo data
     * 
     * Response params
     * - error_code     : int. error code
     * - error_message  : string. error message
     * - message        : string. status message
     * 
     * @param array $aData
     * @return array $aResult
     */
    public function upload($aData)
    {
        extract($aData);
        
        
        if ( !isset($iListingId) || empty($iListingId) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId.")
            );
        }
        $classified = Engine_Api::_()->getItem('classified', $iListingId);
        if( !$classified )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Classified listing doesn't exist or not authorized to delete.")
            );
        }
        if( !isset($_FILES['image']) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("No File.")
            );
        }
        $photoTable = Engine_Api::_()->getDbtable('photos', 'classified');
        $db = $photoTable->getAdapter();
        $db->beginTransaction();
        try 
        {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $classified->getSingletonAlbum();
            $params = array(
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),
                'classified_id' => $classified->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );
            //$photo = Engine_Api::_()->classified()->createPhoto($params, $_FILES['image']);
            $photo = $this->createPhoto($params, $_FILES['image']);
            $photo_id = $photo->photo_id;
            if( !$classified->photo_id ) 
            {
                $classified->photo_id = $photo_id;
                $classified->save();
            }
            $db->commit();
            $photoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl());
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get("Zend_Translate")->_("Uploaded photo successfully."),
                'iPhotoId' => $photo_id,
                'sPhotoUrl' => $photoUrl,
            );
        } 
        catch( Exception $e ) 
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
                'error_line'=>$e->getLine(),
                'error_file'=>$e->getFile(),
            );
            
        }
    }
    
    public function photo_delete($aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!isset($iPhotoId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iPhotoId!")
            );
        }
        $photo = Engine_Api::_()->getItem('classified_photo', $iPhotoId);
        if (!$photo)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("This photo is not existed!")
            );
        }
        if (!isset($iListingId) || empty($iListingId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iListingId")
            );
        }
        $classified = Engine_Api::_()->getItem("classified", $iListingId);
        if( !$classified )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Classified listing doesn't exist!")
            );
        }
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();
        try
        {
            if( $classified->photo_id == $photo->file_id ) 
            {
                $classified->photo_id = 0;
                $classified->save();
            }
            $photo->delete();
            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get("Zend_Translate")->_("Deleted photo successfully!")
            ); 
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }
    
    public function createPhoto($params, $file)
    {
         if( $file instanceof Storage_Model_File )
         {
           $params['file_id'] = $file->getIdentity();
         }
         else
         {
           // Get image info and resize
           $name = basename($file['tmp_name']);
           $path = dirname($file['tmp_name']);
           $extension = ltrim(strrchr($file['name'], '.'), '.');
    
           $mainName = $path.'/m_'.$name . '.' . $extension;
           $thumbName = $path.'/t_'.$name . '.' . $extension;
           
    
            $angle = 0;
            
            if (function_exists("exif_read_data"))
            {
                $exif = exif_read_data($file['tmp_name']);
                if (!empty($exif['Orientation']))
                {
                    switch($exif['Orientation'])
                    {
                        case 8 :
                            $angle = 90;
                            break;
                        case 3 :
                            $angle = 180;
                            break;
                        case 6 :
                            $angle = -90;
                            break;
                    }
                }
            }
           
           $image = Engine_Image::factory();
          
           
           $image -> open($file['tmp_name']);
            if ($angle != 0)
                $image -> rotate($angle);
           $image->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
               ->write($mainName)
               ->destroy();
    
           $image = Engine_Image::factory();
           $image -> open($file['tmp_name']);
            if ($angle != 0)
                $image -> rotate($angle);
           $image->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
               ->write($thumbName)
               ->destroy();
    
           // Store photos
           $photo_params = array(
             'parent_id' => $params['classified_id'],
             'parent_type' => 'classified',
           );
    
           $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
           $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
           $photoFile->bridge($thumbFile, 'thumb.normal');
    
           $params['file_id'] = $photoFile->file_id; // This might be wrong
           $params['photo_id'] = $photoFile->file_id;
           
           // process case file not exists
           if(1){
                if($photoFile->file_id == 0){
                    $photoFile->delete();
                }
                if($thumbFile->file_id ==0){
                    $thumbFile->delete();
                }
                
                $table =  Engine_Api::_()->getDbTable('files','storage');
                if(null == $table->findRow($photoFile->file_id)){
                    $photoFile->delete();
                }
                
                if(null == $table->findRow($thumbFile->file_id)){
                    $thumbFile->delete();
                }
           }
                     
            
    
           // Remove temp files
           @unlink($mainName);
           @unlink($thumbName);
         }
    
         $row = Engine_Api::_()->getDbtable('photos', 'classified')->createRow();
         $row->setFromArray($params);
         $row->save();
         return $row;
    }
}

