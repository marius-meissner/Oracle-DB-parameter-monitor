<?php
error_reporting(E_ALL); ini_set('display_errors', '1');
session_start();
include ("./includes/templates/configuration.php");
include("includes/classes/auth.php");

$account = $_POST["account"];
$password = $_POST["password"];
$login_result = true;

if ($account != "")
{
    $ldap = new ldap($GLOBALS['config']['ldap_host'], $GLOBALS['config']['ldap_user_domain'],  $GLOBALS['config']['ldap_dn']);
    $login_result = $ldap->authenticate ($account, $password);

    if ($login_result == "ok")
    {
        $_SESSION['login'] = true;
        $_SESSION['name'] = $ldap->getFullName($account);
        header("Location: index.php");
    }
    else
    {
        session_destroy();
    }
}
?>


<!doctype html>
<html lang="en-US">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=11"/> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/> 
	<meta charset="utf-8">

	<title>Login</title>
	<link rel="stylesheet" href="css/login.css" type="text/css">
</head>

<body>
    <div id="login">

		<h2><span class="lock"><img src="images/lock.png"></img></span>Sign In</h2>

		<form action="login.php" method="POST">

			<fieldset>
				<?php 
				if ($login_result == "permission" && $_POST["account"] != "") 	{echo '<p><label style="color: #F00;" for="email">You have no permission to access this site!</label></p>';}
				if ($login_result == "password" && $_POST["account"] != "") 	{echo '<p><label style="color: #F00;" for="email">Your Account/Password is wrong!</label></p>';}
				?>
				<p><label for="email">Windows Account</label></p>
				<p><input type="text" name="account" id="email"></p>

				<p><label for="password">Password</label></p>
				<p><input type="password" name="password" id="password" ></p> 

				<p><input type="submit" value="Sign In"></p>

			</fieldset>

		</form>

    </div> 
</body>	
</html>