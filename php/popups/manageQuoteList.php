<?php 

	require '../../common/global.php';
	require '../../common/sessie_check.php';

?>
	<form id="quotelist" class="col s12">
		<input type="hidden" name="contactid" class="contactid" value="">
  		<input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
		<div class="bottombuttons row">
			<div class='col s6'>
				<span class="button waves-effect waves-light col-6" onclick="addNewQuote()"><i class="material-icons">add</i>Nieuwe offerte toevoegen</span> 
			</div> 
			<div class='col s6'>
				<span class="button waves-effect waves-light col-6" onclick="addNewQuote(2)"><i class="material-icons">add</i>Nieuwe STABU offerte toevoegen</span>
			</div>
		</div>
	</form>