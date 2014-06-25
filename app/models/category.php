<?php
/**
 * Description of category
 *
 * @author JanithA
 */
class category extends AppModel {
    
    var $name = 'Category';
    var $actsAs = array('Containable');
    
    var $hasMany = array(
        'Question' => array(
            'className' => 'Question',
            'foreignKey' => 'category_id',
            'dependent' => true
        )
    );
    
    
}

?>
