<?php
class Question extends AppModel {

    var $name = 'Question';
    var $actsAs = array('Revision' => array('limit' => 5, 'ignore' => array('views', 'tags', 'Tag', 'status')));

    var $validate = array(
        'description' => array(
        'rule' => array('minLength', '10'),
        'message' => 'Answers must be at least 10 characters long.'
        ),
            'title' => array(
            'rule' => array('minLength', '10'),
            'message' => 'Titles must be at least 10 characters long.'
        )
    );
	
    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'fields' => array('User.username', 'User.public_key', 'User.identity', 'User.image')
        ),
        'Category' => array(
            'className' => 'Category',
            'foreignKey' => 'category_id',
            'fields' => array('Category.category_name')
        )
    );

    var $hasMany = array(
        'Answer' => array(
            'className'     => 'Answer',
            'foreignKey'    => 'question_id',
            'dependent' => true
        ),
        'Comment' => array(
            'className'     => 'Comment',
            'foreignKey'    => 'post_id',
            'conditions' => array('Comment.type' => 'question'),
            'dependent' => true
        ),
        'Vote' => array(
            'className'     => 'Vote',
            'foreignKey'    => 'post_id',
            'conditions' => array('Vote.post_type' => 'question'),
            'dependent' => true
        )
    );  
    
    var $hasAndBelongsToMany = array('Tag' =>
                                array('className'    => 'Tag',
                                      'joinTable'    => 'question_tags',
                                      'foreignKey'   => 'question_id',
                                      'associationForeignKey'=> 'tag_id',
                                      'conditions'   => '',
                                      'order'        => '',
                                      'limit'        => '',
                                      'unique'       => true,
                                      'finderQuery'  => '',
                                      'deleteQuery'  => '',
                                )
                                );

    public function niceUrl($url) {
		return preg_replace("/[^0-9a-zA-Z-]/", "", str_replace(' ', '-', $url));
    }

    public function monsterSearch($type, $page, $search,$cateId) {
        $week_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))));
        $week = mktime(00, 00, 00, $week_help['0'], $week_help['1'], $week_help['2']);
        $month_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), 1, date('Y'))));
        $month = mktime(00, 00, 00, $month_help['0'], $month_help['1'], $month_help['2']);
        $now = time();
        $record = ($page * 15) - 15;

        $this->bindModel(
            array(
                'hasOne' => array(
                    'Setting' => array(
                        'className' => 'Setting',
                        'foreignKey' => 'value'
                    )
                )
            )
        );


    if($search == 'no') {
        if($type == 'recent') {
            if($cateId != '-1'){
                return $this->find('all', array(
				'contain' => array(
                    'User', 'Tag.tag', 'Answer' => array(
                        'fields' => array('Answer.id')
                    )
                ),
				'order' => 'Question.timestamp DESC',
                                'conditions' => array('Question.category_id'=> $cateId),
				'fields' => array(
					'Question.title', 'Question.views', 'Question.votes', 'Question.url_title',
                    'Question.public_key', 'Question.timestamp', 'User.username',
                    'User.public_key', 'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
                
            }  else {
                return $this->find('all', array(
				'contain' => array(
                    'User', 'Tag.tag', 'Answer' => array(
                        'fields' => array('Answer.id')
                    )
                ),
				'order' => 'Question.timestamp DESC',
				'fields' => array(
					'Question.title', 'Question.views', 'Question.votes', 'Question.url_title',
                    'Question.public_key', 'Question.timestamp', 'User.username',
                    'User.public_key', 'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
            }
			
        }elseif($type == 'unanswered') {
			return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Question.status' => 'open'),
				'order' => 'Question.timestamp DESC',
				'fields' => array(
					'Question.title', 'Question.views','Question.votes',
                    'Question.url_title', 'Question.public_key',
                    'Question.timestamp', 'User.username', 'User.public_key', 
                    'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
		}elseif($type == 'solved') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                           
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Question.status' => 'closed'),
				'order' => 'Question.timestamp DESC',
				'fields' => array(
					'Question.title', 'Question.views',
                    'Question.url_title', 'Question.public_key',
                    'Question.timestamp', 'User.username', 'User.public_key', 
                    'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'hot') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'fields' => array('Answer.id')
                        )
                    ),
				'order' => 'Question.views DESC',
				'fields' => array(
					'Question.title', 'Question.views',
                    'Question.url_title', 'Question.public_key','Question.votes',
                    'Question.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'popular') {
        return $this->find(
            'all', array(
                                'contain' => array(
                    'User', 'Tag.tag', 'Answer' => array(
                        'fields' => array('Answer.id')
                    )
                ),
                            'order' => 'Question.votes DESC',
                            'fields' => array(
                                    'Question.title', 'Question.views',
                'Question.url_title', 'Question.public_key','Question.votes',
                'Question.timestamp', 'User.username', 'User.public_key',
                'User.image', 'User.identity'
                                    ),
                            'limit' => $record . ',' . 15
                    ));
        }elseif($type == 'recommend') {
        return $this->find(
            'all', array(
                                'contain' => array(
                    'User', 'Tag.tag', 'Answer' => array(
                        'fields' => array('Answer.id')
                    )
                ),
                            'order' => 'User.identity DESC',
                            'fields' => array(
                                    'Question.title', 'Question.views',
                'Question.url_title', 'Question.public_key','Question.votes',
                'Question.timestamp', 'User.username', 'User.public_key',
                'User.image', 'User.identity', 'User.user_type'
                                    ),
                            'limit' => $record . ',' . 15,
                            'conditions' => array('AND'=> array('Question.votes >'=> 5 ,array('OR'=> array('AND'=> array('User.user_type' => 'student', 'User.identity >'=> 250)) ,array('AND'=> array('User.user_type' => 'tutor', 'User.identity >'=> 50)))))
                    ));
        }elseif($type == 'week') {
            return $this->find(
                'all', array(
                                    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'fields' => array('Answer.id')
                        )
                    ),
                                'conditions' => array(
                    'Question.timestamp BETWEEN ? and ?' => array($week, $now)),
                                'order' => 'Question.timestamp DESC',
                                'fields' => array(
                                        'Question.title', 'Question.views',
                    'Question.url_title', 'Question.public_key',
                    'Question.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.identity'
                                        ),
                                'limit' => $record . ',' . 15
                        ));
        }elseif($type == 'month') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                           
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Question.timestamp BETWEEN ? and ?' => array($month, $now)),
				'order' => 'Question.timestamp DESC',
				'fields' => array(
					'Question.title', 'Question.views',
                    'Question.url_title', 'Question.public_key',
                    'Question.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.identity'
					),
				'limit' => $record . ',' . 15
			));
        }
    } else {
            $escapedNeedle = $this->getDataSource()->value($type['needle']);

            return $this->find(
                'all', array(
                    'conditions' => array(
                        "MATCH(Question.description, Question.title) against (" . $escapedNeedle . " IN BOOLEAN MODE)"),
                    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            
                            'fields' => array('Answer.id')
                        )
                    ),
                    'fields' => array(
						"match(Question.description, Question.title) against(" . $escapedNeedle . ") as relevance",
                        'Question.title', 'Question.views', 'Question.url_title', 'Question.public_key',
                        'Question.timestamp', 'User.username', 'User.public_key', 'User.image',
                        'User.identity'),
                    'order' => 'relevance DESC',
                    'limit' => $record . ',' . 15)
            );
        }
    }

    public function monsterSearchCount($type, $search) {
        $week_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))));
        $week = mktime(00, 00, 00, $week_help['0'], $week_help['1'], $week_help['2']);
        $month_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), 1, date('Y'))));
        $month = mktime(00, 00, 00, $month_help['0'], $month_help['1'], $month_help['2']);
        $now = time();

        $this->bindModel(
            array(
                'hasOne' => array(
                    'Setting' => array(
                        'className' => 'Setting',
                        'foreignKey' => 'value'
                    )
                )
            )
        );
        

        if($search == 'no') {
            if($type == 'recent' || $type == 'hot' || $type == 'popular') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Question.title) as count')
                );
            }elseif($type == 'unanswered') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Question.title) as count',
                        'conditions' => array(
                            'Question.status' => 'open'))
                );
            }elseif($type == 'solved') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Question.title) as count',
                        'conditions' => array(
                            'Question.status' => 'closed'))
                );
            }elseif($type == 'week') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Question.title) as count',
                        'conditions' => array(
                            'Question.timestamp BETWEEN ? and ?' => array($week, $now)))
                );
            }elseif($type == 'month') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Question.title) as count',
                        'conditions' => array(
                            'Question.timestamp BETWEEN ? and ?' => array($month, $now)))
                );
            }
        }else {
            return $this->find(
                'all', array(
                    'fields' => 'COUNT(Question.title) as count',
                    'conditions' => array(
                        "match(description, title) against('" . $type['needle'] . "')"))
            );
        }
    }

    public function correctRedirect($public_key) {
        $this->Question->recursive = -1;
        $post = $this->find(
            'first', array(
                'conditions' => array('Question.public_key' => $public_key),                          
                'fields' => array('Question.url_title', 'Question.related_id', 'Question.public_key')
            )
        );
        $question = $post;
        if($post['Question']['related_id'] != 0) {
            $post = $this->find(
                'first', array(
                    'conditions' => array('Question.id' => $post['Question']['related_id']),
                    'fields' => array('Question.public_key', 'Question.url_title')
                )
            );
        }
        return $post;
    }
    
    public function searchQuestion($keyword){
        if(!empty($keyword)){

                $questions = $this->find('all', array(
			'conditions' => array('OR' => array(
				"Question.title LIKE" => '%' .$keyword. '%',"Question.description LIKE" => '%' .$keyword. '%')),
			'order' => 'Question.id DESC',
			'limit' => 42	
				));
        
                                return $questions;
            }else{
                return '';
            }       
    }
    
    public function searchQuestionCate($keyword,$category){
        if(!empty($keyword)){

                $questions = $this->find('all', array(
                        'conditions' => array('AND'=> array('OR' => array(
				"Question.title LIKE" => '%' .$keyword. '%',"Question.description LIKE" => '%' .$keyword. '%'),'Question.category' => $category)),
			'order' => 'Question.id DESC',
			'limit' => 42	
				));
        
                                return $questions;
            }else{
                return '';
            }         
    }
    
    public function getQuestionsMapByUser($userId){
        if(!empty($userId))
        {
            return $this->find(
                        'all', array(
                            'conditions' => array('Question.user_id'=>$userId),
                            'fields' => array('Question.public_key', 'Question.url_title','Question.title',
                            ))
                    );
        }  else {
            return '';
        }
    }
}
?>