<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $uses = array('User', 'Question', 'Setting','Answer');
	var $components = array('Auth', 'Session', 'Cookie', 'Email', 'Recaptcha','RequestHandler');
	var $helpers = array('Time', 'Html', 'Form', 'Javascript', 'Number', 'Thumbnail', 'TrickyFileInput', 'Session', 'Recaptcha');
	
	var $allowedTypes = array(
    	'image/jpeg',
    	'image/gif',
    	'image/png',
    	'image/pjpeg',
    	'image/x-png'
  	);

	public function beforeRender() {
            $this->underMaintenance();
	}

	public function beforeFilter() {
            parent::beforeFilter();
            $this->Auth->fields = array(
                'username' => 'email', 
                'password' => 'password'
                );
            $this->isAdmin($this->Auth->user('id'));

            $this->Auth->allow('view', 'register', 'userbar', 'users', 'mini_user_search', 'lost_password','login',
            'miniSearch','view_all_users','view_profile');
	}

	public function login() {

	}
	
	public function lost_password() {
            if(!empty($this->data)) {
                $email_exists = $this->User->find(
                        'first', array(
                                'conditions' => array(
                                        'email' => $this->data['User']['email']	
                                )
                        )
                );
                if(!empty($email_exists)) {
                    $pass = rand(8, 12);
                    $this->data['User']['password'] = $this->Auth->password($pass);
                    $this->data['User']['id'] = $email_exists['User']['id'];
                    $this->User->save($this->data);
                    $this->set('user', $email_exists);
                    $this->set('password', $pass);
                    $this->Email->from = 'AutomobiQuiz <janithoj@gmail.com>';
                    $this->Email->to = $email_exists['User']['email'];
                    $this->Email->subject = 'AutomobiQuiz password recovery.';
                    $this->Email->template = 'recovery';
                    $this->Email->sendAs = 'both';
                    $this->Email->send();
                    $this->Session->setFlash('Go check your email!', 'error');	
                            }else {
                                    $this->Session->setFlash('No user has that email address.', 'error');
                            }
                            $this->redirect('/login');
                }
	}
	
	public function logout(){
		$this->redirect($this->Auth->logout());
	}
	
	public function view_profile($public_key) {
		$user = $this->User->findByPublicKey($public_key);
		$this->pageTitle = $user['User']['username'] . '\'s Profile';
		$this->set('user', $user);
                
                $ques = $this->Question->getQuestionsMapByUser($user['User']['id']);
                $this->set('questions',$ques);
                
                $answeredQuesIds = $this->Answer->getAnswersMapByUser($user['User']['id']);
                $answerQuestions ='';
                
                foreach ($answeredQuesIds as $ids){
                    $answerQuestions[] = $this->Question->find('first',array(
                            'conditions' => array('Question.id'=>$ids['Answer']['question_id']),
                            'fields' => array('Question.public_key', 'Question.url_title','Question.title',
                            ))
                    );
                }
                
                $this->set('answered',$answerQuestions);
	}
	
	public function edit_profile($public_key) {
		if($this->Auth->user('public_key') != $public_key) {
			$this->Session->setFlash('Those are not your settings to change.', 'error');
			$this->redirect('/');	
		}
		$user = $this->User->find(
			'first', array(
				'conditions' => array(
					'public_key' => $public_key
				)
			)
		);
		
		if(empty($this->data)) {
			$this->set('user_info', $user);
		}else {
			$this->set('user_info', $user);
			if($this->Auth->password($this->data['User']['old_password']) == $user['User']['password']) {
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['new_password']);
				$this->data['User']['id'] = $user['User']['id'];
				$this->User->save($this->data);
				$this->Session->setFlash('Settings updated!', 'error');	
			}elseif(empty($this->data['User']['old_password'])) {
				unset($this->data['old_password']);
				unset($this->data['new_password']);
				$this->data['User']['id'] = $user['User']['id'];
				$this->User->save($this->data);
				$this->Session->setFlash('Settings updated, except password.', 'error');
			}else {
				$this->Session->setFlash('Old Password incorrect.  Settings remain unchanged.', 'error');
				$this->redirect('/users/edit_user/' . $public_key);
			}	
                }
	}

	public function __userSave($data) {
		$data['User']['public_key'] = uniqid();
		$data['User']['password'] = $this->Auth->password(uniqid('p'));
		$data['User']['joined'] = time();
		$data['User']['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['User']['url_title'] = $this->Question->niceUrl($data['User']['username']);
		/**
		 * Set up cookie data incase they leave the site and the session ends and they have not registered yet
		 */
		$this->Cookie->write(array('User' => $data['User']));
		/**
		 * Save the data
		 */
		$this->User->save($data);	
		$data['User']['id'] = $this->User->id;
		return $data;
	}
	
	public function view_all_users() {
            $uesrs = $this->User->find('all', array(
                'fields' => array('User.username','User.image','User.user_type','User.identity','User.public_key')
            ));
            
            $this->set('users',$uesrs);
	}
	
	public function register() {
		$this->pageTitle = 'Register';
		
		if($this->Session->read('Auth.User.registered') == 1) {
			$this->Session->setFlash('You are already registered.');
			$this->redirect('/');
		}
		
		if(!empty($this->data)) {

			if($this->Recaptcha->valid($this->params['form']) || $this->Session->read('Auth.User.id')) {
			/**
			 * If the user is logged in via Session or Cookie
			 */
			if($this->Auth->user('id')) {
				$user = $this->User->read(null, $this->Auth->user('id'));
				$user['User']['password'] = $this->Auth->password($this->data['User']['secret']);
				
				/**
				 * Save the user information.
				 */
				if($this->User->save($user)) {
                                    /**
                                     * Push the new registered state to the session.
                                     */
                                    $this->Session->write('Auth.User.registered', 1);
                                    $this->Session->setFlash('You have been registered! Welcome to the community.');
                                    $this->redirect('/users/' . $this->Auth->user('public_key') . '/' . $this->Auth->user('url_title'));
                                    } else {
                                        $this->Session->setFlash('There was an error with your request.');
                                    }
			} else {
				/**
				 * Register a new user
				 */
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['secret']);

				$this->data['User']['public_key'] = uniqid();
				$this->data['User']['joined'] = time();
				$this->data['User']['url_title'] = $this->Question->niceUrl($this->data['User']['username']);
                                $this->data['User']['user_type'] = 'student';

				if($this->User->save($this->data)) {
					$this->Auth->login($this->data);
					$this->redirect('/');
				}
			}
			
                } else {
                        $this->Session->setFlash('Invalid reCAPTCHA entered.', 'error');
                }	
            }
	}

    public function admin() {
            $this->pageTitle = 'Settings';
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that.', 'error');
            $this->redirect('/login');
        }
        if(!$this->User->adminCheck($this->Auth->user('id'))) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $settings = $this->Setting->find(
        	'all', array(
        		'conditions' => array(
        			'OR' => array(
        				'name' => array('rep_vote_up', 'rep_comment', 'rep_vote_down','rep_edit')
        			)
        		)
        	)
        );
        $this->set('settings', $settings);

        if($this->data) {
            foreach($this->data['Setting'] as $key => $value) {
                $data = array(
                    'id' => $key + 1,
                    'value' => $this->data['Setting'][$key]['value']
                );
                $this->Setting->save($data);
                $count = count($this->data);
            }
                $this->Session->setFlash('Settings updated.', 'error');
                $this->redirect('/admin');
        }
    }

    public function deleteUser($id) {
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in.', 'error');
            $this->redirect('/login');
        }elseif(!$this->User->adminCheck($this->Auth->user('id'))) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        
        if($this->User->delete($id)) {
            $this->Session->setFlash('User deleted successfully!', 'error');
            $this->redirect('/admin/users');
        }
    }

    public function admin_list() {
        $this->pageTitle = 'user Management';
        
        $users = $this->User->find('all', array('order' => 'username ASC'));
        $this->set('users', $users);
    }
    
    public function changeUserType($public_key,$type){
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in.', 'error');
            $this->redirect('/login');
        }elseif(!$this->User->adminCheck($this->Auth->user('id'))) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        
        $user = $this->User->findByPublicKey($public_key);
        
        $user['User']['user_type'] = $type; 
        if($this->User->save($user))
        {
            $this->Session->setFlash('Cahnged user type to '.$type.' successfully!', 'error');
            $this->redirect('/admin/users');
        }
    }

    public function avatar() {
		if(!empty($this->data['Upload']['file'])) {
			/* check all image parameters */
			$this->__checkImgParams();
						
			$user = $this->User->findById($this->Auth->user('id'));
			$uploadPath = WWW_ROOT . 'img/uploads/users/';
			$uploadFile = $uploadPath . $this->Auth->user('public_key') . '-' . $this->data['Upload']['file']['name'];
			
			$directory = dir($uploadPath); 
			if(!empty($user['User']['image'])) {
				unlink(WWW_ROOT . $user['User']['image']);
			}
			$directory->close();

			if(move_uploaded_file($this->data['Upload']['file']['tmp_name'], $uploadFile)) {
				$user['User']['image'] = '/img/uploads/users/' . $this->Auth->user('public_key') . '-' . $this->data['Upload']['file']['name'];
				$this->User->id = $user['User']['id'];
				$this->User->save($user);
				
				$this->Session->setFlash('Your profile picture has been set!', 'error');
				$this->redirect(Controller::referer('/'));
			}
			else {
				$this->Session->setFlash('Something went wrong uploading your avatar...', 'error');
				$this->redirect(Controller::referer('/'));
			}
		} else {
			$this->Session->setFlash('We didn\'t catch that avatar, please try again...', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkImgParams() {
		/* check file type */
		$this->__checkType($this->data['Upload']['file']['type']);
		
		/* check file size */
		$this->__checkSize($this->data['Upload']['file']['size']);
		
		/* check image dimensions */
		$this->__checkDimensions($this->data['Upload']['file']['tmp_name']);
		
	}
	
	function __checkType($type = null) {
		$valid = false;
    	foreach($this->allowedTypes as $allowedType) {
      		if(strtolower($type) == strtolower($allowedType)){
        		$valid = true;
      		}
    	}
		if(!$valid) {
			$this->Session->setFlash('You tried to upload an invalid type!  Please upload your pictures in jpeg, gif, or png format!', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkSize($size = null) {
	    if($size > 1024 * 1024 * 2) {
			$this->Session->setFlash('You tried to upload an image that was too large!  Images must be under 2MB.', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkDimensions($filePath) {
		$size = getimagesize($filePath);
		
		if(!$size) {
			$this->Session->setFlash('We could not check that image\'s size, so we can\'t upload it.', 'error');
			$this->redirect(Controller::referer('/'));
		}
		
		$error = '';
		if($size[0] > 800 || $size[1] > 800) {
			$this->Session->setFlash('Images cannot be any larger than 800 by 800 pixels.', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
}
?>