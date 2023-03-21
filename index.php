<?php 
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is logged in redirect to the homepage..
if (isset($_SESSION['loggedin'])) {
	header('Location: /funnel');
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Inloggen - Van der Zeeuw Bouw Ordersysteem</title>	
		
		<?php include 'common/header.php'; ?>		
		
	</head>
	<body class="login">
	
	<div class="holder">

			<img class="logo-s" src="images/logo.png">
	
			<div class="loginformulier">
			<h1>Inloggen</h1>
			
	
			<form id="loginform" method="post">
				
			<div class="input-field">
				<input id="email" name="email" type="text">
				<label for="email">E-mailadres</label>
			</div>
			
			<div class="input-field">
				<input id="password" name="password" type="password">
				<label for="password">Wachtwoord</label>
			</div>
			
				<!--<label for="username">E-mailadres</label>
				<input type="text" name="username" placeholder="E-mailadres" id="username">				
				<label for="password">Wachtwoord</label>
				<input type="password" name="password" placeholder="Wachtwoord" id="password">-->
				<input type="submit" value="inloggen" style="display:none">
			</form>			
			
			<span class="button" onclick="inLoggen()">
			
			<span>Inloggen</span>
			
			<div class="sk-three-bounce">
			<div class="sk-child sk-bounce1"></div>
			<div class="sk-child sk-bounce2"></div>
			<div class="sk-child sk-bounce3"></div>
			</div>
			
			</span>
			
			<div class="vergeten">Wachtwoord vergeten? <a href="">Klik hier.</a></div>
			
			</div>		
	
	</div>
	<div class="sidebar vcenter">
	
	<img src="images/login-achtergrond2.png" class="logincontent">
	
	<!--
	<img class="logo" src="images/logo-white.png">
	<ul class="homepage-menus">
			<li>
				<p class="h-menu-slogan">"Wat een bedrijf!"</p>
				<p class="h-menu-maindesc">Doelstellingen</p>
			</li>
			
			<li>
				<p class="h-menu-header">Klanten</p>
				Onder de indruk van onze comunicatie & Kwaliteit
			</li>
			<li style="align-self: flex-end;">
				<p class="h-menu-header">Medewerkers</p>
				Plezier, vrijheid en ruimte voor iedereen om te groeien
			</li>
			<li>
				<p class="h-menu-header">Organisatie</p>
				<strong style="font-weight: bold;">2021:</strong> Uitbouw per week perfect geplaatst!<br/>
				<strong style="font-weight: bold;">09/2022:</strong> Twee uitbouwen per week perfect geplaatst!
			</li>
		</ul>
	-->
	
	<!--<span class="quote">Op maat gemaakte uitbouw in een week geplaatst.</span>
	<span class="quote2">Groter wonen zonder te verhuizen</span>-->
	
	</div>
	<div style="clear:both"></div>			
			
		</div>
	</body>
	
	<script src="js/login.js" type="text/javascript"></script>	
	
<?php include 'common/footer.php'; ?>