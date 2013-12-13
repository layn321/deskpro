<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>DeskPRO</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	<style type="text/css" media="screen">
		html,body,div,span,object,iframe,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,var,fieldset,form,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,dialog,figure,footer,header,hgroup,menu,nav,section,time,mark,audio,video{vertical-align:baseline;margin:0;padding:0}
		article,aside,dialog,figure,footer,header,hgroup,menu,nav,section,time,mark,audio,video {display:block}
		body{background:#fff;color:#000;font:75%/1.5em Arial, Helvetica, "DejaVu Sans", "Liberation sans", "Bitstream Vera Sans", sans-serif;position:relative}
		textarea{font:101%/1.5em Arial, Helvetica, "DejaVu Sans", "Liberation sans", "Bitstream Vera Sans", sans-serif;border:1px solid #ccc;border-bottom-color:#eee;border-right-color:#eee;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;-ms-box-sizing:border-box;width:100%;margin:0;padding:.29em 0}
		blockquote,q{quotes:none}
		blockquote:before,blockquote:after,q:before,q:after{content:none}
		:focus{outline:none}
		a{text-decoration:underline;border:0}
		a:hover,a:focus{text-decoration:none}
		a img{border:0}
		abbr,acronym{border-bottom:1px dotted;cursor:help;font-variant:small-caps}
		address,cite,em,i{font-style:italic}
		blockquote p{margin:0 1.5em 1.5em;padding:.75em}
		code,kbd,tt{font-family:"Courier New", Courier, monospace, serif;line-height:1.5}
		del{text-decoration:line-through}
		dfn{border-bottom:1px dashed;font-style:italic}
		dl{margin:0 0 1.5em}
		dd{margin-left:1.5em}
		h1,h2,h3,h4,h5,h6{font-weight:700;padding:0}
		h1{font-size:2em;margin:0 0 .75em}
		h2{font-size:1.5em;margin:0 0 1em}
		h3{font-size:1.1666em;margin:0 0 1.286em}
		h4{font-size:1em;margin:0 0 1.5em}
		h5{font-size:.8333em;margin:0 0 1.8em}
		h6{font-size:.666em;margin:0 0 2.25em}
		img{display:inline-block;vertical-align:text-bottom}
		ins{text-decoration:overline}
		mark{background-color:#ff9;color:#000;font-style:italic;font-weight:700}
		ol{list-style:outside decimal}
		p{font-weight:300;margin:0 0 1.5em}
		pre{font-family:"Courier New", Courier, monospace, serif;margin:0 0 1.5em}
		sub{top:.4em;font-size:.85em;line-height:1;position:relative;vertical-align:baseline}
		sup{font-size:.85em;line-height:1;position:relative;bottom:.5em;vertical-align:baseline}
		ul{list-style:outside disc}
		ul,ol{margin:0 0 1.5em 1.5em;padding:0}
		li ul,li ol{margin:0 0 1.5em 1.5em;padding:0}
		table{border-collapse:collapse;border-spacing:0;margin:0 0 1.5em;padding:0}
		caption{font-style:italic;text-align:left}
		tr.alt td{background:#eee}
		td{border:1px solid #000;vertical-align:middle;padding:.333em}
		th{font-weight:700;vertical-align:middle;padding:.333em}
		button{cursor:pointer;font-size:1em;height:2em;line-height:1.5em;padding:0 .5em}
		button::-moz-focus-inner{border:0}
		fieldset{border:0;position:relative;margin:0 0 1.5em;padding:1.5em 0 0}
		fieldset fieldset{clear:both;margin:0 0 1.5em;padding:0 0 0 1.5em}
		input{border:1px solid #ccc;border-bottom-color:#eee;border-right-color:#eee;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;-ms-box-sizing:border-box;font-size:1em;}
		input[type=file]{height:2.25em;padding:0}
		select{border:1px solid #ccc;border-bottom-color:#eee;border-right-color:#eee;font-size:1em;height:2.25em;_margin:0 0 1.3em;margin:0 0 .8em;padding:.2em 0 0}
		optgroup{font-weight:700;font-style:normal;text-indent:.2em}
		optgroup + optgroup{margin-top:1em}
		option{font-size:1em;height:1.5em;text-indent:1em;padding:0}
		label{cursor:pointer;display:block;height:auto;line-height:1.4em;width:100%;margin:0;padding:0}
		label input{background:0;border:0;height:1.5em;line-height:1.5em;width:auto;margin:0 .5em 0 0;padding:0}
		legend{font-size:1.1666em;font-weight:700;left:0;margin:0;padding:0}
		dt,strong,b{font-weight:700}
		.amp{font-family:Baskerville, "Goudy Old Style", Palatino, "Book Antiqua", "URW Chancery L", Gentium, serif;font-style:italic}
		.quo{font-family:Georgia, Gentium, "Times New Roman", Times, serif}
		.lquo{font-family:Georgia, Gentium, "Times New Roman", Times, serif;margin:0 0 0 -.55em}
		.introParagraphArticle:first-letter{float:left;font-size:3.2em;font-weight:700;line-height:1em;margin:0 0 -.2em;padding:.125em .1em 0 0}
		.message{background:#eee;border:1px solid #999;margin:1.5em;padding:.666em}
		.error,.failure{background:#fee;border:1px solid red;margin:1.5em;padding:.666em}
		.notice{background:#eef;border:1px solid #00f;margin:1.5em;padding:.666em}
		.success{background:#efe;border:1px solid #0f0;margin:1.5em;padding:.666em}
		.warning{background:#ffe;border:1px solid #ff0;margin:1.5em;padding:.666em}
		.aside-left{clear:left;float:left;overflow:hidden;margin:0 1.5em 1.5em 0}
		.aside-right{clear:right;float:right;overflow:hidden;margin:0 0 1.5em 1.5em}
		.horizontalForm button{clear:left;float:left;margin:.25em 0 0}
		.horizontalForm input,.horizontalForm textarea{float:left;width:49%;margin:0 0 .8em}
		.horizontalForm select{float:left;_margin:0 0 1.25em;margin:0 0 .75em}
		.horizontalForm label{clear:left;float:left;width:49%;padding:.375em 0}
		.horizontalForm label input{height:1em;line-height:1.5em;width:auto;margin:.25em .5em 0 0}
		.horizontalForm label.singleLine{clear:both;float:none;height:1.5em;width:100%;padding:0}

		var { font-family: 'Monaco', 'Courier New', monospace; font-style: normal; color: #415E85; }

		body {
			background-color: #D0DEF5;
		}

		#top {
			max-width: 900px;
			margin: 5px auto 0 auto;
			text-align: right;
		}

		#container {
			max-width: 900px;
			margin: 15px auto 15px auto;
			background-color: #fff;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border: 2px solid #8B9AB3;
			padding: 15px;
		}
	</style>

	<?php $view['slots']->output('head') ?>
</head>
<body>
<div id="top"><a href="<?php echo $view['router']->generate('dev') ?>">Back to dev main</a></div>
<div id="container">
	<?php $view['slots']->output('_content') ?>
</div>
</body>
</html>