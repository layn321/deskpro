<!-- If a user gets here, then clean URLs are not working so we need to redirect them to the real page -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title></title>
	<script type="text/javascript" charset="utf-8">
	window.onload = function() {
		var loc = window.location.href;
		loc = loc.replace(/^(.*?)\/admin\/?(.*?)$/, '$1/index.php/admin/$2');
		window.location.href = loc;
	};
	</script>
</head>
<body>
	<a href="../index.php/admin">&rarr;</a>
</body>
</html>