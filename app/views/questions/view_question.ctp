<?	
    echo $html->css('wmd.css');
    echo $javascript->link('wmd/showdown.js');
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('wmd/wmd.js');
?>
<style type="text/css">
a.currentlyActive
{
  text-decoration: underline;
}
</style>
<script> 
   var publicKey = '<?php echo $question['Question']['public_key']; ?>'; 
   function viewContent(url){
       $.ajax({
             type: "get",  // Request method: post, get
             url: location.protocol + '//'+location.host+'/AutomobiQuiz/' + url, // URL to request\
             success: function(response) {
                                  document.getElementById("results").innerHTML = response;
                                  $(".comment_area").hide();
                           },
                           error:function (XMLHttpRequest, textStatus, errorThrown) {
                                  alert(textStatus +location.protocol + '//'+location.host+'/AutomobiQuiz/' + url);
                           }
          });
    }  
    
  $(document).ready(function(){
    $(".comment_area").hide();
    
    $(".comment_actions a").click(function(event){
      $(this).parents("div").prev(".comment_area").toggle();
	  $(this).toggle();

      event.preventDefault();
    });
    
    $.ajax({
             type: "get",  // Request method: post, get
             url: location.protocol + '//'+location.host+'/AutomobiQuiz/' + 'questions/view_answers/'+ publicKey + '/time/', // URL to request\
             success: function(response) {
                                  document.getElementById("results").innerHTML = response;
                           },
                           error:function (XMLHttpRequest, textStatus, errorThrown) {
                                  alert(textStatus);
                           }
          });
          $('.sorting_panel_item').removeClass('currentlyActive');
          $("#time").addClass("currentlyActive");
          
          $(function() {
          $('.sorting_panel_item').click(function() {
            $('.sorting_panel_item').removeClass('currentlyActive');
            $(this).addClass('currentlyActive');
          });
        });
  });
</script>


<div id="question" class="question">
    <div class="content_container wrapper">
        <div class="content_actions" style="float: left; width: 55px; margin-right: 10px;">
            <?php echo $html->image('arrow_up.png', array('alt' => 'Vote Up', 'url' => '/vote/' . $question['Question']['public_key'] . '/question/up'));?>
            <span class="large_text" style="display: block; padding: 0px; margin: 0px; color: #ffffff;"><strong><?=$question['Question']['votes'];?></strong></span>
            <?php echo $html->image('arrow_down.png', array('alt' => 'Vote Down', 'url' => '/vote/' . $question['Question']['public_key'] . '/question/down'));?>
        </div>
        <div class="question_content" style="float: left; width: 700px;">
            <h2><?=$question['Question']['title'];?></h2>
            <?=$question['Question']['description'];?>
        </div>
    </div>

    <div class="post_actions wrapper">
        <div style="width: 100px; float: left; margin-left: 10px;">
            
            <?if($question['Question']['user_id'] == $session->read('Auth.User.id') || isset($rep_rights) || $admin) { ?>
                    <?=$html->link(__('edit',true), '/questions/' . $question['Question']['public_key'] . '/question/edit');
            }?>
            <?if($admin){ ?>
                   <?=$html->link(__('del',true),'/questions/delete/'.$question['Question']['id']); ?></a>
            <?}?>

        </div>

        <div class="user_info wrapper">
            <div style="float: left; margin-left:-8px;">
                    <div class="thumb_with_border">
                    <?php echo $html->link( $thumbnail->get(array(
                                            'save_path' => WWW_ROOT . 'img/thumbs',
                                            'display_path' => $this->webroot.  'img/thumbs',
                                            'error_image_path' => $this->webroot. 'img/answerAvatar.png',
                                            'src' => WWW_ROOT .  $question['User']['image'],
                                            'w' => 25,
                                                    'h' => 25,
                                                    'q' => 100,
                            'alt' => $question['User']['username'] . ' picture' )
                    ),'/users/' .$question['User']['public_key'].'/'.$question['User']['username'], array('escape' => false));?>
            </div>
                
            <div style="float: left; line-height: .9;">
                <div>
                    <?=$html->link(
                                    $question['User']['username'],
                                    '/users/' . $question['User']['public_key'] . '/' . $question['User']['username']
                            );
                    ?> 
                    <span style="font-size: 8pt;">&#8226;</span>
                    <h4 style="display: inline;"><?=$question['User']['identity'];?></h4>
                </div> 
                <span>asked <?=$time->timeAgoInWords($question['Question']['timestamp']);?></span>
            </div>
            </div>
        </div>

        <div id="tags" style="clear: left; margin-left: 10px;">
            <? foreach($question['Tag'] as $tag) { ?>
                <div class="tag">
                    <?=$html->link($tag['tag'],'/tags/' . $tag['tag']);?>
                </div>
            <? } ?>
        </div>
    </div>

    <? if(!empty($question['Comment'])) { ?>
            <div id="question_comments" style=" margin-left: 10px;">
                <? foreach($question['Comment'] as $comment) { ?>
                <div class="comment">
                    <?=$comment['content']?> &ndash;
                    <?=$html->link($comment['User']['username'],'/users/' . $comment['User']['public_key'] . '/' . $comment['User']['username']);?>
                    <span><?=$time->timeAgoInWords($comment['timestamp']); ?></span>
                </div>
                <? } ?>
            </div>
    <? } ?>

    <div id="comment_<?=$question['Question']['id'];?>" class="comment_area" style=" margin-left: 10px; padding-bottom: 10px;">
            <?=$form->create(null, array('url' => '/questions/' . $question['Question']['public_key'] . '/question/comment'));?>
            <?=$form->text('Comment.content', array('class' => 'comment_input'));?>
            <?=$form->end('Comment');?>
    </div>
    <?if($session->check('Auth.User.id')){?>
    <div class="comment_actions" style=" margin-left: 10px; padding-bottom: 10px;">
        <?=$html->link(__('add comment',true),'#');?>
    </div>
    <? }?>
</div>

<div id="answers">
    <? if(count($answers)==1){?>
        <h2><?=count($answers)?> Answer </h2>
    <? }else{?>
        <h2><?=count($answers)?> Answers </h2>
    <? } ?>
        
    <? if(count($answers)!=0){?>
    <div class="wrapper" id="sorting_panel" style="height: 30px; margin-top: -35px; float: right; display: inline; margin-left: -20px;" >    
        <ul>
            <li><?=$html->link('Time','javascript:viewContent("questions/view_answers/'.$question['Question']['public_key'].'/time/")',array('class'=>'sorting_panel_item','id'=>'time'))?>   |&nbsp;&nbsp;&nbsp;</li>
            <li><?=$html->link('Votes','javascript:viewContent("questions/view_answers/'.$question['Question']['public_key'].'/votes/")',array('class'=>'sorting_panel_item','id'=>'votes'))?>   |&nbsp;&nbsp;&nbsp;</li>
            <li><?=$html->link('Recommended','javascript:viewContent("questions/view_answers/'.$question['Question']['public_key'].'/recommended/")',array('class'=>'sorting_panel_item','id'=>'recommended'))?></li>
        </ul>
    </div>
    <? } ?>
    <hr/>
    
    <div id="results" class="wrapper"></div>
   
</div>
<?if($session->check('Auth.User.id') && $session->read('Auth.User.user_type')!= 'student'){?>
<div id="user_answer">
    <? if ($session->read('errors')) {
            foreach($session->read('errors.errors') as $error) {
                echo '<div class="error">' . $error . '</div>';
            }
       }?>
    
    <h3><?php __('your answer'); ?></h3>
    
    <?=$form->create('Answer', array('url' => '/questions/' . $question['Question']['public_key'] . '/' . $question['Question']['url_title'] . '/answer')); ?>
    
    <div id="wmd-button-bar" class="wmd-panel"></div>
    <?=$form->textarea('answer_des', array('id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $session->read('errors.data.Answer.answer_des')));?>

    <div id="wmd-preview" class="wmd-panel"></div>
    
    <?$recaptcha->display_form('echo');?>
    <br/>
    <?=$form->end(__d('verb','Answer',true));?>
</div>
<?}?>
