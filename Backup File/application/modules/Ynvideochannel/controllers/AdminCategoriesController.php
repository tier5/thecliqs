<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_AdminCategoriesController extends Core_Controller_Action_Admin {
    protected $_paginate_params = array();
    public function init() {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_categories');
    }

    public function getDbTable() {
        return Engine_Api::_() -> getDbTable('categories', 'ynvideochannel');
    }

    public function indexAction() {
        $table = $this -> getDbTable();
        $node = $table -> getNode($this -> _getParam('parent_id', 0));
        $this -> view -> categories = $node -> getChilren();
        $this -> view -> category = $node;
    }

    public function addCategoryAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $parentId = $this -> _getParam('parent_id', 0);
        $form = $this -> view -> form = new Ynvideochannel_Form_Admin_Category();
        $form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();
        $node = $table -> getNode($parentId);
        //maximum 4 level category
        if ($node -> level > 3) {
            throw new Zend_Exception('Maximum 4 levels of category.');
        }
        // Check post
        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
            // we will add the category
            $values = $form -> getValues();
            $user = Engine_Api::_() -> user() -> getViewer();
            $data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
            $table -> addChild($node, $data);
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }

        // Output
        $this -> renderScript('admin-categories/form.tpl');
    }

    public function deleteCategoryAction() {

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $this -> view -> category_id = $id;
        $table = $this -> getDbTable();
        $node = $table -> getNode($id);
        $categories = array();
        $table -> appendChildToTree($node, $categories);
        unset($categories[0]);

        $hasVideo = $node -> checkHasVideo();
        $optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynvideochannel_video');
        $tableVideo = Engine_Api::_() -> getItemTable('ynvideochannel_video');
        $this -> view -> moveCates = array();;
        // Check post
        if ($this -> getRequest() -> isPost()) {
            // go through logs and see which classified used this category and set it to ZERO
            if (is_object($node)) {
                $arr_optionIds[] = $node -> option_id;
                //delete type + custom field of sub categories
                foreach ($categories as $item)
                {
                    $option = $optionData -> getRowMatching('label', $item -> title);
                    if ($option) {
                        $this -> typeDelete($option -> option_id);
                        $arr_optionIds[] = $option -> option_id;
                    }
                }

                //set video category to 0
                if ($hasVideo) {
                    // Prepare to delete custom fields of this video
                    $tableMaps = Engine_Api::_()->getDbTable('maps', 'ynvideochannel');
                    $tableValues = Engine_Api::_()->getDbTable('values', 'ynvideochannel');
                    $fieldIds = $tableMaps->fetchAll($tableMaps->select()->where('option_id IN (?)', $arr_optionIds));
                    $arr_ids = array();
                    if (count($fieldIds) > 0) {
                        foreach ($fieldIds as $fieldId) {
                            $arr_ids[] = $fieldId->child_id;
                        }
                    }
                    $videos = $tableVideo -> getAllChildrenVideosByCategory($node);
                    foreach ($videos as $items) {
                        foreach ($items as $videoItem) {
                            $db = $tableVideo -> getAdapter();
                            try {
                                $db -> beginTransaction();
                                $videoItem -> category_id = 0;
                                $videoItem -> save();
                                //delete in values table
                                if (count($arr_ids) > 0) {
                                    $valueItems = $tableValues->fetchAll($tableValues->select()->where('item_id = ?', $videoItem -> getIdentity())->where('field_id IN (?)', $arr_ids));
                                    foreach ($valueItems as $valueItem) {
                                        $valueItem->delete();
                                    }
                                }

                                $db -> commit();
                            } catch(Exception $e) {
                                $db -> rollBack();
                                throw $e;
                            }
                        }
                    }
                }

                //set channel category to 0
                $hasChannel = $node -> checkHasChannel();
                if ($hasChannel)
                {
                    $tableChannel = Engine_Api::_() -> getItemTable('ynvideochannel_channel');
                    $channels = $tableChannel -> getAllChildrenChannelsByCategory($node);
                    foreach ($channels as $items) {
                        foreach ($items as $channelItem) {
                            $db = $tableChannel -> getAdapter();
                            try {
                                $db -> beginTransaction();
                                $channelItem -> category_id = 0;
                                $channelItem -> save();
                                $db -> commit();
                            } catch(Exception $e) {
                                $db -> rollBack();
                                throw $e;
                            }
                        }
                    }
                }

                // set playlist category to 0
                $hasPlaylist = $node -> checkHasPlaylist();
                if ($hasPlaylist)
                {
                    $tablePlaylist = Engine_Api::_() -> getItemTable('ynvideochannel_playlist');
                    $playlists = $tablePlaylist -> getAllChildrenPlaylistsByCategory($node);
                    foreach ($playlists as $items) {
                        foreach ($items as $playlistItem) {
                            $db = $tablePlaylist -> getAdapter();
                            try {
                                $db -> beginTransaction();
                                $playlistItem -> category_id = 0;
                                $playlistItem -> save();
                                $db -> commit();
                            } catch(Exception $e) {
                                $db -> rollBack();
                                throw $e;
                            }
                        }
                    }
                }

                //delete its type + node
                $this -> typeDelete($node -> option_id);
                $table -> deleteNode($node);
            }
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }
    }

    public function editCategoryAction() {

        // Must have an id
        if (!($id = $this -> _getParam('id'))) {
            throw new Zend_Exception('No identifier specified');
        }
        // Generate and assign form
        $category = Engine_Api::_() -> getItem('ynvideochannel_category', $id);

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $form = $this -> view -> form = new Ynvideochannel_Form_Admin_Category( array('category' => $category));
        $form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $isSub = false;
        if ($category -> parent_id != '1') {
            $isSub = true;
        }

        // Check post
        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
            // Ok, we're good to add field
            $values = $form -> getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                // edit category in the database
                // Transaction
                $row = Engine_Api::_() -> getItem('ynvideochannel_category', $values["id"]);
                $row -> title = $values["label"];
                $row -> save();
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }

        $form -> setField($category, $isSub);

        // Output
        $this -> renderScript('admin-categories/form.tpl');
    }

    public function ajaxUseParentCategoryAction() {
        $categoryId = $this -> _getParam('id');
        $category = Engine_Api::_() -> getItem('ynvideochannel_category', $categoryId);
        $category -> save();
    }

    public function typeDelete($option_id) {
        $option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynvideochannel_video');
        $field = Engine_Api::_() -> fields() -> getField($option -> field_id, 'ynvideochannel_video');

        // Validate input
        if ($field -> type !== 'profile_type') {
            throw new Exception('invalid input');
        }

        // Do not allow delete if only one type left
        if (count($field -> getOptions()) <= 1) {
            throw new Exception('only one left');
        }
        // Process
        Engine_Api::_() -> fields() -> deleteOption('ynvideochannel_video', $option);
    }

    public function sortAction() {
        $table = $this -> getDbTable();
        $node = $table -> getNode($this -> _getParam('parent_id', 0));
        $categories = $node -> getChilren();
        $order = explode(',', $this -> getRequest() -> getParam('order'));
        foreach ($order as $i => $item) {
            $category_id = substr($item, strrpos($item, '_') + 1);
            foreach ($categories as $category) {
                if ($category -> category_id == $category_id) {
                    $category -> order = $i;
                    $category -> save();
                }
            }
        }
    }

}
