<?	
    echo $html->css('wmd.css');
    echo $javascript->link('wmd/showdown.js');
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('wmd/wmd.js');
?> 
<script>
$(document).ready(function(){
    $('#usernametxt').keydown(function (e){
        if(e.keyCode == 13){
            var url = location.protocol + '//'+location.host+'/AutomobiQuiz/api/user/' + $('#usernametxt').val() +'.json';
             
            $.getJSON(url,function(result){
            $('#search_results').empty();    
            $.each(result, function(i, field){
                user_details = $('<div>')
                $(user_details).attr('class','wrapper');
                $(user_details).attr('style','float: right; background-color:#74a57e; padding: 15px; width:130px; height: 50px;');

                user_name = $('<a>').attr('href',location.protocol + '//'+ location.host + '/AutomobiQuiz/users/' 
                        +field.User.public_key + '/'  
                        + field.User.username).html(field.User.username);
                $(user_name).attr('style','font-weight:bold;');
                $(user_details).append(user_name);
                $(user_details).append('<br/>');

                repuation = $('<p>');
                $(repuation).attr('style','font-size: 14px;display:inline;');
                $(repuation).text(field.User.identity);
                $(user_details).append(repuation);
                $(user_details).append('<br/>');
                $(user_details).append(field.User.user_type);

                    $('<tr>').append(
                        $('<td>').html(user_details)
                    ).appendTo('#search_results');
            });
        });
    }
})
});
</script>
<hr>
<?=$form->label('username');?><br/>
<?=$form->text('username', array('class' => 'big_input', 'autocomplete' => 'on', 'id'=>'usernametxt'));?><br/>
<span id="title_status"class="quiet">Who are you looking for?</span>
<hr>
<table id="search_results"  style=" margin-left: -5px;">
<tr>
<?php  
    $i=-1;
     foreach($users as $value) {
        if(!isset($value['User'])) {
            break;
        }
?>
            <td>
               <div class="wrapper">
			<div style="float: right; background-color:#74a57e; width: 180px; height: 70px; margin-left: 8px;">
				<div class="thumb_with_border" style=" margin-left: 5px; margin-top: 3px;">
		
				<?php echo $html->link( $thumbnail->get(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $value['User']['image'],
						        'w' => 60,
								'h' => 60,
								'q' => 60,
		                        'alt' => $value['User']['username'] . 'picture' )
			),'/users/' .$value['User']['public_key'].'/'.$value['User']['username'], array('escape' => false));?>
				</div>
				<div style="float: left; line-height: .9; margin-top: 10px; margin-left: 5px;">
					<div>
			<?=$html->link(
					$value['User']['username'],
					'/users/' . $value['User']['public_key'] . '/' . $value['User']['username']
				);
			?> 
                        </br>
                        </br>
			<h4 style="display: inline;"><?=$value['User']['identity'];?></h4>
                        </br>
					</div> 
			<span><?=$value['User']['user_type'];?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
        </td>
<?php      $i++;
        if($i < 4 && $i > 0) {
            if($i % 3 == 0) {
?>
        </tr>
        <tr>
<?php
            }
        }elseif($i > 4) {
            if(($i - 3) % 4 == 0) {
?>
        </tr>
        <tr>
<?php          }
        }
    }
?>
        </tr>
    </table>

