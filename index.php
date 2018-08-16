<?php
session_start();

// конфиг
$host = "shemplo.ru";
$user = "visitor";
$db = "db.pluses";
$dbpass = "wru4";
// основные функции
// функции сокращения
function quote($str)
{
	global $mysqli_link;	
	return "'".mysqli_real_escape_string($mysqli_link, $str)."'";	
}
function filterString($str)
{
	return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');	
}
function dbQuery($query)
{
	global $mysqli_link;
	return mysqli_query($mysqli_link, $query);	
}
function dbFetch($res)
{
	return mysqli_fetch_assoc($res);	
}
function dbFetchAll($res)
{
	$data = array();
	
	while($row = mysqli_fetch_assoc($res)){
		$data[] = $row;
	}

	return $data;
	//return  mysqli_fetch_all($res, MYSQLI_ASSOC);// Новый способ
}
function dbSelect($select)
{
	$res = dbQuery($select);
	return dbFetchAll($res);
}
function dbSelectRow($select)
{
	$res = dbQuery($select);
	return dbFetch($res);
}
// конец функций сокращений

// функции авторизации
function isLogin()
{
	return isset($_SESSION['_user_login']);
	// Админы могут быть разные
	//&& $_SESSION['_user_login']==1;
}
function makeLogin()
{
	global $mysqli_link;

	$select = "SELECT id, hpass FROM teachers WHERE phone = ".quote($_POST['login_user']);

	$row = dbSelectRow($select);
		
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
	if (!isLogin() && isset($_POST['login_hidden'])){
		if ($id = makeLogin()){
			// проверка пароля бла бла бла
			$_SESSION['_user_login'] = $id;

			header("Location: ".getScriptName());
		}else{
			unset($_SESSION['_user_login']);
			// облом с сообщением
			$_SESSION['flash_message'] = 'error_login';
			header("Location: ".getScriptName());
		}
	}	
	else header("Location: ".getScriptName());
}
function Logout()
{
	unset($_SESSION['_user_login']);
	header("Location: ".getScriptName());
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
	
	$result = @socket_connect($socket, $address, $port);
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
	if (isset($_GET['act']) && $_GET['act']=='test')print_r($out_m);
	try{
		$obj = json_decode($out_m, true);

		if (isset($obj['list']) && is_array($obj['list']))
			return $obj['list'];
		if (isset($obj['kind']) && $obj['kind']=='ID')
			return $obj['code'];// Для создания
		if (isset($obj['kind']) && $obj['kind']=='INFO')
			return $obj['code']; // 0 - если Pong
	}catch(string $e){
		echo $e;
	}
	return false;
}
// Группы
function getGroups()
{
	return dbSelect("SELECT id, title, comment FROM groups");
}
function getGroupById($id)
{
	$id = intval($id);
	
	return dbSelectRow("SELECT id, title, comment FROM groups WHERE id = $id");
}
function renameGroupById($id, $title)
{
	$id = intval($id);
	$title = filterString($title);
		
	$select = "UPDATE groups SET title = ".quote($title)." WHERE id = $id";	// временно
	dbQuery($select);
}
// Темы
function getTopics()
{
	return dbSelect("SELECT id, title, comment FROM topics");	
}
function getTopicById($id)
{
	$id = intval($id);
		
	return dbSelectRow("SELECT id, title, comment FROM topics WHERE id = $id");
}
function getTopicsByGroupId($id)
{
	// выбрать студентов определенной группы
	$command = "select topics -id ".intval($id);
	$topics_ids = sendMessageServer($command);
	// получить содержимое id
	$arr_id = array();
	if ($topics_ids){
		foreach($topics_ids as $topic_pair){
			if ($topic_pair['pair'][1]==0)
				$arr_id[] = $topic_pair['pair'][0];
		}
	}
	if (count($arr_id) > 0){
		
		return dbSelect("SELECT id, title, comment FROM topics WHERE id IN(".implode(",",$arr_id).")");
	}
	return array();
}
function renameTopicById($id, $title, $comment)
{
	$id = intval($id);
	$title = filterString($title);
	$comment = filterString($comment);
	
	$update = "UPDATE topics SET title = ".quote($title).", comment=". quote($comment).
		" WHERE id = $id";	// временно
	dbQuery($update);
}
// Студенты
function getStudentsByGroupId($id)
{
	// выбрать студентов определенной группы
	$command = "select students -id ".intval($id);
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

		return dbSelect($select);
	}
	return array();
}
function getStudents()
{
	// Запрос для основной базы
	$select = "SELECT id, `name.first` as name_first, `name.last` as name_last FROM students";

	return dbSelect($select);
}
function getStudentById($id)
{
	$id = intval($id);
	
	$select = "SELECT id, `name.first` as name_first, `name.last` as name_last FROM students WHERE id=$id";
	
	return dbSelectRow($select);
}
function getStudentGroup($id)
{
	$command = "select groups -id ".intval($id);
	return sendMessageServer($command);
}
// Задания
function getTasksByTopicId($id)
{
	$command ="select tasks -topic ".intval($id);
	$result = sendMessageServer($command);
	if ($result){
		$arr = array();
		foreach($result as $task){
			$arr[]=array('id'=>$task['pair'][0], 'title'=>$task['pair'][1]);			
		}
		return $arr;
	}
	return array();
}
// Администраторы
function getTeachers()
{
	return  dbSelect("SELECT id, first_name, second_name, last_name, rights FROM teachers");// Новый способ
}
// Добавление ..
function addGroup($name, $comment)
{
	$name = filterString($name);
	$comment = filterString($comment);
	// вызвать добавление группы
	$command = "create group -title $name -comment $comment -headteacher 1"; // headteacher = 1 из админки

	sendMessageServer($command);
}
// Добавление студента
function addStudent($src, $prefix)
{
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		
		$student_name = filterString($src['student_name']);
		$student_last = filterString($src['student_last']);
		
		$command = 'create student -name.first '.$student_name.' -name.last '.$student_last;// добавить ид учителя

		$insert_id = sendMessageServer($command);
		if (intval($insert_id)>0 && intval($src['student_group'])>0){
			$id = intval($insert_id);
			$to = intval($src['student_group']);
			$command = "insert student -student $id -group $to";
			sendMessageServer($command);
		}
	}
}
function addTopic($title, $comment, $group = 0)
{
	$title = filterString($title);
	$command = 'create topic -title '.$title;
	if (strlen($comment)>0){
		$comment = filterString($comment);
		$command.=' -comment '.$comment;
	}
	$insert_id = sendMessageServer($command);
	if (intval($group)>0 && intval($insert_id)>0){
		$command = "insert topic -topic $insert_id -group $group";
		
		sendMessageServer($command);
	}
}
function addTask($src, $prefix)
{
	if (isset($src['task_hidden']) && $prefix =='prefix:task'){
		
		$topic_id = intval($src['topic_id']);
		$title = filterString($src['task_title']);
		$command = "create task -title $title -topic $topic_id";
		$result = sendMessageServer($command);
		
		if ($result===0)
			return 'complete';
	}
	return 'error';
}
// Редактирование студента
function editStudent($src, $prefix)
{
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		
		$id = intval($src['student_id']);
		
		$name = filterString($src['student_name']);
		$lastname = filterString($src['student_last']);		
		
		$update = "UPDATE students SET `name.first`=".quote($name).
			", `name.last`=".quote($lastname).
			" WHERE id = $id";

		dbQuery($update);
	}
}
function editTask($src, $prefix)
{
	// вызвать добавление группы
	$id = intval($src['task_id']);
	$title = filterString($src['task_title']);
	$topic_id = intval($src['topic_id']);
	$command = "update task -title $title -id $id -topic $topic_id"; // headteacher = 1 из админки

	sendMessageServer($command);
	
}

// Перемещение студента в другую группу
function moveStudent($src, $prefix)
{
	if (isset($src['student_hidden']) && $prefix =='prefix:student'){
		// нужна атомарная операция на серваке
		if (isset($src['student_group']))
		{
			$id = intval($src['student_id']);
			$from = intval($src['student_group']);
			$to = intval($src['student_group_new']);
			$command = "move student -id $id -from $from -to $to";
			sendMessageServer($command);
		} else {
			$id = intval($src['student_id']);
			$to = intval($src['student_group_new']);
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
	if (isLogin()){
		global $cur_action;
		
		include 'inc/admin.php'; // обработка роута	
	}else{		
		include 'inc/login.php';
	}
}
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}
function echoPage()
{
	// проверка на аякс может быть здесь
	include 'inc/template.php';
}
function echoTopMenu()
{
	include 'inc/top_menu.php';
}
function echoServerStatus()
{
	include 'inc/status.php';
}
// конец функций главной страницы

// Инициализировать подключение к базе данных
$mysqli_link = mysqli_connect($host, $user, $dbpass, $db);
// Запрос на установку кодировки
dbQuery("SET NAMES utf8");

// Обработка действий админа
$cur_action = isset($_GET['act'])? $_GET['act']: false;
// действия до вывода шаблона
if ($cur_action == 'logout'){
	Logout();
}elseif ($cur_action == 'login'){
	Login();
}else
// Вывод страницы в браузер
	echoPage(); // с шаблоном
