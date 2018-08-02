<?php
echoTopMenu();
$topic = getTopicById($_GET['id']);
?>
<h3>Переименовать тему "<?php echo $topic['title'];?>"</h3>

<form action="?act=rename&target=topic&id=<?php echo $topic['id']?>" method="post">
<table>
<tr>
<td>Название:</td><td><input name="topic_title" value="<?php echo $topic['title'];?>"></td>
</tr><tr>
<td>Комментарий:</td><td><input name="topic_comment" value="<?php echo $topic['comment'];?>"></td>
</tr>
</table>
<input type="submit" value="Обновить">
</form>