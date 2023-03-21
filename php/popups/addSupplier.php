<?php 

	require '../../common/global.php';

?>
	<form id="supplierinfo" class="col s12">
		<input type="hidden" name="supplierid" class="supplierid" value="">
		<div class="row">
			<div class="col s6">
				<label for="soort">Soort</label>
				<select name="soort" id="soort">
					<option value='Hout'>Hout</option>
					<option value='Installatie'>Installatie</option>
					<option value='Staal'>Staal</option>
					<option value='Beton'>Beton</option>
					<option value='Hulpmiddel'>Hulpmiddel</option>
					<option value='Onderaannemer'>Onderaannemer</option>
					<option value='Architect'>Architect</option>
					<option value='Afval'>Afval</option>
					<option value='Algemeen bouwmateriaal'>Algemeen bouwmateriaal</option>
				</select>
				
			</div>
			<div class="input-field col s6">
				<input id="name" type="text" name="name" value="">
				<label for="name" class="name">Naam</label>
			</div>
			<div class="input-field col s6">
				<input id="type" type="text" name="type" value="">
				<label for="type">Account manager</label>
			</div>
			<div class="input-field col s6">
				<input id="accountnumber" type="number" name="accountnumber" value="">
				<label for="accountnumber" class="name">Accountnummer</label>
			</div>
			<div class="input-field col s6">
				<input id="email" type="email" name="email" value="">
				<label for="email" >Email</label>
			</div>
			<div class="input-field col s6">
				<input id="email2" type="email" name="email2" value="">
				<label for="email2" >Email 2</label>
			</div>
			<div class="input-field col s6">
				<input id="phone" type="text" name="phone" value="">
				<label for="phone" >Telefoon</label>
			</div>
			<div class="input-field col s6">
				<input id="mobile_phone" type="text" name="mobile_phone" value="">
				<label for="mobile_phone" >Mobiele telefoon</label>
			</div>
			<div class="col s6">
				<label for="krediet">Krediet</label>
				<select name="krediet" id="krediet">
					<option value='ja'>Ja</option>
					<option value='nee'>Nee</option>
				</select>
				
			</div>
			<div class="col s6">
				<label for="rating">Rating</label>
				<select name="rating" id="rating">
					<option value='1'>1 ster</option>
					<option value='2'>2 sterren</option>
					<option value='3'>3 sterren</option>
					<option value='4'>4 sterren</option>
					<option value='5'>5 sterren</option>
				</select>
				
			</div>
			<div class="input-field col s6">
				<input id="login" type="text" name="login" value="">
				<label for="login" >Login</label>
			</div>
			<div class="input-field col s6">
				<input id="wachtwoord" type="text" name="wachtwoord" value="">
				<label for="wachtwoord" >Wachtwoord</label>
			</div>
		</div>
		
		<div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveSupplierInfo()">Opslaan</span> </div>
	</form>
