
let fullscreentrigger = false;

let offerte_chapter_data = [];


function saveProfile() {



$("#profilesettings").submit();

	

}



$("#profilesettings").submit(function (e) {

e.preventDefault();



let user = $("#username").val();

let email = $("#email").val();



let pass1 = $("#password").val();

let pass2 = $("#password2").val();

let tags = [];
let myEditor = null;



let errors = "";



if(user == '') { 

errors = "error";

melding('Vul een gebruikersnaam in', 'Rood');

} else if(email == '') { 

errors = "error";

melding('Vul een e-mailadres in', 'Rood');

} else if(pass1 != '') {



if(pass1 != pass2) {

errors = "error";

melding('Wachtwoorden komen niet overeen', 'Rood');	

}

	

}



if(errors == '') {

	

var data=$("#profilesettings").serializeArray();

var fd = new FormData();
for(var index = 0; index < data.length; index ++)
{
	fd.append(data[index]['name'], data[index]['value']);
}

var files = $('#file')[0].files;
if(files.length > 0 ){
	fd.append('file', files[0]);
}

fd.append('data_img_changed', $(".avatarpreview-panel .avatarpreview").attr("data-img-changed"))
 $.ajax({

	type: "POST",

	url: "../php/settings/save_profile.php",

	data: fd,
	contentType: false,
	processData: false,
	dataType: "html",

	success: function(result){



	//alert(result);

	

	if(result == 'Wijzigingen opgeslagen') {

		

	//Alles ging goed

	melding(result,'groen');

		

	} else {

	

	//Er ging iets mis

	melding(result,'rood');

	

	}

					

	}

 });	



}

	

});





function deleteUser(userid) {

	

showConfirm('Deze gebruiker verwijderen?','Verwijderen','red','deleteUserConfirm('+userid+')');	

	

}





function deleteUserConfirm(userid) {



closeConfirm();

var curr_userid = $('.userid').html();



if(curr_userid == userid) {



melding('Je kan niet jezelf verwijderen.','rood');

	

} else {



$.ajax({

	type: "POST",

	url: "../php/settings/delete_user.php",

	data: { 'user': userid, 'curr_user': curr_userid },

	dataType: "html",

	success: function(result){



	//alert(result);

	

	if(result == 'Gebruiker verwijderd.') {

		

	//Alles ging goed

	melding(result,'groen');

	$("tr[userrow="+userid+"]").slideUp();

		

	} else {

	

	//Er ging iets mis

	melding(result,'rood');

	

	}

					

	}

 });



} 

	

}





function addUser() {

	

showEmptyModal('Gebruiker toevoegen','addUser.php');	

	

}





function saveUserInfo() {



var userid = $("#userinfo .userid").val();



let gender = $("#userinfo #username").val();



let user = $("#userinfo #username").val();

let email = $("#userinfo #email").val();



let pass1 = $("#userinfo #password").val();

let pass2 = $("#userinfo #password2").val();



let role = $("#userinfo #userrole").val();



let errors = "";



if($('input.addUserGender:checked').size() < 1) {

errors = "error";

melding('Is dit een man of een vrouw?', 'Rood');	

} else if(user == '') { 

errors = "error";

melding('Vul een gebruikersnaam in', 'Rood');

} else if(email == '') { 

errors = "error";

melding('Vul een e-mailadres in', 'Rood');

} else if(pass1 == '' && userid == '') {

errors = "error";

melding('Vul een wachtwoord in', 'Rood');	

} else if(pass1 != pass2) {

errors = "error";

melding('Wachtwoorden komen niet overeen', 'Rood');	

}

	

if(errors == '') {

	

var data=$("#userinfo").serializeArray();



 $.ajax({

	type: "POST",

	url: "../php/settings/save_userinfo.php",

	data: data,

	dataType: "html",

	success: function(result){



	//alert(result);

	

	if(result == 'Gebruiker opgeslagen.') {

		

	//Alles ging goed

	melding(result,'groen');

	

	if(userid != '') { //Edited a user

	

	$("#gebruikers tr[userrow="+userid+"] td:nth-child(1)").html(user);

	$("#gebruikers tr[userrow="+userid+"] td:nth-child(2)").html(email);

		

	} else { //Added a user

	

	addNewUserTableRow();	

	

	}	

	

	closeModal();

		

	} else {

	

	//Er ging iets mis

	melding(result,'rood');

	

	}

					

	}

 });	



}



};



function addNewUserTableRow() {



$.ajax({

	type: "POST",

	url: "../php/settings/get_last_user.php", 	

	dataType: "json",

	success: function(result){



	for(var i = 0; i < result.length; i++) {						

	

	var id = result[i].id;	

	var username = result[i].username;	

	var email = result[i].email;	

	

	var html = "<tr userrow='"+id+"'><td>"+username+"</td><td>"+email+"</td><td style='width:110px'><div onclick='editUser("+id+")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div><div onclick='deleteUser("+id+")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td></tr>";

	

	$("#gebruikers").append(html);

	$('.tooltipped').tooltip();

	

	}

					

	}

	

});	

	

}





function editUser(userid) {	



prefillModal('Gebruiker wijzigen','addUser.php').then(function() {



$.ajax({

	type: "POST",

	url: "../php/settings/get_user.php", 

	data: {userid:userid},

	dataType: "json",

	success: function(result){



	for(var i = 0; i < result.length; i++) {						

	

	var gender = result[i].img;

	var username = result[i].username;	

	var email = result[i].email;

	var accountlevel = result[i].account_level;

	

	$("#userinfo .userid").val(userid);

	$('input.addUserGender[value='+gender+']').prop('checked', true);;

	$("#userinfo #username").val(username);

	$("#userinfo #email").val(email);	

	$("#userinfo #userrole").val(accountlevel);

	

	showPrefilledModal();	

	

	}

					

	}

	

});	



});



}











function deleteChapter(id)
{

	showConfirm('Hoofdstuk en alle regels verwijderen?', 'Verwijderen', 'red', "deleteChapterConfirm('" + id + "')");
	
}

function deleteChapterConfirm(id)
{
	closeConfirm();
	
	$("#chapter_" + id).remove();
	getTotal();
}

function deleteLine(chapter_id, line_id)
{
	$("#chapter_" + chapter_id + " #line_" + line_id).remove();
	getTotal();
}

function moveupLine(chapter_id, line_id)
{
	var currentItem = $("#chapter_" + chapter_id + " #line_" + line_id);
	currentItem.insertBefore(currentItem.prev());
}

function deleteMateriaal(chapter_id, line_id, materiaal_id)
{
	$("#material-item-" + materiaal_id).remove();
	getTotal(chapter_id, line_id);
}

function deleteArbeid(chapter_id, line_id, arbeid_id)
{
	$("#arbeid-item-" + arbeid_id).remove();
	getTotal(chapter_id, line_id);
}

function moveupChapter(id)
{
	var currentItem = $("#chapter_" + id);
	currentItem.insertBefore(currentItem.prev());
}

function toggleChapter(chapter_id)
{
	var currentItem = "#chapter_" + chapter_id;
	$(currentItem).toggleClass("showLineData");
	if(!$(currentItem).hasClass('editedLineData'))
	{
		showChapter(chapter_id);
	}
	$(currentItem).addClass('editedLineData');
	
	if($(currentItem).hasClass("showLineData"))
	{
		$(currentItem + " .chapter-content").slideDown();
	}
	else{
		$(currentItem + " .chapter-content").slideUp();
	}
}

// this is to show data in chapter when user presses expand more button for the first time

function showChapter(chapter_id)
{
	for(var jdex = 0; jdex < offerte_chapter_data.length; jdex ++)
	{
		var chapter = offerte_chapter_data[jdex];
		if(chapter_id == chapter['chapter_id'])
		{
			if(chapter['line_data'].length == 0)
			{
				addLine(chapter_id);
			}
			else{
				for(var index = 0; index < chapter['line_data'].length; index ++){
					addLine(chapter_id, chapter['line_data'][index]);
					
				}
				
			}
		}
	}
		
}

function calculate()
{
	let flag = false;
	var total = 0
	let vat = 0;
	let vat_per_percent = {"P0.09" : 0, "P0.21" : 0};

	$(".calculating-box .total").html("---");
	$(".calculating-box .vat-percent").html("");
	$(".calculating-box .vat-excel").html("---");
	$(".calculating-box .total-excel").html("---");


	$(".chapter-line").each(function(){
		var subtotal = $(this).find(".chapter-line-subtotal").val();
		subtotal = subtotal.replace(/\�/g, '');
		subtotal = subtotal.replace(/\./g, '');
		subtotal = subtotal.replace(/\,/g, '.');
		// if(subtotal === undefined)
		// 	subtotal = 0;
		var vatpercent = $(this).find(".chapter-line-vat").val();
		if(vatpercent === undefined)
			vatpercent = 0;
		if($.isNumeric(subtotal))
		{
			total += Number(subtotal);
			vat += Number(vatpercent) * Number(subtotal) / (1 + Number(vatpercent));
			vat_per_percent["P" + Number(vatpercent)] += Number(vatpercent) * Number(subtotal) / (1 + Number(vatpercent));
		}
		else{	
			flag = true;
		}
	});
	$(".calculating-box .btw_row_2").hide();
	$(".calculating-box .total").html(addCommas(total.toFixed(2).replace(/\./g, ',')));
		$(".calculating-box .vat-percent").html("");
		$(".calculating-box .vat-excel").html("0");
		var row = 1;
		if(vat_per_percent["P0.09"]){
			$(".calculating-box .btw_row_" + row + " .vat-percent").html('&nbsp;&nbsp;' + "9%");
			$(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_per_percent["P0.09"].toFixed(2).replace(/\./g, ',')));
			row++;
		}
			
		if(vat_per_percent["P0.21"]){
			$(".calculating-box .btw_row_" + row + " .vat-percent").append("21%");
			if(vat_per_percent["P0.09"])
				$(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_per_percent["P0.21"].toFixed(2).replace(/\./g, ',')));
			else
				$(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_per_percent["P0.21"].toFixed(2).replace(/\./g, ',')));
			if(row == 2)
				$(".calculating-box .btw_row_2").show();
		}
			
		
		$(".calculating-box .total-excel").html(addCommas((total - vat).toFixed(2).replace(/\./g, ',')));
}






function getTotal(chapter_id = null, line_id = null) {

	var factor = $("#offerte_factor").val();
	var rate = $("#offerte_rate").val();
	if(factor != '')
	{
		factor = getRealNumber(factor);
	}
	else 
		factor = 0;
	factor = factor / 100;
	if(rate != '')
	{
		rate = getRealNumber(rate);
	}
	else
		rate = 0;
	if(line_id == null)
	{
		for(var index = 0; index < $(".chapter-container").length; index ++)
		{
			var item_line_id = $($(".chapter-container")[index]).find(".chapter-line-id").val();
			if(item_line_id != null)
				getTotal(null, item_line_id);
		}
	}
	else{
		var container = "#line_" + line_id;
		var total = 0;
		var materiaal_total = 0;
		var winst_total = 0;
		if($(container + " .chapter-line-am-option").val() == 'nee')
		{
			var ar_value = getRealNumber($(container + " .chapter-line-total").val());
			// ar_value = getRealNumber(ar_value);
			winst_total = ar_value  * factor;

			var profit = $(container + " .chapter-line-profit").val();
			if(profit == "0")
				total = ar_value + winst_total;
			else 
				total = ar_value;
		}
		else{
			var ar_value_total = 0;
			for(var index = 0; index < $(container + " .line-row-arbeid-box .arbeid-item").length; index ++)
			{
				var item_str = $(container + " .line-row-arbeid-box .arbeid-item")[index];
				var quantity = $(item_str).find(".arbeid_quantity").val();

				quantity = (stuks != '' ? getRealNumber(quantity) : 0);
				var a_t = quantity * rate;
				ar_value_total += quantity;
				$(item_str).find(".label-arbeid-item-total").text(addCommas(parseFloat(a_t).toFixed(2)));
			}

			$(container + " .chapter-line-arbeid").val(addCommas(parseFloat(ar_value_total)));
			var arbeid_value = $(container + " .chapter-line-arbeid").val();
			var arbeid_total = getRealNumber(arbeid_value) * rate;
			$(container + " .label-arbeid-total").text(addCommas(parseFloat(arbeid_total).toFixed(2)));

			
			for(var index = 0; index < $(container + " .line-row-materiaal-box .material-item").length; index ++)
			{
				var item_str = $(container + " .line-row-materiaal-box .material-item")[index];
				var stuks = $(item_str).find(".materiaal_stuks").val();
				var price = $(item_str).find(".materiaal_price").val();

				stuks = (stuks != '' ? getRealNumber(stuks) : 0);
				price = (price != '' ? getRealNumber(price) : 0);
				var m_t = stuks* price;
				materiaal_total += m_t;
				$(item_str).find(".label-materiaal-item-total").text(addCommas(parseFloat(m_t).toFixed(2)));
			}
			$(container + " .label-materiaal-total").text(addCommas(parseFloat(materiaal_total).toFixed(2)));
			winst_total  = (materiaal_total + arbeid_total) * factor;
			total = arbeid_total + materiaal_total + winst_total;
		}
		$(container + " .label-winst-total").text(addCommas(parseFloat(winst_total).toFixed(2)));
		$(container + " .label-total-value").text(addCommas(parseFloat(total).toFixed(2)));

	}

}
function saveDefaultQuote()
{

	var userid 	   = $("#quoteinfo .userid").val();
	var quoteData = {};
	var chapterData = [];
	for(var index = 0; index < $(".chapter-container input[name='chapter_id[]']").length; index ++)
	{
		
		var chapter_id = $(".chapter-container input[name='chapter_id[]']").eq(index).val();
		var chapter_name = $(".chapters #chapter_" + chapter_id + " input[name='chapter_name[]']").val();
		var line_datas = [];
		for( var jdex = 0; jdex < $("#chapter_" + chapter_id + " .chapter-line-id").length; jdex ++)
		{
			var line_id = $("#chapter_" + chapter_id + " .chapter-line-id").eq(jdex).val();
			var line_data = {
				line_title : $("#line_" + line_id + " .chapter-line-header").val(),
				line_descr : $("#line_" + line_id + " .chapter-line-description").val(),
				quanitty  : $("#line_" + line_id + " .chapter-line-quantity").val(),
				unit : $("#line_" + line_id + " .chapter-line-unit").val(),
				price      : $("#line_" + line_id + " .chapter-line-price").val(),
				vat        : $("#line_" + line_id + " .chapter-line-vat").val(),
				subtotal   : $("#line_" + line_id + " .chapter-line-subtotal").val(),
				sort_order : jdex,
				price_inc: $("#line_" + line_id + " .chapter-line-price-inc").val()
			};
			line_datas.push(line_data);
			
		}

		chapterData.push({
			chapter_name : chapter_name,
			line_data    : line_datas
		});
	}
	quoteData = {
		chapterData : chapterData
	};

	$.ajax({
		type: "POST",
		url: "../php/settings/save_default_quote.php",
		data: quoteData,
		dataType: "json",
		success: function(result) {
			if(result['message'] == 'Regels opgeslagen.') {
				//Alles ging goed
				melding(result['message'], 'groen');
				
			} else {
				//Er ging iets mis
				melding(result['message'], 'rood');
			}
		},
		error : function(e1,e2, e3) {

		}
	});
	
}



function manageDefaultQuote()
{
	prefillVeryLargeModal('Standaard offerte regels', 'addQuote.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/settings/get_default_quote_details.php",
			dataType: "html",
			success: function(result) {
				if(!Array.isArray(result)) result = $.parseJSON(result);
				var title = '<div class="save-button"><span class="button waves-effect waves-light btn" onclick="saveDefaultQuote()">Regels opslaan</span></div>';
				$(".popup.very-large .title").append(title);
				for(var index = 0; index < result.length; index ++)
				{
					chapter_id = result[index]['chapter_id'];
					var chapterHtml =
                                    "<div class='chapter-container' id='chapter_" + chapter_id + "'>" +
                                    "<input type='hidden' name='chapter_id[]' value='" + chapter_id + "'>" +
                                    "<div class='chapter-wrapper'><div class='chapter-header row'><div class='col s4'>" +
                                    "<input type='text' name='chapter_name[]' value='" + result[index]['chapter_name'] + "' placeholder='Hoofdstuk'>" +
                                    "</div><div class='col s1'><p>Aantal</p></div><div class='col s1'></div><div class='col s1'><p>Prijs ex.BTW</p></div><div class='col s1'><p>BTW</p></div><div class='col s1'><p>Prijs inc.BTW</p></div><div class='col s2'><p>Subtotaal</p></div><div class='col icon-list'>" +
                                    "<div onclick='deleteChapter(\"" + chapter_id + "\")' class='actiebutton' data-position='top'><i class='material-icons'>delete</i></div>" +
									
                                    "<div onclick='moveupChapter(\"" + chapter_id + "\")' class='actiebutton tooltipped moveup-btn' data-position='top' data-tooltip=''><i class='material-icons'>arrow_upward</i></div>" +
                                    "<div onclick='addLine(\"" + chapter_id + "\")' class='actiebutton tooltipped' data-position='top' data-tooltip=''><i class='material-icons'>add</i></div>" +
									"<div onclick='toggleChapter(\"" + chapter_id + "\")' class='actiebutton tooltipped expand-btn' data-position='top' data-tooltip=''><i class='material-icons'>expand_more</i></div>" +
                                    "</div></div>" +
                                    "<div class='chapter-content'></div>" +
                                    "</div></div>";

					$("#quoteinfo .chapters").append(chapterHtml);

					for(var jdex = 0; jdex < result[index]['line_data'].length; jdex ++)
					{
						var line_data = result[index]['line_data'][jdex];

						var line_id = makeid(15);
						var lineHtml = "<div class='row chapter-line' id='line_" + line_id + "'>" + 
										"<div class='input-field col s4'><input type='hidden' class='chapter-line-id' value='" + line_id + "' />" +
										"<input type='text' class='chapter-line-header' value='" + line_data['line_title'] + "' placeholder='Titel van deze regel' )'/></div>" + 
										"<div class='input-field col s1'><input type='text' placeholder='Aantal stuks' min='0' value='" + line_data['quanitty'] + "' class='chapter-line-quantity special-number' onchange='convertNumber(this, 2)'  oninput = 'getTotal(\"" + chapter_id + "\", \"" + line_id + "\")'/></div>" +
										"<div class='input-field col s1'><select class='chapter-line-unit'><option value=''></option><option value='m2' " + ( line_data['unit'] == 'm2' ? 'selected' : '') + ">m2</option><option value='m3' " + ( line_data['unit'] == 'm3' ? 'selected' : '') + ">m3</option><option value='m' " + ( line_data['unit'] == 'm' ? 'selected' : '') + ">m</option></select></div>" + 
										"<div class='input-field col s1 nopadding-panel'>&euro;<input type='text' placeholder='Prijs ex.BTW' min='0' value='" + line_data['price'] + "' class='chapter-line-price special-number' onchange='convertNumber(this)' oninput = 'getTotal(\"" + chapter_id + "\", \"" + line_id + "\")'/></div>" +
										"<div class='input-field col s1'><select class='chapter-line-vat'  onchange = 'getTotal(\"" + chapter_id + "\", \"" + line_id + "\")'><option value='0.09' " + ( line_data['vat'] == 0.09 ? 'selected' : '') + ">9 %</option><option value='0.21' " + ( line_data['vat'] == 0.21 ? 'selected' : '') + ">21 %</option></select></div>" +
										"<div class='input-field col s1 nopadding-panel'>&euro;<input type='text' placeholder='Prijs ex.BTW' min='0' value='" + line_data['price_inc'] + "' class='chapter-line-price-inc' readonly/></div>" +
										"<div class='input-field col s2'>&euro;<input type='text' class='chapter-line-subtotal'   value='" + line_data['subtotal'] + "' readonly/></div>" +
										"<div class='col s6 nopadding'>" + 
										"<div onclick='deleteLine(\"" + chapter_id + "\", \"" + line_id + "\")' class='actiebutton' data-position='top' ><i class='material-icons'>delete</i></div>" + 
										"<div onclick='moveupLine(\"" + chapter_id + "\", \"" + line_id + "\")' class='actiebutton tooltipped moveup-btn' data-position='top' data-tooltip=''><i class='material-icons'>arrow_upward</i></div></div>" +
										"<div class='col s4'><textarea type='text' class='chapter-line-description materialize-textarea' placeholder='Beschrijving van deze regel.'>" + line_data['line_descr'] + "</textarea></div></div>" ;

						$("#chapter_" + chapter_id +" .chapter-content").append(lineHtml);
						
						M.textareaAutoResize($('#line_' + line_id + ' textarea.chapter-line-description'));
						// $('#line_' + line_id + ' textarea.chapter-line-description').trigger({type: 'keypress', which: 80, keyCode: 80});
						
					}
				}
				if(result.length == 0)
				{
					addChapter()

				}
				calculate();
				$('.chapter-line-vat').formSelect();
				$('.chapter-line-unit').formSelect();
				
			}
			
		})
	});
	showPrefilledVeryLargeModal('max');
}



function manageDefaultQuoteIntro()
{
	prefillVeryLargeModal('Standaard intoductietekst', 'addDefaultQuoteText.php').then(function() {
		var data = {
			mode : '_INTRO'
		};
		$.ajax({
			type: "POST",
			url: "../php/settings/get_default_quote_text.php",
			data: data,
			dataType: "json",
			success: function(result) {
				if(result['message'] == 'Successfully selected')
				var title = '<div class="save-button"><span class="button waves-effect waves-light btn" onclick="saveDefaultQuoteIntro()">Tekst opslaan</span></div>';
				$(".popup.very-large .title").append(title);
				$("#rich-texteditor").html(result['text']);
				ClassicEditor
					.create(document.querySelector('#rich-texteditor'), {
				
				toolbar: {
					items: [
						'bold',
						'italic',
						'bulletedList',
						'numberedList',
						'outdent',
						'indent',
						'undo',
						'redo',
						'underline',
						'strikethrough'
					]
				},
				language: 'en',
				licenseKey: '',
				
				
			})
					.then( editor => {
						console.log( 'Editor was initialized', editor );
						myEditor = editor;
					} );
			},
			error: function(e1, e2, e3)
			{

			}
		});
	});
	showPrefilledVeryLargeModal();
}

function manageDefaultQuoteVoor()
{
	prefillVeryLargeModal('Standaard Voorwaarden', 'addDefaultQuoteText.php').then(function() {
		var data = {
			mode : '_VOOR'
		};
		$.ajax({
			type: "POST",
			url: "../php/settings/get_default_quote_text.php",
			data: data,
			dataType: "json",
			success: function(result) {
				if(result['message'] == 'Successfully selected')
				var title = '<div class="save-button"><span class="button waves-effect waves-light btn" onclick="saveDefaultQuoteVoor()">Tekst opslaan</span></div>';
				$(".popup.very-large .title").append(title);
				$("#rich-texteditor").html(result['text']);
				// $(".rich-texteditor").ckeditor(); 
				ClassicEditor
					.create(document.querySelector('#rich-texteditor'), {
				
				toolbar: {
					items: [
						'bold',
						'italic',
						'bulletedList',
						'numberedList',
						'outdent',
						'indent',
						'undo',
						'redo',
						'underline',
						'strikethrough'
					]
				},
				language: 'en',
				licenseKey: '',
				
				
			})
					.then( editor => {
						console.log( 'Editor was initialized', editor );
						myEditor = editor;
					} );
			},
			error: function(e1, e2, e3)
			{

			}
		});
	});
	showPrefilledVeryLargeModal();
}

function manageTagForOfferte()
{
	window.location.href = "/tags/";
}

function saveDefaultQuoteIntro()
{
	var text = myEditor.getData();
	var data = {
		text : text,
		mode : '_INTRO'
	};
	$.ajax({
		type: "POST",
		url: "../php/settings/save_default_quote_text.php",
		dataType: "html",
		data: data,
		success: function(result) {
			if(result == 'default quote saved')
			{
				melding(result,'groen');
			}
			else
				melding(result, 'Rood');
		}
	});
}

function saveDefaultQuoteVoor()
{
	var text = myEditor.getData();
	var data = {
		text : text,
		mode : '_VOOR'
	};
	$.ajax({
		type: "POST",
		url: "../php/settings/save_default_quote_text.php",
		dataType: "html",
		data: data,
		success: function(result) {
			if(result == 'default quote saved')
			{
				melding(result,'groen');
			}
			else
				melding(result, 'Rood');
		}
	});
}



function fileselected()
{
    var files = $('#file')[0].files;
    if(files.length > 0)
    {
        $(".btn-filelog").addClass('file_selected');
    }
    else{
        $(".btn-filelog").removeClass('file_selected');
    }
}





function addChapter(chapter = [])
{
	var id;
	if($.isEmptyObject(chapter))
	{
		id = makeid(15);
   	 	
	}
	else{
		id = chapter['chapter_id'];
	}

	var chapterHtml =
        "<div class='chapter-container' id='chapter_" + id + "'>" +
        "<input type='hidden' name='chapter_id[]' value='" + id + "'>" +
        "<div class='chapter-wrapper'><div class='chapter-header row'><div class='col s3'>" +
        "<input type='text' name='chapter_name[]' value='' placeholder='Hoofdstuk'>" +
        "</div><div class='col s1'><p>Arbeid/materiaal?</p></div><div class='col s1'><p>Percentageregel?</p></div><div class='col s1'><p>Eenheid</p></div><div class='col s1'><p>Totaal ex. BTW</p></div><div class='col icon-list'>" +
        "<div onclick='deleteChapter(\"" + id + "\")' class='actiebutton' data-position='top' ><i class='material-icons'>delete</i></div>" +
        "<div onclick='moveupChapter(\"" + id + "\")' class='actiebutton tooltipped moveup-btn' data-position='top' data-tooltip=''><i class='material-icons'>arrow_upward</i></div>" +
        "<div onclick='addLine(\"" + id + "\")' class='actiebutton tooltipped' data-position='top' data-tooltip=''><i class='material-icons'>add</i></div>" +
		"<div onclick='toggleChapter(\"" + id + "\")' class='actiebutton tooltipped expand-btn' data-position='top' data-tooltip=''><i class='material-icons'>expand_more</i></div>" +
        "</div></div>" +
        "<div class='chapter-content'></div>" +
        "</div></div>";
		
	$("#offerteinfo .chapters").append(chapterHtml);
	
	if(chapter.length == 0)
	{
		addLine(id);
	}
	else{
		if(chapter['line_data'].length == 0)
		{
			// addLine(id);
		}
		else{
			// for(var index = 0; index < chapter['line_data'].length; index ++){
			// 	addLine(id, chapter['line_data'][index]);
				
			// }
			
		}
		$("#offerteinfo .chapters #chapter_" + id + " input[name='chapter_name[]']").val(chapter['chapter_title']);
	}
	$(".popup.very-large select").formSelect();
}

function closeText(ele)
{
	var item = $(ele).parent().parent().find('.chapter-line-description');
	if(item.is(':visible'))
	{
		item.hide();
	}
	else{
		item.show();
	}
}

function setOfferteLabel(line_id)
{
	var item = "#line_" + line_id; 
	var text = $(item + " .chapter-line-header").val();
	if(text == undefined)
	text = "";
	var num = text.split(' ')[0].replace(/[^\d.-]/g, '');
	text = text.substring(num.length);
	text = text.toLowerCase();
	$(item + " .line-row-arbeid .label-arbeid-header").text(num + " Arbeid " + text);
	$(item + " .line-row-materiaal .label-materiaal-header").text(num + " Materiaal " + text);
	$(item + " .line-row-winst .label-winst-header").text(num + " Winst & risico");
}

function setAMOption(line_id)
{
	var chapter_line = ".chapters #line_" + line_id;
	var am_option = $(chapter_line + " .chapter-line-am-option").val();
	if(am_option == 'nee')
	{
		$(chapter_line).addClass('line-am-arbeid');
	}
	else{
		$(chapter_line).removeClass('line-am-arbeid');
	}

}
function setProfit(line_id)
{
	var chapter_line = ".chapters #line_" + line_id;
	var profit_option = $(chapter_line + " .chapter-line-profit").val();
	if(profit_option == '0')
	{
		$(chapter_line).removeClass('line-profit');
		
	}
	else{
		
		$(chapter_line).addClass('line-profit');
	}
	

}
function changedAMOption(line_id)
{
	setAMOption(line_id);
	getTotal(null, line_id);
}
function changedProfit(line_id)
{
	setProfit(line_id);
	var chapter_line = ".chapters #line_" + line_id;
	$(chapter_line + " .chapter-line-total").val("");
	getTotal(null, line_id);
}
function setUnit(line_id)
{
	var chapter_line = ".chapters #line_" + line_id;
	var unit = $(chapter_line + " .chapter-line-unit").val();
	
	$(chapter_line + " .line-row-total .label-total-header").text('Totaal per ' + unit);
}
function changedUnit(line_id)
{
	setUnit(line_id);
}

function getLineProfitSelectOptionsHtml()
{
	var html = "<option value='0'>Nee</option>";
	for(var index = 1; index <= 10; index ++ )
	{
		html += "<option value='" + (index / 100) + "'>" + index + "%</option>";
	}
	for( index = 1; index <= 10; index ++ )
	{
		html += "<option value='" + (-1 * index / 100) + "'>" + (-1 * index) + "%</option>";
	}
	return html;
}


function addLine(id, line_data = []) {
    var line_id = makeid(15);
	if(!($.isEmptyObject(line_data)))
		line_id = line_data['id'];

	if($.isEmptyObject(line_data) && !$("#chapter_" + id).hasClass('showLineData'))
	{
		toggleChapter(id);
	}
    var newLineHtml = "<div class='chapter-line' id='line_" + line_id + "'><div class='row'>" +
        "<div class='input-field col s3'><input type='hidden' class='chapter-line-id' value='" + line_id + "' />" +
        "<input type='text' class='chapter-line-header' placeholder='Titel van deze regel' oninput='setOfferteLabel(\"" + line_id + "\")'/>" + 
		"<div onclick='closeText(this)' class='actiebutton btn-closetext' data-position='top' data-tooltip=''><i class='material-icons'>format_align_justify</i></div>" +

		"<div onclick='editLineTag(\"" + line_id + "\")' class='actiebutton btn-edittag' data-position='top' data-tooltip=''><i class='material-icons'>label_outline</i></div>" +
		"</div>" +
        "<div class='input-field col s1'><select class='chapter-line-am-option'  onchange='changedAMOption(\"" + line_id + "\")'><option value='ja'>Ja</option><option value='nee'>Nee</option></select></div>" +
		"<div class='input-field col s1'><select class='chapter-line-profit' onchange='changedProfit(\"" + line_id + "\")'>" + getLineProfitSelectOptionsHtml() + "</select></div>" +
        "<div class='input-field col s1'><select class='chapter-line-unit' onchange='changedUnit(\"" + line_id + "\")'><option value='stuk'>stuk</option><option value='m1'>m1</option><option value='m2'>m2</option><option value='m3'>m3</option></select></div>" +
        "<div class='input-field col s1'><input type='text' class='chapter-line-total' onchange='convertNumber(this)' oninput = 'getTotal(\"" + id + "\", \"" + line_id + "\")'  min='0'/></div>" +
        
        "<div class='col s5 nopadding'>" +
        "<div onclick='deleteLine(\"" + id + "\", \"" + line_id + "\")' class='actiebutton' data-position='top'><i class='material-icons'>delete</i></div>" +
        "<div onclick='moveupLine(\"" + id + "\", \"" + line_id + "\")' class='actiebutton tooltipped moveup-btn' data-position='top' data-tooltip=''><i class='material-icons'>arrow_upward</i></div></div>" +
        "<div class='col s3' style='clear:both;'><textarea type='text' class='chapter-line-description materialize-textarea' placeholder='Beschrijving van deze regel.'></textarea></div></div>" + 
		"<div class='row line-row-tag-box'>" + 
			"<div class='line-row-tag-standard'>" +
				"<div class='col s2'><p class='label-tag-standard-header'>Kies een standaard tag</p></div>" +
				"<div class='input-field col s1'><select class='chapter-line-tag-standard'>" + getTagSelectOptionsHtml('STANDARD') + "</select></div>" + 
			"</div>" + 
			"<div class='line-row-tag-fase'>" + 
				"<div class='col s2'><p class='label-tag-fase-header'>Kies een fase tag</p></div>" +
				"<div class='input-field col s1'><select class='chapter-line-tag-fase'>" + getTagSelectOptionsHtml('FASE') + "</select></div>" + 
			"</div>" + 
		"</div>" + 
		"<div class='row line-row-arbeid-box'>" +
		"<div class='arbeid-header'>" + 
			"<div class='col s3'>Arbeid</div>" + 
			"<div class='col s1'>&nbsp;</div>" + 
			"<div class='col s1'>Aantal</div>" + 
			"<div class='col s1'>Totaal</div>" + 
			"<div class='col s6 nopadding' style='margin-top: -20px'>" +
			"<div onclick='addArbeid(\"" + id + "\", \"" + line_id + "\")' class='actiebutton' data-position='top'><i class='material-icons'>add</i></div></div></div>" +
			"<div class='arbeid-content'>" + 
		
			"</div>" +
		"</div>" + 
		"<div class='row line-row-arbeid'><div class='col s3'><p class='label-arbeid-header'></p></div>" +
		"<div class='col s1'>&nbsp;</div>" + 
		"<div class='col s1'><input type='text' class='chapter-line-arbeid' readonly onchange='convertNumber(this, 2)' oninput = 'getTotal(\"" + id + "\", \"" + line_id + "\")'/></div>" + 
		"<div class='col s1'><p class='label-arbeid-total'></p></div>" + 
		"</div>" +
		"<div class='row line-row-materiaal-box'>" + 
		"<div class='materiaal-header'>" + 
			"<div class='col s3'>Materiaal</div>" + 
			"<div class='col s1'>Stuks per eenheid</div>" + 
			"<div class='col s1'>Prijs</div>" + 
			"<div class='col s1'>Totaal</div>" + 
			"<div class='col s6 nopadding' style='margin-top: -20px'>" +
			"<div onclick='addMateriaal(\"" + id + "\", \"" + line_id + "\")' class='actiebutton' data-position='top'><i class='material-icons'>add</i></div>" +
		"</div></div>" +
		"<div class='materiaal-content'>" + 
		
		"</div>" +
		"</div>" + 
		
		"<div class='row line-row-materiaal'>" + 
		"<div class='col s3'><p class='label-materiaal-header'></p></div>" +
		"<div class='col s3'>&nbsp;</div>" +
		"<div class='col s1'><p class='label-materiaal-total'></p></div>" + 
		"</div>" + 
		"<div class='row line-row-winst'>" + 
		"<div class='col s3'><p class='label-winst-header'></p></div>" + 
		"<div class='col s3'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-winst-total'></p></div>" + 
		"</div>" +
		"<div class='row line-row-total'>" + 
		"<div class='col s3'><p class='label-total-header'></p></div>" +
		"<div class='col s3'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-total-value'></p></div>"
		"</div>" + 
		"</div>";
    $("#chapter_" + id + " .chapter-content").append(newLineHtml);
	if($.isEmptyObject(line_data))
	{
		addMateriaal(id, line_id);
		addArbeid(id, line_id);
	}
	else{
		for(var index = 0; index < line_data['materiaal_data'].length; index ++)
			addMateriaal(id, line_id, line_data['materiaal_data'][index]);
		for(var index = 0; index < line_data['arbeid_data'].length; index ++)
			addArbeid(id, line_id, line_data['arbeid_data'][index]);
		var chapter_line = ".chapters #line_" + line_id;
		$(chapter_line + " .chapter-line-am-option").val(line_data['line_am_option']);	
		$(chapter_line + " .chapter-line-header").val(line_data['line_title']);
		$(chapter_line + " .chapter-line-description").val(line_data['line_description']);
		$(chapter_line + " .chapter-line-unit").val(line_data['line_unit']);
		$(chapter_line + " .chapter-line-total").val(line_data['line_total']);
		$(chapter_line + " .chapter-line-arbeid").val(line_data['arbeid']);
		$(chapter_line + " .chapter-line-profit").val(line_data['line_profit']);
		$(chapter_line + " .chapter-line-tag-standard").val(line_data['standard_tag_id']);
		$(chapter_line + " .chapter-line-tag-fase").val(line_data['fase_tag_id']);
	}


	setOfferteLabel(line_id);
	setUnit(line_id);
	setAMOption(line_id);
	setProfit(line_id);
	getTotal(id, line_id);
	
    $(".popup.very-large select").formSelect();
 
}

function addArbeid(chapter_id, line_id, arbeid_data = [])
{
	var arbeid_id = makeid(15);
	if(!($.isEmptyObject(arbeid_data)))
	arbeid_id = arbeid_data['id'];
	var newLineHtml = "<div class='arbeid-item' id='arbeid-item-" + arbeid_id + "'>" +
	"<div class='col s3'><input type='text' class='arbeid_title'/></div>" + 
	"<div class='col s1'>&nbsp;</div>" +
	"<div class='col s1'><input type='text' class='arbeid_quantity' onchange='convertNumber(this, 2)' oninput='getTotal(\"" + chapter_id + "\", \"" + line_id + "\")' /></div>" +
	"<div class='col s1'><p class='label-arbeid-item-total'></p></div>" +
	"<div class='col nopadding'><div onclick='deleteArbeid(\"" + chapter_id + "\", \"" + line_id + "\", \"" + arbeid_id + "\")' class='actiebutton deletearbeid-btn' data-position='top'><i class='material-icons'>delete</i></div></div>" +
	"</div>";
	$("#line_" + line_id + " .arbeid-content").append(newLineHtml);
	if(!($.isEmptyObject(arbeid_data))){
		$("#arbeid-item-" + arbeid_id  + " .arbeid_title").val(arbeid_data['arbeid_title']);
		$("#arbeid-item-" + arbeid_id  + " .arbeid_quantity").val(arbeid_data['quantity']);
	}
}

function addMateriaal(chapter_id, line_id, material_data = [])
{
	var material_id = makeid(15);
	if(!($.isEmptyObject(material_data)))
		material_id = material_data['id'];
	var newLineHtml = "<div class='material-item' id='material-item-" + material_id + "'>" +
	"<div class='col s3'><input type='text' class='materiaal_title'/></div>" + 
	"<div class='col s1'><input type='text' class='materiaal_stuks' onchange='convertNumber(this, 2)' oninput='getTotal(\"" + chapter_id + "\", \"" + line_id + "\")' /></div>" +
	"<div class='col s1'><input type='text' class='materiaal_price' onchange='convertNumber(this)' oninput='getTotal(\"" + chapter_id + "\", \"" + line_id + "\")' /></div>" +
	"<div class='col s1'><p class='label-materiaal-item-total'></p></div>" +
	"<div class='col nopadding'><div onclick='deleteMateriaal(\"" + chapter_id + "\", \"" + line_id + "\", \"" + material_id + "\")' class='actiebutton deletemateriaal-btn' data-position='top'><i class='material-icons'>delete</i></div></div>" +
	"</div>";
	$("#line_" + line_id + " .materiaal-content").append(newLineHtml);
	if(!($.isEmptyObject(material_data))){
		$("#material-item-" + material_id  + " .materiaal_title").val(material_data['materiaal_title']);
		$("#material-item-" + material_id  + " .materiaal_stuks").val(material_data['stuks']);
		$("#material-item-" + material_id  + " .materiaal_price").val(material_data['price']);
	}
		
}

function manageDefaultOfferte(version = 1)
{
	prefillVeryLargeModal('Standaard offerte regels', 'addDefaultOfferte.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/settings/get_default_offerte_details.php",
      data : {
				version : version
			},
			dataType: "json",
			success: function(result_array) {
				var result = result_array['chapter_list'];
				offerte_chapter_data = result;
				var factor = result_array['factor'];
				var rate = result_array['rate'];
				var inkoop = result_array['inkoop'];
				var kosten = result_array['kosten'];
				tags = result_array['tags'];
				var title ="";
				if(factor != '')
				{
					factor = getRealNumber(factor);
				}
				else 
					factor = 0;
				factor = factor * 100;
				factor = addCommas(parseFloat(factor).toFixed(2));
				title += '<div class="offerte_header_container"><label>Winst & risico factor %</label><input type="text" onchange="convertNumber(this, 2)" oninput = "getTotal()" id="offerte_factor" value="' + factor + '"/></div>';

				title += '<div class="offerte_header_container"><label>Algemene kosten %</label><input type="text" onchange="convertNumber(this, 2)" id="offerte_kosten" value="' + kosten + '"/></div>';

				title += '<div class="offerte_header_container"><label>Arbeid kosten</label><input type="text" onchange="convertNumber(this)" oninput = "getTotal()" id="offerte_rate" value="' + rate + '"/></div>';

				title += '<div class="offerte_header_container"><label>Arbeid inkoop</label><input type="text" onchange="convertNumber(this)" id="offerte_inkoop" value="' + inkoop + '"/></div>';

				
				title += '<div class="btn-quote-fullscreen tooltipped" onclick="trggerFullscreen()"  class="actiebutton tooltipped" data-position="top" data-tooltip="Fullscreen"><i class="material-icons">fullscreen</i></div>';

				title += '<div class="save-button" ><span class="button waves-effect waves-light btn" id="save-default-offerte" onclick="saveDefaultOfferte(' + version + ')">Regels opslaan</span></div>';

				$(".popup.very-large .title").append(title);

				for(var index = 0; index < result.length; index ++)
				{
					addChapter(result[index]);
				}
				if(result.length == 0)
				{
					addChapter();
				}
				trggerFullscreen(1);
				showPrefilledVeryLargeModal('max');
			},
			error: function(e1, e2, e3)
			{

			}
		});
	});
}

function saveDefaultOfferte(version = 1)
{

	var userid 	   = $("#offerteinfo .userid").val();
	var offerteData = {};
	var chapterData = [];
	var rate = $("#offerte_rate").val();
	var factor = $("#offerte_factor").val();

	if(factor != '')
	{
		factor = getRealNumber(factor);
	}
	else factor = 0;
	factor = addCommas(factor / 100);
	var inkoop = $("#offerte_inkoop").val();
	var kosten = $("#offerte_kosten").val();
	for(var index = 0; index < $(".chapter-container input[name='chapter_id[]']").length; index ++)
	{
		
		var chapter_id = $(".chapter-container input[name='chapter_id[]']").eq(index).val();
		var chapter_name = $(".chapters #chapter_" + chapter_id + " input[name='chapter_name[]']").val();
		var line_datas = [];
		if($(".chapters #chapter_" + chapter_id).hasClass('editedLineData'))
		{
			for( var jdex = 0; jdex < $("#chapter_" + chapter_id + " .chapter-line-id").length; jdex ++)
			{
				var line_id = $("#chapter_" + chapter_id + " .chapter-line-id").eq(jdex).val();
				
	
				var materiaal_array = [];
				for(var kdex = 0; kdex < $("#line_" + line_id + " .material-item").length; kdex ++)
				{
					var item_name = "#line_" + line_id + " .material-item";
					var materiaal_data = {
						materiaal_title : $(item_name).eq(kdex).find('.materiaal_title').val(),
						stuks : $(item_name).eq(kdex).find('.materiaal_stuks').val(),
						price : $(item_name).eq(kdex).find('.materiaal_price').val(),
					};
					materiaal_array.push(materiaal_data);
				}
	
				var arbeid_array = [];
				for(var kdex = 0; kdex < $("#line_" + line_id + " .arbeid-item").length; kdex ++)
				{
					var item_name = "#line_" + line_id + " .arbeid-item";
					var arbeid_data = {
						arbeid_title : $(item_name).eq(kdex).find('.arbeid_title').val(),
						quantity : $(item_name).eq(kdex).find('.arbeid_quantity').val(),
					};
					arbeid_array.push(arbeid_data);
				}
	
	
				var line_data = {
					line_title : $("#line_" + line_id + " .chapter-line-header").val(),
					line_description : $("#line_" + line_id + " .chapter-line-description").val(),
					line_am_option : $("#line_" + line_id + " .chapter-line-am-option").val(),
					line_unit : $("#line_" + line_id + " .chapter-line-unit").val(),
					line_total : $("#line_" + line_id + " .chapter-line-total").val(),
					line_profit : $("#line_" + line_id + " .chapter-line-profit").val(),
					arbeid : $("#line_" + line_id + " .chapter-line-arbeid").val(),
					materiaal_total : $("#line_" + line_id + " .label-materiaal-total").text(),
					sort_order : jdex,
					materiaal_data : materiaal_array,
					arbeid_data: arbeid_array,
					standard_tag_id : $("#line_" + line_id + " .chapter-line-tag-standard").val(),
					fase_tag_id : $("#line_" + line_id + " .chapter-line-tag-fase").val()
				};
				if(line_id.length != 15)
					line_data['id'] = line_id;
				line_datas.push(line_data);
				
			}
		}
		else{
			for(var kdex = 0; kdex < offerte_chapter_data.length; kdex ++)
			{
				var temp_item = offerte_chapter_data[kdex];
				if(temp_item['chapter_id'] == chapter_id)
				{
					for( var jdex = 0; jdex < temp_item['line_data'].length; jdex ++)
					{
						var line_item = temp_item['line_data'][jdex];
						var line_data = {
							id : line_item['id'],
							line_title : line_item['line_title'],
							line_description : line_item['line_description'],
							line_am_option : line_item['line_am_option'],
							line_unit : line_item['line_unit'],
							line_total : line_item['line_total'],
							line_profit : line_item['line_profit'],
							arbeid : line_item['arbeid'],
							materiaal_total : line_item['materiaal_total'],
							sort_order : line_item['sort_order'],
							materiaal_data : line_item['materiaal_data'],
							arbeid_data: line_item['arbeid_data'],
							standard_tag_id : line_item['standard_tag_id'],
							fase_tag_id : line_item['fase_tag_id']
						};
						line_datas.push(line_data);
					}
					break;
				}
			}
		}
		


		var chapter_item = {
			chapter_title : chapter_name,
			line_data    : line_datas,
			sort_order : index
		};
		if(chapter_id.length != 15)
			chapter_item['id'] = chapter_id;
		chapterData.push(chapter_item);
	}
	offerteData = {
		chapterData : chapterData,
		factor : factor,
		rate : rate,
		inkoop : inkoop,
		kosten : kosten,
		version : version
	};
	showLoading();
	$.ajax({
		type: "POST",
		url: "../php/settings/save_default_offerte.php",
		data: offerteData,
		dataType: "json",
		success: function(result) {
			hideLoading();
			if(result['message'] == 'Regels opgeslagen.') {
				//Alles ging goed
				melding(result['message'], 'groen');
				closeVeryLargeModal();
				
			} else {
				//Er ging iets mis
				melding(result['message'], 'rood');
			}
			
		},
		error : function(e1,e2, e3) {
			hideLoading();
    melding("error", 'rood');
			closeVeryLargeModal();
		}
	});
	
}

function addTag() {
	showEmptyModal('Tag toevoegen','addTag.php');		
}

function saveTagInfo() {
	var name = $("#tag_name").val();
	var type = $("#tag_type").val();
	var tag_id = $("#tag_id").val();
	if(name == '')
	{
		melding('Vul een naam in', 'Rood');
		return;
	}

	var data = {
		name : name,
		type : type
	};
	if(tag_id != "")
		data['tag_id'] = tag_id;
	$.ajax({
		type: "POST",
		url: "../php/settings/save_tag.php",
		data: data,
		dataType: 'JSON',

		success: function(result) {

			if(result['status'] == 'success')
			{
				melding('Tag opgeslagen','groen');
				var item = result['item'];
				var html = "<td>" + item['name'] + "</td>"
				+ "<td>" + (item['type'] == 'STANDARD' ? 'standaard' : 'fase') + "</td>"
				+ "<td style='width:110px'>" + "<div onclick='editTag(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div>" +

				"<div onclick='deleteTag(" + item['id'] + ")'class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div>" + "</td>";
				if(item['mode'] == 'INSERT')
				{
					html = "<tr tagrow='" + item['id'] + "'>"
						+ html
						+ "</tr>";
					$("#tags tbody").append(html);
				}
				if(item['mode'] == 'UPDATE')
				{
					$("#tags tr[tagrow=" + item['id'] + "]").html(html);
				}
				
				closeModal();
			}
		},

		error: function(e1, e2, e3) {

		}
	});
	
}

function editTag(tag_id)
{
	prefillModal('Tag wijzigen','addTag.php').then(function() {



		$.ajax({
		
			type: "POST",
		
			url: "../php/settings/get_tag.php", 
		
			data: {tag_id : tag_id},
		
			dataType: "json",
		
			success: function(result){
		
		
		
			for(var i = 0; i < result.length; i++) {						
		
			
		
				var name = result[i].name;
			
				var type = result[i].type;	
			
				
		
				$("#taginfo #tag_name").val(name);
			
				$("#taginfo #tag_type").val(type);	
			
				$("#taginfo #tag_id").val(result[i].id);			
			
				showPrefilledModal();			
		
			}		
		},
		error: function(e1, e2, e3)
		{
			
		}	
		});	
		
		
	});
}

function deleteTag(tag_id)
{
	showConfirm('Deze tag verwijderen?','Verwijderen','red','deleteTagConfirm('+tag_id+')');	

}

function deleteTagConfirm(tag_id)
{
	closeConfirm();
	$.ajax({

		type: "POST",
	
		url: "../php/settings/delete_tag.php",
	
		data: { 'tag_id': tag_id },
	
		dataType: "JSON",
	
		success: function(result){
			// result = JSON.parse(result);
			if(result['status'] == 'success')
			{
				melding('Successfully Deleted','groen');
				$("tr[tagrow=" + result['tag_id'] + "]").slideUp();
			}
			else{
				melding(result['message'],'rood');
			}
		},
		error: function(e1, e2, e3){

		}
	});
}

function editLineTag(line_id)
{
	var container = "#offerteinfo #line_" + line_id + " ";
	if($(container).hasClass('tag-highlighted'))
	{
		$(container).removeClass('tag-highlighted')
	}
	else {
		$(container).addClass('tag-highlighted')
	}
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                var height = this.height;
                var width = this.width;
                var previous_img = $("#file").attr("data-preview-img");
                // if (!((height >= 100 && width >= 100) && (height <= 500 && width <= 500))) {
                //     alert("Student photo should be 100px * 100px ~ 500px * 500px.");
                //     $('#' + previous_img).attr('src', "");
                //     $('#photo').val('');
                //     return false;
                // }

                $(previous_img).attr('src', e.target.result);
                $(previous_img).attr("data-img-changed", 1);
                $("#file").parent().addClass("file_selected");
            }
            
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function deleteIMG()
{
	var root_path = $("#root_path").val();
	$(".avatarpreview-panel .avatarpreview").attr("data-img-changed", 1);
	$(".avatarpreview-panel .avatarpreview").attr('src', root_path + "images/users/afbeelding.jpg");
	$(".avatarpreview-panel").removeClass("file_selected");
	$("#file").val("");
}
