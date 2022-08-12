<?php

define('DB_HOST', '{{DB_HOST}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_NAME', '{{DB_NAME}}');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$ITEMS = array();

$ip_server = $_SERVER['SERVER_ADDR'];

function get(&$var, $default=null) {
    return isset($var) ? $var : $default;
}

switch(get($_GET['action'])) {
	case 'new':
		$title = get($_GET['title']);
		if (strlen(trim($title)) > 0 ) {
			$stmt = $db->prepare('INSERT INTO todo VALUES(NULL, ?, FALSE)');
			if(!$stmt->execute(array($title))) {
					die(print_r($stmt->errorInfo(), true));
			}
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
	case 'delete':
			$id = get($_GET['id']);
			if(is_numeric($id)) {
				$stmt = $db->prepare('DELETE FROM todo WHERE id = ?');
				if(!$stmt->execute(array($id))) {
					die(print_r($stmt->errorInfo(), true));
				}
			}
			header("Location: ".$_SERVER['SCRIPT_NAME']);
			die();
	case 'clear':
			$sql = 'DELETE from todo';
			$db->exec($sql);
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
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PHP TODO LIST App</title>
	<link rel="stylesheet" href="/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
	<style>
		:root {
			--primary: #EA882E;
			--secondary: #FFA34D;
			--support: #FFB066;
		}
		* {
			box-sizing: border-box;
		}
		body, html {
			font-family: 'Ubuntu';
			margin: 0px;
			background-color: #F4F4F4;
		}
		.wrapper {
			max-width: 768px;
			background-color: #FFFFFF;
			margin: 4rem auto;
			padding: 2rem;
			box-shadow: 10px 10px 18px -10px rgba(0,0,0,0.18);
			border-radius: 5px 5px;
		}
		div.header {
			margin-bottom: 2rem;
		}
		h1 {
			color: var(--primary);
			margin-bottom: 0;
		}
		p {
			margin: .5rem 0;
		}
		div {
			margin-left: 30px;		
			margin-top: 15px;
		}
		div input {
			/* height: 28px;
			font-size: 1.2em; */
		}
		div button {
			border-radius: .25rem;
			border: 1px solid var(--secondary);
			cursor: pointer;
			padding: .5rem 1.5rem;
			/* height: 28px;*/
			/* font-size: 1.2em; */
		}
		div button.primary {
			background-color: var(--support);
			color: #FFFFFF;
		}
		div button.outline {
			background-color: transparent;
			color: var(--secondary);
		}
		div button:hover {
			background-color: var(--secondary);
			color: #FFFFFF;
		}
		div ul {
			margin: 0px;
			padding: 0px;			
			/* max-width: 500px; */
		}
		
		li a {
			/* font-size: 1.25em; */
			padding: .5rem;
			display: block;
		}
		li:hover {
			background-color: #EEE;
		}
		li {
			display: block;
			margin-bottom: .5rem;
		}
		li.checked span {
			color: #C4C4C4;
			text-decoration: line-through;
		}
		li.checked i:before {
			color: var(--support);
			font-family: 'FontAwesome';
			content: '\f00c';
			padding:0 6px 0 0;
			font-style: normal;
		}
		li.unchecked i:before {
			color: var(--support);
			font-family: 'FontAwesome';
			content: '\f24a';
			padding:0 6px 0 0;
			font-style: normal;
		}
		li a {
			text-decoration: none;
			color:inherit;
		}
		ul li{list-style-type:none;font-size:1em;}

		.delete-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f05e';
			padding:0 6px 0 0;
			font-style: normal;
		}

		.add-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f067';
			padding:0 6px 0 0;
			font-style: normal;
		}
		.clear-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f1f8';
			padding:0 6px 0 0;
			font-style: normal;
		}		
		div#new-task {
			display: flex;
			flex-flow: row nowrap;
			gap: 1rem;
			align-items: center;
		}

		span.task-input {
			border-bottom: 1px solid var(--secondary);
			width: 100%;
		}

		input {
			padding: 1rem .5rem;
			width: 100%;
			border: none;
		}

		input:focus{
			outline: var(--primary);
			background-color: #F6F6F6;
		}

		div.footer {
			text-align: center;
		}

		@media screen and (min-width: 768px) {
			span.task-input {
				flex: 1;
			}
		}

	</style>
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<h1>PHP TODO APP</h1>
			<p>Simple PHP todo list application example</p>
		</div>
		<div id="new-task">
			<span class="task-input">
				<input id="task-title" name="title" type="text" placeholder="Task Title">
			</span>
			<span class="task-action">
				<button id='new-task-button' class="primary add-button">Add</button>
			</span>
		</div>
		<div id="task-list">
			<ul>
				<?php foreach($ITEMS as $ITEM): ?>
				<li class=<?php if($ITEM['done']): ?>"checked"<?php else: ?>"unchecked"<?php endif;?>>
					<button class="outline delete-button" style="float: right;" data-href="?action=delete&id=<?=$ITEM['id']?>">Delete</button>
					<a href="?action=toggle&id=<?=$ITEM['id']?>">
						<i></i>
						<span><?=htmlspecialchars($ITEM['title'])?></span>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php if(empty($ITEMS)): ?>
				<p style="margin: 2rem 0;">You don't have any tasks.</p>
			<?php endif; ?>
		</div>
		
		<div style="text-align: right;">
			<small>Running on <?php echo $ip_server; ?></small>
		</div>
	</div>
	<div class="footer">
		<button id='clear-all-button' class="outline clear-button">Clear All</button>
	</div>
	<script>
		
		document.getElementById('new-task-button').onclick = function(){
			window.location.href = '?action=new&title=' + encodeURI(document.getElementById('task-title').value);		
		};

		document.getElementById('clear-all-button').onclick = function(){
			window.location.href = '?action=clear';
		};

		const deleteButtons = document.querySelectorAll(".delete-button");
		deleteButtons.forEach( button => {
			button.onclick = function(){
				var button_href = this.getAttribute('data-href');
				window.location.href = button_href;
			}
		});

		// console.log(deleteButtons);

		// document.getElementById('delete-button').onclick = function(){
		// 	console.log(this.getAttribute('data-href'));
		// 	// window.location.href = '?action=clear';
		// };
	</script>
</body>
</html>