<?php 

require 'common/sessie_check.php';



$currentpage = 'instellingen';

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Instellingen - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

		

</head>



<?php 



require( 'common/connection.php');


// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

if ($stmt = $con->prepare('SELECT id, username, email, activation_code, img FROM accounts WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	$stmt->bind_param('s', $_SESSION['id']);

	$stmt->execute();

	// Store the result so we can check if the account exists in the database.

	$stmt->store_result();

}



if ($stmt->num_rows > 0) {

	$stmt->bind_result($id, $username, $email, $activation, $img);

	$stmt->fetch();	

}



?>



	<body class="app">

	
		
		<?php include 'common/navigatie.php'; ?>
	
		

		<div class="appcontent">

		

			<div class="titlebar">

				<div class="titlebarcontainer">

					<h2>Instellingen</h2>

					<div class="submenu">

						<a class="<?php if($currentpage == 'instellingen') { echo "actief"; } ?>" href="/instellingen/">Profiel</a>

						<a class="<?php if($currentpage == 'gebruikers') { echo "actief"; } ?>" href="/gebruikers/">Gebruikers</a>

                        <a class="<?php if($currentpage == 'leads-instellingen') { echo "actief"; } ?>" href="/leads-instellingen/">Leads</a>
						<a class="<?php if($currentpage == 'werkplanning-setting') { echo "actief"; } ?>" href="/werkplanning-instellingen/">Werkplanning</a>
						
					</div>

					<div style="clear:both"></div>

				</div>

				<div style="clear:both"></div>

			</div>

			

			<div class="contentcontainer" style="max-width:700px">

			

					<div class="row">

					<form id="profilesettings" class="col s12">

					

					<input type="hidden" name="userid" value="<?=$_SESSION['id'];?>">

					

					  <div class="row">

						<div class="col s4 radiobutton">

						  <img src="<?=$root;?>images/users/man.jpg" class="avatarpreview">

						  <label>						   

							<input name="group1" value="man" type="radio" <?php if($img == 'man') { echo "checked"; } ?> />

							<span>Man</span>							

						  </label>

						</div>

						<div class="col s4 radiobutton">

						<img src="<?=$root;?>images/users/vrouw.jpg" class="avatarpreview">

						  <label>						   

							<input name="group1" value="vrouw" type="radio" <?php if($img == 'vrouw') { echo "checked"; } ?> />

								<span>Vrouw</span>							

							</label>

						</div>

						<div class="col s4 radiobutton">

							
							
								<?php 

									if(!empty($_SESSION['img_path']))
									{
										echo '<div class="btn-filelog avatarpreview-panel file_selected" >';
										echo '<img src="' . $root . 'upload/' . $_SESSION['img_path'] . '" class="avatarpreview" data-img-changed = 0>';
										
									}
									else{
										echo '<div class="btn-filelog avatarpreview-panel" >';
										echo '<img src="' . $root . 'images/users/afbeelding.jpg" class="avatarpreview" data-img-changed = 0>';
									}
								?>
								
								
								<i class="material-icons avatar-choose">attach_file</i>

								<div class="avatar-overlay"></div>
								<i class="material-icons avatar-delete" onclick="deleteIMG()">close</i>
								<input type="file" id="file" name="avatar_file" onchange="readURL(this)" data-preview-img=".avatarpreview-panel .avatarpreview">
								
							</div>

							<label>						   

								<input name="group1" value="afbeelding" type="radio" <?php if($img == 'afbeelding') { echo "checked"; } ?> />

								<span>Afbeelding</span>							

						  </label>

						</div>

					  </div>	

					  <div class="row">

						<div class="input-field col s6">

						  <input <?php if($_SESSION['ac_level'] != '1') { echo "readonly"; } ?> id="username" type="text" name="username" value="<?=$username;?>">

						  <label for="username">Gebruikersnaam</label>

						</div>

						<div class="input-field col s6">

						  <input id="email" type="text" name="email" value="<?=$email;?>">

						  <label for="email">E-mailadres</label>

						</div>

					  </div>					 

					  <div class="row">

						<div class="input-field col s6">

						  <input id="password" type="password" name="password">

						  <label for="password">Nieuw wachtwoord</label>

						</div>

						<div class="input-field col s6">

						  <input id="password2" type="password" name="password2">

						  <label for="password2">Herhaal wachtwoord</label>

						</div>

					  </div>	



					  <div class="bottombuttons">

						<span class="button waves-effect waves-light btn" onclick="saveProfile()">Opslaan</div>

					  </div>

					  

					</form>

				  </div>

			

			</div>

		

		</div>			

	

	<script src="<?=$root;?>js/settings.js" type="text/javascript"></script>	

	

<?php include 'common/footer.php'; ?>
