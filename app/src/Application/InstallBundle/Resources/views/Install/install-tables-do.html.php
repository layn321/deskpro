<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<html>
<head>
<script type="text/javascript">
	var installStatus = {
		update: function(info) {
			window.parent.installStatus.update(info);
		},
		done: function() {
			window.parent.installStatus.done();
		},
		doneBatch: function(batch) {
			window.parent.installStatus.doneBatch(batch);
		},
		setCount: function(count) {
			window.parent.installStatus.setCount(count);
		}
	};
</script>
</head>
<body>
