var status_list = ['', 'Active', 'Inactive', 'Lead'];
var source_list = ['', 'Facebook', 'Website', 'Adwords', 'Voltooide PDF', 'Configurator gestart', 'Afspraak verzoek', 'Telefoon'];
var contact_type = ['', 'E-mail', 'Telefoon', 'Gesprek', 'Whatsapp'];

function getFormatedDate(date) {
	let fdate = new Date(date);
	return("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear();
}

function getFormatedDateTime(date) {
	let fdate = new Date(date);
	return("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear() + ' ' + ("0" + fdate.getHours()).slice(-2) + ':' + ("0" + fdate.getMinutes()).slice(-2);
}

function deleteContact(contactid) {
	showConfirm('Dit contact verwijderen?', 'Verwijderen', 'red', 'deleteContactConfirm(' + contactid + ')');
}

function deleteContactConfirm(contactid) {
	closeConfirm();
	$.ajax({
		type: "POST",
		url: "../php/funnel/delete_contact.php",
		data: {
			'contact': contactid
		},
		dataType: "html",
		success: function(result) {
			//alert(result);
			if(result == 'Contact verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				$("tr[contactrow=" + contactid + "]").slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		}
	});
}

function addContact() {
	showEmptyModal('Contact toevoegen', 'addContact.php');
}

function saveContactInfo() {
	var contactid = $("#contactinfo .contactid").val();
	let user = $("#contactinfo #name").val();
	let city = $("#contactinfo #city").val();
	let address = $("#contactinfo #address").val();
	let email = $("#contactinfo #email").val();
	let phone = $("#contactinfo #phone").val();
	//let source = $("#contactinfo #source").val();
	//let c_status = $("#contactinfo #c_status").val();
	let errors = "";
	if(user == '') {
		errors = "error";
		melding('Vul een gebruikersnaam in', 'Rood');
	} else if(city == '') {
		errors = "error";
		melding('Vul een stad in', 'Rood');
	} else if(address == '') {
		errors = "error";
		melding('Vul een adres in', 'Rood');
	} else if(email == '') {
		errors = "error";
		melding('Vul een e-mailadres in', 'Rood');
	} else if(phone == '') {
		errors = "error";
		melding('Vul een telefoonummer in', 'Rood');
	}
	if(errors == '') {
		var data = $("#contactinfo").serializeArray();
		$.ajax({
			type: "POST",
			url: "../php/funnel/save_contact.php",
			data: data,
			dataType: "html",
			success: function(result) {
				//alert(result);
				if(result == 'Contact opgeslagen.') {
					melding(result, 'groen');
					if(contactid != '') {
						updateContactTableRow(contactid);
					} else {
						addNewContactTableRow();
					}
					closeModal();
				} else {
					melding(result, 'rood');
				}				
			}
		});
	}
};

function addNewContactTableRow() {
	$.ajax({
		type: "POST",
		url: "../php/funnel/get_last_contact.php",
		dataType: "json",
		success: function(result) {
			for(var i = 0; i < result.length; i++) {
				var id = result[i].id;
				var name = result[i].name;
				var city = result[i].city;
				var address = result[i].address;
				var email = result[i].email;
				var phone = result[i].phone;
				var source = result[i].source;
				var c_status = result[i].c_status;
				var created_date = result[i].created_date;
				
				var html = "<tr contactrow='" + id + "'><td>" + name + "</td><td>" + city + "</td><td>" + address + "</td><td>" + email + "</td><td>" + phone + "</td><td class='source-column'>" + source_list[source];
				if(source == 6 || source == 7)
					html += '<div onclick="report_problems(' + id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Report"><i class="material-icons">report_problems</i></div>';
				
				 html += "</td><td style='display: none;'>" + c_status + "</td><td>" + convertDateFormat(created_date) + "</td><td></td><td></td><td><div onclick='manageProjectFileLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> <div onclick='manageContactLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Logboek'><i class='material-icons'>assignment</i></div> <div onclick='editContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div> </td></tr>";
				
				var containerID = (c_status == 1) ? '#actievecontacten' : '#inactievecontacten';
				if(c_status != 3)
				{
					$(containerID + " table").append(html);
					$('.tooltipped').tooltip();
				}
				
			}
		}
	});
}

function editContact(contactid) {
	prefillModal('Contact wijzigen', 'addContact.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/funnel/get_contact.php",
			data: {
				contactid: contactid
			},
			dataType: "json",
			success: function(result) {
				for(var i = 0; i < result.length; i++) {
					var name = result[i].name;
					var city = result[i].city;
					var address = result[i].address;
					var email = result[i].email;
					var phone = result[i].phone;
					var source = result[i].source;
					var c_status = result[i].c_status;
					var timer = result[i].timer;
					$("#contactinfo .contactid").val(contactid);
					$("#contactinfo #name").val(name);
					$("#contactinfo #city").val(city);
					$("#contactinfo #address").val(address);
					$("#contactinfo #email").val(email);
					$("#contactinfo #phone").val(phone);
					$("#contactinfo #source").val(source);
					$("#contactinfo #c_status").val(c_status);
					var title = name + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';
					$(".popup.large .title").html(title);
					$(".popup.large .logs").html('');
					showPrefilledModal();
				}				
			}
		});
	});
}

function manageContactLog(contactid) {
	prefillModal('Logboek', 'manageContactLog.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/funnel/get_contact_log.php",
			data: {
				contactid: contactid
			},
			dataType: "json",
			success: function(result) {
				if(result) {
					var contactloghtml = '';
					$("#contactlog .contactid").val(contactid);
					for(var i = 0; i < result.length; i++) {
						var clid = result[i].clid;
						// if ( clid == null ) continue;
						var name = result[i].name;
						var email = result[i].email;
						var phone = result[i].phone;
						var timer = result[i].timer;
						var type = result[i].entry_type;
						var date = result[i].entry_date;
						var user = result[i].username;
						var desc = result[i].entry_description;
						if(i == 0) {
							
							var title = '<span class="name">' + name + ' </span>' + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';
							$(".popup.large .title").html(title);
						}
						if(timer) {
							contactloghtml += getContactlogStr(contactid,clid, result[i]);
						}
					}
					$(".popup.large .logs").html(contactloghtml);
					$('.tooltipped').tooltip();
					showPrefilledModal();
				}
			},
			error: function(e1, e2, e3){

			}
		});
	});
}

function saveContactLog() {
	let date = new Date().toISOString().slice(0, 19).replace("T", " "); //convert js datetime format to mysql one.
	$("#contactlog .date").val(date);
	let contactid = $("#contactlog .contactid").val();
	let userid = $("#contactlog .userid").val();
	let type = $("#contactlog #type").val();
	let desc = $("#contactlog #desc").val();	
	let entrydate = $("#contactlog #entrydate").val();	
	let entrytime = $("#contactlog #entrytime").val();
	var files = $('#file')[0].files;
	let errors = "";
	if(contactid == '') {
		errors = "error";
		melding('Fatale fout', 'Rood');
	} else if(userid == '') {
		errors = "error";
		melding('Fatale fout', 'Rood');
	} else if(type == '') {
		errors = "error";
		melding('kies een contactwijze', 'Rood');
	} else if(desc == '') {
		errors = "error";
		melding('Vul een notitie in', 'Rood');
	} else if(entrydate == '') {
		errors = "error";
		melding('Vul een datum in', 'Rood');
	} else if(entrytime == '') {
		errors = "error";
		melding('Vul een tijd in', 'Rood');
	}
	if(errors == '') {
		var data_array = $("#contactlog").serializeArray();
        
        var data = new FormData();

        for(var index = 0; index < data_array.length; index ++)
        {
            data.append(data_array[index]['name'],data_array[index]['value']);
        }
        if(files.length > 0)
            data.append('file',files[0]);
        else 
            data.append('file', '');
		$(".file-loading-icon").show();
		$(".file-icon").hide();
		$.ajax({
			type: "POST",
			url: "../php/funnel/save_contact_log.php",
			data: data,
			dataType: "json",
			contentType: false,
            processData: false,
			success: function(result) {
				if(result['message'] == 'Logboek opgeslagen.') {
					//Alles ging goed
					melding(result['message'], 'groen');
					if(contactid != '') {
						addNewContactLog(result['new_one'], result['next_one']);
						// $("tr[contactrow=" + contactid + "] td:nth-child(8)").html(getFormatedDate(entrydate));
						// $("tr[contactrow=" + contactid + "] td:nth-child(9)").html(contact_type[type]);
						// $("tr[contactrow=" + contactid + "] td:nth-child(10)").html('<span class="new badge green" data-badge-caption="">0m</span>');
						
						// $(".popup.large .title .badge").remove();
						// $(".popup.large .title .name").append('<span class="new badge green" data-badge-caption="">0m</span>');
						$("#contactlog #type").val('');
						$("#contactlog #type").formSelect();
						$("#contactlog #desc").val('');
						$("#contactlog #entrydate").val('');
						$("#contactlog #entrytime").val('');
						$("#file").val("");
						$(".btn-filelog").removeClass('file_selected');
						updateContactTableRow(contactid);
						updateContactPopup(contactid);
						$(".file-loading-icon").hide();
                        $(".file-icon").show();
					}
				} else {
					//Er ging iets mis
					melding(result['message'], 'rood');
				}
			}
		});
	}
};

function addNewContactLog(new_one, next_one) {
	var id = new_one['id'];

	var contactid = new_one['contact_id'];

	var contactloghtml = getContactlogStr(contactid, id, new_one, true);
    var file_path = new_one['file_path'];
    
	var item = $(".popup.large .logs");
	if(next_one != ''){
		item = $("#clog-" + next_one['id']);
		item.after(contactloghtml);
	}
		
	else{
		item.prepend(contactloghtml);
	}
	$('.tooltipped').tooltip();
	$('#clog-' + id).slideDown();

}


function showInnerConfirm(clID) {
	$("#ipc-" + clID).show();
	$("#ipo-" + clID).show();
	$("#ipc-" + clID).addClass('visible');
	$("#ipo-" + clID).addClass('visible');
}

function closeInnerConfirm(clID) {
	$("#ipc-" + clID).removeClass('visible');
	$("#ipo-" + clID).removeClass('visible');
	setTimeout(function() {
		$("#ipo-" + clID).hide();
		$("#ipc-" + clID).hide();
	}, 300);
}

function deleteCLogConfirm(contactid, clID) {
	//closeConfirm();
	closeInnerConfirm(clID)
	$.ajax({
		type: "POST",
		url: "../php/funnel/delete_contact_log.php",
		data: {
			'clog': clID
		},
		dataType: "html",
		success: function(result) {
			if(result == 'Logboek item verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				updateContactPopup(contactid);
				updateContactTableRow(contactid);
				$("#clog-" + clID).slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		}
	});
}

function updateContactTableRow(contactid) {
	$.ajax({
		type: "POST",
		url: "../php/funnel/get_contact_log.php",
		data: {
			contactid: contactid
		},
		dataType: "html",
		success: function(result) {
			if(result) {
				if(!Array.isArray(result)) result = $.parseJSON(result);
				var id = result[0].cid;
				var name = result[0].name;
				var city = result[0].city;
				var address = result[0].address;
				var email = result[0].email;
				var phone = result[0].phone;
				var source = result[0].source;
				var c_status = result[0].c_status;
				var type = result[0].entry_type;
				var date = result[0].entry_date;
				var timer = result[0].timer;
				var created_date = result[0].created_date;
				old_c_status = $("tr[contactrow=" + id + "] td:nth-child(7)").html();
				if(c_status == 3) {			// changed to lead
					$("table tr[contactrow=" + id + "]").remove();
				}
				else {
					var source_html = source_list[source];
					if(source == 6 || source == 7)
						source_html += '<div onclick="report_problems(' + id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Report"><i class="material-icons">report_problems</i></div>';

					if(old_c_status == c_status) {
						$("tr[contactrow=" + id + "] td:nth-child(1)").html(name);
						$("tr[contactrow=" + id + "] td:nth-child(2)").html(city);
						$("tr[contactrow=" + id + "] td:nth-child(3)").html(address);
						$("tr[contactrow=" + id + "] td:nth-child(4)").html(email);
						$("tr[contactrow=" + id + "] td:nth-child(5)").html(phone);
						
						$("tr[contactrow=" + id + "] td:nth-child(6)").html(source_html);
						$("tr[contactrow=" + id + "] td:nth-child(10)").html(timer);
					} else {
						$("table tr[contactrow=" + id + "]").remove();
						var html = "<tr contactrow='" + id + "'><td>" + name + "</td><td>" + city + "</td><td>" + address + "</td><td>" + email + "</td><td>" + phone + "</td><td>" + source_html + "</td><td style='display: none;'>" + c_status + "</td><td>" + convertDateFormat(created_date) + "</td><td></td><td></td><td><div onclick='manageProjectFileLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> <div onclick='manageContactLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Logboek'><i class='material-icons'>assignment</i></div> <div onclick='editContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td></tr>";
						var containerID = (c_status == 1) ? '#actievecontacten' : '#inactievecontacten';
						$(containerID + " table").append(html);
						$('.tooltipped').tooltip();
					}
					if(type && date && timer) {
						// $("tr[contactrow=" + id + "] td:nth-child(8)").html(getFormatedDate(date));
						$("tr[contactrow=" + id + "] td:nth-child(9)").html(contact_type[type]);
						$("tr[contactrow=" + id + "] td:nth-child(10)").html(timer);
					} else {
						// $("tr[contactrow=" + id + "] td:nth-child(8)").html('');
						$("tr[contactrow=" + id + "] td:nth-child(9)").html('');
						$("tr[contactrow=" + id + "] td:nth-child(10)").html('');
					}
				}
			}
		}
	});
}

function updateContactPopup(contactid) {
	$.ajax({
		type: "POST",
		url: "../php/funnel/get_last_contact_log.php",
		data: {
			contactid: contactid
		},
		dataType: "html",
		success: function(result) {
			if(result) {
				if(!Array.isArray(result)) result = $.parseJSON(result);
				
				$(".popup.large .title .badge").remove();
				if(result.length > 0)
				{
					let timer = result[0].timer;
					$(".popup.large .title .name").append(timer);
					
				}
				
			}
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

function updateTimerWiget(contactid)
{
	$.ajax({
		type: "POST",
		url: "../php/funnel/get_contact_log.php",
		data: {
			contactid: contactid
		},
		dataType: "json",
		success: function(result) {
			if(result) {
				var contactloghtml = '';
				$("#contactlog .contactid").val(contactid);
				$(".popup.large .title").html("");
				for(var i = 0; i < result.length; i++) {
					var clid = result[i].clid;
					// if ( clid == null ) continue;
					var name = result[i].name;
					var email = result[i].email;
					var phone = result[i].phone;
					var timer = result[i].timer;
					var type = result[i].entry_type;
					var date = result[i].entry_date;
					var user = result[i].username;
					var desc = result[i].entry_description;
					if(i == 0) {
						
						var title = '<span class="name">' + name + ' </span>' + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';
						$(".popup.large .title").html(title);
					}
				}
			}
		},
		error: function(e1, e2, e3){

		}
	});
}
