<?php
class Vote extends AppModel {
    var $name = 'Vote';

    public function castVote($user_id, $postType, $public_key, $type) {
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'Question' => array(
                        'className' => 'Question',
                        'foreignKey' => 'post_id'
                    ),
                    'Answer' => array(
                        'className' => 'Answer',
                        'foreignKey' => 'post_id'
                    ),
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id'
                    )
                )
            ), false
        );
        $this->Question->recursive = -1;
        $this->Answers->recursive = -1;
        $this->User->recursive = -1;
        
        $voter_info = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $user_id
                ),
                'fields' => array('User.identity')
            )
        );
        
        $info='';
        $post_info = '';
        
        if($postType == 'question')
        {
            $info = $this->Question->find(
                'first', array(
                    'conditions' => array(
                        'Question.public_key' => $public_key
                    ),
                    'fields' => array(
                        'Question.id', 'Question.votes', 'Question.user_id'
                    )
                )
            );
            
            if(!empty($info)){
                $post_info['Post']['id'] = $info['Question']['id'];
                $post_info['Post']['votes'] = $info['Question']['votes'];
                $post_info['Post']['user_id'] = $info['Question']['user_id'];
            }
        }elseif ($postType == 'answer') {
             $info = $this->Answer->find(
                'first', array(
                    'conditions' => array(
                        'Answer.public_key' => $public_key
                    ),
                    'fields' => array(
                        'Answer.id', 'Answer.votes', 'Answer.user_id', 'Answer.question_id'
                    )
                )
            );
             
            if(!empty($info)){
                $post_info['Post']['id'] = $info['Answer']['id'];
                $post_info['Post']['votes'] = $info['Answer']['votes'];
                $post_info['Post']['user_id'] = $info['Answer']['user_id'];
                $post_info['Post']['question_id'] = $info['Answer']['question_id'];
            }
        }
        
        $existing_vote = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.user_id' => $user_id,
                    'Vote.post_id' => $post_info['Post']['id'],
                    'Vote.post_type' => $postType
                )
            )
        );
        $poster_rep = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $post_info['Post']['user_id']
                ),
                'fields' => array('User.identity')
            )
        );
        
        $one_vote_up = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.type' => 'up',
                    'Vote.post_id' => $post_info['Post']['id']
                ),
                'fields' => array(
                    'Vote.id'
                )
            )
        );
        $one_vote_down = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.type' => 'down',
                    'Vote.post_id' => $post_info['Post']['id']
                ),
                'fields' => array(
                    'Vote.id'
                )
            )
        );
        if(!empty($existing_vote)) {
            return 'exists';
        }else{
            $this->create();
            $this->data['Vote']['user_id'] = $user_id;
            $this->data['Vote']['post_id'] = $post_info['Post']['id'];
            $this->data['Vote']['timestamp'] = time();
            $this->data['Vote']['type'] = $type;
            $this->data['Vote']['post_type'] = $postType;
            $this->save($this->data);
            if($type == 'up') {
                if(empty($one_vote_up)) {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] + 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'identity' => $poster_rep['User']['identity'] + 10
                    );
                }else {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] + 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'identity' => $poster_rep['User']['identity'] + 1
                    );
                }
            }elseif($type == 'down') {
                if(empty($one_vote_down)) {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] - 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'identity' => $poster_rep['User']['identity'] - 5
                    );
                }else {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] - 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'identity' => $poster_rep['User']['identity'] - 1
                    );
                }
                $voter_rep = array(
                    'id' => $user_id,
                    'identity' => $voter_info['User']['identity'] - 1
                );
                $this->User->save($voter_rep);
            }
            
            $this->User->save($reputation);
            if($postType == 'question'){
                 $this->Question->save($vote);
            }elseif ($postType == 'answer') {
                $this->Answer->save($vote);
            } 
        }
    }
}
?>