<?php echoTopMenu();?>
<div class="clearfix">
<div class="left_column">
<script>
function changeTopic(id)
{
	if (id > 0){
		location.href="?act=tasks&id="+id;
	}
}
</script>
<h1>Задания</h1>
<table>
<tr><td>
Тема заданий:</td><td>
<?php
$topic_id = 0;
if (isset($_GET['id']) && intval($_GET['id'])>0){
	$topic_id = intval($_GET['id']);
	$cur_topic = getTopicById($topic_id);
}
?>
<select name="topic" onchange="changeTopic(this.value)">
<option value="0">--Выберите тему</option>
<?php
$topics = getTopics();
foreach($topics as $topic):
?>
<option <?php if ($topic_id==$topic['id'])echo "selected"?> value="<?php echo $topic['id']?>"><?php echo $topic['title']?></option>
<?php
endforeach;
?>
</select></td>
</tr>
</table>

<?php
if (isset($cur_topic)):
$tasks = getTasksByTopicId($cur_topic['id']);
?>
<script>
topicId = <?php echo $cur_topic['id'];?>;
</script>
<table>
<?php foreach($tasks as $task):?>
<tr>
<td>
	<?php echo $task['title']?>
</td>
<td>
	<a href="?act=edit&target=task&id=<?php echo $task['id']?>&topic_id=<?php echo $cur_topic['id'];?>">изменить</a>
</td>
</tr>
<?php
endforeach;
?>
</table>
<?php
endif;
?>
</div>

<div id="add_student" class="right_column">
<h3>Добавление нового задания</h3>
<form action="?act=add&target=task" method="post">
<input type="hidden" name="task_hidden" value="1">
<table>
<tr>
	<td>Название:</td><td><input type="text" name="task_title"></td>
</tr>
<tr>
	<td>Тема:</td><td><?php
if (isset($cur_topic)):
	echo $cur_topic['title'];
	?>
	<input type="hidden" name="topic_id" value="<?php echo $cur_topic['id']?>">
	<?php
else: echo "Не выбрана";
endif;
?></td>
</tr>
</table>
<br>
<input type="submit" value="Добавить" <?php if (!isset($cur_topic))echo "disabled"?>>
</form>
</div>