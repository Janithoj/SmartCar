<?	
    echo $html->css('wmd.css');
    echo $javascript->link('wmd/showdown.js');
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('wmd/wmd.js');
?> 

<script>
function loadData(){
    var url = location.protocol + '//'+location.host+'/AutomobiQuiz/api/question/' + $('#keywordtxt').val() +'.json';
            $.getJSON(url,function(result){ 
                $('#records_table').empty();
                $('#key_title').empty();
                
                 $('#key_title').text($('#keywordtxt').val());
                
                $.each( result, function( i, item ) {
                link =  $('<a>').attr('href',location.protocol + '//'+ location.host + '/AutomobiQuiz/questions/' 
                        +item.Question.public_key + '/'  
                        + item.Question.url_title).html(item.Question.title); 
                user_details = $('<div>')
                $(user_details).attr('class','wrapper');
                $(user_details).attr('style','float: right; background-color:#74a57e;');

                user_name = $('<a>').attr('href',location.protocol + '//'+ location.host + '/AutomobiQuiz/users/' 
                        +item.User.public_key + '/'  
                        + item.User.username).html(item.User.username);
                $(user_name).attr('style','font-weight:bold;');
                $(user_details).append(user_name);
                $(user_details).append('<span style="font-size: 8pt;">&nbsp;&#8226;&nbsp;</span>');

                repuation = $('<p>');
                $(repuation).attr('style','font-size: 14px;display:inline;');
                $(repuation).text(item.User.identity);
                $(user_details).append(repuation);

                    $('<tr>').append(
                        $('<td>').html(link).append(user_details)
                    ).appendTo('#records_table');
                });
        });
}
       
$(document).ready(function(){
    // load data when first time come to the page.
    loadData();
    $('#keywordtxt').keydown(function (e){
        if(e.keyCode == 13){
            loadData();
        }
    }); 
});
</script>

<hr>
<?if(!empty($keyword)){ $key = $keyword; }else{$key = '';}?>
<?=$form->label('Keyword');?><br/>
<?=$form->text('keyword', array('class' => 'big_input', 'autocomplete' => 'off', 'id'=>'keywordtxt', 'style'=>'width:400px;', 'value'=> $key));?><br/>
<hr>

<h4>Search Results for: <span id="key_title">&nbsp;<?=$key?></span></h4>
<hr>
<div class="wrapper" id="results">
    <table id="records_table"></table>
    <hr>
</div>