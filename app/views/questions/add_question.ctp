<?
    echo $html->css('wmd.css');
    echo $html->css('thickbox.css');
    
    echo $html->script('wmd/showdown.js');
    echo $html->script('wmd/wmd.js');
    
    echo $html->script('jquery/thickbox-compressed.js');
    echo $html->script('jquery/jquery.bgiframe.min.js');
    echo $html->script('jquery/jquery.ajaxQueue.js');
    echo $html->script('jquery/jquery.js');
    echo $html->script('jquery/jquery.autocomplete.js');
    echo $html->css('jquery.autocomplete.css');

    echo $html->script('/tags/suggest'); 
?>

  <script>
  $(document).ready(function(){
	$("#resultsContainer").show("blind");
	
	$("#tag_input").autocomplete(tags, {
		minChars: 0,
		multiple: true,
		width: 350,
		matchContains: true,
		autoFill: false,
		formatItem: function(row, i, max) {
			return row.name + " (<strong>" + row.count + "</strong>)";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.count;
		},
		formatResult: function(row) {
			return row.name;
		}
	});
	
	$("#PostTitle").blur(function(){
		if($("#PostTitle").val().length >= 10) {
			$("#title_status").toggle();
			getResults();
		} else {
			$("#title_status").show();
		}
	});

	function getResults()
	{
	
		$.get("/mini_search",{query: $("#PostTitle").val(), type: "results"}, function(data){
		
			$("#resultsContainer").html(data);
			$("#resultsContainer").show("blind");
		});
	}	
	
	$("#PostTitle").keyup(function(event){
		if($("#PostTitle").val().length < 10) {
			$("#title_status").html('<span class="red"><?= __('Titles must be at least 10 characters long.',true) ?></span>');
		} else {
			$("#title_status").html('<?= __('What is your question about?',true) ?>');
		}
	});
	
  });
</script>

<h2><?= __('Ask a question',true) ?></h2>
<? if ($session->read('errors')) {
                foreach($session->read('errors.errors') as $error) {
                        echo '<div class="error">' . $error . '</div>';
                }
        }
?>

<?=$form->create('Question', array('action' => 'add_question'));?>

<?=$form->label(__('Category',true));?><br/>
<? 
$items;
foreach ($categories as $cate)
{
   $items[] = $cate['Category']['category_name'];
}
echo $form->input('category_name',
    array('label' => '',
      'options' => $items,
      'class' => 'wmd-panel big_input'
      ));
?>
<span id="tag_status" class="quiet"><?= __('Select a category your question belongs to',true) ?></span>
<br/>
<?=$form->label(__('Title',true));?>
<br/>

<?=$form->text('title', array('class' => 'wmd-panel big_input', 'value' => $session->read('errors.data.Post.title')));?><br/>
<span id="title_status" class="quiet"><?= __('What is question about your vehicle? Be specific',true) ?></span>

<div id="resultsContainer"></div>

<div id="wmd-button-bar" class="wmd-panel"></div>
<?=$form->textarea('description', array(
	'id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $session->read('errors.data.Question.description')
	));
?>

<div id="wmd-preview" class="wmd-panel"></div>

<?=$form->label(__('Tags',true));?><br/>
<?=$form->text('tags', array('id' => 'tag_input', 'class' => 'wmd-panel big_input'));?><br/>
<span id="tag_status" class="quiet"><?= __('at least one tag such as(Paint,Pistons,Wheel Alignment), max 5 tags',true) ?></span>

<? if(!$session->check('Auth.User.id')) { ?>
    <h2><?= __('Who Are You?',true) ?></h2>
    <span class="quiet"><?= __('Have an account already?',true) ?> <a href="#"><?= __('Login before answering!',true) ?></a></span><br/>
            <?=$form->label(__('Name',true));?><br/>
            <?=$form->text('User.username', array(
                    'class' => 'big_input medium_input', 
                    'value' => $session->read('errors.data.User.username')
                    ));
            ?><br/>
            <?=$form->label(__('Email',true));?><br/>
            <?=$form->text('User.email', array(
                    'class' => 'big_input medium_input',
                    'value' => $session->read('errors.data.User.email')
                    ));
            ?><br/>		
<? } ?>
<br/><br/>
<?=$form->checkbox('Question.notify', array('checked' => true));?>
<span style="margin-left: 5px;"><?= __('Send me new responses to my posts via email',true) ?></span><br/><br/>

<?$recaptcha->display_form('echo');?><br/>

<?=$form->end( __('Post your question',true));?>

