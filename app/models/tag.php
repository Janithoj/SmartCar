<?php
class Tag extends AppModel {

    var $name = 'Tag';
    
    var $actsAs = array('Containable');
    
    var $hasAndBelongsToMany = array(
        'Question' => array(
            'className'    => 'Question',
	        'joinTable'    => 'question_tags',
	        'foreignKey'   => 'tag_id',
	        'associationForeignKey'=> 'question_id',
	        'conditions'   => '',
	        'order'        => '',
	        'limit'        => '',
	        'unique'       => true,
	        'finderQuery'  => '',
	        'deleteQuery'  => '',
        )
    );

    public function getSuggestions() {
            return $this->query("SELECT COUNT(question_tags.tag_id) as count, tags.tag
                        FROM question_tags, tags
                        WHERE question_tags.tag_id=tags.id
                        GROUP BY question_tags.tag_id
                        ORDER BY count DESC");

    }

    public function tagSearch($tag, $page) {
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
        
        $results = $this->find(
            'all', array(
                'contain' => array(
                    'Question' => array(
                        'User' => array(
                            'fields' => array('User.public_key', 'User.username')
                        ),
                        'Answer' => array(
                            'fields' => array('Answer.id')
                        ),
                        'fields' => array('Question.title', 'Question.url_title', 'Question.public_key',
                                          'Question.views', 'Question.timestamp'),
                        'limit' => $page . ',' . 10
                    )
                ),
                'fields' => array('Tag.tag') 
            )
        );
       
        $questions ='';
        
        
        for ($i =0; $i<count($results); $i++)
        {
            if($results[$i]['Tag']['tag'] == $tag){
                $questions['Question'] = $results[$i]['Question'];
            }
        }
        
        for ($j =0; $j<count($questions); $j++)
        {
            $tags_per_site = $this->Question->find(
                'all', array(
                    'contain' => array(
                        'Tag.tag' 
                    ),
                    'conditions' => array(
                        'Question.id' => $questions['Question'][$j]['id']
                    )
                )
            );       
        }
        
        $data='';
        for ($j =0; $j<count($questions); $j++)
        {
            $data[$j]['Question'] = $questions['Question'][$j];
            $data[$j]['User'] = $questions['Question'][$j]['User'];
            $data[$j]['Answer'] = $questions['Question'][$j]['Answer'];
            $data[$j]['Tag'] = $tags_per_site[$j]['Tag'];
            unset($data[$j]['Question']['User']);
            unset($data[$j]['Question']['Answer']);
        }
       
        $final_results = array_reverse($data);
        return $final_results;
    }
}
?>