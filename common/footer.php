	<script src="<?=$root;?>js/materialize.js" type="text/javascript"></script>	

	<script src="<?=$root;?>js/ckeditor.js" type="text/javascript"></script>

	<script src="<?=$root;?>js/app.js" type="text/javascript"></script>

	<script src="<?=$root;?>js/jquery.toast.min.js" type="text/javascript"></script>

	

	<div class="popup-overlay-very-large"></div>
	<div class="popup-very-large-container">
		<div class="popup very-large">
			<div class="popupheader">

		<div class="title"></div>

		<i onclick="closeVeryLargeModal()" class="material-icons btn-modal-close">close</i>

		</div>

		<div class="contents"></div>

		<div class="logs"></div>

			<div class="popupfooter"></div>
		</div>
	</div>

	
	

	<div class="popup-overlay">
		
	</div>
	

	<div class="popup confirm">

		<div class="text"></div>

		<div class="buttons">

			<span class="button blank" onclick="closeConfirm()">Annuleren</span>

			<span class="button"></span>

		</div>

	</div>

	
	<div class="popup-large-container">
	<div class="popup large">

		<div class="popupheader">

			<div class="title"></div>

			<i onclick="closeModal()" class="material-icons btn-modal-close">close</i>

		</div>

		<div class="contents"></div>
		
		<div class="middle-section"></div>
		<div class="logs"></div>
	</div>
	</div>

	<div class="spin-container">
		<div class="spin-loader">Loading...</div>
	</div>
	<div id="img-modal" class="modal">
	<i onclick="closePrevModal()" class="material-icons btn-modal-close close">close</i>
		<img class="modal-content" id="img01">
	</div>




	

	</body>

	

</html>