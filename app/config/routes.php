<?php
/* SVN FILE: $Id: routes.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
    
    Router::connect('/', array('controller' => 'questions', 'action' => 'display'));
    Router::connect('/maintenance', array('controller' => 'questions', 'action' => 'maintenance'));
	
    Router::connect('/questions/:type/:page/:cateId', array('controller' => 'questions', 'action' => 'view_all'), array('pass' => array('type','page','cateId'),'cateId' => '[0-9-]+'));
    Router::connect('/questions/unanswered', array('controller' => 'questions', 'action' => 'view_all', 'unanswered'));
    Router::connect('/questions/hot', array('controller' => 'questions', 'action' => 'view_all', 'hot'));
    Router::connect('/questions/recent', array('controller' => 'questions', 'action' => 'view_all', 'recent'));
    Router::connect('/questions/popular', array('controller' => 'questions', 'action' => 'view_all', 'popular'));
    Router::connect('/questions/recommend', array('controller' => 'questions', 'action' => 'view_all', 'recommend'));
    Router::connect('/questions/month', array('controller' => 'questions', 'action' => 'display', 'month'));
    Router::connect('/questions/week', array('controller' => 'questions', 'action' => 'display', 'week'));
    Router::connect('/questions/delete/:id', array('controller' => 'questions', 'action' => 'delete_question'), array('pass' => array('id'), 'id' => '[A-z0-9-]+'));
    Router::connect('/questions/solved', array('controller' => 'questions', 'action' => 'display', 'solved'));
    Router::connect('/search/*', array('controller' => 'questions', 'action' => 'display'));
    Router::connect('/mini_search', array('controller' => 'questions', 'action' => 'miniSearch'));
    Router::connect('/user_search', array('controller' => 'questions', 'action' => 'user_search'));
    Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about'));
    Router::connect('/help', array('controller' => 'pages', 'action' => 'display', 'help'));
	
    Router::connect('/tags', array('controller' => 'tags', 'action' => 'tag_list'));
    Router::connect('/tags/:page', array('controller' => 'tags', 'action' => 'tag_list'), array('pass' => array('page'), 'page' => '[0-9-]+'));
    Router::connect('/tags/:tag_name', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag_name'), 'tag_name' => '[A-z-]+'));
    Router::connect('/tags/:tag_name/:page', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag_name', 'page'), 'tag_name' => '[A-z-]+', 'page' => '[0-9-]+'));
    Router::connect('/tag_search/*', array('controller' => 'tags', 'action' => 'find_tag'));

    Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
    Router::connect('/admin', array('controller' => 'users', 'action' => 'admin'));
    Router::connect('/admin/user_delete/:id', array('controller' => 'users', 'action' => 'deleteUser'), array('pass' => array('id'), 'id' => '[A-z0-9-]+'));
    Router::connect('/admin/users', array('controller' => 'users', 'action' => 'admin_list'));
    Router::connect('/admin/users/:page', array('controller' => 'users', 'action' => 'admin_list'), array('pass' => array('page'), 'page' => '[0-9-]+'));
    Router::connect('/admin/promote/:public_key/:type', array('controller' => 'users', 'action' => 'changeUserType'), array('pass' => array('public_key','type'), 'public_key' => '[A-z0-9-]+','type' => '[A-z0-9-]+'));
    Router::connect('/bugs/changeStatus/status/:status', array('controller' => 'bugs', 'action' => 'changeStatus'), array('pass' => array('status'), 'status' => '[A-z0-9-]+'));

    Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
    Router::connect('/register', array('controller' => 'users', 'action' => 'register'));

    Router::connect('/questions/research', array('controller' => 'questions', 'action' => 'research'));
    Router::connect('/questions/ask', array('controller' => 'questions', 'action' => 'add_question'));
    Router::connect('/questions/view_answers/:public_key/:sort_type', array('controller' => 'questions', 'action' => 'view_answers'), array('pass' => array('public_key','sort_type'), 'public_key' => '[A-z0-9-]+', 'sort_type' => '[A-z0-9-]+'));
    Router::connect('/questions/:public_key/:postType/edit', array('controller' => 'questions', 'action' => 'edit_question'), array('pass' => array('public_key','postType'), 'public_key' => '[A-z0-9-]+', 'postType' => '[A-z0-9-]+'));
    Router::connect('/questions/:public_key/:title/answer', array('controller' => 'questions', 'action' => 'add_answer'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/questions/:public_key/:post_type/comment', array('controller' => 'questions', 'action' => 'comment'), array('pass' => array('public_key','post_type'), 'public_key' => '[A-z0-9-]+', 'post_type' => '[A-z]+'));
    Router::connect('/questions/:public_key/correct', array('controller' => 'questions', 'action' => 'markCorrect'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/questions/:public_key/:title', array('controller' => 'questions', 'action' => 'view_question'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/answers/:public_key/edit', array('controller' => 'questions', 'action' => 'edit_question'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));

    Router::connect('/tags/:tag', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag'), 'tag' => '[A-z0-9-]+'));

    Router::connect('/vote/:public_key/:postType/:type', array('controller' => 'questions', 'action' => 'vote'), array('pass' => array('public_key','postType' ,'type'), 'public_key' => '[A-z0-9-]+', 'postType'=> '[A-z]+', 'type' => '[A-z]+'));
	
    Router::connect('/view_users', array('controller' => 'users', 'action' => 'view_all_users'));
    Router::connect('/users/edit_user/:public_key', array('controller' => 'users', 'action' => 'edit_profile'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/upload', array('controller' => 'users', 'action' => 'avatar'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/:title', array('controller' => 'users', 'action' => 'view_profile'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/:title/bar.png', array('controller' => 'users', 'action' => 'userbar'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));

    Router::connect('/tags/suggest.js', array('controller' => 'tags', 'action' => 'suggest'));
      
    Router::connect('/api/user/:keyword', array('controller' => 'search', 'action' => 'search_user','[method]' => 'GET'), array('pass' => array('keyword'), 'keyword' => '[A-z0-9-]+'));
    Router::connect('/api/question/:keyword', array('controller' => 'search', 'action' => 'search_question','[method]' => 'GET'), array('pass' => array('keyword'), 'keyword' => '[A-z0-9-]+'));
    Router::connect('/api/tag/:keyword', array('controller' => 'search', 'action' => 'search_tag','[method]' => 'GET'), array('pass' => array('keyword'), 'keyword' => '[A-z0-9-]+'));
    
    Router::connect('/questions/search_results/:keyword',array('controller' => 'questions', 'action' => 'search_results'), array('pass' => array('keyword'), 'keyword' => '[A-z0-9-]+'));
    
    Router::parseExtensions('json');
       
?>
