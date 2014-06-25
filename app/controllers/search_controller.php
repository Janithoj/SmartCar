<?php
class SearchController extends AppController {

	var $name = 'Search';
	var $uses = array('Question', 'User', 'Answer', 'Setting', 'Tag', 'QuestionTag', 'Vote');
	var $components = array('Auth', 'Session', 'Cookie', 'Markdown','RequestHandler');
	var $helpers = array('Javascript');
	
	public function beforeFilter() {
		parent::beforeFilter();
                $this->Auth->allow('search_question','search_question_category','search_user');
                
                if  ($this->RequestHandler->isXml()) {
                    $this->RequestHandler->respondAs('xml');
                    $this->RequestHandler->renderAs($this, 'xml');
                } elseif ($this->RequestHandler->ext == 'json'|| $this->RequestHandler->isAjax()){ 
                    $this->RequestHandler->respondAs('json');
                    $this->RequestHandler->renderAs($this, 'json');
                }
               
                $this->autoRender = false;
	}
        
        
        public function search_question($keyword){
            
            $questions = $this->Question->searchQuestion($keyword);
            
            echo json_encode($questions);
        }

        public function search_user($keyword){
            $this->User->recursive = -1;
            $users = $this->User->SearchUsers($keyword);
		
            echo json_encode($users);
		
        }

        public function search_tag($keyword){

            $tags = $this->Tag->find('all', array(
                            'conditions' => array('Tag.tag LIKE' => '%' . $keyword . '%')
                    ));
             echo json_encode($tags);
        }
}
?>