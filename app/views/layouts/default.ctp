<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$title_for_layout;?> | AutomobiQuiz</title>
	<?=$html->css('screen.css');?>
	<?=$html->css('prettify.css');?>
	<?=$html->script('prettify/prettify.js');?>
        <?=$html->script('jquery/jquery.js');?>
        <?=$html->script('jquery/jquery.ui-1.7.2');?>
	<?=$html->css('skin.css');?>
        <?=$html->css('jeegoopopup/skin/style.css');?>
        <?=$html->script('jquery/jquery-1.10.2.min.js');?>
        <?=$html->script('jquery/jquery.jeegoopopup.1.0.0.js');?>
	
<script type="text/javascript">

setTimeout(function() {
    $('#note').fadeOut('fast');
}, 3000);

  function viewContent(url){
       $.ajax({
             type: "get",  // Request method: post, get
             url: location.protocol + '//'+location.host+'/AutomobiQuiz/' + url + $('#search_box').val(), // URL to request\
             success: function(response) {
                                  document.getElementById("content").innerHTML = response;
                           },
                           error:function (XMLHttpRequest, textStatus, errorThrown) {
                                  alert(textStatus);
                           }
          });
    }  
</script>
  
</head>
<body onload="prettyPrint()">
    <div id="top_actions" class="wrapper">
        <? if($this->params['url']['url'] != 'about'){?>
            <? if(!isset($keyword)){?>
                <?=$form->create('Question', array('action' => 'search_results','style'=>'float: left; display: inline; width:465px;'));?>
                <?=$form->text('keyword', array('style'=> 'width:400px; margin-left: 10px; margin-top: 10px; display: inline; float:left;'));?>
                <?=$form->submit('Search', array('type'=>'image','src' => '/AutomobiQuiz/img/search.png','id' => 'searchbtn','style'=>'display: inline; margin-top: 10px; padding: 0px;', 'class'=>'search'));  ?>
                <?=$form->end();?>
            <? }?>
        <? }?>

            <ul class="tabs">
                <? if($session->check('Auth.User.id')) { ?>
                    <li style="margin-right: -10px; margin-top: -5px; display: inline;">
                        <?=$html->link( $thumbnail->get(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .$session->read('Auth.User.image'),
						        'w' => 25,
								'h' => 25,
								'q' => 100,
		                        'alt' => $session->read('Auth.User.username'). 'picture' )
			),'/users/' .$session->read('Auth.User.public_key').'/'.$session->read('Auth.User.username'), array('escape' => false));?>
                    </li>
                    <li>
                        <a href ="#"><?php __($session->read('Auth.User.username'))?></a>
                        <ul>
                            <li>
                                <li><?=$html->link("View Profile", '/users/' . $session->read('Auth.User.public_key') . '/' . $session->read('Auth.User.username'));?></li>
                                <li><?=$html->link(__('Edit Profile',true),'/users/edit_user/' . $session->read('Auth.User.public_key'))?></li>
                            </li>
                        </ul>
                    </li>
                <? } ?>
                <? if(!$session->check('Auth.User.id')) { ?>
                        <li><?=$html->link(__('login',true),array('controller' => 'users', 'action' => 'login'));?></li>
                <? } ?>
                <? if(!$session->check('Auth.User.id')) { ?>
                    <li><?=$html->link(__('register',true),array('controller' => 'users', 'action' => 'register'));?></li>
                <? } ?>
                    
                <li><?=$html->link(__('about',true),array('controller' => 'pages', 'action' => 'display', 'about'));?></li>
                    
                <? if($session->check('Auth.User.id') && $session->read('Auth.User.user_type') == 'admin') { ?>
                <li>
                    <?=$html->link(__('admin',true),array('controller' => 'users', 'action' => 'admin'));?>
                    <ul>
                        <li>
                            <?=$html->link(ucfirst(__('User Management',true)),array('controller' => 'users', 'action' => 'admin_list'));?>
                        </li>
                        <li>
                            <?=$html->link(ucfirst(__('Settings',true)),array('controller' => 'users', 'action' => 'admin'));?>
                        </li>
                    </ul>
                </li>
                <? } ?>

                <? if($session->check('Auth.User.id')) { ?>
                    <li>
                        <?=$html->link(__('logout',true),array('controller' => 'users', 'action' => 'logout'));?>
                    </li>
                <? } ?>
            </ul>
    </div>
     
    <div id="page" class="wrapper">
        <div class="wrapper" id="header">

            <div class="wrapper">
                <a href="<?=$this->webroot; ?>"><?php echo $html->image('logo.png', array('alt' => 'Logo', 'id' => 'logo')); ?></a>

                <ul class="tabs" >
                  <li><?=$html->link(__('Questions',true),'/');?></li>
                  <li><?=$html->link(__('Tags',true),'/tags');?></li>
                  <li><?=$html->link(__('Unanswered',true),'/questions/unanswered');?></li>
                  <li><?=$html->link(__('Users',true),'/view_users');?></li>
                </ul>
                <ul class="tabs" style="float: right;">
                      <li>
                          <?=$html->link(__('Ask a question',true),'/questions/research');?>
                      </li>
                </ul>
            </div>
        </div>

        <div id="body" class="wrapper">
              <div id="content" class="wrapper">
                  <div id ="note">
                    <?php echo $session->flash(); ?>
                  </div>
                    <?=$content_for_layout;?>
              </div>
          <div id="sidebar" class="wrapper">
              
                <div class="total_questions">
                    <span class="largest_text"><?=$QuesCount?></span></br>
                    <span>Questions</span>
                </div>

                <div id="categories" class="wrapper">
                    <h1>Categories</h1>

                    <ul>
                        <? foreach ($categories as $cate)
                        {?>
                        <li><?=$html->link(__($cate['Category']['category_name'],true),'/questions/recent/1/'.$cate['Category']['id']);?></li>
                        <?}?>   
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div id="footer">
        <div>
            <ul class="tabs" style="margin-top: 15px;">
                <li><?=$html->link(__('home',true),'/');?></li>
                <li><?=$html->link(__('ask a question',true),'/questions/research');?></li>
                <li><?=$html->link(__('about',true),'/about');?></li>
            </ul>
            <hr style="margin-top: 160px;">
            <h4 style="margin-top: 15px; color:  #ffffff; margin-left: 25px;"> Designed and implemented by Janith Amarawickrma 2010080</h4>
        </div>    
    </div>
</body>
</html>
