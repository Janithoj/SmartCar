<?	
    echo $html->css('wmd.css');
    echo $javascript->link('wmd/showdown.js');
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('wmd/wmd.js');
?>
<script> 
 $(document).ready(function(){
     $('#lnkpd').fadeOut('slow');
    $('#cbx').change(function(){
        if(this.checked)
            $('#lnkpd').fadeIn('slow');
        else
            $('#lnkpd').fadeOut('slow');

    });
});
</script>

<h3><?= __('Research before post',true) ?></h3>
<hr>

<? if ($session->read('errors')) {
		foreach($session->read('errors.errors') as $error) {
			echo '<div class="error">' . $error . '</div>';
		}
	}
?>
<p>Have you thoroughly searched for an answer before asking your question? Sharing your research helps everyone. Tell us what you found (on this site or elsewhere) and why it didn’t meet your needs. This demonstrates that you’ve taken the time to try to help yourself, it saves us from reiterating obvious answers, and above all, it helps you get a more specific and relevant answer!</p>
<?=$form->create('Question', array('action' => 'search_results'));?>
<?=$form->label(__('Type the question title your expecting to post',true));?><br/>
<?=$form->text('keyword', array('class' => 'wmd-panel big_input','style' => 'width: 650px'));?><br/><br/>
<?=$form->end( __('Search',true));?>
<hr>

<h4>Be specific</h4>
<p>If you ask a vague question, you’ll get a vague answer. But if you give us details and context, we can provide a useful, relevant answer.</p>

<h4>Make it relevant to others</h4>
<p>We like to help as many people at a time as we can. Make it clear how your question is relevant to more people than just you, and more of us will be interested in your question and willing to look into it.</p>

<h4>Keep an open mind</h4>
<p>The answer to your question may not always be the one you wanted, but that doesn’t mean it is wrong. A conclusive answer isn’t always possible. When in doubt, ask people to cite their sources, or to explain how/where they learned something. Even if we don’t agree with you, or tell you exactly what you wanted to hear, remember: we’re just trying to help.</p>

<hr>

<?=$form->input('policy_cbx', array('type'=>'checkbox', 'label'=>' I  thanks, I will keep these tips in mind when asking' ,'id'=>'cbx'));?>
<div style='float: right; font-size: 14px; text-decoration: underline; font-weight:  bold'>
<?=$html->link('Proceed >>', '/questions/ask', array('class' => 'button', 'id'=>'lnkpd'));?>
</div>
