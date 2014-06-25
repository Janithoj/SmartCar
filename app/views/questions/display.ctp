<?
    echo $javascript->link('jquery/jquery.js');
    echo $javascript->link('jquery/jquery.bgiframe.min.js');
    echo $javascript->link('jquery/jquery.ajaxQueue.js');
?>
<style type="text/css">
a.currentlyActive
{
  text-decoration: underline;
}
</style>

<script>
    function viewContent(url){
       $.ajax({
             type: "get",  // Request method: post, get
             url: url, // URL to request
             success: function(response) {
                                  document.getElementById("results").innerHTML = response;
                           },
                           error:function (XMLHttpRequest, textStatus, errorThrown) {
                                  alert(textStatus);
                           }
          });
    }

$(document).ready(function(){
     $.ajax({
             type: "get",  // Request method: post, get
             url: 'questions/hot/', // URL to request
             success: function(response) {
                                  document.getElementById("results").innerHTML = response;
                           },
                           error:function (XMLHttpRequest, textStatus, errorThrown) {
                                  alert(textStatus);
                           }
          });
          $('.sorting_panel_item').removeClass('currentlyActive');
          $("#hot").addClass("currentlyActive");
          
          $(function() {
          $('.sorting_panel_item').click(function() {
            $('.sorting_panel_item').removeClass('currentlyActive');
            $(this).addClass('currentlyActive');
          });
        });
});
</script>


<div class="wrapper" id="sorting_panel" style="height: 50px; margin-bottom: 10px;" >
    <ul>
        <li ><?=$html->link('Active','javascript:viewContent("questions/hot/")',array('class'=>'sorting_panel_item','id'=>'hot'))?>   |&nbsp;&nbsp;&nbsp;</li>
        <li><?=$html->link('Time','javascript:viewContent("questions/recent/")',array('class'=>'sorting_panel_item','id'=>'recent'))?>   |&nbsp;&nbsp;&nbsp;</li>
        <li><?=$html->link('Popular','javascript:viewContent("questions/popular/")',array('class'=>'sorting_panel_item','id'=>'popular'))?>   |&nbsp;&nbsp;&nbsp;</li>
        <li><?=$html->link('Recommended','javascript:viewContent("questions/recommend/")',array('class'=>'sorting_panel_item','id'=>'recommend'))?>   |</li>
    </ul>
    <hr>
</div>
<div class="wrapper" id ="results"></div>

