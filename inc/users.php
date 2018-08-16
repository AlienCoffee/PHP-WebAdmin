<?php echoTopMenu(); ?>
<h1>Администраторы</h1>
<p>Список администраторов</p>
<?php 
$users = getTeachers();
if (count($users)>0):
?>
<table>
<tr><th>ИД</th><th>Имя</th><th>Права</th></tr>
<?php foreach($users as $user):?>
<tr><td><?php echo $user['id']?></td><td><?php echo $user['first_name']?></td><td><?php echo $user['rights']?></td></tr>
<?php endforeach; ?>
</table>
<?php else:?>Не найдено
<?php endif;
