<?php
echoTopMenu();

$group = getGroupById($_GET['id']);
?>
<h3>Переименовать группу</h3>
Текущее название:<b><?php echo $group['title'];?></b>
<br><br>
<form action="?act=rename&target=group&id=<?php echo $group['id']?>" method="post">
Новое название:<input name="group_name" value="<?php echo $group['title'];?>">
<input type="submit" value="Переименовать">
</form>