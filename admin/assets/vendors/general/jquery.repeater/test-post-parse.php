<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

<pre>
<?= json_encode($_POST, JSON_PRETTY_PRINT); ?>
</pre>
	<form method="POST">
		<input type="text" name="group[1][foo]" value="A"/>
		<input type="text" name="group[2][foo]" value="B"/>
		<input type="submit" value="Submit"/>
	</form>
</body>
</html>