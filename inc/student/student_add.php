<?php echoTopMenu();?>
<h3>Добавление студента</h3>

<form method="post">
<input type="hidden" name="student_hidden" value="1">
<table>
<tr>
<td>Имя:</td><td><input type="text" name="student_name" value=""></td>
</tr><tr>
<td>Фамилия:</td><td><input type="text" name="student_last" value=""></td>
</tr>
<tr>
<?php
if (isset($_GET['gid'])):
	$gid = intval($_GET['gid']);
	$group = getGroupById($gid);
	?>
	<input type="hidden" name="student_group" value="<?php echo $gid?>">
	<td>В группу:</td><td><?php echo $group['title']?></td>
	<?php
else:
	$groups = getGroups();
	
	?>
	<td>Группа:</td><td>
	<select name="student_group">
	<option value="0">--Выберите группу</option>
	<?php foreach($groups as $group):?>
	<option value="<?php echo $group['id']?>"><?php echo $group['title']?></option>
	<?php endforeach;?>
	</select></td>
	<?php	
endif;
?>
</tr>
</table>
	<input type="submit" value="Добавить">
</form>