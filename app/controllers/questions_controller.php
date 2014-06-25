<?php
class QuestionsController extends AppController {

	var $name = 'Questions';
	var $uses = array('Question', 'User', 'Answer', 'Setting', 'Tag', 'QuestionTag', 'Vote','Comment');
	var $components = array('Auth', 'Session', 'Markdownify', 'Markdown', 'Cookie', 'Email', 'Recaptcha', 'Htmlfilter','RequestHandler');
	var $helpers = array('Javascript', 'Time', 'Cache', 'Thumbnail', 'Recaptcha', 'Session');
	
	public function beforeRender() {
		$this->underMaintenance();
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
                
		$this->Auth->allow('view_question', 'display', 'miniSearch', 'maintenance','view_all','view_answers','search_results');
		$this->isAdmin($this->Auth->user('id'));
		
		$this->Cookie->name = 'user_cookie';
		$this->Cookie->time =  604800;  // or '1 hour'
		$this->Cookie->path = '/'; 
		$this->Cookie->domain = $_SERVER['SERVER_NAME'];   
		$this->Cookie->key = 'MZca3*f113vZ^%v ';

		/**
		 * If a user leaves the site and the session ends they will be relogged in with their cookie information if available.
		 */
		if($this->Cookie->read('User')) {
			$this->Auth->login($this->Cookie->read('User'));
		}
	}
	
	public function afterFilter() {
		$this->Session->delete('errors');
	}
        
	public function delete_question($id) {
            if(!$this->isAdmin($this->Auth->user('id'))) {
                            $this->Session->setFlash(__('You are not allow to access that..',true), 'error');
                            $this->redirect('/');
            }
            
            $this->Question->delete($id);
            $this->Session->setFlash(__('Question deleted',true), 'error');
            $this->redirect('/');
	}

	
	public function add_question() {
		$this->set('title_for_layout', __('Ask a question',true));
		
		if(!empty($this->data)) {	
                   
                    $this->data['reCAPTCHA'] = $this->params['form'];
                    $this->__validateData($this->data, '/questions/ask', true);

                    
                    if(!empty($this->data['User'])) {
                            $user = $this->__userSave(array('User' => $this->data['User']));
                            $this->Auth->login($user);
                    }

                    if(!empty($user)) {
                            $userId = $user['User']['id'];
                    } else {
                            $userId = $this->Auth->user('id');
                    }

                    $post = $this->__saveQuestion($userId, $this->data);

                    $this->redirect('/questions/' . $post['public_key'] . '/' . $post['url_title']);
		}       
	}
        
        public function  research(){
    
        }
                
        public function add_answer($public_key) {
            
                $question = $this->Question->findByPublicKey($public_key);
                
                if($this->Auth->user('user_type')=='student'){
                    $this->Session->setFlash('You are not allowed to answer questions.','error');
                    $this->redirect('/questions/' . $question['Question']['public_key'] . '/' . $question['Question']['url_title']);
                }
            
               
		if(empty($this->data)) {
                    return;
                }
                    $this->data['reCAPTCHA'] = $this->params['form'];
                    $this->__validateData($this->data, '/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title'] . '#user_answer', true);

                    if(!empty($this->data['User'])) {
                            $user = $this->__userSave(array('User' => $this->data['User']));
                            $this->Auth->login($user);
                    }

                    if(!empty($user)) {
                            $userId = $user['User']['id'];
                    $username = $user['User']['username'];
				} else {
					$userId = $this->Auth->user('id');
                    $username = $this->Auth->user('username');
				}
                     
                $this->saveAnswer($question['Question']['id'],$userId,$this->data);
               	
                if($question['Question']['notify'] == 1) {
                    $user = $this->User->find(
                        'first', array(
                            'conditions' => array(
                                'User.id' => $question['Question']['user_id']
                            ),
                            'fields' => array('User.email', 'User.username')
                        )
                    );
                      
                    $this->Email->smtpOptions = array(
                        'port'=>'25',
                        'timeout'=>'30',
                        'host' => 'ssl://smtp.gmail.com',
                        'username'=>'janithoj@gmail.com',
                        'password'=>'codename47',
                    );
                    $this->set('question', $question);
                    $this->set('username', $username);
                    $this->set('dear', $user['User']['username']);
                    $this->Email->delivery = 'smtp';
                    $this->Email->delivery = 'debug';
                    $this->Email->from    = 'Username <janithoj@gmail.com>';
                    $this->Email->to      = $question['Question']['username'];
                    $this->Email->subject = __('Your question has been answered!',true);
                    $this->Email->template = 'notification';
                    $this->Email->sendAs = 'both';
                    $this->set('smtp_errors', $this->Email->smtpError);
                    $this->Email->send();
                    
                    $this->log($this->Email->smtpError, 'debug');
                }		
                $this->redirect('/questions/' . $question['Question']['public_key'] . '/' . $question['Question']['url_title']);
	}
        
        public function search_results($keyword =''){
            $this->set('ketword',$keyword);
            if(!empty($this->data))
            {
                $this->set('keyword', $this->data['Question']['keyword']);
            }
        }

        public function __validateData($data, $redirectUrl, $reCaptcha = false) {
                $this->User->set($data);
                $this->Question->set($data);
		
                $errors = array();
		$recaptchaErrors = array();
		
		if($reCaptcha == true) {
			if(!$this->Recaptcha->valid($data['reCAPTCHA'])) {
				$data['Question']['description'] = $this->Markdownify->parseString($data['Question']['description']);
				$recaptchaErrors = array('recaptcha' => __('Invalid reCAPTCHA entered.',true));
				$errors = array(
					'errors' => $recaptchaErrors,
					'data' => $data
					);
				$this->Session->write(array('errors' => $errors));
				$this->redirect($redirectUrl);				
			}
		}
		
		if(!$this->Question->validates() || !$this->User->validates()) {
			$data['Question']['description'] = $this->Markdownify->parseString($data['Questions']['description']);
			$validationCheck = array_merge($this->User->invalidFields(),$this->Question->invalidFields(), $recaptchaErrors);
			$errors = array(
				'errors' => $validationCheck,
				'data' => $data
				);
			$this->Session->write(array('errors' => $errors));
			$this->redirect($redirectUrl);
		}
	}
        
        public function _validateContent($content){
            /**
		 * Filter out any nasty XSS
		 */
		$content = str_replace('<code>', '<code class="prettyprint">', $content);
		$content = @$this->Htmlfilter->filter($content);
		
		/**
		 * Spam Protection
		 */
		$content = strip_tags($content);
		// Get links in the content
		$links = preg_match_all("#(^|[\n ])(?:(?:http|ftp|irc)s?:\/\/|www.)(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,4}(?:[-a-zA-Z0-9._\/&=+%?;\#]+)#is", $content, $matches);
		$links = $matches[0];
		
		$totalLinks = count($links);
		$length = strlen($content);

		// Keyword search
		$blacklistKeywords = $this->Setting->find('first', array('conditions' => array('name' => 'blacklist')));
		$blacklistKeywords = unserialize($blacklistKeywords['Setting']['description']);

		$blacklistWords = array('.html', '.info', '?', '&', '.de', '.pl', '.cn');
		
		$firstWord = substr($content, 0, stripos($content, ' '));
		$firstDisallow = array_merge($blacklistKeywords, array('interesting', 'cool', 'sorry'));
                
                // Random character match
		// -1 point per 5 consecutive consonants
		$consonants = preg_match_all('/[^aAeEiIoOuU\s]{5,}+/i', $content, $matches);
		$totalConsonants = count($matches[0]);
        }

        
        public function __saveQuestion($userId, $data) {
            
		$this->data['Question']['type'] = 'open';
		$this->data['Question']['user_id'] = $userId;
		$this->data['Question']['timestamp'] = time();
                $this->data['Question']['public_key'] = uniqid();
                $this->data['Question']['title'] = str_replace('-',' ',$this->data['Question']['title']);
                $this->data['Question']['url_title'] = $this->Question->niceUrl($this->data['Question']['title']);
                $this->data['Question']['category_id'] = $this->data['Question']['category_name'];
                
		if(!empty($this->data['Question']['tags'])) {
			$this->Question->Behaviors->attach('Tag', array('table_label' => 'tags', 'tags_label' => 'tag', 'separator' => ', '));
		}
                
                $this->_validateContent($this->data['Question']['description']);

		$manyTimes = $this->Question->find('count', array(
			'conditions' => array('Question.description' => $this->data['Question']['description'])
			));
			
		/** 
		 * Save Data
		 */
		if($this->Question->save($this->data)) { 
                    $user_info = $this->User->find('first', array('conditions' => array('id' => $userId)));
                    $this->User->save(array('id' => $userId, 'question_count' => $user_info['User']['question_count'] + 1));
                   
                    $post = $this->data['Question'];

                    $this->Question->Behaviors->detach('Tag');
                    
                    $tags = array(
                                    'id' => $this->Question->id,
                                    'tags' => ''
                            );

                    $this->Question->save($tags);

                    return $post;
		
                } else {
                    return false;
		}
        }
        
        public function  saveAnswer($questionId,$userId,$data){
                $this->data['Answer']['status'] = 'pending';
		$this->data['Answer']['user_id'] = $userId;
		$this->data['Answer']['timestamp'] = time();
                $this->data['Answer']['public_key'] = uniqid();
                $this->data['Answer']['question_id'] = $questionId;
                
                $content = $this->data['Answer']['answer_des'];
                $this->_validateContent($content);
                
		$manyTimes = $this->Answer->find('count', array(
			'conditions' => array('Answer.answer_des' => $this->data['Answer']['answer_des'])
			));
                
		/** 
		 * Save Data
		 */
		if($this->Answer->save($this->data['Answer'])) { 
                    $user_info = $this->User->find('first', array('conditions' => array('id' => $userId)));
                    $this->User->save(array('id' => $userId, 'answer_count' => $user_info['User']['answer_count'] + 1));
		
                }
        }
        
        public function __userSave($data) {
		$data['User']['public_key'] = uniqid();
		$data['User']['password'] = $this->Auth->password(uniqid('p'));
		$data['User']['joined'] = time();
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

	
	public function view_question($public_key) {

		$this->Question->recursive = 2;
		$question = $this->Question->findByPublicKey($public_key);
	
		if($this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
			$this->Session->setFlash(__('The question you are trying to view no longer exists.',true), 'error');
			$this->redirect('/');
		}
		
		$this->Answer->recursive = 3;
		$answers = $this->Answer->find('all', array(
				'conditions' => array(
                    'Answer.question_id' => $question['Question']['id'],
                    
                ),
				'order' => 'Answer.timestamp DESC'
			));
                
                
		if(!empty($question)) {
			$views = array(
					'id' => $question['Question']['id'],
					'views' => $question['Question']['views'] + 1
				);
			$this->Question->save($views);
		}
        if($this->Auth->user('id') && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
            $this->set('rep_rights', 'yeah!');
        }
		$this->set('title_for_layout', $question['Question']['title']);
		$this->set('question', $question);
		$this->set('answers', $answers);
	}
        
        public function view_answers($question_public_key,$sort_type){
                $this->Question->recursive = 1;
		$question = $this->Question->findByPublicKey($question_public_key);
	
		if($this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
			$this->Session->setFlash(__('The question you are trying to view no longer exists.',true), 'error');
			$this->redirect('/');
		}
		
		$this->Answer->recursive = 3;
                
                if($sort_type == 'time')
                {
                    $answers = $this->Answer->find('all', array(
                                    'conditions' => array(
                                    'Answer.question_id' => $question['Question']['id']),
                                    'order' => 'Answer.timestamp DESC'
                            ));
                }elseif ($sort_type == 'votes') {
                    $answers = $this->Answer->find('all', array(
                                    'conditions' => array(
                                    'Answer.question_id' => $question['Question']['id']),
                                    'order' => 'Answer.votes DESC'
                        ));
                }elseif ($sort_type == 'recommended') {
                    $answers = $this->Answer->find('all', array(
                                    'conditions' => array(
                                    'Answer.question_id' => $question['Question']['id'],'Answer.status' => 'correct'),
                        ));
                }
            
                if($this->Auth->user('id') && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
                    $this->set('rep_rights', 'yeah!');
                }
                $this->set('question', $question);
                $this->set('answers', $answers);
                        
                if ($this->RequestHandler->isAjax())
                { 
                    $this->layout = 'ajax';
                }
        }

        public function edit_question($public_key,$postType) {
            if($postType=='question'){
                $post = $this->Question->findByPublicKey($public_key);
                $this->set('title_for_layout', $post['Question']['title']);
                $redirect = $post;
                
                if($post['Question']['user_id'] != $this->Auth->user('id') && !$this->isAdmin($this->Auth->user('id')) && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
                    $this->Session->setFlash(__('That is not your question to edit, and you need more reputation!',true), 'error');
                    $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
                }
            }elseif ($postType=='answer') {
                $post = $this->Answer->findByPublicKey($public_key);
                $redirect = $this->Question->findById($post['Answer']['question_id']);
                
                if($post['Answer']['user_id'] != $this->Auth->user('id') && !$this->isAdmin($this->Auth->user('id')) && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
                    $this->Session->setFlash(__('That is not your question to edit, and you need more reputation!',true), 'error');
                    $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
                }
            }
            
            if($postType == 'question') {
                $tags = $this->QuestionTag->find(
                    'all', array(
                        'conditions' => array(
                            'QuestionTag.question_id' =>  $post['Question']['id']
                        )
                    )
                );
                $this->Tag->recursive = -1;
                foreach($tags as $key => $value) {
                    $tag_names[$key] = $this->Tag->find(
                        'first', array(
                            'conditions' => array(
                                'Tag.id' => $tags[$key]['QuestionTag']['tag_id']
                            ),
                            'fields' => array('Tag.tag')
                        )
                    );
                    if($key == 0) {
                        $tag_list = $tag_names[$key]['Tag']['tag'];
                    }else {
                        $tag_list = $tag_list . ', ' . $tag_names[$key]['Tag']['tag'];
                    }
                }
                
                if(!empty($tag_list))
                    $this->set('tags', $tag_list);
            }

            if(!empty($this->data['Post']['tags'])) {
                $this->Question->Behaviors->attach('Tag', array('table_label' => 'tags', 'tags_label' => 'tag', 'separator' => ', '));
            }

            if(!empty($this->data)) {
                if($postType == 'question'){
                    $this->data['Question']['id'] = $post['Question']['id'];
                    $this->data['Question']['url_title'] = $this->Question->niceUrl($this->data['Question']['title']);
                    
                    if($this->Question->save($this->data)) {
                            $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
                    }
                }  elseif($postType == 'answer') {
                    $values['Answer'] = $this->data['Question'];
                    $values['Answer']['id'] = $post['Answer']['id'];
                    if($this->Answer->save($values)) {
                            $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
                    }
                } 
            } else {
                $content ='';
                if($postType == 'question'){
                    $content['Post'] = $post['Question'];
                    //$content['Post']['description'] = $this->Markdownify->parseString($content['Post']['description']);
                    $content['Post']['type'] = 'question';
                }elseif ($postType == 'answer') {
                    $content['Post'] = $post['Answer'];
                    //$content['Post']['answer_des'] = $this->Markdownify->parseString($content['Question']['answer_des']);
                    $content['Post']['type'] ='answer' ;
                }
                $this->set('post', $content);
            }
        }
        
        public function view_all($type='recent', $page=1,$cateId = '-1')
        {
            $this->set('title_for_layout', ucwords($type) . ' Questions');
            $this->Question->recursive = -1;

            if(isset($this->passedArgs['type'])) {
                $search = $this->passedArgs['search'];
                if($search == 'yes') {
                    $type = array(
                        'needle' => $this->passedArgs['type']
                    );
                }else {
                    $type = $this->passedArgs['type'];
                }
                $page = $this->passedArgs['page'];
            }elseif(!empty($this->data['Question'])) {
                $type = $this->data['Question'];
                $search = 'yes';
            }else {
                $search = 'no';
            }

            if($page <= 1) {
                $page = 1;
            }else{
                $previous = $page - 1;
                $this->set('previous', $previous);
            }

            $questions = $this->Question->monsterSearch($type, $page, $search,$cateId);
            $count = $this->Question->monsterSearchCount($type, $search);
            
            if($count['0']['0']['count'] % 15 == 0) {
                $end_page = $count['0']['0']['count'] / 15;
            }else {
                $end_page = floor($count['0']['0']['count'] / 15) + 1;
            }

            if(($count['0']['0']['count'] - ($page * 15)) > 0) {
                $next = $page + 1;
                $this->set('next', $next);
            }

            $keywords = array('hot','popular', 'week', 'month', 'recent', 'solved', 'unanswered','recommend');
            if(($search == 'no') && (!in_array($type, $keywords))) {
                $this->Session->setFlash(__('Invalid search type.',true), 'error');
                $this->redirect('/questions/hot');
            }

//            if(empty($questions)) {
//                if(isset($type['needle'])) {
//                    $this->Session->setFlash(__('No results for',true) . ' "' . $type['needle'] . '"!', 'error');
//                }else {
//                    $this->Session->setFlash(__('No results for',true) . ' "' . $type . '"!', 'error');
//                }	
//            }
                
            if($search == 'yes') {
                $this->set('type', $type['needle']);
            }else {
                $this->set('type', $type);
            }
            $this->set('questions', $questions);
            $this->set('end_page', $end_page);
            $this->set('current', $page);
            $this->set('search', $search);
            
            if ($this->RequestHandler->isAjax())
            { 
                $this->layout = 'ajax';
            }
        }

        public function display() {     
            
	}

	public function miniSearch() {
            Configure::write('debug', 0);
            $this->autoLayout = false;
            $questions = $this->Question->monsterSearch(array('needle' => $_GET['query']), 1, 'yes');
            $this->set('questions', $questions);
	}
	
	public function markCorrect($public_key) {
            $answer = $this->Answer->findByPublicKey($public_key);

            $question = $this->Question->findById($answer['Answer']['question_id']);
            /**
             * Check to make sure the logged in user is authorized to edit this Question
             */
            if($question['Question']['user_id'] != $this->Auth->user('id')) {
                    $this->Session->setFlash(__('You are not allowed to mark as correct.',true));
                    $this->redirect('/questions/' . $question['Question']['public_key'] . '/' . $question['Question']['url_title']);
            }

            $rep = $this->User->find(
                'first', array(
                    'conditions' => array(
                        'User.id' => $answer['Answer']['user_id']
                    ),
                    'fields' => array('User.identity', 'User.id')
                )
            );

                    /**
                     * Set the Question as correct, and its question as closed.
                     */
            $quest = array(
                'id' => $question['Question']['id'],
                'status' => 'closed'
            );
            $answ = array(
                'id' => $answer['Answer']['id'],
                'status' => 'correct'
            );
            $user = array(
                'User' => array(
                    'id' => $rep['User']['id'],
                    'identity' => $rep['User']['identity'] + 15
                )
            );
            $this->Answer->save($answ);
            $this->Question->save($quest);
            $this->User->save($user);
            $this->redirect('/questions/' . $question['Question']['public_key'] . '/' . $question['Question']['url_title'] . '#a_' . $answer['Answer']['public_key']);
    }

    
    
    public function maintenance() {
    }
    
    public function comment($public_key,$post_type) {
        if(!$this->Auth->user('id')) {
                $this->Session->setFlash('You need to be logged in to do that!', 'error');
                $this->redirect('/');
        }
        
        $post='';
        $redirect ='';
        if($post_type=='question'){
            $post = $this->Question->findByPublicKey($public_key);
            $redirect = $post;
        }elseif ($post_type=='answer') {
            $post = $this->Answer->findByPublicKey($public_key);
            $ques = $this->Question->find('first',array(
                'conditions'=> array('Question.id'=>$post['Answer']['question_id']),
                'fields' => array('Question.public_key')
            ));
            $redirect = $this->Question->findByPublicKey($ques['Question']['public_key']);
        }
        
        if(!$this->Setting->repCheck($this->Auth->user('id'), 'rep_comment')) {
            $this->Session->setFlash('You need more reputation to do that!', 'error');
            $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
        }
        $user = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id'))));

        if(!empty($this->data)) {
                if($post_type=='question'){
                    $this->data['Comment']['post_id'] = $post['Question']['id'];
                }elseif ($post_type=='answer') {
                    $this->data['Comment']['post_id'] = $post['Answer']['id'];
                }
                $this->data['Comment']['type'] = $post_type;
                $this->data['Comment']['content'] = $this->Markdown->parseString(htmlspecialchars($this->data['Comment']['content']));
                $this->data['Comment']['user_id'] = $this->Auth->user('id');
                $this->data['Comment']['timestamp'] = time();
                if($this->Comment->save($this->data)) {
                $this->redirect('/questions/' . $redirect['Question']['public_key'] . '/' . $redirect['Question']['url_title']);
            }
        }
    }
    
    function vote($public_key,$postType, $type) {
        $this->Question->recursive = -1;
        $this->Answer->recursive = -1;
        $title ='';
        
        if($postType == 'question'){
            $title = $this->Question->find(
                'first', array(
                    'conditions' => array(
                        'Question.public_key' => $public_key
                    ),
                    'fields' => array('Question.url_title','Question.public_key',
                                      'Question.user_id')
                )
            );
        }
        elseif ($postType == 'answer') {
            $title = $this->Answer->find(
                'first', array(
                    'conditions' => array(
                        'Answer.public_key' => $public_key
                    ),
                    'fields' => array('Answer.question_id', 'Answer.public_key',
                                      'Answer.user_id')
                )
            );
            
            $relatedQues =  $this->Question->find(
                'first', array(
                    'conditions' => array(
                        'Question.id' => $title['Answer']['question_id']
                    ),
                    'fields' => array('Question.url_title','Question.public_key',
                                      'Question.user_id')
                )
            );
        }
        
        $check = $title;
        
        if(!isset($_SESSION['Auth']['User']['id'])) {
                $this->Session->setFlash('You must be logged in to do that!', 'error');
                $this->redirect('/login');
        }
        
        if($type == 'up') {
            $check_against = 'rep_vote_up';
        }elseif($type == 'down') {
            $check_against = 'rep_vote_down';
        }
        
        if($postType == 'question'){
            if($check['Question']['user_id'] == $_SESSION['Auth']['User']['id']) {
                $this->Session->setFlash('You cannot vote for yourself.', 'error');
                $this->redirect('/questions/' . $title['Question']['public_key'] . '/' . $title['Question']['url_title']);
            }
             if(!$this->Setting->repCheck($_SESSION['Auth']['User']['id'], $check_against)) {
                $this->Session->setFlash('You need more reputation to do that!', 'error');
                $this->redirect('/questions/' . $title['Question']['public_key'] . '/' . $title['Question']['url_title']);
            }
            
            $vote = $this->Vote->castVote($_SESSION['Auth']['User']['id'],'question', $public_key, $type);
            
            if($vote == 'exists') {
                $this->Session->setFlash('You have already voted for that!', 'error');
                $this->redirect('/questions/' . $title['Question']['public_key'] . '/' . $title['Question']['url_title']);
            }else {
                $this->Session->setFlash('Voted successfully!', 'error');
                $this->redirect('/questions/' . $title['Question']['public_key'] . '/' . $title['Question']['url_title']);
            }
        }elseif ($postType == 'answer') {
            if($check['Answer']['user_id'] == $_SESSION['Auth']['User']['id']) {
                $this->Session->setFlash('You cannot vote for yourself.', 'error');
                $this->redirect('/questions/' . $relatedQues['Question']['public_key'] . '/' . $relatedQues['Question']['url_title']);
            }
            if(!$this->Setting->repCheck($_SESSION['Auth']['User']['id'], $check_against)) {
                $this->Session->setFlash('You need more reputation to do that!', 'error');
                $this->redirect('/questions/' . $relatedQues['Question']['public_key'] . '/' . $relatedQues['Question']['url_title']);
            }
            
            $vote = $this->Vote->castVote($_SESSION['Auth']['User']['id'],'answer', $public_key, $type);
            
            if($vote == 'exists') {
                $this->Session->setFlash('You have already voted for that!', 'error');
                $this->redirect('/questions/' . $relatedQues['Question']['public_key'] . '/' . $relatedQues['Question']['url_title']);
            }else {
                $this->Session->setFlash('Voted successfully!', 'error');
                $this->redirect('/questions/' . $relatedQues['Question']['public_key'] . '/' . $relatedQues['Question']['url_title']);
            }
        } 
    }
}
?>