<?php echoTopMenu();?>
<div class="clearfix">
<div class="left_column">
<h1>Темы занятий</h1>
<script>
function changeGroup(id)
{
	if (id > 0){
		location.href="?act=topics&id="+id;
	} else {
		location.href="?act=topics";
	}
}
</script>
<table>
<tr><td>
Группа:</td><td>
<?php
$group_id = 0;

if (isset($_GET['id']) && intval($_GET['id'])>0){
	$group_id = intval($_GET['id']);
}
?>
<select name="group" onchange="changeGroup(this.value)">
<option value="0">--Выберите группу</option>
<?php
$groups = getGroups();
$cur_group = false;
foreach($groups as $group):
?>
<option <?php if ($group['id']==$group_id){$cur_group = $group; echo "selected";}?> value="<?php echo $group['id']?>"><?php echo $group['title']?></option>
<?php
endforeach;
?>
</select></td>
</tr>
</table>
<table>
<tr><th width="100"></th><th width="80"></th><th></th></tr>
<?php
if ($cur_group)
	$topics = getTopicsByGroupId($cur_group['id']);
else
	$topics = getTopics();

foreach($topics as $topic):
?>
<tr><td><a href="?act=tasks&id=<?php echo $topic['id']?>"><?php echo $topic['title']?></a></td>
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
<input type="hidden" name="topic_hidden" value="1">
<table>
<tr>
	<td>Название:</td><td><input type="text" name="topic_title"></td>
</tr>
<tr>
	<td>Комментарий:</td><td><input type="text" name="topic_comment"></td>
</tr>
<tr>
	<td>Группа:</td><td><?php
if ($cur_group):
	echo $cur_group['title'];
	?>
	<input type="hidden" name="topic_group" value="<?php echo $cur_group['id']?>">
	<?php
else: echo "Не выбрана";
endif;
?>
	</td>
</tr>
</table>
<br>
<input type="submit" value="Добавить">
</form>
</div>