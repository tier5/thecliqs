<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 16.07.12
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */

class Page_AdminImportController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_import');
  }

  public function indexAction()
  {
    $this->view->form = $form = new Page_Form_Admin_Import_File();

    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_import', array(), 'page_admin_import_file');

    if (!$this->getRequest()->isPost()){
      return ;
    }

    if( !$form->isValid($values = $this->getRequest()->getPost()) ) {
      return;
    }

    if(!isset( $_FILES['import_file'] )) {
      return;
    }

    $form->import_file->receive();

    $seperator = $form->getValue('seperator');
    $marker = $form->getValue('marker');
    $activity = $form->getValue('activity');

    try{
      $file = $form->import_file->getFileName();
      $name = basename($file);
      $path = APPLICATION_PATH . '/public/temporary';

      $file = Engine_Api::_()->storage()->create($path.'/'.$name, array('parent_type' => 'user'));
    } catch( Exception $e ) {
      throw $e;
    }
    unlink($path.'/'.$name);

    // Redirect
    return $this->_helper->redirector->gotoRoute(array(
      'module' => 'page',
      'controller' => 'import',
      'action' => 'save-file',
      'file_id' => $file->file_id,
      'seperator' => $seperator,
      'marker' => $marker,
      'activity' => $activity
      ), 'admin_default', true);
  }

  public function saveFileAction()
  {
    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_import', array(), 'page_admin_import_file');

    $file_id = $this->_getParam('file_id');
    $seperator = $this->_getParam('seperator');
    $marker = $this->_getParam('marker');
    $activity = $this->_getParam('activity');

    if( !$file_id ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'index'), 'admin_default', true);
    }

    $file = Engine_Api::_()->getItem('storage_file', $file_id);

    $handle = fopen($file->storage_path, "r");

    $seek = 1;

    while(count($options = fgetcsv($handle, 0, $seperator)) <= 1) $seek++;
    $options[] = '';
    $form = new Page_Form_Admin_Import_Configure($options);
    $form->file_id->setValue($file->file_id);
    $form->seperator->setValue($seperator);
    $form->marker->setValue($marker);
    $form->activity->setValue($activity);

    $this->view->form = $form;
    fclose($handle);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    /**
     *  @var $table Page_Model_DbTable_Imports
     */

    $table = Engine_Api::_()->getDbTable('imports', 'page');
    $db = $table->getDefaultAdapter();

    $db->beginTransaction();
    try{
      $row = $table->createRow();
      $row->file_id = $file_id;
      $row->file_name = $file->name;
      $row->seek = $seek;
      $row->options = serialize($values);

      $row->save();

    } catch(Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->commit();

    return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);
  }

  public function filesAction()
  {
    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_import', array(), 'page_admin_import_csvfiles');

    $params = array(
      'ipp' => $this->_getParam('ipp', 10),
      'page' => $this->_getParam('page', 1),
      'order' => $this->_getParam('order', 'creation_date ASC')
    );

    $this->view->paginator = Engine_Api::_()->getDbTable('imports', 'page')->getAllImportsPaginator($params);
  }

  public function deleteAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();
    $import_id = $this->_getParam('id', false);

    if( $import_id ) {
      $import = Engine_Api::_()->getItem('page_import', $import_id);
      $import->delete();
    }

    $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);
  }

  public function sitepageAction()
  {
    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_import', array(), 'page_admin_import_sitepage');

    $table = Engine_Api::_()->getDbTable('modules', 'core');
    if( !$this->view->isSitepage = $table->hasModule('sitepage') )
      return;

    $db = Engine_Db_Table::getDefaultAdapter();
    $urls = $db->select()->from('engine4_page_pages', array("page_url" => 'url' ));

    $select = $db->select()
      ->from(array('sitepage' => 'engine4_sitepage_pages'), array("count" => new Zend_Db_Expr('Count(*)')))
      ->where('page_url NOT IN ?', $urls);

    $sitepageCount = $select->query()->fetch();
    $sitepageCount = $sitepageCount['count'];
    $this->view->pageCount = $sitepageCount;

    if( $this->view->isSitepage = $table->hasModule('sitepage') && $sitepageCount > 0 ) {
      $form = new Page_Form_Admin_Import_Sitepage();

      $linkHTML = $this->view->htmlLink($this->view->url(array(
        'module' => 'page',
        'controller' => 'import',
        'action' => 'import-sitepage'
      ), 'admin_default', true), $this->view->translate('Import'), array('class' => 'smoothbox'));

      $description = sprintf($this->view->translate('There is %s pages, If you want to import pages, click %s'), $sitepageCount, $linkHTML);
      $form->addNotice($description);

      $this->view->form = $form;
    }
  }

  public function importSitepageAction()
  {

    $table = Engine_Api::_()->getDbTable('modules', 'core');
    if( $this->view->isSitepage = $table->hasModule('sitepage') ) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $urls = $db->select()->from('engine4_page_pages', array("page_url" => 'url' ));
      $select = $db->select()
        ->from(array('sitepage' => 'engine4_sitepage_pages'), array("count" => new Zend_Db_Expr('Count(*)')))
        ->where('page_url NOT IN ?', $urls);

      $pageCount = $select->query()->fetch();
      $pageCount = $pageCount['count'];
      $this->view->pageCount = $pageCount;

      if( $pageCount ) {
        $sitePage = $db->select()
          ->from('engine4_sitepage_pages')
          ->where('page_url NOT IN ?', $urls)
          ->limit(1)
          ->query()
          ->fetchAll();
        $sitePage = $sitePage[0];

        $db->beginTransaction();
        try {
          $page = Engine_Api::_()->getItemTable('page')->createRow();
          $page->name = $sitePage['page_url'];
          $page->url = $sitePage['page_url'];
          $page->title = $sitePage['title'];
          $page->displayname = $sitePage['title'];
          $page->description = $sitePage['body'];
          $page->view_count = $sitePage['view_count'];
          $page->user_id = $sitePage['owner_id'];
          $page->enabled = 1;

          $location = $db->select()->from('engine4_sitepage_locations')->where('page_id = ?', $sitePage['page_id'])->limit(1)->query()->fetchAll();
          if(count($location)) {
            $location = $location[0];
            $page->country = $location['country'];
            $page->state = $location['state'];
            $page->city = $location['city'];
            $page->street = $location['address'];
          }

          $page->website = $sitePage['website'];
          $page->phone = $sitePage['phone'];
          $page->comment_count = $sitePage['comment_count'];
          $page->creation_date = $sitePage['creation_date'];
          $page->modified_date = $sitePage['modified_date'];
          $page->featured = $sitePage['featured'];
          $page->approved = $sitePage['approved'];

          $page->search = $sitePage['search'];
          $page->parent_type = 'user';
          $page->parent_id = $sitePage['owner_id'];
          $page->note = $sitePage['notes'];
          $page->sponsored = $sitePage['sponsored'];
          $page->save();

          // Page Photo
          if( $sitePage['photo_id'] ) {
            $photo = Engine_Api::_()->getDbTable('files', 'storage')->getFile($sitePage['photo_id']);
            $page->setPhoto(array('tmp_name' => $photo->storage_path));
          }

          $this->createContent($page, $sitePage);

          //Adding Members
          $user_ids = $db->select()->from('engine4_sitepage_manageadmins', array('user_id'))->where('page_id = ?', $sitePage['page_id'])->query()->fetchAll();

          foreach( $user_ids as $user_id ) {
            $user_id = $user_id['user_id'];
            $user = Engine_Api::_()->getItem('user', $user_id);
            $page->membership()->addMember($user)->setUserApproved($user)->setResourceApproved($user)->setUserTypeAdmin($user);
            $page->setAdmin($user);
            $page->getTeamList()->add($user);
          }

          // Privacy Settings
          $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'Registered Members',
            'likes' => 'Likes, Admins and Owner',
            'team' => 'Admins and Owner Only'
          );
          $authTb = Engine_Api::_()->authorization()->getAdapter('levels');

          $view_options = (array) $authTb->getAllowed('page', $page->getOwner(), 'auth_view');
          $view_options = array_intersect_key($availableLabels, array_flip($view_options));

          $comment_options = (array) $authTb->getAllowed('page', $page->getOwner(), 'auth_comment');
          $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

          $posting_options = (array) $authTb->getAllowed('page', $page->getOwner(), 'auth_posting');
          $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

          if ( $page->save() ) {
            $values = array(
              'auth_view' => key($view_options),
              'auth_comment' => key($comment_options),
              'auth_album_posting' => key($posting_options),
              'auth_blog_posting' => key($posting_options),
              'auth_disc_posting' => key($posting_options),
              'auth_doc_posting' => key($posting_options),
              'auth_event_posting' => key($posting_options),
              'auth_music_posting' => key($posting_options),
              'auth_video_posting' => key($posting_options)
            );
            $page->setPrivacy($values);
          }

          // Adding Tags
          $tags = $db->select()
            ->from(array('tags' => 'engine4_core_tags'), array('text'))
            ->joinLeft(array('tagmaps' => 'engine4_core_tagmaps'), 'tags.tag_id = tagmaps.tag_id', array())
            ->where("tagmaps.resource_type = 'sitepage_page'")
            ->where('tagmaps.resource_id = ?', $sitePage['page_id'])
            ->group('tags.text')
            ->query()
            ->fetchAll()
          ;

          if( count($tags) ) {
            $tags_arr = array();
            foreach($tags as $tag) {
              $tags_arr[] = $tag['text'];
            }
            $page->tags()->addTagMaps($page->getOwner(), $tags_arr);
            $page->keywords = implode(",", $tags_arr);
          }

          $page->save();

          // Import Categories
          $labales = $db->select()->from('engine4_page_fields_options', array('label'));
          $count = $db->select()
            ->from('engine4_sitepage_categories', array('count' => new Zend_Db_Expr("COUNT(*)")))
            ->where('category_name NOT IN ?', $labales)
            ->query()
            ->fetch();

          if( $count['count'] ) {
            $this->importFields();
          }

          // Page Category
          if( !$sitePage['category_id'] ) {
            $db->insert('engine4_page_fields_search', array(
              'item_id' => $page->page_id,
              'profile_type' => 1
            ));
            $db->insert('engine4_page_fields_values', array(
              'item_id' => $page->page_id,
              'field_id' => 1,
              'value' => 1
            ));
          } else {
            $category = $db->select()
              ->from('engine4_sitepage_categories')
              ->where('category_id = ?', $sitePage['category_id'])
              ->limit(1)
              ->query()
              ->fetchAll();

            $category = $category[0];

            $profile_type = $db->select()
              ->from('engine4_page_fields_options')
              ->where("label = ?", $category['category_name'])
              ->where('field_id = 1')->limit(1)
              ->query()
              ->fetchAll();

            $profile_type = $profile_type[0];
            $db->insert('engine4_page_fields_search', array(
              'item_id' => $page->page_id,
              'profile_type' => $profile_type['option_id']
            ));
            $db->insert('engine4_page_fields_values', array(
              'item_id' => $page->page_id,
              'field_id' => 1,
              'value' => $profile_type['option_id']
            ));

            if( $sitePage['subsubcategory_id'] > 0 ) {
              $category = $db->select()
                ->from('engine4_sitepage_categories')
                ->where('category_id = ?', $sitePage['subsubcategory_id'])
                ->limit(1)
                ->query()
                ->fetchAll();

              $category = $category[0];
              $profile_type = $db->select()
                ->from('engine4_page_fields_options')
                ->where("label = ?", $category['category_name'])
                ->where('field_id <> 1')
                ->limit(1)
                ->query()
                ->fetchAll();
              $profile_type = $profile_type[0];

              $db->insert('engine4_page_fields_values', array(
                'item_id' => $page->page_id,
                'field_id' => $profile_type['field_id'],
                'value' => $profile_type['option_id']
              ));
            } elseif( $sitePage['subcategory_id'] > 0 ) {
              $category = $db->select()
                ->from('engine4_sitepage_categories')
                ->where('category_id = ?', $sitePage['subcategory_id'])
                ->limit(1)
                ->query()
                ->fetchAll();

              $category = $category[0];

              $profile_type = $db->select()
                ->from('engine4_page_fields_options')
                ->where("label = ?", $category['category_name'])
                ->where('field_id <> 1')
                ->limit(1)
                ->query()
                ->fetchAll();
              $profile_type = $profile_type[0];

              $db->insert('engine4_page_fields_values', array(
                'item_id' => $page->page_id,
                'field_id' => $profile_type['field_id'],
                'value' => $profile_type['option_id']
              ));
            }
          }

          // Page Markers
          if( count($location) ) {
            $marker = Engine_Api::_()->getDbTable('markers', 'page')->createRow();
            $marker->page_id = $page->page_id;
            $marker->latitude = $location['latitude'];
            $marker->longitude = $location['longitude'];
            $marker->save();
          }

          // Page Claim
          $claims = $db->select()
            ->from('engine4_sitepage_claims')
            ->where('page_id = ?', $sitePage['page_id'])
            ->query()
            ->fetchAll();
          $status = array(0 => 'pending', 1 => 'approved', 2 => 'declined', 4 => 'pending');
          foreach( $claims as  $claim ) {
            $db->insert('engine4_page_claims', array(
              'page_id' => $sitePage['page_id'],
              'claimer_name' => $claim['nickname'],
              'claimer_email' => $claim['email'],
              'claimer_phone' => $claim['contactno'],
              'descrition' => $claim['comments'],
              'creation_date' => $claim['creation_date'],
              'user_id' => $claim['user_id'],
              'status' => $status[$claim['status']],
            ));

            if( !count($db->select()->from('engine4_user_settings')->where("name = 'claimable_page_creator'")->where('user_id = ?', $claim['user_id'])->query()->fetchAll()) ) {
              $db->insert('engine4_user_settings', array(
                'name' => 'claimable_page_creator',
                'user_id' => $claim['user_id'],
                'value' => '1'));
            }
          }

          //Activity
          $action_types = array(
            'sitepage_new' => 'page_create',
            'sitepage_post' => 'post',
            'sitepage_post_self' => 'post',
          );

          foreach($action_types as $key => $type) {
            // Activity actions
            $actions = $db->select()
              ->from('engine4_activity_actions')
              ->where("object_type = 'sitepage_page'")
              ->where('type = ?', $key)
              ->where('object_id = ?', $sitePage['page_id'])
              ->query()
              ->fetchAll();


            foreach( $actions as $action ) {
              $db->insert('engine4_activity_actions', array(
                'type' => $type,
                'subject_type' => 'user',
                'subject_id' => $action['subject_id'],
                'object_type' => 'page',
                'object_id' => $page->page_id,
                'body' => $action['body'],
                'params' => $action['params'],
                'date' => $action['date'],
                'attachment_count' => $action['attachment_count'],
                'comment_count' => $action['comment_count'],
                'like_count' => $action['like_count']
              ));

              $action_id = $db->lastInsertId();

              // Activity Stream
              $db->insert('engine4_activity_stream', array(
                'target_type' => 'page_feed',
                'target_id' => 0,
                'subject_type' => 'user',
                'subject_id' => $sitePage['owner_id'],
                'object_type' => 'page',
                'object_id' => $page->page_id,
                'type' => $type,
                'action_id' => $action_id
              ));

              $db->insert('engine4_activity_stream', array(
                'target_type' => 'page_registered',
                'target_id' => 0,
                'subject_type' => 'user',
                'subject_id' => $sitePage['owner_id'],
                'object_type' => 'page',
                'object_id' => $page->page_id,
                'type' => $type,
                'action_id' => $action_id
              ));

              $db->insert('engine4_activity_stream', array(
                'target_type' => 'page',
                'target_id' => $page->page_id,
                'subject_type' => 'user',
                'subject_id' => $sitePage['owner_id'],
                'object_type' => 'page',
                'object_id' => $page->page_id,
                'type' => $type,
                'action_id' => $action_id
              ));

              if( $type == 'page_create' ) {
                $db->insert('engine4_activity_stream', array(
                  'target_type' => 'owner',
                  'target_id' => $sitePage['owner_id'],
                  'subject_type' => 'user',
                  'subject_id' => $sitePage['owner_id'],
                  'object_type' => 'page',
                  'object_id' => $page->page_id,
                  'type' => $type,
                  'action_id' => $action_id
                ));
                $db->insert('engine4_activity_stream', array(
                  'target_type' => 'parent',
                  'target_id' => $sitePage['owner_id'],
                  'subject_type' => 'user',
                  'subject_id' => $sitePage['owner_id'],
                  'object_type' => 'page',
                  'object_id' => $page->page_id,
                  'type' => $type,
                  'action_id' => $action_id
                ));
              }

              // Attachment
              $db->query("
                INSERT INTO `engine4_activity_attachments` SELECT
                '' AS `attachment_id`,
                {$action_id} AS `action_id`,
                'page' AS `type`,
                {$page->page_id} AS `id`,
                1 AS `mode`
                FROM `engine4_activity_attachments`
                WHERE `action_id` = {$action['action_id']} AND `type` = 'sitepage_page' AND `id` = {$sitePage['page_id']}
              ");


              // Activity Comment
              $db->query("
                INSERT INTO `engine4_activity_comments` SELECT
                '' AS `comment_id`,
                {$action_id} AS `resource_id`,
                'user' AS `poster_type`,
                poster_id AS `poster_id`,
                body AS `body`,
                creation_date AS `creation_date`,
                like_count AS `like_count`
                FROM `engine4_activity_comments`
                WHERE `resource_id` = {$action['action_id']}
              ");
              $db->query("
                INSERT INTO `engine4_activity_comments` SELECT
                '' AS `comment_id`,
                {$action_id} AS `resource_id`,
                'user' AS `poster_type`,
                poster_id AS `poster_id`,
                body AS `body`,
                creation_date AS `creation_date`,
                like_count AS `like_count`
                FROM `engine4_core_comments`
                WHERE `resource_id` = {$sitePage['page_id']} AND `resource_type` = 'sitepage_page'
              ");
              $comment_count = $db->select()
                ->from('engine4_activity_comments', array('count' => new Zend_Db_Expr("COUNT(*)")))
                ->where('resource_id = ?', $action_id)
                ->query()
                ->fetch();
              $comment_count = $comment_count['count'];
              $db->update('engine4_activity_actions', array('comment_count' => $comment_count), "action_id = {$action_id}");

              // Activity LIKEs
              $db->query("INSERT INTO `engine4_activity_likes` SELECT
                '' AS `like_id`,
                {$action_id} AS `resource_id`,
                'user' AS `poster_type`,
                poster_id AS `poster_id`
                FROM `engine4_activity_likes`
                WHERE `resource_id` = {$action['action_id']}
              ");
            }
          }

          // CORE LIKEs
          $db->query("INSERT INTO `engine4_core_likes` SELECT
            '' AS `like_id`,
            'page' AS `resource_type`,
            {$page->page_id} AS `resource_id`,
            'user' AS `poster_type`,
            poster_id AS `poster_id`,
            creation_date AS `creation_date`
            FROM `engine4_core_likes`
            WHERE `resource_type` = 'sitepage_page' AND
                  `resource_id` = {$sitePage['page_id']}
            ");

          // Comment
          $db->query("
                INSERT INTO `engine4_core_comments` SELECT
                '' AS `comment_id`,
                'page' AS `resource_type`,
                {$page->page_id} AS `resource_id`,
                'user' AS `poster_type`,
                poster_id AS `poster_id`,
                body AS `body`,
                creation_date AS `creation_date`,
                like_count AS `like_count`
                FROM `engine4_core_comments`
                WHERE `resource_id` = {$sitePage['page_id']} AND `resource_type` = 'sitepage_page'
              ");

        }catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
        $db->commit();
      } else {
        // Page Favorites
        $favs = $db->select()->from('engine4_sitepage_favourites', array('page_id' => 'page_id_for', 'page_fav_id' => 'page_id'))->query()->fetchAll();
        $page_ids = array();

        foreach( $favs as $fav ) {
          $id = $db->select()
            ->from(array('p' => 'engine4_page_pages'), array('page_id'))
            ->joinLeft(array('s' => 'engine4_sitepage_pages'), "s.page_url = p.url", array())
            ->where('s.page_id = ?', $fav['page_id'])
            ->query()->fetch();

          $id1 = $db->select()->from(array('p' => 'engine4_page_pages'), array('page_id'))->joinLeft(array('s' => 'engine4_sitepage_pages'), "s.page_url = p.url", array())->where('s.page_id = ?', $fav['page_fav_id'])->limit(1)->query()->fetch();
          $page_ids[] = array('page_id' => $id['page_id'], 'page_fav_id' => $id1['page_id']);
        }


        foreach( $page_ids as $page_id) {
          $db->query("INSERT IGNORE INTO engine4_page_favorites (`page_id`, `page_fav_id`) VALUES
          ( {$page_id['page_id']} , {$page_id['page_fav_id']} );");
        }
      }
    }

  }

  public function startAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $import_id = $this->_getParam('id', false);
    if( !$import_id ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);
    }

    $this->view->import = $import = Engine_Api::_()->getItem('page_import', $import_id);
    if( !$import->import_id || !$import->file_id ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $values = unserialize($import->options);
    $seperator = $values['seperator'];
    $file = $import->getFile();
    $file_path = $file->storage_path;
    /**
     * @var $pageTbl Page_Model_DbTable_Pages
     */
    $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $db = $pageTbl->getDefaultAdapter();

    $fileObj = new splFileObject($file_path);

    $fileObj->seek($import->seek);

    if( $fileObj->eof() ) {
      $import->status = 2;
      $import->save();
      return ;
    }

    //  skip empty lines
    $data_str = $fileObj->current();
    $data = $this->str_getcsv($data_str, $seperator);

    $i = 0;

    while($i < 100 && !$fileObj->eof()) {
      if(count($data) <= 1) {
        $fileObj->next();
        $data_str = $fileObj->current();
        $data = $this->str_getcsv($data_str, $seperator);
        $import->seek++;
        $import->save();
        continue;
      }

      $i++;
      //------------------- begin of proccess -----------------------//
      $db->beginTransaction();
      $data[] = '';
      try {
        // Checking page url
        $values['url'] = $data[$values['title']];
        $values['url'] = str_replace(' ', '', $values['url']);
        $values['url'] = strtolower(trim($values['url']));
        $values['url'] = preg_replace('/[^a-z0-9-]/', '-', $values['url']);
        $values['url'] = preg_replace('/-+/', "-", $values['url']);

        $page_url = $values['url'];
        $counter = 1;
        while( $pageTbl->checkUrl($page_url) ) {
          $page_url = $values['url'] . '-' . $counter;

          $page_url = strtolower(trim($page_url));
          $page_url = preg_replace('/[^a-z0-9-]/', '-', $page_url);
          $page_url = preg_replace('/-+/', "-", $page_url);

          $counter++;
        }
        $values['url'] = $page_url;

        $page = $pageTbl->createRow();

        $page->title = $data[$values['title']];
        $page->displayname = $data[$values['title']];
        $page->description = $data[$values['description']];
        $page->country = $data[$values['country']];
        $page->state = $data[$values['state']];
        $page->city = $data[$values['city']];
        $page->street = $data[$values['street']];
        $page->website = $data[$values['website']];
        $page->phone = $data[$values['phone']];
        $page->creation_date = $data[$values['creation_date']];
        $page->featured = $data[$values['featured']];
        $page->sponsored = $data[$values['sponsored']];

        $page->user_id = $values['user_id'];
        $page->parent_type = 'user';
        $page->parent_id = $values['user_id'];
        $page->url = $values['url'];
        $page->name = $values['url'];

        $page->approved = 1;
        $page->enabled = 1;
        $page->save();

        $user = Engine_Api::_()->getItem('user', $page->user_id);

        // Page Markers
        if (isset($values['marker']) && $values['marker']) {
          $address = array($data[$values['country']], $data[$values['state']], $data[$values['city']], $data[$values['street']]);
          $page->addMarkerByAddress($address);
        }

        // Set Owners
        $page->membership()->addMember($user)->setUserApproved($user)->setResourceApproved($user)->setUserTypeAdmin($user);
        $page->setAdmin($user);
        $page->getTeamList()->add($user);

        // Content
        $page->createContent();

        // Fields
        $db->insert('engine4_page_fields_search', array('item_id' => $page->page_id, 'profile_type' => $values['option_id']));
        $db->insert('engine4_page_fields_values', array('item_id' => $page->page_id, 'field_id' => 1, 'value' => $values['option_id']));

        // Privacy
        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'Registered Members',
          'likes' => 'Likes, Admins and Owner',
          'team' => 'Admins and Owner Only'
        );
        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_comment');
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

        $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_posting');
        $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

        $auth_values = array(
          'auth_view' => key($view_options),
          'auth_comment' => key($comment_options),
          'auth_album_posting' => key($posting_options),
          'auth_blog_posting' => key($posting_options),
          'auth_disc_posting' => key($posting_options),
          'auth_doc_posting' => key($posting_options),
          'auth_event_posting' => key($posting_options),
          'auth_music_posting' => key($posting_options),
          'auth_video_posting' => key($posting_options)
        );
        $page->setPrivacy($auth_values);

        // Activity
        if (isset($values['activity']) && $values['activity']) {
          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($user, $page, 'page_create');
        }

      } catch(Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $db->commit();

      $fileObj->next();
      $data_str = $fileObj->current();
      $data = $this->str_getcsv($data_str, $seperator);
      $import->seek++;
      $import->import_count++;
      $import->status = 1;
      $import->save();
//------------------- end of proccess -----------------------//
    }

    if( $fileObj->eof() ) {
      $import->status = 2;
    } else {
      $import->status = 1;
    }

    $import->save();
  }

  public function multiDeleteAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);

    $ids = $this->_getParam('items');

    if (!empty($ids)) {
      $table = Engine_Api::_()->getItemTable('page_import');
      $where = $table->getAdapter()->quoteInto('import_id IN (?)', $ids);

      $imports = $table->select()->where($where);
      $imports = $table->fetchAll($imports);
      foreach( $imports as $import ) {
        $import->delete();
      }

    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'page', 'controller' => 'import', 'action' => 'files'), 'admin_default', true);
  }

  protected  function importFields()
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    // Page Type
    $select = $db->select()
      ->from('engine4_page_fields_options', array('label'))
      ->where('field_id = 1');

    $categories = $db->select()
      ->from('engine4_sitepage_categories')
      ->where('cat_dependency = 0')
      ->where('category_name NOT IN ?', $select)
      ->query()
      ->fetchAll();

    foreach( $categories as $category ) {
      $db->insert('engine4_page_fields_options', array(
        'field_id' => 1,
        'label' => $category['category_name'],
        'order' => 999
      ));
      $option_id = $db->lastInsertId();

      $sub_categories = $db->select()
        ->from('engine4_sitepage_categories')
        ->where('cat_dependency = ?', $category['category_id'])
        ->query()
        ->fetchAll();

      if( count($sub_categories) ) {
        $db->insert('engine4_page_fields_meta', array(
          'type' => 'select',
          'label' => 'Page Type',
          'required' => 1
        ));

        $field_id = $db->lastInsertId();
        $db->insert('engine4_page_fields_maps', array(
          'field_id' => 1,
          'option_id' => $option_id,
          'child_id' => $field_id,
          'order' => 9999
        ));

        foreach( $sub_categories as $sub_category ) {
          $db->insert('engine4_page_fields_options', array(
            'field_id' => $field_id,
            'label' => $sub_category['category_name'],
            'order' => 999
          ));
          $sub_sub_categories = $db->select()
            ->from('engine4_sitepage_categories')
            ->where('subcat_dependency = ?', $sub_category['category_id'])
            ->query()
            ->fetchAll();
          foreach( $sub_sub_categories as $sub_sub_category ) {
            $db->insert('engine4_page_fields_options', array(
              'field_id' => $field_id,
              'label' => $sub_sub_category['category_name'],
              'order' => 999));
          }
        }
      }

    }
  }

  protected function createContent($page, $sitePage)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    $main = $db->select()
      ->from('engine4_sitepage_content')
      ->where('contentpage_id = ?', $sitePage['page_id'])
      ->where("name = 'main'")
      ->limit(1)
      ->query()
      ->fetch();

    if( !$main ) {
      $page->createContent();
      $db->commit();
      return;
    }

    $db->insert('engine4_page_content', array(
      'is_timeline' => 0,
      'page_id' => $page->page_id,
      'name' => 'main',
      'type' => 'container',
      'parent_content_id' => 0,
      'params' => '[""]',
      'order' => 2,
    ));
    $main_id = $db->lastInsertId();

    $containers = $db->select()
      ->from('engine4_sitepage_content')
      ->where('contentpage_id = ?', $sitePage['page_id'])
      ->where('parent_content_id = ?', $main['content_id'])
      ->order('parent_content_id ASC')
      ->query()
      ->fetchAll();
    ;

    $widget_names = array(
      'core.container-tabs' => 'core.container-tabs',
      'sitepage.mainphoto-sitepage' => 'page.profile-photo',
      'sitepage.options-sitepage' => 'page.profile-options',
      'sitepage.page-like' => 'like.box',
      'sitepage.insights-sitepage' => 'page.page-statistics',
      'sitepage.favourite-page' => 'page.favorite-pages',
      'sitepage.page-like-button' => 'like.status',
      'sitepage.info-sitepage' => 'page.profile-fields',
      'seaocore.feed' => 'page.feed',
      'core.profile-links' => 'page.page-links',
    );
    $widget_params = array(
      'core.container-tabs' => '{"max":"10"}',
      'sitepage.mainphoto-sitepage' => '{"title":"Photo","titleCount":false}',
      'sitepage.options-sitepage' => '{"title":"Options","titleCount":false}',
      'sitepage.page-like' => '{"title":"like_Like Club","titleCount":true}',
      'sitepage.insights-sitepage' => '{"title":"Statistics","titleCount":"false"}',
      'sitepage.favourite-page' => '{"title":"Favorite Pages","titleCount":"true"}',
      'sitepage.page-like-button' => '[""]',
      'sitepage.info-sitepage' => '{"title":"Info","titleCount":"true"}',
      'seaocore.feed' => '{"title":"Updates","titleCount":"false"}',
      'core.profile-links' => '{"title":"Links","titleCount":"true"}'
    );
    foreach( $containers as $container ) {
      if( $container['params'] == null ) {
        $container['params'] = '';
      }
      $db->insert('engine4_page_content', array(
        'page_id' => $page->page_id,
        'name' => $container['name'],
        'type' => $container['type'],
        'parent_content_id' => $main_id,
        'params' => $container['params'],
        'order' => $container['order'],
        'is_timeline' => 0
      ));
      $container_id = $db->lastInsertId();

      $widgets = $db->select()
        ->from('engine4_sitepage_content')
        ->where('contentpage_id = ?', $sitePage['page_id'])
        ->where('parent_content_id = ?', $container['content_id'])
        ->query()
        ->fetchAll();

      foreach( $widgets as $widget ) {
        if( !$widget_names[$widget['name']] )
          continue;

        $db->insert('engine4_page_content', array(
          'page_id' => $page->page_id,
          'name' => $widget_names[$widget['name']],
          'type' => 'widget',
          'parent_content_id' => $container_id,
          'params' => $widget_params[$widget['name']],
          'order' => $widget['order'],
          'is_timeline' => 0
        ));

        if( $widget['name'] == 'core.container-tabs' ) {
          $tab_id = $db->lastInsertId();
          $tab_widgets = $db->select()
            ->from('engine4_sitepage_content')
            ->where('contentpage_id = ?', $sitePage['page_id'])
            ->where('parent_content_id = ?', $widget['content_id'])
            ->query()
            ->fetchAll();

          foreach( $tab_widgets as $tab_widget ) {
            if( $widget_names[$tab_widget['name']] == null )
              continue;

            $db->insert('engine4_page_content', array(
              'page_id' => $page->page_id,
              'name' => $widget_names[$tab_widget['name']],
              'type' => 'widget',
              'parent_content_id' => $tab_id,
              'params' => $widget_params[$tab_widget['name']],
              'order' => $tab_widget['order'],
              'is_timeline' => 0
            ));
          }
          $db->insert('engine4_page_content', array(
            'page_id' => $page->page_id,
            'name' => 'page.profile-staff',
            'type' => 'widget',
            'parent_content_id' => $tab_id,
            'params' => '{"title":"Staff","titleCount":"true"}',
            'order' => 999,
            'is_timeline' => 0
          ));
        }

        if( $widget['name'] == 'sitepage.page-like-button' ) {
          $db->insert('engine4_page_content', array(
            'page_id' => $page->page_id,
            'name' => 'page.search',
            'type' => 'widget',
            'parent_content_id' => $container_id,
            'params' => '[""]',
            'order' => $widget['order'] + 1,
            'is_timeline' => 0
          ));
        }
      }
    }
  }

  public function getuserAction()
  {
    $users = $this->getUsersByText($this->_getParam('text'), $this->_getParam('limit', 40));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $users as $user ) {
        $data[] = $user->username;
      }
    } else {
      foreach( $users as $user ) {
        $data[] = array(
          'id' => $user->user_id,
          'label' => $user->username,
          'photo' => $this->view->itemPhoto($user, 'thumb.icon')
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  private function getUsersByText($text = null, $limit = 10)
  {
    /**
     * @var $table User_Model_DbTable_Users
     **/
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->order('username ASC')
      ->limit($limit);

    if ($text) {
      $select->where('username LIKE ?', '%'.$text.'%');
    }

    return $table->fetchAll($select);
  }

  protected function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\")
  {
    $fp = fopen("php://memory", 'r+');
    fputs($fp, $input);
    rewind($fp);
    $data = fgetcsv($fp, null, $delimiter, $enclosure);
    fclose($fp);
    return $data;
  }
}
