<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DeskPRO</title>
	<link rel="stylesheet" type="text/css" href="../../web/stylesheets/install/install.css" />
	<script type="text/javascript" src="../../web/vendor/jquery/jquery.min.js"></script>
	<style type="text/css">
		html, body {
			width: 100%;
			height: 100%;
			padding: 0;
			margin: 0;
			background-color: #ECEEF0;
		}

		body > table {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
		}
		body > table > tbody > tr > td {
			vertical-align: middle;
			border-top: 10px solid transparent;
			border-bottom: 10px solid transparent;
		}

		#dp_logo {
			background: url(../../web/images/dp-logo-color.png);
			width: 200px;
			height: 59px;
			cursor: pointer;
			text-decoration: none;
			overflow: hidden;
			text-indent: -1000px;

			position: absolute;
			right: 15px;
			top: 15px;
			z-index: 1;
		}

		.dp-wrapper {
			position: relative;
			background-color: #fff;
			border: 1px solid #D0D2D3;
			-webkit-border-radius: 6px;
			-moz-border-radius: 6px;
			border-radius: 6px;
			padding: 25px;
		}

		.page-header {
			position: relative;
			height: 87px;
			padding: 0;
			margin: 0;
			margin: -25px;
			margin-bottom: 22px;
		}

		.page-header .inner {
			padding: 20px;
			padding-top: 25px;
		}

		.next-loading {
			display: block;
			background: transparent url(../../web/images/spinners/loading-small-flat.gif) no-repeat 0 50%;

			overflow: hidden;
			width: 16px;
			height: 27px;
			margin: 3px 0 0 8px;
			display: none;
		}

		.submit-area.clicked .next-loading { display: block; }
		.submit-area.clicked .btn { display: none; }

		.alert-message {
			clear: both;
			margin-top: 7px;
		}
	</style>
	<script type="text/javascript">
		function sendReportError(type) {
			type = type || '';

			$.ajax({
				url: '<?php echo $view['router']->generate('install_send_install_report_error') ?>?type=' + type
			});
		}


	</script>
	<?php $view['slots']->output('head') ?>
</head>
<body>
<table cellspacing="0" cellpadding="0" width="100%" height="100%">
	<tbody>
		<tr>
			<td align="center" valign="middle" width="100%" height="100%">
				<div class="container">
				<div class="dp-wrapper">
					<a id="dp_logo" href="http://support.deskpro.com/">DeskPRO</a>
					<div class="page-header">
						<div class="inner">
							<h1>Installation<?php if ($view['slots']->has('subtitle')): ?>&nbsp;<small><?php $view['slots']->output('subtitle') ?></small><?php endif ?></h1>
						</div>
					</div>
					<?php $view['slots']->output('_content') ?>
				</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>
