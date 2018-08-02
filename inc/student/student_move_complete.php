<!--<div>Студент перемещен успешно. Подождите 3 секунды для перехода</div>-->
<div>Студент перемещен в другую группу успешно. Подождите 3 секунды для перехода</div>
<?php 
$get ="";
if (isset($_GET['gid'])):
	$gid = intval($_GET['gid']);
	$get = "&gid=$gid";
endif;
?>
<meta http-equiv="refresh" content="3;?act=students<?php echo $get;?>">