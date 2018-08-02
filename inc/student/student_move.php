<?php echoTopMenu();?>
<h3>Перемещение студента в другую группу</h3>
<?php
if (isset($_GET['id'])):
$student = getStudentById($_GET['id']);
$student_groups_id = getStudentGroup($_GET['id']);

$group = false;
if (isset($student_groups_id[0])){	
	$group = getGroupById($student_groups_id[0]);		
}
?>
<form action="" method="post">
<input type="hidden" name="student_hidden" value="1">
<input type="hidden" name="student_id" value="<?php echo $student['id']?>">
<table>
<tr>
<td>Имя:</td><td><?php echo $student['name_first']?></td>
</tr><tr>
<td>Фамилия:</td><td><?php echo $student['name_last']?></td>
</tr><tr>
<td>Группа:</td><td><?php
if (is_array($group)): 
	echo $group['title'];
	?>
	<input type="hidden" name="student_group" value="<?php echo $group['id']?>">	
	<?php
elseif (isset($_GET['gid'])):
	$group = getGroupById($_GET['gid']);
	echo $group['title'];
	?>
	<input type="hidden" name="student_group" value="<?php echo $group['id']?>">	
	<?php
else:
	echo "не указана";
endif;

?></td></tr>
<tr><td>Новая группа</td>
<?php
$groups = getGroups();
	
?>
<td><select name="student_group_new">
	<?php foreach($groups as $group):?>
	<option value="<?php echo $group['id']?>"><?php echo $group['title']?></option>
	<?php endforeach;?>
	</select>
</td>
</tr>
</table>	
<input type="submit" value="Переместить">
</form>
<?php
endif;
?>
