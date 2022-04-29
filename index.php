<?php

$font = 'fanwood_text-webfont.woff';
$font_data = base64_encode(file_get_contents($font));
$contents = file_get_contents('contents.txt');
$title = strtok($contents, "\n");

echo <<<END
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<style>
			@font-face {
				font-family: Fanwood;
				src: url(data:application/x-font-woff;charset=utf-8;base64,{$font_data});
			}
			* {
				font-family: Fanwood;
			}
			html {
				background: #cde;
				color: black;
				font-size: 1.3em;
			}
				html.dark, html.dark button {
					color: #b0b0b0;
					background: #141a21;
				}
			button {
				width: 2.3em;
				height: 2.3em;
				background: 0;
				border: 0;
			}
			#controls {
				display: none;
				position: absolute;
				top: .3em;
				right: .3em;
			}
				#controls.show {
					display: block;
				}
			#contents {
				max-width: 30em;
				margin: 0 auto;
				padding: 3em .3em;
				line-height: 1.4;
				font-size: 1em;
				white-space: pre-wrap;
			}
				#contents:first-line {
					font-size: 3em;
				}
		</style>
		<title>{$title}</title>
	</head>
	<body>
		<div id="controls">
			<button onclick="darkMode()">&#9711;</button>
			<button onclick="fontSize(-.1)">&#65293;</button>
			<button onclick="fontSize(+.1)">&#65291;</button>
		</div>
		<pre id="contents">{$contents}</pre>
	</body>
	<script>
		size = 1, style = document.getElementById('contents').style;
		darkMode = () => document.documentElement.classList.toggle('dark');
		fontSize = (val) => style.fontSize = (size += val) +'em';
		document.getElementById('controls').className='show';
	</script>
</html>
END;

?>