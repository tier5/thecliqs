<?php return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'quiz',
    'version' => '4.1.5p1',
    'path' => 'application/modules/Quiz',
    'title' => 'Quizzes',
    'description' => 'Quizzes Plugin',
    'author' => 'Hire-Experts LLC',
    'meta' => array(
      'title' => 'Quizzes',
      'description' => 'Quizzes Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'callback' => array(
      'path' => 'application/modules/Quiz/settings/install.php',
      'class' => 'Quiz_Installer',
    ),
   'actions' => array(
       'preinstall',
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable'
     ),
    'directories' => array(
      'application/modules/Quiz',
    ),
    'files' => array(
      'application/languages/en/quiz.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Quiz_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Quiz_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'quiz',
    'quiz_result'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'quiz_browse' => array(
      'route' => 'quizzes/:page/*',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'index',
        'page' => 1
      )
    ),
    'quiz_manage' => array(
      'route' => 'quizzes/manage/:page',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'manage',
        'page' => '1'
      )
    ),
    'quiz_view' => array(
      'route' => 'quizzes/:quiz_id/:slug',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'quiz_id' => '\d+'
      )
    ),
    // User
    
    'quiz_create' => array(
      'route' => 'quizzes/create',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'create'
      )
    ),
    'quiz_specific' => array(
      'route' => 'quizzes/:action/:quiz_id/*',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete|create-result|create-question|publish|take)',
        'quiz_id' => '\d+',
      )
    ),
    'quiz_delete_result' => array(
      'route' => 'quiz/delete-result/:quiz_id/:result_id',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'delete-result'
      )
    ),
    'quiz_edit_result' => array(
      'route' => 'quiz/edit-result/:quiz_id/:result_id',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'edit-result'
      )
    ),
    'quiz_delete_question' => array(
      'route' => 'quiz/delete-question/:quiz_id/:question_id',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'delete-question'
      )
    ),
    'quiz_edit_question' => array(
      'route' => 'quiz/edit-question/:quiz_id/:question_id',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'edit-question'
      )
    ),
    'quiz_admin_manage_level' => array(
      'route' => 'admin/quiz/level_id/:level_id',
      'defaults' => array(
        'module' => 'quiz',
        'controller' => 'admin-level',
        'action' => 'index'
      ),
      'reqs' => array(
        'level_id' => '\d+'
      )
    ),
  ),
);