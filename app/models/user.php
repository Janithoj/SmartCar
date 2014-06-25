<?php
class User extends AppModel {

    var $name = 'User';
    var $validate = array(
		'username' => array(
			'rule' => array('custom', "/^[A-z0-9 ]+/i"),
			'message' => 'Usernames must contain only letters and numbers'),
			
		'email' => array(
			'emailvalid' => array('rule' => 'email',
								  'message' => 'Not a valid email address'),
			'isunique'   => array('rule' => 'isUnique',
								  'message' => 'Email already in use.')),
		'secret' => array(
			'min' => array(
				'rule' => array('minLength', '6'),
				'message' => 'Password must be at least 4 characters long.'),
			'notempty' => array('rule' => 'notEmpty',
								'message' => 'Password cannot be left empty')
		)
	);
    
    var $hasMany = array(
        'Answer' => array(
            'className'     => 'Answer',
            'foreignKey'    => 'user_id',
            'dependent' => true
        ),
        'Question' => array(
            'className'     => 'Question',
            'foreignKey'    => 'user_id',
            'dependent' => true
        ),
        'Comment' => array(
            'className'     => 'Comment',
            'foreignKey'    => 'user_id',
            'dependent' => true
        ),
        'Vote' => array(
            'className'     => 'Vote',
            'foreignKey'    => 'user_id',
            'dependent' => true
        )
    ); 

    public function adminCheck($user_id) {
        $rights = $this->find(
            'first', array(
                'conditions' => array('User.id' => $user_id),
                'fields' => array('User.user_type')
            )
        );
        
        if($rights['User']['user_type']=='admin'){
            return true;
        }
        else {return false;} 
    }
    
    public function SearchUsers($keyword){
       
        $users = $this->find('all', array(
			'conditions' => array(
				"User.username LIKE" => '%' .$keyword. '%'),
			'fields' => array('User.username', 'User.public_key', 'User.identity', 'User.image', 'User.user_type'),
			'order' => 'User.identity DESC',
			'limit' => 42	
				));
        
                                return $users;

    }
}
?>