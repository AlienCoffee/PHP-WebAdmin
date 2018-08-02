<?php echoTopMenu();?>
<h3>Редактирование данных студента</h3>
<?php
if (isset($_GET['id'])):
	$student = getStudentById($_GET['id']);
?>
<form action="" method="post">
<input type="hidden" name="student_hidden" value="1">


<input type="hidden" name="student_id" value="<?php echo $student['id']?>">
<table>
<tr><td>
Имя:</td><td> <input type="text" name="student_name" value="<?php echo $student['name_first']?>"></td>
</tr><tr><td>
Фамилия:</td><td> <input type="text" name="student_last" value="<?php echo $student['name_last']?>"></td>
</tr>
<?php
if (isset($_GET['gid'])):
$group = getGroupById($_GET['gid']);
?>
<tr>
<td>Группа:</td><td><?php echo $group['title'];?></td>
</tr>
<?php

endif;
?>
</table>
<input type="submit" value="Изменить">
</form>
<?php
else:
echo "Не указан Id студента";
endif;