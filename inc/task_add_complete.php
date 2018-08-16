<div>Задача добавлена успешно. Подождите 3 секунды для перехода</div>
<?php

$id = "";
if (isset($_POST['topic_id'])){
	$id = '&id='.intval($_POST['topic_id']);
} 
?>
<meta http-equiv="refresh" content="3;?act=tasks<?php echo $id?>">