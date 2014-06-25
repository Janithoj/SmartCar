<?	
    echo $html->css('wmd.css');
    echo $javascript->link('wmd/showdown.js');
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('wmd/wmd.js');
?> 

</script>
<div>
 <? foreach($answers as $answer) { ?>
    <div class="<?=($answer['Answer']['status'] == 'correct') ? 'answered' : 'answer';?>" id="a_<?=$answer['Answer']['id']?>">
        <div style="background-color:#4e9fc8; margin-bottom: 10px">
        <div class="content_container wrapper">
            <div class="content_actions" style="float: left; width: 55px; margin-right: 10px; margin-left: 5px; margin-top: 10px;">
                
                <?=$html->image('a_arrow_up.png', array('alt' => 'Vote Up','url' => '/vote/' . $answer['Answer']['public_key'] . '/answer/up')); ?>
                <span class="large_text" style="display: block; padding: 0px; margin: 0px; color:  #ffffff"><strong><?=$answer['Answer']['votes'];?></strong></span>
                <?=$html->image('a_arrow_down.png', array('alt' => 'Vote Down','url' => '/vote/' . $answer['Answer']['public_key'] . '/answer/down')); ?>

                <? if($question['Question']['user_id'] == $session->read('Auth.User.id') && $answer['Answer']['status'] != 'correct' && $question['Question']['status'] != 'closed') {?>
                <div class="checkmark">
                        <?=$html->link('','/questions/' .$answer['Answer']['public_key'] . '/' . 'correct');?>
                </div>
                <? } if($answer['Answer']['status'] == 'correct') {
                        echo $html->image('checkmark_green.png');
                } ?>
            </div>
            
            <div class="answer_content" style="float: left; width: 680px; margin-top: 10px;">
                <?=$answer['Answer']['answer_des'];?>
            </div>
        </div>

        <div class="post_actions wrapper">
            <div class="user_info wrapper">
                <div style="float: left; margin-left:-5px;">
                    <div class="thumb_with_border">
                    <?php echo $html->link( $thumbnail->get(array(
                                            'save_path' => WWW_ROOT . 'img/thumbs',
                                            'display_path' => $this->webroot.  'img/thumbs',
                                            'error_image_path' => $this->webroot. 'img/answerAvatar.png',
                                            'src' => WWW_ROOT .  $answer['User']['image'],
                                            'w' => 25,
                                                    'h' => 25,
                                                    'q' => 100,
                            'alt' => $answer['User']['username'] . 'picture' )
                    ),'/users/' .$answer['User']['public_key'].'/'.$answer['User']['username'], array('escape' => false));?>
                    </div>
                    <div style="float: left; line-height: .9;">
                        <?=$html->link($answer['User']['username'],'/users/' . $answer['User']['public_key'] . '/' . $answer['User']['username']);?> 
                        <span style="font-size: 8pt;">&#8226;</span>
                        <h4 style="display: inline;"><?=$answer['User']['identity'];?></h4>
                        <span>answered <?=$time->timeAgoInWords($answer['Answer']['timestamp']);?></span>
                    </div>  
                </div>
            </div>
            <div style="margin-left: 10px;">
                <? if($answer['Answer']['user_id'] == $session->read('Auth.User.id') || isset($rep_rights)) { ?>
                <?=$html->link(__('edit',true),'/questions/' . $answer['Answer']['public_key'] . '/answer/edit'); } ?>
            </div>
        </div>

        <? if(!empty($answer['Comment'])) { ?>
                <div id="comments" style="margin-left: 10px;">
                    <? foreach($answer['Comment'] as $comment) { ?>
                    <div class="comment">
                        <?=$comment['content']?> &ndash; 
                        <?=$html->link($comment['User']['username'], array('controller' => 'users', 'action' => 'view_question', $comment['User']['public_key'], $comment['User']['username']));?>
                        <span><?=$time->timeAgoInWords($comment['timestamp']); ?></span>
                    </div>
                    <? } ?>
                </div>
        <? }  else {?>
                <div style=" height: 10px;"></div>
           <? } ?>

        <div id="comment_<?=$answer['Answer']['id'];?>" class="comment_area" style="margin-left: 10px;padding-bottom: 10px;">
            <?=$form->create(null, array('url' => '/questions/' . $answer['Answer']['public_key'] . '/answer/comment'));?>
            <?=$form->text('Comment.content', array('class' => 'comment_input'));?> 
            <?=$form->end('Comment');?>
        </div>
        <?if($session->check('Auth.User.id')){?>
        <div class="comment_actions" style="margin-left: 10px;padding-bottom: 10px;">
            <?=$html->link('add comment','#');?>
        </div>	
        <?}?>
    </div>
    </div>
    <? } ?>  
    
    </div>