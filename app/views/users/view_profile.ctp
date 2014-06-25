<link rel="stylesheet" href="stylesheets/print.css" type="text/css" media="print" charset="utf-8">
  <!--[if lte IE 6]><link rel="stylesheet" href="stylesheets/lib/ie.css" type="text/css" media="screen" charset="utf-8"><![endif]-->
<?php echo $this->Html->script(array(
        'jquery/jquery.js',
        'jquery/jquery.tabs.js',
        'jquery/jquery.ui-1.7.2.js',
        'jquery/ui.core.js'
        )); 
 ?>	

	<script type="text/javascript">
	 $(function() { $("#tabs").tabs(); }); 
	</script>
<script type="text/javascript">
$(document).ready(function(){
$('#tabs div').hide(); // Hide all divs
$('#tabs div:first').show(); // Show the first div
$('#tabs ul li').addClass('inactive'); // set all links to inactive
$('#tabs ul li:first').removeClass('inactive'); //remove inactive class from first link...
$('#tabs ul li:first').addClass('active'); // ...and set the class of the first link to active
$('#tabs ul li a').click(function(){ //When any link is clicked
	$('#tabs ul li').removeClass('active'); // Remove active class from all links
	$('#tabs ul li').removeClass('inactive');	
	$('#tabs ul li').addClass('inactive'); // set all links to inactive
	$(this).parent().removeClass('inactive'); //remove inactive class from the link that was clicke
	$(this).parent().addClass('active'); //Set clicked link class to active
	var currentTab = $(this).attr('href'); // Set variable currentTab to value of href attribute of clicked link
	$('#tabs div').hide(); // Hide all divs
	$(currentTab).show(); // Show div with id equal to variable currentTab
	return false;
	});
});
</script>
<div class="wrapper" style="background-color: #ffaa00; margin-top: 5px;">
<div id="userAvatar" style=" margin-left: 10px; margin-top: 10px;">
	<div id="image">
		<? if(empty($user['User']['image'])) { ?>
			<?=$html->image('answerAvatar.png'); ?>
		<? }else { 
echo $thumbnail->show(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $user['User']['image'],
						        'w' => 150,
								'h' => 150,
								'q' => 150,
		                        'alt' => $user['User']['username'] . ' picture' )
			);
		} ?>
	</div>
</div>
<div id="userInfo" style=" margin-top: 10px; width: 585px;">
	<? if(!empty($user['User']['info'])) { 
		echo $user['User']['info'];	
	}else { 
		echo $user['User']['username'] . ' has not added any information about themselves yet!';
	 } ?> 
</div>
    
<div style=" margin-left: 10px;">
    <h3 style="float: left;">Details</h3></br>
    <table style="float: left; margin-top: 20px; margin-left: 0px; width: 760px;">
            <tr>
                    <td><b>Display Name</b></td>
                    <td><?= $user['User']['username'] ?></h2>
                    <td><b>Email</b></td>
                    <td><?= $user['User']['email'] ?></h2>  
                    <td><b>Website</b></td>
                    <td><?=$html->link($user['User']['website'],'https://'.$user['User']['website'])?></h2>  
            </tr>
             <tr>
                    <td><b>Full Name</b></td>
                    <td><?= $user['User']['full_name'] ?></h2>
                        <td><b>Location</b></td>
                    <td><?= $user['User']['location'] ?></h2>
                    <td><b>Member Type</b></td>
                    <td><?= $user['User']['user_type'] ?></h2>          
            </tr>
            <tr>
                    <td><b>Joined</b></td>
                    <td><?=$time->timeAgoInWords($user['User']['joined']);?></td>
                    <td><b>Age</b></td>
                    <td><?= $user['User']['age'] ?></h2>
                        
                    <td><?
                    if($user['User']['user_type']=='student'){?>
                        <b>Loyalty</b>
                    <? }elseif ($user['User']['user_type']=='tutor'|| $user['User']['user_type']=='admin') {?>
                        <b>Reputation</b>
                    <?}?>
                    </td>
                    <td><?=$user['User']['identity'];?></td>
            </tr>
    </table>
</div>
</div>
<div id="tabs" style="margin-top: 10px; width: 790px;">
            <ul>
                <li style=" background-color: #CC3333;">
                    <a href="#tab-3">
                            <h3>Q</h3>
                    </a>
                </li>
                <li style=" background-color: #8c81b8;">
                    <a href="#tab-4">
                            <h3>A</h3>
                    </a>
                </li>
            </ul>
	
	
	
	<div class="tabPanel" id="tab-3">
            <h3>questions asked:</h3>
            </br>
	    <? if(!empty($questions)){ foreach($questions as $value) { ?>
		<p>
	    <? if(!empty($value)) {
	        echo $html->link($value['Question']['title'], '/questions/' . $value['Question']['public_key'] . '/' . $value['Question']['url_title']);
		}
		?>
		</p>
            <? } }?>
	</div><!-- end questions tab -->
	
	<div class="tabPanel" id="tab-4">
		<h3>answers given:</h3>
                 </br>
                <? if(!empty($answered)):?>
                <?foreach($answered as $value) : ?>
                    <p>
                        <? if(!empty($value)) : ?>
                        <?=$html->link($value['Question']['title'], '/questions/' .$value['Question']['public_key'] . '/' . $value['Question']['url_title']);?>
                        <? endif; ?>
                    </p>
		<? endforeach; ?>
                <? endif;?>
	</div><!-- end answers tab -->
	
	<div class="tabPanel" id="tab-5">
		<h3>tags assigned:</h3>
		<p>content for tags tab</p>
	</div>
</div><!-- end tabs-->