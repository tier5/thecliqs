<?php

/**
 * class Ynrestapi_Api_Forum
 */
class Ynrestapi_Api_Forum extends Ynrestapi_Api_Base
{
    /**
     * @param $params
     */
    public function get($params)
    {
        self::requireScope('forums');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        if (!$this->requireAuthIsValid('forum', null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $categoryTable = Engine_Api::_()->getItemTable('forum_category');
        $categories = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));

        $forumTable = Engine_Api::_()->getItemTable('forum_forum');
        $forumSelect = $forumTable->select()
            ->order('order ASC')
        ;
        $forums = array();
        foreach ($forumTable->fetchAll() as $forum) {
            if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'view')) {
                $order = $forum->order;
                while (isset($forums[$forum->category_id][$order])) {
                    $order++;
                }
                $forums[$forum->category_id][$order] = $forum;
                ksort($forums[$forum->category_id]);
            }
        }

        $data = array();

        foreach ($categories as $category) {
            if (empty($forums[$category->category_id])) {
                continue;
            }

            $row = array(
                'id' => $category->category_id,
                'title' => Zend_Registry::get('Zend_Translate')->_($category->getTitle()),
                'forums' => array(),
            );

            foreach ($forums[$category->category_id] as $forum) {
                $row['forums'][] = Ynrestapi_Helper_Meta::exportOne($forum, array('listing'));
            }

            $data[] = $row;
        }

        self::setSuccess(200, $data);
        return true;
    }
}
