<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @link http://book.cakephp.org/view/957/The-App-Controller
 */
class AppController extends Controller {
    public $pageTitle;
    var $uses = array('Category','Question');
    var $helpers = array('Tab','Session','Thumbnail');

	public function beforeFilter() {

            /**
             * reCAPTCHA API Information
             */
            $this->Recaptcha->publickey =  Configure::read('recaptcha.publickey');
	    $this->Recaptcha->privatekey = Configure::read('recaptcha.privatekey');
            
            $categories = $this->Category->find('all', array('contain' => false));
            $this->set('categories', $categories);
            
            $QuestionCount = $this->Question->find('count',  array('contain' => false));
            $this->set('QuesCount',$QuestionCount);
	}
	
	/**
	 * Set class var admin to true if the user id is admin.
	 * @param integer $id
	 */
	public function isAdmin( $id='') {
		if($this->User->adminCheck($id)) {
                    $this->set('admin', true);
                    return true;
		} else {
		    $this->set('admin', false);
		    return false;
		}
	}
	public function underMaintenance() {
		$maintenance = $this->Setting->find('first', array('conditions' => array('name' => 'site_maintenance')));
		if(($maintenance['Setting']['value'] == 'yes') && ($_SERVER['REQUEST_URI'] != '/maintenance')) {
			$this->redirect('/maintenance');
		}
	}
	public function __encrypt($string) {
	    return $string;
	}

	public function __decrypt($string) {
        return $string;
	}



}
