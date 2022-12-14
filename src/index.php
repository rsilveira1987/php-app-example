<?php

define('DB_HOST', '{{DB_HOST}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_NAME', '{{DB_NAME}}');

try {
	$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
} catch(PDOException $e) {
	$message = $e->getMessage();
	print "<pre>$message</pre>";
	die();
}

$ITEMS = array();

$ip_server = $_SERVER['SERVER_ADDR'];

// Progress information
$stmt = $db->prepare('SELECT round(SUM(done) * 100/(SELECT count(*) FROM todo),2) AS progress  FROM todo');
$ret = $stmt->execute($sql);
$progressInfo = $stmt->fetch()['progress'] ?? '0.00';

// Tasks information
$stmt = $db->prepare('SELECT count(*) as done_tasks FROM todo WHERE done = 1');
$ret = $stmt->execute($sql);
$doneTasksInfo = $stmt->fetch()['done_tasks'] ?? 0;

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
			--primary80: #EEA058;
			--primary60: #F2B882;
			--primary40: #F7CFAB;
			--primary20: #FBE7D5;

			--black: #808080;
			--black80: #999999;
			--black60: #B3B3B3;
			--black40: #CCCCCC;
			--black20: #E6E6E6;
			--black10: #F3F3F3;

			--secondary: #FFA34D;
			--support: #FFB066;

			--gradient: linear-gradient( to right, var(--primary40), var(--primary80));
		}
		* { box-sizing: border-box; }
		body, html {
			font: 0.9em/1.2 'Ubuntu';
			margin: 0px;
			background-color: var(--black10);
		}
		header {
			padding: 1rem;
			background-color: var(--primary60);
			/* background-image: var(--gradient); */
			color: #FFFFFF;
			font-weight: bold;
			display: flex;
			flex-flow: row nowrap;
			justify-content: space-between;
		}
		.wrapper {
			max-width: 768px;
			background-color: #FFFFFF;
			margin-bottom: 2rem;
			padding: 2rem 1rem;
			box-shadow: 10px 10px 18px -10px rgba(0,0,0,0.18);
			border-radius: 5px 5px;
		}
		.container {
			max-width: 768px;
			margin: 0 1rem;
		}
		div.header {
			margin-bottom: 2rem;
		}
		h1 {
			color: var(--primary60);
			margin: 0;
		}
		p {
			color: var(--black);
			margin: .5rem 0;
		}
		button {
			/* position: relative;
            overflow: hidden; */
			border-radius: .25rem;
			outline: 0;
            border: 0;
			padding: .75rem 1.5rem;
			color: #fff;
			font-weight: bold;
            cursor: pointer;
		}
		button.btn-primary {
			background-color: var(--primary60);
			color: #FFFFFF;
			font-weight: bold;
		}
		button.btn-secondary {
			background-color: transparent;
			color: var(--secondary);
		}
		button.btn-outline {
			border: 1px solid var(--secondary);
		}
		button:hover {
			background-color: var(--secondary);
			color: #FFFFFF;
		}
		button:active {
			transform: translate(1px,1px);
		}
		span.ripple {
            position: absolute; /* The absolute position we mentioned earlier */
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 600ms linear;
            background-color: rgba(255, 255, 255, 0.9);
        }
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
		div ul {
			margin: 0px;
			padding: 0px;			
			color: var(--black);
		}
		
		li a {
			padding: .5rem;
			display: block;
		}
		li:hover {
			background-color: var(--black20);
		}
		li {
			display: flex;
			flex-flow: row nowrap;
			margin-bottom: 1rem;
			background-color: #FFFFFF;
			border-radius: 5px 5px;
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
			flex: 1;
			display: block;
			text-decoration: none;
			color:inherit;
			padding: 1rem;
		}
		ul li{list-style-type:none;font-size:1em;}
		.delete-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f1f8';
			/* padding:0 6px 0 0; */
			font-style: normal;
		}

		.add-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f067';
			padding:0 6px 0 0;
			font-style: normal;
		}
		.clear-button {
			margin: 2rem 0;
			width: 100%;
		}
		.clear-button:before {
			color: inherit;
			font-family: 'FontAwesome';
			content: '\f1f8';
			padding:0 6px 0 0;
			font-style: normal;
		}		
		input {
			padding: 1rem .5rem;
			width: 100%;
			border: none;
			color: var(--black);
			font-size: 1em;
		}

		input:focus{
			outline: var(--primary); 
			background-color: var(--black10);
		}

		div.footer {
			text-align: center;
		}

		span.task-input {
			border-bottom: 1px solid var(--primary60);
			width: 100%;
		}

		span.task-action,
		span.task-action button {
			width: 100%;
		}

		div#new-task {
			display: flex;
			flex-flow: column nowrap;
			gap: 2rem;
			align-items: center;
		}
		div#task-list {
			padding-bottom: 8rem;
		}
		.footer {
			position: fixed;
			bottom: 0;
			width: 100%;
			padding: 0 1rem;
			background-color: var(--black10);
			border-top: 1px solid var(--black20);
		}
		
		div.progress-bar {
			background-color: var(--black20);
			width: 100%;
			height: 5px;
			/* margin-bottom: 2rem; */
		}
		div.progress-bar-inner {
			height: 100%;
			background: var(--gradient);
		}
		div.progress {
			display: flex;
			flex-flow: row nowrap;
			align-items: center;
			gap: 16px;
			line-height: 100%;
			color: var(--black);
			margin-bottom: 2rem;
		}
		div.progress-bar {
			flex: 1;
		}
		div.progress-info::after {
			content: '%';
			display: inline-block;
			color: inherit;
		}
		@media screen and (min-width: 768px) {
			body, html {
				font: 1.1em/1.2 'Ubuntu';
			}
			.wrapper {
				margin: 2rem auto;
				padding: 2rem;
			}
			.container {
				margin: 0 auto;
			}
			.clear-button {
				width: auto;
				margin: 1rem 0;
			}
			span.task-input {
				flex: 1;
			}
			span.task-action,
			span.task-action button {
				width: auto;
			}
			div#new-task {
				display: flex;
				flex-flow: row nowrap;
				gap: 1rem;
				align-items: center;
			}
			div#task-list {
				padding: 0;
			}
			.footer {
				position: initial;
				padding: 0 1rem;
				background-color: var(--black10);
				border: none;
				padding: 0;
			}

		}

	</style>
</head>
<body>
	<header>
		<span>TODO LIST APP</span>
		<small>Running on <?php echo $ip_server; ?></small>
	</header>
	
	<div class="wrapper">
		<div class="header">
			<h1>What do you need to do?</h1>
		</div>
		<form action="" method="GET">
			<div id="new-task">
				<span class="task-input">
					<input id="task-title" name="title" type="text" placeholder="Task Title">
					<input name="action" value="new" type="hidden">
				</span>
				<span class="task-action">
					<button id='new-task-button' class="btn-primary add-button">Add</button>
				</span>
			</div>			
		</form>	
	</div>

	<?php if(empty($ITEMS)): ?>
		<p style="text-align: center;">You have no tasks in your list.</p>
	<?php else: ?>
		
		<div class="container">
			<p>Your progress (<?= $doneTasksInfo . "/" . count($ITEMS) ?>)</p>
			<div class="progress">
				<div class="progress-bar">
					<div class="progress-bar-inner" style="width:<?= $progressInfo ?>%;"></div>
				</div>
				<div class="progress-info">
					<?= $progressInfo ?>
				</div>
			</div>
		</div>
		
		<div class="container">
			<div id="task-list">
				<ul>
					<?php foreach($ITEMS as $ITEM): ?>
					<li class=<?php if($ITEM['done']): ?>"checked"<?php else: ?>"unchecked"<?php endif;?>>
						<a href="?action=toggle&id=<?=$ITEM['id']?>">
							<div>
								<i></i>
								<span><?=htmlspecialchars($ITEM['title'])?></span>
							</div>
						</a>
						<button class="btn-secondary delete-button" style="float: right;" data-href="?action=delete&id=<?=$ITEM['id']?>"></button>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="footer">
			<button id='clear-all-button' class="btn-secondary btn-outline clear-button">Clear All</button>
		</div>
	<?php endif; ?>
	<script>

		const input = document.querySelector("#task-title");
		window.onload = function(){
			input.focus();
		};
		
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

		// buttons
		function createRipple(event) {
			// ripple function
			const button = event.currentTarget;
			const circle = document.createElement("span");
			const diameter = Math.max(button.clientWidth, button.clientHeight);
			const radius = diameter / 2;
			circle.style.width = circle.style.height = `${diameter}px`;
			circle.style.left = `${event.clientX - (button.offsetLeft + radius)}px`;
			circle.style.top = `${event.clientY - (button.offsetTop + radius)}px`;
			circle.classList.add("ripple");

			const ripple = button.getElementsByClassName("ripple")[0];

			if (ripple) {
				ripple.remove();
			}

			button.appendChild(circle);
		}

		// const buttons = document.getElementsByTagName("button");
		// for (const button of buttons) {
		// 	button.addEventListener("click", createRipple);
		// }
		
	</script>
</body>
</html>