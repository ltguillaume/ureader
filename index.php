<?php

error_reporting(0); // E_ALL

$scriptDir = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/");
$dir       = substr(strtok($_SERVER["REQUEST_URI"], "?"), strlen($scriptDir));

$book      = trim($dir, "/") ?: ".";
$config    = "config.php";
$contents  = "$book/contents.";
$counters  = "";
$font      = "fanwood_text.woff";
$icon      = pathinfo(__FILE__)["filename"] .".ico";
$wordsPMin = 250;

if (is_readable($config))
	include $config;
if (is_readable("$book/$config"))
	include "$book/$config";

include "lang.php";
$lang = $lang ?? substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
$str  = $str[$lang] ?? $str["en"];

foreach (["md", "txt"] as $ext) {
	if (is_readable($contents . $ext)) {
		$contents .= $ext;
		$format    = $ext;
		break;
	}
}
if (!isset($format))
	exit($str->notFound);

$contents  = file_get_contents($contents);
$title     = preg_replace("/^#+ */", "", strtok($contents, "\n"));

if (isset($watchword)) {
	if (!isset($_GET["ww"]) && !isset($_POST["ww"]))
		$prompt = "$str->enterWw:";
	else if ((isset($_GET["ww"]) && $_GET["ww"] != $watchword) || (isset($_POST["ww"]) && $_POST["ww"] != $watchword))
		$prompt = "$str->wrongWw<br>$str->enterWw:";

	if (isset($prompt))
		$contents = <<<WW
			<form action="//{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}" method="post">
				<br>{$prompt}<br>
				<input type="password" name="ww" autofocus><br>
				<input type="submit" value="{$str->submit}">
			</form>
			<script>
				document.documentElement.className = "ww";	// Disable white-space and JavaScript
			</script>
WW;
}

$counters = "";

if (!isset($prompt)) {
	$words    = str_word_count($contents);
	$rTime    = round($words / $wordsPMin);
	$counters = $rTime < 1 ? "" : "<span title=\"$words $str->words\" onclick=\"swapInfo(this)\">$str->rTime: $rTime $str->rMinutes</span>";

	if ($format == "md") {
		function getImage($img) {
			$book    = $GLOBALS["book"];
			$caption = $img[1];
			$image   = $img[2];
			if ($float = str_ends_with($caption, " <") ? "left" : (str_ends_with($caption, " >") ? "right" : ""))
				$caption = substr($caption, 0, -2);
			return
				"<figure class=\"$float\">".
					"<img src=\"$image\" onclick='window.open(\"$image\")'>".
					"<figcaption>$caption</figcaption>".
				"</figure>";
		};

		$contents = preg_replace("/_(.+?)_/m", "<i>$1</i>", $contents);
		$contents = preg_replace("/\*(.+?)\*/m", "<b>$1</b>", $contents);
		$contents = preg_replace("/^##\s*(.+?)$\n/m", "<h2>$1</h2>", $contents);
		$contents = preg_replace("/^#\s*(.+?)$\n/m",  "<h1>$1</h1>", $contents);
		$contents = preg_replace("/([^!])\[(.+?)\]\((.+?)\)/", "$1<a rel=\"noreferrer\" href=\"$3\">$2</a>", $contents);
		$contents = preg_replace_callback("/!\[(.*?)\]\((.+?)\)/", "getImage", $contents);
	}
	$contents = preg_replace("/(?<!href=\")(https?:\/\/.+?)(\s|$)/", "<a rel=\"noreferrer\" href=\"$1\">$1</a>$2", $contents);
}

echo <<<END
<!DOCTYPE html>
<html>
	<head>
		<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📖</text></svg>">
		<link rel="stylesheet" href="{$scriptDir}/index.css" type="text/css">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta name="referrer" content="no-referrer">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<noscript>
			<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
		</noscript>
		<script>
			document.documentElement.className = "js";
		</script>
		<style>
			@font-face {
				font-family: ReaderFont;
				src: url({$scriptDir}/{$font});
			}
		</style>
		<title>{$title}</title>
	</head>
	<body>
		<div id="controls">
			{$counters}&nbsp;&nbsp;
			<button title="{$str->setTheme} (T)" onclick="setTheme()">&#9706;</button>
			<button title="{$str->decrSize} (-)" onclick="setSize(-1)">&#65293;</button>
			<button title="{$str->incrSize} (+)" onclick="setSize(+1)">&#65291;</button>
		</div>
		<div id="book">
			<div id="contents">{$contents}</div>
		</div>
		<button id="pagenum" title="{$str->jumpPage} (P)" alt="{$str->selMode}" onclick="jumpPage()" oncontextmenu="setScroll(event)"></button>
	</body>
	<script src="{$scriptDir}/index.js"></script>
</html>
END;

?>