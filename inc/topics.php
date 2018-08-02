<?php echoTopMenu();?>
<div class="clearfix">
<div class="left_column">
<h1>Темы занятий</h1>
<table>
<tr><th width="100"></th><th width="80"></th><th></th></tr>
<?php
$topics = getTopics();

foreach($topics as $topic):
?>
<tr><td><?php echo $topic['title']?></td>
<td style="color:gray"><?php echo $topic['comment']?></td>
<td><a href="?act=rename&target=topic&id=<?php echo $topic['id']?>">переименовать</a></td></tr>
<?php
endforeach;
?>
</table>
</div>
<div id="add_student" class="right_column">
<h3>Добавление новой темы</h3>
<form action="?act=add&target=topic" method="post">
<table>
<tr>
	<td>Название:</td><td><input type="text" name="topic_title"></td>
</tr>
<tr>
	<td>Комментарий:</td><td><input type="text" name="topic_comment"></td>
</tr>
</table>
<br>
<input type="submit" value="Добавить">
</form>
</div>