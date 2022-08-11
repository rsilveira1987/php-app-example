<?php
define('DB_HOST', '{{DB_HOST}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_NAME', '{{DB_NAME}}');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$ITEMS = array();

function get(&$var, $default=null) {
    return isset($var) ? $var : $default;
}

switch(get($_GET['action'])) {
case 'new':
	$title = get($_GET['title']);
	$stmt = $db->prepare('INSERT INTO todo VALUES(NULL, ?, FALSE)');
	if(!$stmt->execute(array($title))) {
			die(print_r($stmt->errorInfo(), true));
	}
	header("Location: ".$_SERVER['SCRIPT_NAME']);
	die();
case 'toggle':
	$id = get($_GET['id']);
	if(is_numeric($id)) {
		$stmt = $db->prepare('UPDATE todo SET done = !done WHERE id = ?');
		if(!$stmt->execute(array($id))) {
			die(print_r($stmt->errorInfo(), true));
		}
	}
	header("Location: ".$_SERVER['SCRIPT_NAME']);
	die();
default:
	break;
}
	$stmt = $db->prepare('SELECT * from todo');
	if ($stmt->execute()) {
		$ITEMS = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
?>
<html>
<head>
	<title> Sample TODO App </title>
	<style>
		div, body, html {
			margin: 0px;
			background-color: #eee;
		}
		h1 {
			padding: 30px;
		}
		div {
			margin-left: 30px;		
			margin-top: 15px;
		}
		div input {
			height: 28px;
			font-size: 1.2em;
		}
		div button {
			height: 28px;
			font-size: 1.2em;
		}
		div ul {
			margin: 0px;
			padding: 0px;			
			border: 1px solid #333;
			max-width: 500px;
			background-color: #ffe;
			-webkit-box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
			-moz-box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
			box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
			border-radius: 5px 5px;
		}
		li a {
			font-size: 1.25em;
			display: block;
		}
		li:hover {
			background-color: #fff;
			-webkit-box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
			-moz-box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
			box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
		}
		li {
			display: block;
		}
		li.checked span {
			text-decoration: line-through;
		}
		li.checked i:before {
			color:green;
			content: '\2713';
			padding:0 6px 0 0;
		}
		li.unchecked i:before {
			content: '\2713';
			color:transparent;
			padding:0 6px 0 0;
		}
		li a {
			text-decoration: none;
			color:inherit;
		}
		ul li{list-style-type:none;font-size:1em;}

	</style>
</head>
<body>
	<h1>Sample TODO</h1>
	<div id="new-task">
		<input id="task-title" name="title" type="text" placeholder="Task Title"><button id='new-task-button'>Add</button>
	</div>
	<div id="task-list">
		<ul>
			<?php foreach($ITEMS as $ITEM): ?>
			<li class=<?php if($ITEM['done']): ?>"checked"<?php else: ?>"unchecked"<?php endif;?>>
				<a href="?action=toggle&id=<?=$ITEM['id']?>">
				<i></i><span>
				<?=htmlspecialchars($ITEM['title'])?></span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<script>
		document.getElementById('new-task-button').onclick = function(){
			window.location.href = '?action=new&title=' + encodeURI(document.getElementById('task-title').value);		
		};
	</script>
</body>
</html>