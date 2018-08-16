 <?php
$standart = false;
// Обработка действий админа
switch($cur_action){
				
	case 'groups':
		include 'groups.php';
		break;
		
	case 'students':
		include 'students.php';
		break;
		
	case 'topics':
		include 'topics.php';
		break;
		
	case 'tasks':
		include 'tasks.php';
		break;
		
	case 'users':
		include 'users.php';
		break;
		
	case 'rename':
		if (!isset($_GET['target']))
			break;	
		
		if ($_GET['target']=='group'){
			// Обработка пост
			if (isset($_POST['group_name'])){
				renameGroupById($_GET['id'],$_POST['group_name']);
				include 'group_rename_complete.php';
			}else{
				include 'group_rename.php';
			}			
		}elseif ($_GET['target']=='topic'){
			// Обработка пост
			if (isset($_POST['topic_title'])){
				renameTopicById($_GET['id'],$_POST['topic_title'],$_POST['topic_comment']);
				include 'topic_rename_complete.php';
			}else{
				include 'topic_rename.php';
			}			
		}
		break;
			
	case 'add':
		if (!isset($_GET['target']))
			break;
		
		if ($_GET['target']=='group'){
			// Обработка пост
			if (isset($_POST['group_name'])){
				addGroup($_POST['group_name'], $_POST['group_comment']);
				include 'group_add_complete.php';
			}else{
				$standart = true;						
			}					
		}elseif ($_GET['target']=='topic'){
			// Обработка пост
			if (isset($_POST['topic_hidden'])){
				addTopic($_POST['topic_title'], $_POST['topic_comment'], $_POST['topic_group']);
				include 'topic_add_complete.php';
			}else{
				$standart = true;							
			}					
		}elseif ($_GET['target']=='student'){
			// Обработка пост
			if (isset($_POST['student_hidden'])){
				addStudent($_POST, 'prefix:student');// префикс для наглядности и следующих фич
				include 'student/student_add_complete.php';
			}else{
				$standart = true;							
			}					
		}elseif ($_GET['target']=='task'){
			// Обработка пост
			$result = addTask($_POST, 'prefix:task');
			
			if ($result=='complete'){					
				include 'task_add_complete.php';
			}					
		}						
		break;
		
	case 'edit':
		if (!isset($_GET['target']))
			break;
		if ($_GET['target']=='student'){
			// Обработка пост
			if (isset($_POST['student_hidden'])){
				editStudent($_POST, 'prefix:student');
				include 'student/student_edit_complete.php';
			}else{
				include 'student/student_edit.php';					
			}					
		}elseif ($_GET['target']=='task'){
			// на случай если яваскрипт отключен либо аякс выключен
				include 'task_edit.php';
		}
		break;
		
	case 'move':
		if (!isset($_GET['target']))
			break;
		if ($_GET['target']=='student'){
			// Обработка пост
			// Можно потом реализовать перемещение пачкой, когда галочкой отметят студентов
			$result = moveStudent($_POST, 'prefix:student');
			if ($result=='complete'){
				include 'student/student_move_complete.php';
			}else{
				include 'student/student_move.php';
			}						
		}
		break;
	case 'test':
		include 'test.php';// всякие тестовые штуки
		break;
	default:
		$standart = true; // стандартная обработка
		break;
};	

if ($standart){
	echoTopMenu();	
	echoServerStatus();	
	
	include 'news.php';
}
