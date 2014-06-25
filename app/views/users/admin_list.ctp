<script type="text/javascript">
function altRows(id){
	if(document.getElementsByTagName){  
		
		var table = document.getElementById(id);  
		var rows = table.getElementsByTagName("tr"); 
		 
		for(i = 0; i < rows.length; i++){          
			if(i % 2 == 0){
				rows[i].className = "evenrowcolor";
			}else{
				rows[i].className = "oddrowcolor";
			}      
		}
	}
}
window.onload=function(){
	altRows('alternatecolor');
}
</script>

<h2>User Management</h2></br>

<table class="altrowstable" id="alternatecolor" style ="padding: 30px; width: 760px;">
<tr>
    <th><b>Username</b></th>
    <th><b>User Type</b></th>
    <th><b>Actions</b></th>
</tr>
<?
$items = array('admin','tutor','student');
?>
<?foreach ($users as $values){?>
<tr>
    <td><?=$values['User']['username']?></td>
    <td><?=$values['User']['user_type']?></td>
    <td> <?=$html->link('Delete  ','/admin/user_delete/'.$values['User']['id'] )?> | <?=$html->link('Send message  ','')?> |Change type
     <?if($values['User']['user_type'] == 'student')
     {
        echo $html->link(' student>  ','/admin/promote/'.$values['User']['public_key'].'/student',array('style'=>'text-decoration:underline;'));
        echo $html->link('tutor> ','/admin/promote/'.$values['User']['public_key'].'/tutor');
        echo $html->link('admin>  ','/admin/promote/'.$values['User']['public_key'].'/admin');
        
     }elseif ($values['User']['user_type'] == 'tutor') {
        echo $html->link(' student>  ','/admin/promote/'.$values['User']['public_key'].'/student');
        echo $html->link('tutor> ','/admin/promote/'.$values['User']['public_key'].'/tutor',array('style'=>'text-decoration:underline;'));
        echo $html->link('admin>  ','/admin/promote/'.$values['User']['public_key'].'/admin');
                 
     }elseif ($values['User']['user_type'] == 'admin') {
        echo $html->link(' student>  ','/admin/promote/'.$values['User']['public_key'].'/student');
        echo $html->link('tutor> ','/admin/promote/'.$values['User']['public_key'].'/admin');
        echo $html->link('admin>  ','/admin/promote/'.$values['User']['public_key'].'/admin',array('style'=>'text-decoration:underline;'));     
     }

?> </td>
</tr>
<?}?>
</table>
