<?php echoTopMenu();?>
<div class="clearfix">
<div class="left_column">
<h1>Студенты</h1>

<?php
if (isset($_GET['gid'])):

	$gid = intval($_GET['gid']);
	$group = getGroupById($gid); // если группа неправильная - написать проверку
	$students = getStudentsByGroupId($gid);
?>

<h2><?php echo $group['title'];?></h2>
<p>Список студентов группы</p>
<?php else:
	$students = getStudents();
?>
<p>Список всех студентов</p>
<?php endif;?>
<?php if (count($students)==0):?>
<p><b>В группе нет студентов</b></p>
<?php endif;?>
<table>
<?php

foreach($students as $student):
$get ="";
if (isset($_GET['gid'])):
	$gid = intval($_GET['gid']);
	$get = "&gid=$gid";
endif;

?>
<tr>
<td>
<?php echo $student['name_first']." ".$student['name_last']?> 
</td><td>
<a href="?act=edit&target=student&id=<?php echo $student['id'].$get?>">редактировать</a>
</td><td> 
<a href="?act=move&target=student&id=<?php echo $student['id'].$get?>">переместить</a>
</td>
</tr>
<?php endforeach;?>
</table>
</div>
<div id="add_student" class="right_column">
<h3>Добавление студента</h3>
<?php
$action_add="?act=add&target=student";
if (isset($_GET['gid'])){
	$action_add.="&gid=$gid";
}
?>
<form action="<?php echo $action_add?>" method="post">
<input type="hidden" name="student_hidden" value="1">
<table>
<tr>
<td>Имя:</td><td><input type="text" id="student_name" name="student_name" value=""></td>
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
<br>
	<input type="submit" value="Добавить">
</form>
</div>