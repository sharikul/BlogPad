<?php
	
	if( isset( $_POST['pword'] ) && trim( $_POST['pword'] ) !== '') {
		$password = crypt($_POST['pword']);
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Crypt Password Generator - BlogPad</title>
	<style>
		body {
			background-color: whiteSmoke;
			font-family: Arvo, serif;
		}

		header {
			margin-left: 2.5em;
			text-align: center;
		}

		h4 {
			font-family: Open Sans, sans-serif;
			font-weight: lighter;
		}

		#container {
			margin: 3em 3em;
			width: 95%;
			text-align: center;
		}

		form, input {
			margin: 0;
		}

		input[type=text], input[type=submit] {
			width: 25em;
			padding: .5em;
			background: azure;
			border: 1px solid lightgray;
			outline: none;
			font-family: Open Sans, sans-serif;
			font-size: 1em;
			text-align: center;
		}

		input[type=submit] {
			width: 6em;
			background: beige;
		}

		#crypword {
			background: FloralWhite;
			width: 25em;
			margin: auto;
			padding: 5px;
			border: 1px solid lightgray;
		}
	</style>
	<link href='http://fonts.googleapis.com/css?family=Arvo|Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
	<header>
		<h1>Crypt Password Generator</h1>
		<h4 id="definition">Use this tool to generate a secure password for your BlogPad accounts.</h4>
	</header>
	
	<div id="container">
		<form method="POST">
			<input type="text" name="pword" placeholder="Enter your password here." required>
			<br>
			<br>
			<input type="submit" value="Generate">

			<?php if( isset($password) ): ?>
				<h4>Your Crypted Password is:</h4>
				<input type="text" readonly placeholder="Your Password" value="<?php echo $password;?>">
				<h4>Supply the crypted password above as the value to the 'password' key <br>in the account array that you're setting up.</h4>
			<?php endif; ?>
		</form>
	</div>
</body>
</html>