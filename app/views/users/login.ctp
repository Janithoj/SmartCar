<p>
	<?php __("Login below to sign into your account"); ?>.
</p>
<p>
	Want to get an account? 
	<?=$html->link(
			'Register',
			array('controller' => 'users', 'action' => 'register')
		);
	?>	
</p>
<div id="login_panel" class="block_label">
<?php
    $session->flash('auth');
    echo $form->create('User', array('action' => 'login'));
    echo $form->input('email', array('class' => 'large_input'));
    echo $form->input('password', array('class' => 'large_input'));
    echo $form->end('Login');
?>
</div>
<p>
    Forget your password?  <?=$html->link(' Click here',array('controller' => 'users', 'action' => 'lost_password'))?>
</p>