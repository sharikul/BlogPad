<?php

require '../classes/BlogPad.php';
require '../classes/User.php';

session_start();

if( User::logged_in() ) {
    header('Location: '.BlogPad::get_blog_homepage().'/admin/');
    exit;
}

$messages = array();

if( isset($_POST['username']) ) {

    $username = strip_tags(trim( $_POST['username'] ));
    $password = trim($_POST['password']);

    if( !User::exists($username) ) {
        $messages[] = "User '$username' doesn't exist.";
    }

    // If $messages is empty at this point, there's no error so far.
    if( empty($messages) ) {

        // Fetch the password stored.
        $accounts = BlogPad::get_setting('accounts');
        $pword = $accounts[$username]['password'];

        // Stores boolean on whether password matches or not.
        $is_match = crypt($password, $pword) === $pword;

        if( !$is_match ) {
            $messages[] = sprintf('Incorrect password supplied for account `%s`. Did you use <em>%s</em> to generate your password?', $username, '<a href="'.BlogPad::get_blog_homepage().'/crypt_gen.php">crypt_gen.php</a>');
        }

        else {

            session_start();
            // Sign the user in!
            User::login($username, $pword);

            header('Location: '.BlogPad::get_blog_homepage().'/admin/');
            exit;
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - BlogPad</title>

    <style>
        body {
            background-color: whiteSmoke;
            font-family: Arvo, serif;
        }

        form {
            margin: 5% auto;
            width: 16%;
            border: 1px solid lightgray;
            padding: 5px 15px;
        }

        input[type=text], input[type=password] {
            border: 1px solid lightgray;
            background: hsla(103, 4%, 90%, 0.59);
            font-size: 1em;
            outline: none;
            padding: 5px;
            width: 94%;
            font-family: Open Sans, sans-serif;
            font-weight: bold;
            color: hsla(103, 4%, 24%, 0.55);
        }

        label {
            display: block;
            margin: 5px 1px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        input[type=submit] {
            font-family: Open Sans, sans-serif;
            font-size: 1em;
            background: hsla(96, 4%, 83%, 0.66);
            border: 1px solid lightgray;
            color: hsla(107, 11%, 31%, 2.55);
            margin: 0.5em 0.5em;
            padding: 3px 15px;
            border-radius: 4px;
            box-shadow: 0px 0px 2px hsla(96, 4%, 83%, 0.66);
            cursor: pointer;
            outline: none;
        }

        #center {
            text-align: center;
        }

        ul li {
            font-family: Open Sans, sans-serif;
            font-size: .9em;
        }

    </style>

    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>

    <form method="POST" spellcheck=false autocomplete=off>
        <label for="username">Username</label>
            <input type="text" name="username" id="username">
        
        <br>
        <br>
        <label for="password">Password</label>
            <input type="password" name="password" id="password">
        
        <div id="center">
            <input type="submit" value="Login" id="submit">
        </div>
        
    <?php if(!empty($messages)):?>

        <ul>

        <?php foreach($messages as $message): ?>
            <li><?php echo $message;?></li>
        <?php endforeach;?>

        </ul>

    <?php endif;?>
    </form>

    <script>
        var username = document.getElementById('username'),
            password = document.getElementById('password'),
            submit = document.getElementById('submit');

        submit.onclick = function() {
            return username.value.trim() !== '' && password.value.trim() !== '';
        }

    </script>

</body>
</html>