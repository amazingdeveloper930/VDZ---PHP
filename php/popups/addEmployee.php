<?php 

	require '../../common/global.php';
	require( '../../common/connection.php');
	$stmt = $con -> prepare("SELECT * FROM employees WHERE teamleader = 0");
	$stmt -> execute();
	$result = $stmt -> get_result();
	if($stmt = $con -> prepare('SELECT P.*, C.name, C.address FROM projects P Join contacts C ON P.contact_id = C.id Order By address ASC'))
{
    //$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result_pro = $stmt->get_result();
}


?>
	<form id="employeeinfo" class="col s12">
		<input type="hidden" name="employeeid" class="employeeid" value="">
		<div class="row">
			<div class="input-field col s5">
				<input id="name" type="text" name="name" value="">
				<label for="name" class="name">Voornaam</label>
			</div>
			<div class="input-field col s6">
				<input id="achternaam" type="text" name="achternaam" value="" style="width: calc(100% - 60px);">
				<label for="achternaam" class="achternaam">Achternaam</label>
				<img src="<?=$root;?>images/users/employee.png" class="img-user-avatar"> 
			</div>

			<div class="file-field input-field col s1 btn-filelog">
				
				<div class="preloader-wrapper small active file-loading-icon">
					<div class="spinner-layer spinner-blue-only">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div><div class="gap-patch">
						<div class="circle"></div>
					</div><div class="circle-clipper right">
						<div class="circle"></div>
					</div>
					</div>
				</div>
				<div class="file-icon">
					<i class="material-icons">attach_file</i>
					<input type="file" id="file" onchange="fileselected()">
				</div>
				<div class="file-path-wrapper">
					<input class="file-path validate" type="text" hidden>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="select-box-field col s6">
				<label for="ei_specialisme" class="name">Specialisme</label>
				<select id="ei_specialisme" name="ei_specialisme" class='browser-default'>
					<option value="Allround">Allround</option>
					<option value="Timmerman">Timmerman</option>
					<option value="Loodgieter">Loodgieter</option>
					<option value="Electricien">Electricien</option>
					<option value="Schilder">Schilder</option>
					<option value="Stuccer">Stuccer</option>
					<option value="Sloper">Sloper</option>
					<option value="Lasser">Lasser</option>
					<option value="Chauffeur">Chauffeur</option>
				</select>
			</div>
			<div class="select-box-field col s6">
				<label for="ei_projecttype" class="name">Afdeling</label>
				<select id="ei_projecttype" name="ei_projecttype" class='browser-default'> 
					<option value='Productie'>Productie</option>
					<option value='Portaal'>Portaal</option>
					<option value='Plaatsing'>Plaatsing</option>
					<option value='Service'>Service</option>
				</select>
				
			</div>
		</div>

		<div class="row">
			<div class="select-box-field col s6">
				<label for="ei_teamleader" class="name">Is teamleider</label>
				<select id="ei_teamleader" name="ei_teamleader" class='browser-default' onchange="teamleaderOptionChanged()"> 
					<option value="0" default>Is teamleider</option>
					<?php
						while($row = $result -> fetch_assoc())
						{
							echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
						}
					?>
				</select>
				
			</div>
			<div class="select-box-field col s6">
				<label for="ei_inweekplanning" class="name">In weekplanning?</label>
				<select id="ei_inweekplanning" name="ei_inweekplanning" class='browser-default'>
					<option value="Ja">Ja</option>
					<option value="Nee">Nee</option>
				</select>
				
			</div>
		</div>
		<div class="row">
			<div class="select-box-field col s12">
				
				<select id="ei_contact_id" name="ei_contact_id" class='browser-default'>
                            <?php  
                                if ($result_pro)
                                while ($row = $result_pro->fetch_assoc())
                                {
                                    echo '<option value="' . $row['contact_id'] . '">' . $row['address'] . '</option>';
                                }
                            ?>
					</select>
			</div>
		</div>
		<div class="row">
			<div class="select-box-field col s6">
				<label for="ei_woonadres_nl" class="name">Woonadres NL</label>
				<select id="ei_woonadres_nl" name="ei_woonadres_nl" class='browser-default' onchange="woonadres_changed()">
					<option value="Jaques oppenheim 14">Jaques oppenheim 14</option>
					<option value="Jaques oppenheim 20">Jaques oppenheim 20</option>
					<option value="Haarlem">Haarlem</option>
					<option value="Marco Polostraat Amsterdam">Marco Polostraat Amsterdam</option>
					<option value="Overig">Overig</option>
				</select>
				
			</div>
			<div class="select-box-field col s6">
				<label for="ei_visa" class="name">Visa</label>
				<select id="ei_visa" name="ei_visa" class='browser-default'>
					<option value=""></option>
					<option value="Ja">Ja</option>
					<option value="Nee">Nee</option>
					<option value="In progress">In progress</option>
				</select>
				
			</div>
		</div>
		<div class="row" id="row_overig">
			<div class="input-field col s12">
				<input type="text" id="overig" name="overig" value="">
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="telefoonnummer1" type="text" name="telefoonnummer1" value="">
				<label for="telefoonnummer1">Telefoonnummer 1</label>
			</div>
			<div class="input-field col s6">
				<input id="telefoonnummer2" type="text" name="telefoonnummer2" value="">
				<label for="telefoonnummer2">Telefoonnummer 2</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="email" type="text" name="email" value="">
				<label for="email">E-mail</label>
			</div>
			<div class="input-field col s6">
				<input id="geboortedatum" type="date" name="geboortedatum" value="">
				<label for="geboortedatum">Geboortedatum</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="aankomst_datum" type="date" name="aankomst_datum" value="">
				<label for="aankomst_datum">Indiensttreding</label>
			</div>
			<div class="input-field col s6">
				<input id="vertrek_datum" type="date" name="vertrek_datum" value="">
				<label for="vertrek_datum">Einde contract</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="aankomst_datum2" type="date" name="aankomst_datum2" value="">
				<label for="aankomst_datum2">Indiensttreding 2</label>
			</div>
			<div class="input-field col s6">
				<input id="vertrek_datum2" type="date" name="vertrek_datum2" value="">
				<label for="vertrek_datum2">Einde contract 2</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="ice_telefoon" type="text" name="ice_telefoon" value="">
				<label for="ice_telefoon">ICE telefoon</label>
			</div>
			<div class="input-field col s6">
				<input id="ice_naam" type="text" name="ice_naam" value="">
				<label for="ice_naam">ICE naam</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="computer_login" type="text" name="computer_login" value="">
				<label for="computer_login">Computer login</label>
			</div>
			<div class="input-field col s6">
				<input id="computer_wachtwoord" type="text" name="computer_wachtwoord" value="">
				<label for="computer_wachtwoord">Computer wachtwoord</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="zakelijke_email" type="text" name="zakelijke_email" value="">
				<label for="zakelijke_email">Zakelijke e-mail</label>
			</div>
			<div class="input-field col s6">
				<input id="email_wachtwoord" type="text" name="email_wachtwoord" value="">
				<label for="email_wachtwoord">E-mail wachtwoord</label>
			</div>
		</div>
		
		<div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveEmployeeInfo()">Opslaan</span> </div>
	</form>