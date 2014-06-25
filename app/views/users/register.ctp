<h2>Register Yourself</h2>


	<p>
            Register and be a member of AutomobiQuiz and enjoy the features. 
	</p>
	
	<div class="block_label">
	<?=$form->create('User', array('action' => 'register'));?>
	
	<?=$form->input('username', array('class' => 'large_input'));?>

	<?=$form->input('email', array('class' => 'large_input'));?>

	<?=$form->input('secret', array('type' => 'password', 'label' => 'Password', 'class' => 'large_input'));?> 
	<?$recaptcha->display_form('echo');?>
	<?=$form->end('Register');?>
	</div>