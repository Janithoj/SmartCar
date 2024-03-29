<?
	echo $html->css('wmd.css');
	echo $javascript->link('wmd/showdown.js');
	echo $javascript->link('wmd/wmd.js');

        echo $javascript->link('jquery/jquery.js');
	echo $javascript->link('jquery/jquery.bgiframe.min.js');
	echo $javascript->link('jquery/jquery.ajaxQueue.js');
	echo $javascript->link('jquery/thickbox-compressed.js');
	echo $javascript->link('jquery/jquery.autocomplete.js');
	echo $javascript->link('/tags/suggest');

	echo $html->css('thickbox.css');
	echo $html->css('jquery.autocomplete.css');
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
			$("#title_status").html('<span class="red">Titles must be at least 10 characters long.</span>');
		} else {
			$("#title_status").html('What is your question about?');
		}
	});

  });
  </script>

<h2>Edit<?=($post['Post']['type'] == 'answer') ? ' Your Answer' : ': ' . $post['Post']['title'];?></h2>

<?=$form->create(null, array(
		'url' => '/questions/' . $post['Post']['public_key'] . '/' . $post['Post']['type'] . '/edit')
	); ?>

<? if ($post['Post']['type'] == 'question') { ?>
<?=$form->label('title');?><br/>

<?=$form->text('title', array('class' => 'wmd-panel big_input', 'value' => $post['Post']['title'], 'id' => 'PostTitle'));?><br/>
<span id="title_status"class="quiet">What is your automotive question about?</span>
<? } ?>
<div id="wmd-button-bar" class="wmd-panel"></div>
<? if ($post['Post']['type'] == 'question') { ?>
<?=$form->textarea('description', array('id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $post['Post']['description'])); ?>
<? }elseif ($post['Post']['type'] == 'answer') { ?>
<?=$form->textarea('answer_des', array('id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $post['Post']['answer_des'])); ?>
<?}?>
<div id="wmd-preview" class="wmd-panel"></div>
<? if ($post['Post']['type'] == 'question' && isset($tags)) { ?>
<?=$form->label('tags');?><br/>
<?=$form->text('tags', array('class' => 'wmd-panel big_input', 'value' => $tags, 'id' => 'tag_input'));?><br/>
<span id="tag_status" class="quiet">Combine multiple words into single-words.</span>
<? } ?>
<br/>
<?=$form->end('Edit');?>