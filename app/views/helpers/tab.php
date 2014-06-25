<?php  

class TabHelper extends AppHelper 
{ 
    var $helpers = array('Html'); 
     
    /** 
     * Returns a UL list with li and a, soan tags 
     * 
     * @param array $data 
     * array( 
     *      'TabName' => array( 
     *          'match' => array('controller' => 'Controller', 'action' => 'Action'), 
     *          'link'  => array('controller' => 'Controller', 'action' => 'Action', 'Param', 'Param', 'Param'), 
     *      ), 
     * ); 
     * @param array $options  
     * @return text 
     * @author Henrik 
     */ 
    function tabs($data, $ulOptions = array()) { 
        $out = array(); 
        $points = array(); 
        $here = Router::parse($this->here); 
        $checks = array('controller', 'action'); 
         
        //normalize urls 
        foreach($data as $name => $options) { 
            $points[$name] = 0; 
             
            if (!isset($options['match'])) { 
                continue; 
            } 
             
            $url = Router::parse(Router::normalize($options['match'])); 
             
            foreach($checks as $check) { 
                if ($url[$check] == $here[$check]) { 
                    $points[$name]++; 
                } else { 
                    continue 2; 
                } 
            } 
             
            foreach($url['pass'] as $key => $value) { 
                if (isset($here['pass'][$key]) && $value == $here['pass'][$key]) { 
                    $points[$name]++; 
                } 
            } 
        } 
         
        arsort($points); 
        $activeKey = array_shift(array_flip($points)); 
         
        foreach($data as $name => $options) { 
            $link = $options['link']; 
            $out[] = $this->Html->tag('li', $this->Html->link($this->Html->tag('span', $name), $link, array(), null, false), ife($name == $activeKey, array('class' => 'active'))); 
        } 
         
        return $this->Html->tag('ul', join("\n", $out), $ulOptions); 
    } 
} 
 
?>