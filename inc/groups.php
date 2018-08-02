<?php echoTopMenu();?>
<div class="clearfix">

<div class="left_column">
<h1>Группы</h1>

<table>
<?php
$groups = getGroups();

foreach($groups as $group):
?>
<tr>
	<td><a href="?act=students&gid=<?php echo $group['id']?>"><?php echo $group['title']?></a></td>
	<td><a href="?act=rename&target=group&id=<?php echo $group['id']?>">переименовать</a></td>
</tr>
<?php
endforeach;
?>
</table>
</div>
<div class="right_column">
<h3>Добавление новой группы</h3>
<form action="?act=add&target=group" method="post">
<table>
<tr>
	<td>Название:</td><td><input type="text" name="group_name" value=""></td>
</tr>
<tr>
	<td>Комментарий:</td><td><input type="text" name="group_comment" value=""></td>
</tr>
</table>
<br>
<input type="submit" value="Добавить группу">
</form>
</div>
</div>