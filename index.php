<?php
session_start();

// конфиг
$host = "shemplo.ru";
$user = "visitor";
$db = "db.pluses";
$dbpass = "wru4";
// основные функции

// функции авторизации	
function isLogin()
{
	return isset($_SESSION['_user_login']) && $_SESSION['_user_login']==1;		
}
function MakeLogin()
{	
	global $mysqli_link;
	
	$select = "SELECT id, hpass FROM teachers WHERE phone = '".
	mysqli_real_escape_string($mysqli_link, $_POST['login_user'])."'";
		
	$res = mysqli_query($mysqli_link, $select);
	$row = mysqli_fetch_assoc($res);
	if (isset($row['id'])) {
		$hpass = explode(':', $row['hpass']);
		if (md5($_POST['password_user'].$hpass[1])==$hpass[0])
			return intval($row['id']);				
	}
	return false;
}
function getScriptName()
{
	
	if (strstr($_SERVER['SCRIPT_NAME'],"index.php")){		
		return "./";
	} 
	return $_SERVER['SCRIPT_NAME'];
}
function Login()
{
	if ($id = MakeLogin()){
		// проверка пароля бла бла бла
		$_SESSION['_user_login'] = $id;
			
		header("Location: ".getScriptName());
	}else{
		// облом с сообщением
		$_SESSION['flash_message'] = 'error_login';
		header("Location: ".getScriptName());
	}		
}
function Logout()
{
	unset($_SESSION['_user_login']);
	header("Location: ".getScriptName());
}
function isLogout()
{
	return isset($_GET['act']) && $_GET['act']=='logout';
}
// конец авторизации
// начало функций ядра

function lengthMsg($msg)
{
	$len = strlen($msg);
	$out = '';
	for ($i=0; $i<4; $i++){
		$mask = (($len>>($i*8)) & 0xff);
		$out = chr($mask).$out;
	}
	return $out.$msg;
}
function sendMessageServer($msg)
{		
	$address = 'shemplo.ru'; // //Адрес работы сервера
	$port = 2000; //Порт работы сервера (лучше какой-нибудь редкоиспользуемый)
	$msg = '{content:"'.$msg.'"}';	// обернуть сообщение
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
		return false;
	}
	
	$result = socket_connect($socket, $address, $port);
	if ($result === false) {
		return false;
	} 
	$msg = lengthMsg($msg);		
	socket_write($socket, $msg, strlen($msg));
			
	$out = socket_read($socket,4); //Читаем сообщение от сервера
	$len = (ord($out[0])<<24)+(ord($out[1])<<16)+(ord($out[2])<<8)+ord($out[3]);
	
	$out_m = socket_read($socket, $len);	
	
	//Останавливаем работу с сокетом
	if (isset($socket)) {
		socket_close($socket);			
	}	
	try{
		$obj = json_decode($out_m, true);

		if (isset($obj['list']) && is_array($obj['list'])) 
			return $obj['list'];
	}catch(string $e){
		echo $e;			
	}		
	return false;
}

function getGroups()
{
	global $mysqli_link;
	
	$_groups = array();
	
	$select = "SELECT id, title, comment FROM groups";	// временно
			
	$res = mysqli_query($mysqli_link, $select);
	while($row = mysqli_fetch_assoc($res)){
		$_groups[] = $row;			
	}
		
	return $_groups;
		
}
function getGroupById($id)
{	
	global $mysqli_link;	
	
	$select = "SELECT id, title, comment FROM groups WHERE id = ".intval($id);	// временно
			
	$res = mysqli_query($mysqli_link, $select);
	$row = mysqli_fetch_assoc($res);
	if ($row){
		return $row;
	}
		
	return false;
}
function renameGroupById($id, $new_group_name)
{
	global $mysqli_link;
	
	$select = "UPDATE groups SET title = '". 
		mysqli_real_escape_string($mysqli_link, $new_group_name).
		"' WHERE id = ".intval($id);	// временно
	mysqli_query($mysqli_link, $select);		
}
function getTopics()
{
	global $mysqli_link;
	$_topics = array();
		
	$select = "SELECT id, title, comment FROM topics";	// временно
			
	$res = mysqli_query($mysqli_link, $select);
	while($row = mysqli_fetch_assoc($res)){
		$_topics[] = $row;			
	}
		
	return $_topics;
}
function getTopicById($id)
{		
	global $mysqli_link;
	$select = "SELECT id, title, comment FROM topics WHERE id = ".intval($id);	// временно
			
	$res = mysqli_query($mysqli_link, $select);
	$row = mysqli_fetch_assoc($res);
	if ($row){						
		return $row;
	}
		
	return false;
}
function renameTopicById($id, $new_topic_title, $new_topic_comment)
{
	global $mysqli_link;
	$select = "UPDATE topics SET title = '". 
		mysqli_real_escape_string($mysqli_link, $new_topic_title).
		"', comment='". 
		mysqli_real_escape_string($mysqli_link, $new_topic_comment).
		"' WHERE id = ".intval($id);	// временно
	mysqli_query($mysqli_link, $select);	
}
function getStudentsByGroupId($id)
{
	global $mysqli_link;
	// выбрать студентов определенной группы
	$command = "select students -id $id";
	$student_ids = sendMessageServer($command);
	// получить содержимое id
	$arr_id = array();
	if ($student_ids){
		foreach($student_ids as $student_pair){
			if ($student_pair['pair'][1]==0)
				$arr_id[] = $student_pair['pair'][0];
		}
	}
	if (count($arr_id) > 0){
		// Для базы плюсов			
		$select = "SELECT id, `name.first` as name_first, `name.last` as name_last FROM students WHERE id IN(".implode(",",$arr_id).")";
		
		$res = mysqli_query($mysqli_link, $select);
			
		while ($row = mysqli_fetch_assoc($res)){	
			$students[] = $row;
		}
		return $students;
	}
	return array();
}
function getStudents()
{		
	global $mysqli_link;
	
	$students = array();
		
	// Запрос для основной базы
	$select = "SELECT id, `name.first` as name_first, `name.last` as name_last FROM students";		
	
	$res = mysqli_query($mysqli_link, $select);
		
	while ($row = mysqli_fetch_assoc($res)){	
		$students[] = $row;
	}
		
	return $students;
}
function getStudentById($id)
{
	global $mysqli_link;
	
	$select = "SELECT id, `name.first` as name_first, `name.last` as name_last FROM students WHERE id=".intval($id);
	$res = mysqli_query($mysqli_link, $select);
		
	if ($row = mysqli_fetch_assoc($res)){
		return $row;
	}
	return false;
}
function getStudentGroup($id)
{
	$command = "select groups -id ".intval($id);
	return sendMessageServer($command);		
}
function addGroup($name, $comment)
{
	// вызвать добавление группы
	$command = "create group -title $name -comment $comment -headteacher 1"; // headteacher = 1 из админки
		
	sendMessageServer($command);
}
function addTopic($title, $comment)
{
	$command = 'create topic '.$title;
		
	sendMessageServer($msg);
}
// Редактирование студента
function editStudent($src, $prefix)
{
	global $mysqli_link;
	
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		$update = "UPDATE students SET `name.first`='".
			mysqli_real_escape_string($mysqli_link, $src['student_name']).
			"', `name.last`='".
			mysqli_real_escape_string($mysqli_link, $src['student_last']).
			"' WHERE id = ".intval($src['student_id']);
		
		$res = mysqli_query($mysqli_link, $update);	
	}		
}
// Добавление студента
function addStudent($src, $prefix)
{
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		$command = 'create student -name.first '.$src['student_name'];// добавить ид учителя
		sendMessageServer($command);				
	}			
}
// Перемещение студента в другую группу
function moveStudent($src, $prefix)
{
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		// нужна атомарная операция на серваке
		if (isset($src['student_group']))
		{
			$id = $src['student_id'];
			$from = $src['student_group'];
			$to = $src['student_group_new'];
			$command = "move student -id $id -from $from -to $to";
			sendMessageServer($command);
		} else {
			$id = $src['student_id'];
			$to = $src['student_group_new'];
			$command = "insert student -student $id -group $to";
			sendMessageServer($command);
		}
		return "complete";
	}
	
	return "normal";		
}
// конец функций ядра
// начало функций главной страницы
function render()
{
	if (!isLogin()){
		include 'inc/login.php';
	}else{
		include 'inc/admin.php';
	}
}
function isLoginPost()
{
	if (isset($_POST['port']) && $_POST['port']=='login_port') 
		return true;
	return false;		
}
function echoPage()
{
	include 'inc/template.php';			
}
function echoTopMenu()
{
	include 'inc/top_menu.php';		
}
// конец функций главной страницы

// Инициализировать подключение к базе данных

$mysqli_link = mysqli_connect($host, $user, $dbpass, $db);
/* Запрос на установку кодировки */
mysqli_query($mysqli_link, "SET NAMES utf8");

if (!isLogin() && isLoginPost()){
	Login();	
}elseif (isLogout()){
	Logout();
}
// Вывод страницы в браузер
echoPage();
