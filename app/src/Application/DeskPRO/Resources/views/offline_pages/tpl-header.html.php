<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title><?php echo $title ?></title>
        <style>
         /*!
		 * Bootstrap v2.1.0
		 *
		 * Copyright 2012 Twitter, Inc
		 * Licensed under the Apache License v2.0
		 * http://www.apache.org/licenses/LICENSE-2.0
		 *
		 * Designed and built with all the love in the world @twitter by @mdo and @fat.
		 */
		.clearfix{*zoom:1;}.clearfix:before,.clearfix:after{display:table;content:"";line-height:0;}
		.clearfix:after{clear:both;}
		.hide-text{font:0/0 a;color:transparent;text-shadow:none;background-color:transparent;border:0;}
		.input-block-level{display:block;width:100%;min-height:30px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
		article,aside,details,figcaption,figure,footer,header,hgroup,nav,section{display:block;}
		audio,canvas,video{display:inline-block;*display:inline;*zoom:1;}
		audio:not([controls]){display:none;}
		html{font-size:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}
		a:focus{outline:thin dotted #333;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
		a:hover,a:active{outline:0;}
		sub,sup{position:relative;font-size:75%;line-height:0;vertical-align:baseline;}
		sup{top:-0.5em;}
		sub{bottom:-0.25em;}
		img{max-width:100%;height:auto;vertical-align:middle;border:0;-ms-interpolation-mode:bicubic;}
		#map_canvas img{max-width:none;}
		button,input,select,textarea{margin:0;font-size:100%;vertical-align:middle;}
		button,input{*overflow:visible;line-height:normal;}
		button::-moz-focus-inner,input::-moz-focus-inner{padding:0;border:0;}
		button,input[type="button"],input[type="reset"],input[type="submit"]{cursor:pointer;-webkit-appearance:button;}
		input[type="search"]{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;-webkit-appearance:textfield;}
		input[type="search"]::-webkit-search-decoration,input[type="search"]::-webkit-search-cancel-button{-webkit-appearance:none;}
		textarea{overflow:auto;vertical-align:top;}
		body{margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:20px;color:#333333;background-color:#ffffff;}
		a{color:#0088cc;text-decoration:none;}
		a:hover{color:#005580;text-decoration:underline;}
		.img-rounded{-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;}
		.img-polaroid{padding:4px;background-color:#fff;border:1px solid #ccc;border:1px solid rgba(0, 0, 0, 0.2);-webkit-box-shadow:0 1px 3px rgba(0, 0, 0, 0.1);-moz-box-shadow:0 1px 3px rgba(0, 0, 0, 0.1);box-shadow:0 1px 3px rgba(0, 0, 0, 0.1);}
		.img-circle{-webkit-border-radius:500px;-moz-border-radius:500px;border-radius:500px;}
		p{margin:0 0 10px;}
		.lead{margin-bottom:20px;font-size:20px;font-weight:200;line-height:30px;}
		small{font-size:85%;}
		strong{font-weight:bold;}
		em{font-style:italic;}
		cite{font-style:normal;}
		.muted{color:#999999;}
		h1,h2,h3,h4,h5,h6{margin:10px 0;font-family:inherit;font-weight:bold;line-height:1;color:inherit;text-rendering:optimizelegibility;}h1 small,h2 small,h3 small,h4 small,h5 small,h6 small{font-weight:normal;line-height:1;color:#999999;}
		h1{font-size:36px;line-height:40px;}
		h2{font-size:30px;line-height:40px;}
		h3{font-size:24px;line-height:40px;}
		h4{font-size:18px;line-height:20px;}
		h5{font-size:14px;line-height:20px;}
		h6{font-size:12px;line-height:20px;}
		h1 small{font-size:24px;}
		h2 small{font-size:18px;}
		h3 small{font-size:14px;}
		h4 small{font-size:14px;}
		.page-header{padding-bottom:9px;margin:20px 0 30px;border-bottom:1px solid #eeeeee;}
		ul,ol{padding:0;margin:0 0 10px 25px;}
		ul ul,ul ol,ol ol,ol ul{margin-bottom:0;}
		li{line-height:20px;}
		ul.unstyled,ol.unstyled{margin-left:0;list-style:none;}
		dl{margin-bottom:20px;}
		dt,dd{line-height:20px;}
		dt{font-weight:bold;}
		dd{margin-left:10px;}
		.dl-horizontal dt{float:left;width:120px;clear:left;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
		.dl-horizontal dd{margin-left:130px;}
		hr{margin:20px 0;border:0;border-top:1px solid #eeeeee;border-bottom:1px solid #ffffff;}
		abbr[title]{cursor:help;border-bottom:1px dotted #999999;}
		abbr.initialism{font-size:90%;text-transform:uppercase;}
		blockquote{padding:0 0 0 15px;margin:0 0 20px;border-left:5px solid #eeeeee;}blockquote p{margin-bottom:0;font-size:16px;font-weight:300;line-height:25px;}
		blockquote small{display:block;line-height:20px;color:#999999;}blockquote small:before{content:'\2014 \00A0';}
		blockquote.pull-right{float:right;padding-right:15px;padding-left:0;border-right:5px solid #eeeeee;border-left:0;}blockquote.pull-right p,blockquote.pull-right small{text-align:right;}
		blockquote.pull-right small:before{content:'';}
		blockquote.pull-right small:after{content:'\00A0 \2014';}
		q:before,q:after,blockquote:before,blockquote:after{content:"";}
		address{display:block;margin-bottom:20px;font-style:normal;line-height:20px;}
		.btn{display:inline-block;*display:inline;*zoom:1;padding:4px 14px;margin-bottom:0;font-size:14px;line-height:20px;*line-height:20px;text-align:center;vertical-align:middle;cursor:pointer;color:#333333;text-shadow:0 1px 1px rgba(255, 255, 255, 0.75);background-color:#f5f5f5;background-image:-moz-linear-gradient(top, #ffffff, #e6e6e6);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));background-image:-webkit-linear-gradient(top, #ffffff, #e6e6e6);background-image:-o-linear-gradient(top, #ffffff, #e6e6e6);background-image:linear-gradient(to bottom, #ffffff, #e6e6e6);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);border-color:#e6e6e6 #e6e6e6 #bfbfbf;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#e6e6e6;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);border:1px solid #bbbbbb;*border:0;border-bottom-color:#a2a2a2;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;*margin-left:.3em;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);-moz-box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);}.btn:hover,.btn:active,.btn.active,.btn.disabled,.btn[disabled]{color:#333333;background-color:#e6e6e6;*background-color:#d9d9d9;}
		.btn:active,.btn.active{background-color:#cccccc \9;}
		.btn:first-child{*margin-left:0;}
		.btn:hover{color:#333333;text-decoration:none;background-color:#e6e6e6;*background-color:#d9d9d9;background-position:0 -15px;-webkit-transition:background-position 0.1s linear;-moz-transition:background-position 0.1s linear;-o-transition:background-position 0.1s linear;transition:background-position 0.1s linear;}
		.btn:focus{outline:thin dotted #333;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
		.btn.active,.btn:active{background-color:#e6e6e6;background-color:#d9d9d9 \9;background-image:none;outline:0;-webkit-box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);-moz-box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);}
		.btn.disabled,.btn[disabled]{cursor:default;background-color:#e6e6e6;background-image:none;opacity:0.65;filter:alpha(opacity=65);-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;}
		.btn-large{padding:9px 14px;font-size:16px;line-height:normal;-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;}
		.btn-large [class^="icon-"]{margin-top:2px;}
		.btn-small{padding:3px 9px;font-size:12px;line-height:18px;}
		.btn-small [class^="icon-"]{margin-top:0;}
		.btn-mini{padding:2px 6px;font-size:11px;line-height:16px;}
		.btn-block{display:block;width:100%;padding-left:0;padding-right:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
		.btn-block+.btn-block{margin-top:5px;}
		.btn-primary.active,.btn-warning.active,.btn-danger.active,.btn-success.active,.btn-info.active,.btn-inverse.active{color:rgba(255, 255, 255, 0.75);}
		.btn{border-color:#c5c5c5;border-color:rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.25);}
		.btn-primary{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#006dcc;background-image:-moz-linear-gradient(top, #0088cc, #0044cc);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));background-image:-webkit-linear-gradient(top, #0088cc, #0044cc);background-image:-o-linear-gradient(top, #0088cc, #0044cc);background-image:linear-gradient(to bottom, #0088cc, #0044cc);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);border-color:#0044cc #0044cc #002a80;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#0044cc;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-primary:hover,.btn-primary:active,.btn-primary.active,.btn-primary.disabled,.btn-primary[disabled]{color:#ffffff;background-color:#0044cc;*background-color:#003bb3;}
		.btn-primary:active,.btn-primary.active{background-color:#003399 \9;}
		.btn-warning{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#faa732;background-image:-moz-linear-gradient(top, #fbb450, #f89406);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));background-image:-webkit-linear-gradient(top, #fbb450, #f89406);background-image:-o-linear-gradient(top, #fbb450, #f89406);background-image:linear-gradient(to bottom, #fbb450, #f89406);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffbb450', endColorstr='#fff89406', GradientType=0);border-color:#f89406 #f89406 #ad6704;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#f89406;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-warning:hover,.btn-warning:active,.btn-warning.active,.btn-warning.disabled,.btn-warning[disabled]{color:#ffffff;background-color:#f89406;*background-color:#df8505;}
		.btn-warning:active,.btn-warning.active{background-color:#c67605 \9;}
		.btn-danger{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#da4f49;background-image:-moz-linear-gradient(top, #ee5f5b, #bd362f);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));background-image:-webkit-linear-gradient(top, #ee5f5b, #bd362f);background-image:-o-linear-gradient(top, #ee5f5b, #bd362f);background-image:linear-gradient(to bottom, #ee5f5b, #bd362f);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);border-color:#bd362f #bd362f #802420;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#bd362f;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-danger:hover,.btn-danger:active,.btn-danger.active,.btn-danger.disabled,.btn-danger[disabled]{color:#ffffff;background-color:#bd362f;*background-color:#a9302a;}
		.btn-danger:active,.btn-danger.active{background-color:#942a25 \9;}
		.btn-success{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#5bb75b;background-image:-moz-linear-gradient(top, #62c462, #51a351);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351));background-image:-webkit-linear-gradient(top, #62c462, #51a351);background-image:-o-linear-gradient(top, #62c462, #51a351);background-image:linear-gradient(to bottom, #62c462, #51a351);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff51a351', GradientType=0);border-color:#51a351 #51a351 #387038;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#51a351;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-success:hover,.btn-success:active,.btn-success.active,.btn-success.disabled,.btn-success[disabled]{color:#ffffff;background-color:#51a351;*background-color:#499249;}
		.btn-success:active,.btn-success.active{background-color:#408140 \9;}
		.btn-info{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#49afcd;background-image:-moz-linear-gradient(top, #5bc0de, #2f96b4);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#2f96b4));background-image:-webkit-linear-gradient(top, #5bc0de, #2f96b4);background-image:-o-linear-gradient(top, #5bc0de, #2f96b4);background-image:linear-gradient(to bottom, #5bc0de, #2f96b4);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff2f96b4', GradientType=0);border-color:#2f96b4 #2f96b4 #1f6377;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#2f96b4;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-info:hover,.btn-info:active,.btn-info.active,.btn-info.disabled,.btn-info[disabled]{color:#ffffff;background-color:#2f96b4;*background-color:#2a85a0;}
		.btn-info:active,.btn-info.active{background-color:#24748c \9;}
		.btn-inverse{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#363636;background-image:-moz-linear-gradient(top, #444444, #222222);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#444444), to(#222222));background-image:-webkit-linear-gradient(top, #444444, #222222);background-image:-o-linear-gradient(top, #444444, #222222);background-image:linear-gradient(to bottom, #444444, #222222);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff444444', endColorstr='#ff222222', GradientType=0);border-color:#222222 #222222 #000000;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#222222;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-inverse:hover,.btn-inverse:active,.btn-inverse.active,.btn-inverse.disabled,.btn-inverse[disabled]{color:#ffffff;background-color:#222222;*background-color:#151515;}
		.btn-inverse:active,.btn-inverse.active{background-color:#080808 \9;}
		button.btn,input[type="submit"].btn{*padding-top:3px;*padding-bottom:3px;}button.btn::-moz-focus-inner,input[type="submit"].btn::-moz-focus-inner{padding:0;border:0;}
		button.btn.btn-large,input[type="submit"].btn.btn-large{*padding-top:7px;*padding-bottom:7px;}
		button.btn.btn-small,input[type="submit"].btn.btn-small{*padding-top:3px;*padding-bottom:3px;}
		button.btn.btn-mini,input[type="submit"].btn.btn-mini{*padding-top:1px;*padding-bottom:1px;}
		.btn-link,.btn-link:active{background-color:transparent;background-image:none;-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;}
		.btn-link{border-color:transparent;cursor:pointer;color:#0088cc;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;}
		.btn-link:hover{color:#005580;text-decoration:underline;background-color:transparent;}

			#topbar {
				text-align: right;
				background-color: #272727;
			}

			#topbar .content {
				width: 800px;
				margin: 0 auto 0 auto;
				color: #999;
				font-size: 11px;
				padding: 4px;
			}

			#topbar .badge {
				background-color: #5D5D5D;
				padding: 0px 3px;
				border-radius: 3px;
				-webkit-border-radius: 3px;
			}

			#topbar a {
				color: #999;
				text-decoration: underline;
			}

			#page {
				width: 800px;
				margin: 0 auto 0 auto;
			}

			#page .logo {
				padding: 15px 10px 10px 10px;
			}

			#page .logo .links {
				float: right;
				margin-top: 16px;
			}

			#page .content {
				margin: 10px;
				border: 1px solid #C0C0C0;
				border-radius: 5px;
				-webkit-border-radius: 5px;
				padding: 20px;
				font-size: 10pt;
			}

			#page .content h2 {
				margin: 0;
				margin-bottom: 13px;
				font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
				font-size: 16pt;
				font-weight: normal;
			}
        </style>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				var emailform = $('.email-form');
				var emailsent = $('.email-form-sent');

				emailform.find('button.send-btn').on('click', function() {
					var message = emailform.find('textarea').val();

					emailform.find('.send-btn').hide();
					emailform.find('.send-loading').show();
					$.ajax({
						url: '<?php echo $billing_url ?>billing-send-question',
						data: { message: message },
						type: 'POST',
						dataType: 'json',
						success: function() {
							emailform.hide();
							emailsent.show();
						}
					});
				});
			});
		</script>
    </head>
    <body>
		<div id="topbar">
			<div class="content">
				<span class="badge">Call (US)</span> 1-888-DESKPRO
				&nbsp;&nbsp;&nbsp;
				<span class="badge">Call (UK)</span> (+44) 20-3582-1980
				&nbsp;&nbsp;&nbsp;
				<span class="badge">Email</span> <a href="mailto:sales@deskpro.com">sales@deskpro.com</a>
			</div>
		</div>
        <div id="page">
			<div class="logo">
				<div class="links">
					<a class="btn btn-danger btn-small" href="https://www.deskpro.com/">DeskPRO.com</a>
					&nbsp;&nbsp;
					<a class="btn btn-danger btn-small" href="https://support.deskpro.com/">Help &amp; Support</a>
				</div>
				<a href="https://www.deskpro.com/">
					<img src="<?php echo $asset_url ?>/images/dp-logo-color.png" alt="" border="" />
				</a>
			</div>

			<div class="content">
				<h2><?php echo $title ?></h2>
				<div class="block">