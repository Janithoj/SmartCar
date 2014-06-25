<h2>Settings</h2></br>

<form action="?" method="post" >
<div class="detailed_inputs" style=" background-color:  cornflowerblue; margin-top: 5px;">
	<div>
		<h3>Vote up</h3>
		<input type="text" name="data[Setting][0][value]" value="<?=$settings['0']['Setting']['value'];?>"/></br>
                <h3><span class="small"><?=$settings['0']['Setting']['description'];?></span></h3>
	</div>
	<div>
		<h3>Comment </h3>
		<input type="text" name="data[Setting][1][value]" value="<?=$settings['1']['Setting']['value'];?>"/></br>
                <h3><span class="small"><?=$settings['1']['Setting']['description'];?></span></h3>
	</div>
	<div>
		<h3>Vote Down </h3>
		<input type="text" name="data[Setting][2][value]" value="<?=$settings['2']['Setting']['value'];?>"/></br>
                <h3><span class="small"><?=$settings['2']['Setting']['description'];?></span></h3>
	</div>
	<div>
		<h3>Edit </h3>
		<input type="text" name="data[Setting][3][value]" value="<?=$settings['3']['Setting']['value'];?>"/></br>
                <h3><span class="small"><?=$settings['3']['Setting']['description'];?></span></h3>
	</div>
        <hr>
	<div class="submit">
		<input type="submit" value="Update Settings"/>
	</div>
</div>
</form>