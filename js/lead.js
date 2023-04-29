
var lead_type = ['', 'Deal', 'Geen deal', 'Wijzigen offerte', 'Wacht op antwoord'];
var contact_type = ['', 'E-mail', 'Telefoon', 'Gesprek', 'Whatsapp'];


function getFormatedDate(date) {
    let fdate = new Date(date);
    return ("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear();
}

function getFormatedDateTime(date) {
    let fdate = new Date(date);
    return ("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear() + ' ' + ("0" + fdate.getHours()).slice(-2) + ':' + ("0" + fdate.getMinutes()).slice(-2);
}

function deleteLead(contactid) {
    showConfirm('Deze lead verwijderen?', 'Verwijderen', 'red', 'deleteLeadConfirm(' + contactid + ')');
}

function closeVeryLargeModalForQuote() {

    
    $(".popup.very-large").removeClass('visible');
    $(".popup-very-large-container").removeClass('visible');
    $("body").removeClass('popup-active');
    setTimeout(function() {
        $(".popup-very-large-container").hide();
        $(".popup-overlay-very-large").hide();

        showModal();
    }, 300);
}

function getLeadStatus(contact_id, type) {
    if (type < 5)
        return lead_type[type];
    else {
        var theResponse = null;
        $.ajax({
            type: "POST",
            url: "../php/leads/get_nearest_meeting.php",
            data: {
                'leadid': contact_id
            },
            async: false,
            dataType: "json",
            success: function(result) {
                var status = "Afspraak";
                if (result.length > 0) {
                    status += " (" + result[0]['date_formated'] + ")";
                }
                theResponse = status;
            },
            error: function(e1, e2, e3) {

            }
        });
        return theResponse;
    }
}

function deleteLeadConfirm(contactid) {
    closeConfirm();
    $.ajax({
        type: "POST",
        url: "../php/leads/delete_lead.php",
        data: {
            'contact': contactid
        },
        dataType: "html",
        success: function(result) {
            //alert(result);
            if (result == 'Lead verwijderd.') {
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
    showEmptyModal('Lead toevoegen', 'addLead.php');
}

function saveContactInfo() {
    var contactid = $("#contactinfo .contactid").val();
    let user = $("#contactinfo #name").val();
    let city = $("#contactinfo #city").val();
    let address = $("#contactinfo #address").val();
    let email = $("#contactinfo #email").val();
    let phone = $("#contactinfo #phone").val();
    let errors = "";
    if (user == '') {
        errors = "error";
        melding('Vul een gebruikersnaam in', 'Rood');
    } else if (city == '') {
        errors = "error";
        melding('Vul een stad in', 'Rood');
    } else if (address == '') {
        errors = "error";
        melding('Vul een adres in', 'Rood');
    } else if (email == '') {
        errors = "error";
        melding('Vul een e-mailadres in', 'Rood');
    } else if (phone == '') {
        errors = "error";
        melding('Vul een telefoonummer in', 'Rood');
    }
    if (errors == '') {
        var data = $("#contactinfo").serializeArray();
        $.ajax({
            type: "POST",
            url: "../php/leads/save_lead.php",
            data: data,
            dataType: "html",
            success: function(result) {
                //alert(result);
                if (result == 'Lead opgeslagen.') {
                    melding(result, 'groen');
                    if (contactid != '') {
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
        url: "../php/leads/get_last_lead.php",
        dataType: "json",
        success: function(result) {
            for (var i = 0; i < result.length; i++) {
                var id = result[i].id;
                var name = result[i].name;
                var city = result[i].city;
                var address = result[i].address;
                var email = result[i].email;
                var phone = result[i].phone;
                var l_status = result[i].l_status;
                var prio_select_html = "<option value='0'>N.v.t</option>";
                for(var j = 1; j <= 20; j ++ )
                    prio_select_html += "<option value='" + j + "'>" + j + "</option>";

                var html = "<tr contactrow='" + id + "'><td>" + name + "</td><td>" + city + "</td><td>" + address + "</td><td>" + email + "</td><td>" + phone + "</td><td>Nog te versturen</td><td></td><td><select onchange='changedPrio(" + id + ")' class='select-prio browser-default'>" + prio_select_html + "</select></td><td></td><td><div onclick='manageProjectFileLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> <div onclick='manageContactLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Logboek'><i class='material-icons'>assignment</i></div> <div onclick='manageQuoteList(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Offerte'><i class='material-icons'>insert_drive_file</i></div> <div onclick='editContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div><div onclick='deleteLead(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td></tr>";

                var containerID = "#actieveleads";

                $(containerID + " table").append(html);
                $('.tooltipped').tooltip();

            }
        }
    });
}

function editContact(contactid) {
    prefillModal('Lead wijzigen', 'addLead.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_lead.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {
                for (var i = 0; i < result.length; i++) {
                    var name = result[i].name;
                    var city = result[i].city;
                    var address = result[i].address;
                    var email = result[i].email;
                    var phone = result[i].phone;

                    var timer = result[i].timer;
                    $("#contactinfo .contactid").val(contactid);
                    $("#contactinfo #name").val(name);
                    $("#contactinfo #city").val(city);
                    $("#contactinfo #address").val(address);
                    $("#contactinfo #email").val(email);
                    $("#contactinfo #phone").val(phone);
                    

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
    prefillModal('Logboek', 'manageLeadLog.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_lead_log.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {
                if (result) {
                    var contactloghtml = '';
                    for (var i = 0; i < result.length; i++) {
                        var clid = result[i].clid;

                        var name = result[i].name;
                        var email = result[i].email;
                        var phone = result[i].phone;
                        var timer = result[i].timer;
                        var type = result[i].entry_type;
                        var date = result[i].entry_date;
                        var user = result[i].username;
                        var desc = result[i].entry_description;

                        var status_type = contact_type[type];

                        if (result[i].entry_title == 'lead') {
                            status_type = getLeadStatus(contactid, type);
                        }
                        if (i == 0) {
                            $("#contactlog .contactid").val(contactid);
                            var title = '<span class="name">' + name + ' </span>' + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';
                            $(".popup.large .title").html(title);
                        }
                        if (timer && type != 0) {
                            contactloghtml += getContactlogStr(contactid,clid, result[i]);
                        }
                    }
                    $(".popup.large .logs").html(contactloghtml);
                    $('.tooltipped').tooltip();
                    showPrefilledModal();
                }
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
    if (contactid == '') {
        errors = "error";
        melding('Fatale fout', 'Rood');
    } else if (userid == '') {
        errors = "error";
        melding('Fatale fout', 'Rood');
    } else if (type == '') {
        errors = "error";
        melding('kies een contactwijze', 'Rood');
    } else if (desc == '') {
        errors = "error";
        melding('Vul een notitie in', 'Rood');
    } else if(entrydate == '') {
		errors = "error";
		melding('Vul een datum in', 'Rood');
	} else if(entrytime == '') {
		errors = "error";
		melding('Vul een tijd in', 'Rood');
	}
    if (errors == '') {
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
            url: "../php/leads/save_lead_log.php",
            data: data,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                if (result['message'] == 'Logboek opgeslagen.') {
                    //Alles ging goed
                    melding(result['message'], 'groen');
                    if (contactid != '') {
                        updateContactTableRow(contactid);
                        
                        
                        addNewContactLog(result['new_one'], result['next_one']);
                        
                        updateContactPopup(contactid);
                        
                        $("#contactlog #type").val('');
                        $("#contactlog #type").formSelect();
                        $("#contactlog #desc").val('');
						$("#contactlog #entrydate").val('');
						$("#contactlog #entrytime").val('');
                        $("#file").val("");
                        $(".btn-filelog").removeClass('file_selected');
                        if (lead_type[type] == 'Deal') {
                            $("table tr[contactrow=" + contactid + "]").remove();
                        }

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
	if(next_one != '' && next_one['entry_type'] != 0){
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
        url: "../php/leads/delete_lead_log.php",
        data: {
            'clog': clID
        },
        dataType: "html",
        success: function(result) {
            if (result == 'Logboek item verwijderd.') {
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
        url: "../php/leads/get_lead_log.php",
        data: {
            contactid: contactid
        },
        dataType: "json",
        success: function(result) {
            if (result) {
                if (!Array.isArray(result)) result = $.parseJSON(result);
                var id = result[0].cid;
                var name = result[0].name;
                var city = result[0].city;
                var address = result[0].address;
                var email = result[0].email;
                var phone = result[0].phone;

                var l_status = result[0].l_status;
                var type = result[0].entry_type;
                var date = result[0].entry_date;
                var timer = result[0].timer;
                old_l_status = $("tr[contactrow=" + id + "] td:nth-child(7)").html();
                var old_quote = $("tr[contactrow=" + id + "] td:nth-child(6)").html();


                if (old_l_status == lead_type[l_status]) {
                    $("tr[contactrow=" + id + "] td:nth-child(1)").html(name);
                    $("tr[contactrow=" + id + "] td:nth-child(2)").html(city);
                    $("tr[contactrow=" + id + "] td:nth-child(3)").html(address);
                    $("tr[contactrow=" + id + "] td:nth-child(4)").html(email);
                    $("tr[contactrow=" + id + "] td:nth-child(5)").html(phone);

                    $("tr[contactrow=" + id + "] td:nth-child(7)").html(getLeadStatus(id, type));
                    $("tr[contactrow=" + id + "] td:nth-child(8)").html(timer);

                } else {
                    $("table tr[contactrow=" + id + "]").remove();
                    var html = "<tr contactrow='" + id + "'><td>" + name + "</td><td>" + city + "</td><td>" + address + "</td><td>" + email + "</td><td>" + phone + "</td><td></td><td>" + getLeadStatus(id, l_status) + "</td><td></td><td><div onclick='manageProjectFileLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> <div onclick='manageContactLog(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Logboek'><i class='material-icons'>assignment</i></div> <div onclick='manageQuoteList(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Offerte'><i class='material-icons'>insert_drive_file</i></div> <div onclick='editContact(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteLead(" + id + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td></tr>";
                    var containerID = (lead_type[l_status] != 'Geen deal') ? '#actieveleads' : '#inactieveleads';
                    $(containerID + " table").append(html);
                    $('.tooltipped').tooltip();
                }
                if (lead_type[l_status] == 'Deal') {
                    $("table tr[contactrow=" + id + "]").remove();
                }
                if (type != null && date && timer) {

                    $("tr[contactrow=" + id + "] td:nth-child(7)").html(getLeadStatus(id, type));
                    $("tr[contactrow=" + id + "] td:nth-child(8)").html(timer);
                } else {
                    $("tr[contactrow=" + id + "] td:nth-child(7)").html('');
                    $("tr[contactrow=" + id + "] td:nth-child(8)").html('');
                }
                $("tr[contactrow=" + id + "] td:nth-child(6)").html(old_quote);
            }
        }
    });
}

function updateContactPopup(contactid) {
    $.ajax({
        type: "POST",
        url: "../php/leads/get_last_lead_log.php",
        data: {
            contactid: contactid
        },
        dataType: "json",
        success: function(result) {
            $(".popup.large .title .badge").remove();

            if (result.length > 0) {
                let timer = result[0].timer;
                $(".popup.large .title .badge").remove();
                $(".popup.large .title .name").append(timer);
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

