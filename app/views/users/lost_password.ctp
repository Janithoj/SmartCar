<p>Enter your email address that registered with the site and a new password will be sent to you shortly.</p>
<?
    echo $form->create('User', array('action' => 'lost_password'));
    echo $form->input('email &nbsp; &nbsp;', array('class' => 'large_input'));
    echo $form->end('Send me');
?>