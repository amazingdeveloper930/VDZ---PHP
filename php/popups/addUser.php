<form id="userinfo" class="col s12">
					
  <input type="hidden" name="userid" class="userid" value="">
	
  <div class="row">
	<div class="col s6 radiobutton">
	  <img src="https://orders2.vanderzeeuwbouw.nl/images/users/man.jpg" class="avatarpreview">
	  <label>						   
		<input class="addUserGender" name="group1" value="man" type="radio">
		<span>Man</span>							
	  </label>
	</div>
	<div class="col s6 radiobutton">
	<img src="https://orders2.vanderzeeuwbouw.nl/images/users/vrouw.jpg" class="avatarpreview">
	  <label>						   
		<input class="addUserGender" name="group1" value="vrouw" type="radio">
		<span>Vrouw</span>							
	  </label>
	</div>
  </div>	
  <div class="row">
	<div class="input-field col s6">
	  <input id="username" type="text" name="username" value="">
	  <label for="username" class="active">Gebruikersnaam</label>
	</div>
	<div class="input-field col s6">
	  <input id="email" type="text" name="email" value="">
	  <label for="email" class="active">E-mailadres</label>
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
  <div class="row">
	<div class="col s12">
		<label>Kies een rol</label>
		<select id="userrole" name="userrole">      
		<option value="2">Gebruiker</option>
		<option value="1">Beheerder</option>      
		</select>
    
    </div>
  </div>

  <div class="bottombuttons">
	<span class="button waves-effect waves-light btn" onclick="saveUserInfo()">Opslaan</span>
  </div>
						
</form>