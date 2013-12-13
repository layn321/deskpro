<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<html>
<head>
<script type="text/javascript">
	if (window.parent != window) {
		window.parent.DpStatus.update(<?php echo json_encode($results) ?>, <?php echo $batch ?>);
		window.parent.DpStatus.doneBatch(<?php echo $batch ?>);
	}
</script>
</head>
<body>
</body>
</html>
