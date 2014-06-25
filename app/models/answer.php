<?php
class Answer extends AppModel {

    var $name = 'Answer';
    var $useTable = 'answers';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'fields' => array('User.username', 'User.public_key', 'User.identity', 'User.image')
        ),
    );

    var $hasMany = array(
        'Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'post_id',
            'conditions' => array('Comment.type' => 'answer'),
            'dependent' => true
        ),
        'Vote' => array(
            'className'     => 'Vote',
            'foreignKey'    => 'post_id',
            'conditions' => array('Vote.post_type' => 'answer'),
            'dependent' => true
        )
    );
    
    public function getAnswersMapByUser($userId){
        if(!empty($userId))
        {
            return $this->find(
                        'all', array(
                            'conditions' => array('Answer.user_id'=>$userId),
                            'fields' => array('Answer.question_id'
                            ))
                    );
        }  else {
            return '';
        }
    }
}
?>