<?php
function getServerStatusHelper(){// помощник

	if (isset($_SESSION['last_status'])){
		$result = $_SESSION['last_status']; 
		if ($result['timestamp']<time()-60*5){
			unset($_SESSION['last_status']);
		}
		$result = $result['status'];	
	}else{
		// получить состояние соединения с сервером
		$result = sendMessageServer('ping');
		// кэшировать состояние
		$_SESSION['last_status'] = array('status'=>$result, 'timestamp'=>time());
	}
	if ($result===0){
		return 'доступен';		
	} 
	
	return '<span class="disconnected">недоступен</span>';	
}
?>
<span class="server_status">Статус сервера: <?php echo getServerStatusHelper();?></span>