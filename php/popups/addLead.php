<?php 

	require '../../common/global.php';

?>
	<form id="contactinfo" class="col s12">
		<input type="hidden" name="contactid" class="contactid" value="">
		<div class="row">
			<div class="input-field col s6">
				<input id="name" type="text" name="name" value="">
				<label for="name" class="active">Naam</label>
			</div>
			<div class="input-field col s6">
				<input id="city" type="text" name="city" value="">
				<label for="city">Stad</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s12">
				<input id="address" type="text" name="address" value="">
				<label for="address">Adres</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				<input id="email" type="text" name="email" value="">
				<label for="email" class="active">E-mailadres</label>
			</div>
			<div class="input-field col s6">
				<input id="phone" type="text" name="phone" value="">
				<label for="phone">Telefoonnummer</label>
			</div>
		</div>

		<div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveContactInfo()">Opslaan</span> </div>
	</form>