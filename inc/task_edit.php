<?php
// Обработка пост
if (isset($_POST['task_hidden'])):
	editTask($_POST, 'prefix:task');
	$id = "";
	if (isset($_GET['topic_id'])){
		$id = '&id='.intval($_GET['topic_id']);
	} 
?>
<div>Задача отредактирована успешно. Подождите 3 секунды для перехода</div>
<meta http-equiv="refresh" content="3;?act=tasks<?php echo $id?>">
<?php
else:
?>
<?php echoTopMenu();?>
<h3>Редактирование задачи</h3>
<?php
	$topic_id = intval($_GET['topic_id']);
	$task_id = intval($_GET['id']);

	$tasks = getTasksByTopicId($topic_id);
	// найти нужный таск по id
	$found = -1;

	for($i=0; $i<count($tasks); $i++)
	{
		if ($tasks[$i]['id']==$task_id){
			$found = $i;
			break;
		}
	}
	if ($found>=0):
	

?>
<form action="" method="post">
<input type="hidden" name="task_hidden" value="1">
<input type="hidden" name="task_id" value="<?php echo intval($_GET['id'])?>">
<input type="hidden" name="topic_id" value="<?php echo intval($_GET['topic_id'])?>">
Название задачи: <input type="text" name="task_title" value="<?php echo $tasks[$found]['title'];?>">
<input type="submit" value="Сохранить">
</form>
<?php
	else:
		echo "Задача не найдена";
	endif;
endif;