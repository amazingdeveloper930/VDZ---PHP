function editEmployee(employeeid) {	



    prefillModal('Personeel wijzigen','addEmployee.php').then(function() {
    
    
    
    $.ajax({
    
        type: "POST",
    
        url: "../php/settings/get_employee.php", 
    
        data: {employeeid:employeeid},
    
        dataType: "json",
    
        success: function(result){
    
    
    
        for(var i = 0; i < result.length; i++) {						
    
        
    
        var name = result[i].name;
        var id = result[i].id;
        var type = result[i].type;
        var avatar = result[i].file_path;
        var teamleader = result[i].teamleader;
        var member_count = result[i].member_count;
        $("#employeeinfo #ei_teamleader").val(teamleader);
        $("#employeeinfo .employeeid").val(employeeid);
        $("#employeeinfo #ei_teamleader option[value=" + id + "]").remove();
        $("#employeeinfo #name").val(name);	
        $("#employeeinfo #ei_projecttype").val(type);	

        $("#employeeinfo #achternaam").val(result[i].achternaam);
        $("#employeeinfo #ei_specialisme").val(result[i].specialisme);
        $("#employeeinfo #ei_inweekplanning").val(result[i].inweekplanning);
        $("#employeeinfo #ei_woonadres_nl").val(result[i].woonadres_nl);
       
        if(result[i].woonadres_nl != 'Overig')
            $("#employeeinfo #row_overig").hide();
        $("#employeeinfo #overig").val(result[i].overig);
        $("#employeeinfo #ei_visa").val(result[i].visa);
        $("#employeeinfo #telefoonnummer1").val(result[i].telefoonnummer1);
        $("#employeeinfo #telefoonnummer2").val(result[i].telefoonnummer2);
        $("#employeeinfo #email").val(result[i].email);
        $("#employeeinfo #geboortedatum").val(result[i].geboortedatum);
        $("#employeeinfo #aankomst_datum").val(result[i].aankomst_datum);
        $("#employeeinfo #vertrek_datum").val(result[i].vertrek_datum);
        $("#employeeinfo #aankomst_datum2").val(result[i].aankomst_datum2);
        $("#employeeinfo #vertrek_datum2").val(result[i].vertrek_datum2);
        $("#employeeinfo #ice_telefoon").val(result[i].ice_telefoon);
        $("#employeeinfo #ice_naam").val(result[i].ice_naam);
        $("#employeeinfo #computer_login").val(result[i].computer_login);
        $("#employeeinfo #computer_wachtwoord").val(result[i].computer_wachtwoord);
        $("#employeeinfo #zakelijke_email").val(result[i].zakelijke_email);
        $("#employeeinfo #email_wachtwoord").val(result[i].email_wachtwoord);



        if(avatar)
        {
            $("#employeeinfo .img-user-avatar").attr('src', $("#root_path").val() + "upload/" + avatar);
        }
       
        if(teamleader == 0 && member_count > 0)
            $("#employeeinfo #ei_teamleader").attr("disabled", true);
        $("#employeeinfo #ei_contact_id").val(result[i].contact_id);
        $("#employeeinfo select").formSelect();
    
        teamleaderOptionChanged();
        showPrefilledModal();	
    
        
    
        }
    
                        
    
        }
    
        
    
    });	
    
    
    
    });
    
    
    
    }
    function addEmployee()
    {
        prefillModal('Personeel toevoegen','addEmployee.php').then(function() {
        
        
            $("#employeeinfo .employeeid").val('');
            $("#employeeinfo select").formSelect();
            
            showPrefilledModal();
            
        });
            
    }

    function teamleaderOptionChanged()
    {
        if($("#ei_teamleader").val() == "0")
        {
            $("#ei_contact_id").show();
        }
        else{
            $("#ei_contact_id").hide();
        }
    }
    function deleteEmployee(employeeid)
    {
        
        showConfirm('Deze Medewerker verwijderen?','Verwijderen','red','deleteEmployeeConfirm('+employeeid+')');	
    }
    function deleteEmployeeConfirm(employeeid)
    {
        closeConfirm();
    
        $.ajax({
    
            type: "POST",
        
            url: "../php/settings/delete_employee.php",
        
            data: { employeeid: employeeid },
        
            dataType: "html",
        
            success: function(result){
        
            if(result == 'Medewerker verwijderd') {
        
                
        
            //Alles ging goed
        
            melding(result,'groen');
        
            $("#employee-list .employee-item[employeerow="+employeeid+"]").slideUp();
        
            } else {
        
            melding(result,'rood');
    
            }			
        
            }
        
         });
    
    
    }
    
    function saveEmployeeInfo()
    {
        var name = $("#employeeinfo #name").val();
        var employeeid = $(".employeeid").val();
        var type = $("#ei_projecttype").val();
        var teamleader = $("#ei_teamleader").val();
        var files = $('#file')[0].files;
        var fd = new FormData();
        fd.append('name', name);
        fd.append('type', type);
        fd.append('teamleader', teamleader);
        fd.append('employeeid', employeeid);

        fd.append('achternaam', $("#employeeinfo #achternaam").val());
        fd.append('specialisme', $("#employeeinfo #ei_specialisme").val());
        fd.append('inweekplanning', $("#employeeinfo #ei_inweekplanning").val());
        fd.append('woonadres_nl', $("#employeeinfo #ei_woonadres_nl").val());
        
        if($("#employeeinfo #ei_woonadres_nl").val() == 'Overig')
            fd.append('overig', $("#employeeinfo #overig").val());
        else
            fd.append('overig', '');
        fd.append('visa', $("#employeeinfo #ei_visa").val());
        fd.append('telefoonnummer1', $("#employeeinfo #telefoonnummer1").val());
        fd.append('telefoonnummer2', $("#employeeinfo #telefoonnummer2").val());
        fd.append('email', $("#employeeinfo #email").val());
        fd.append('geboortedatum', $("#employeeinfo #geboortedatum").val());
        fd.append('aankomst_datum', $("#employeeinfo #aankomst_datum").val());
        fd.append('vertrek_datum', $("#employeeinfo #vertrek_datum").val());
        fd.append('aankomst_datum2', $("#employeeinfo #aankomst_datum2").val());
        fd.append('vertrek_datum2', $("#employeeinfo #vertrek_datum2").val());
        fd.append('ice_telefoon', $("#employeeinfo #ice_telefoon").val());
        fd.append('ice_naam', $("#employeeinfo #ice_naam").val());
        fd.append('computer_login', $("#employeeinfo #computer_login").val());
        fd.append('computer_wachtwoord', $("#employeeinfo #computer_wachtwoord").val());
        fd.append('zakelijke_email', $("#employeeinfo #zakelijke_email").val());
        fd.append('email_wachtwoord', $("#employeeinfo #email_wachtwoord").val());

        fd.append('contact_id', $("#employeeinfo #ei_contact_id").val());
        
        if(files.length > 0 ){
            fd.append('file', files[0]);
            $(".file-loading-icon").show();
            $(".file-icon").hide();
        }
        if(name == '')
        {
            melding('Vul een name in', 'Rood');
        }	
        
        else{
    
            $.ajax({
                type: "POST",
                url: "../php/settings/save_employeeinfo.php",
                data: fd,	
                dataType: "json",	
                contentType: false,
                processData: false,
                success: function(result){
                    
                $(".file-loading-icon").hide();
                $(".file-icon").show();

                if(result['message'] == 'Medewerker opgeslagen') {
    
                melding(result['message'],'groen');
                var path = result['employee']['file_path'];
            
                if(employeeid != '') { //Edited a user
                    var container = "#employee-list .employee-item[employeerow="+employeeid+"]";
                    $(container + " .employee-name").html(name);
                    $(container + " .employee-type").html(type + "<div class='employee-type-identify employee-type-i-" + type + "'></div>");
                    $(container + " .employee-teamleader").html(result['employee']['teamleader_name']);
                    
                    $(container + " .employee-achternaam").html(result['employee']['achternaam']);
                    $(container + " .employee-woonadres_nl").html(result['employee']['woonadres_nl']);
                    $(container + " .employee-visa").html(result['employee']['visa']);
                    $(container + " .employee-specialisme").html(result['employee']['specialisme']);
                   
                    if(result['employee']['aankomst_datum'] != '')
                        $(container + " .employee-aankomst_datum").html(convertDateFormat(result['employee']['aankomst_datum']));
                    if(result['employee']['vertrek_datum'] != '')
                        $(container + " .employee-vertrek_datum").html(convertDateFormat(result['employee']['vertrek_datum']));
                    $(container + " .employee-weekplanning").html(result['employee']['inweekplanning']);

                    if(path){
                        $("#employee-list .employee-item[employeerow="+employeeid+"] .employee-photo").html("<img src='" + $("#root_path").val() + "upload/" + path + "'/>");
                        $("#employee-list .employee-item[employeerow="+employeeid+"] .employee-photo").attr('onclick', 'openPrev("' + path + '")');
                    }
                        
            
            
                } else { //Added a user		
            

                    $("#employee-list").append("<tr class='employee-item' employeerow=" + result['employee']['id'] + "><td class='employee-name'>" + result['employee']['name'] + "</td> "
                    + "<td class='employee-achternaam'>" + result['employee']['achternaam'] + "</td> "
                    + "<td class='employee-photo'> <img src='" +("#root_path").val() + "images/users/vehicle.png' /> + </td> "
                    + "<td class='employee-specialisme'>" + result['employee']['specialisme'] + "</td> "
                    + "<td class='employee-type'>" + result['employee']['type'] + "<td class='employee-type-identify employee-type-i-" + result['employee']['type'] + "'></td></td>"  + "<td class='employee-teamleader'>" + result['employee']['teamleader_name'] + "</td> "
                    + "<td class='employee-aankomst_datum'>" + convertDateFormat(result['employee']['aankomst_datum']) + "</td> "
                    + "<td class='employee-vertrek_datum'>" + convertDateFormat(result['employee']['vertrek_datum']) + "</td> "
                    + "<td class='employee-weekplanning'>" + result['employee']['inweekplanning'] + "</td> "
                    + "<td class='actionbuttons'>"
                    + "<div onclick='manageEmployeeFileLog(" + result['employee']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Beschikbaarheid'><i class='material-icons'>date_range</i></div> " +
                    + "<div onclick='manageEmployeeFileLog(" + result['employee']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> " +
                    "<div onclick='employeeNote(" + result['employee']['id'] + ")' class='actiebutton tooltipped icon_employeenote' data-position='top'><i class='material-icons'>insert_comment</i></div>" +
                    + "<div onclick='editEmployee(" + result['employee']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteEmployee(" + result['employee']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td></tr> <tr class='row_employee_note' employee_id = " + result['employee']['id'] + "></tr>");
                    if(path){
                        $("#employee-list .employee-item[employeerow="+result['employee']['id']+"] .employee-photo").html("<img src='" + $("#root_path").val() + "upload/" + path + "'/>");
                        $("#employee-list .employee-item[employeerow="+result['employee']['id']+"] .employee-photo").attr('onclick', 'openPrev("' + path + '")');
                    }
                    
            
                }
    
                closeModal();
            
                } else {
            
                melding(result['message'],'rood');
            
                }									
            
                },
                error: function(e1,e2,e3){

                }
            
             });	
    
        }
    }

    $( "#employee-list" ).sortable({
        items: "li.employee-item",
        update: function( ) {
            // do stuff
            var employee_list = [];
            $("#employee-list li.employee-item").each(function(index, item){
                employee_list.push({employee: $(item).attr('employeerow'), sort_order: index + 1});
            });
            var data = {
                employee_list: employee_list
            }
            $.ajax({
                type: "POST",
                url: "../php/settings/save_employee_order.php",
                dataType: "json",
                data: data,
                success: function(result) {
                    if(result['message'] == 'Medewerker opgeslagen')
                    {
                        melding(result['message'],'groen');
                    }
                    else
                        melding(result['message'], 'Rood');
                },
                error: function(e1, e2, e3)
                {
    
                }
            });
        }
      });

function addNewVehicle()
{
    prefillModal('Voertuigen wijzigen', 'addVehicle.php').then(function() {
        $("#vehicleinfo select").formSelect();
       
        showPrefilledModal();
    });
}



function addNewLaptop()
{
    prefillModal('Laptop + telefoons wijzigen', 'addLaptop.php').then(function() {
        showPrefilledModal();
    });
}


function saveLaptopInfo()
{
    let soort = $("#laptopinfo #soort").val();
    let merk = $("#laptopinfo #merk").val();
    let type = $("#laptopinfo #type").val();
    let aanschafdatum = $("#laptopinfo #aanschafdatum").val();
    let abonnement_tot = $("#laptopinfo #abonnement_tot").val();
    let abonnement_provider = $("#laptopinfo #abonnement_provider").val();
    let maandprijs = $("#laptopinfo #maandprijs").val();
    let employee = $("#laptopinfo #employee").val();
    let errors = "";
    let laptopid = $("#laptopinfo .laptopid").val();
    if(soort == '') {
		errors = "error";
		melding('Vul een soort in', 'Rood');
	} else if(employee == '') {
		errors = "error";
		melding('Vul een employee in', 'Rood');
	}
    else if(merk == '') {
		errors = "error";
		melding('Vul een merk in', 'Rood');
	}
    else if(type == '') {
		errors = "error";
		melding('Vul een type in', 'Rood');
	}
    else if(aanschafdatum == '') {
		errors = "error";
		melding('Vul een aanschafdatum in', 'Rood');
	}
    else if(abonnement_tot == '') {
		errors = "error";
		melding('Vul een abonnement tot in', 'Rood');
	}
    else if(abonnement_provider == '') {
		errors = "error";
		melding('Vul een abonnement provider in', 'Rood');
	}
    else if(maandprijs == '') {
		errors = "error";
		melding('Vul een maandprijs in', 'Rood');
	}
    if(errors != '')
        return;
    var data = {
        soort : soort,
        employee : employee,
        merk : merk,
        type : type,
        aanschafdatum : aanschafdatum,
        abonnement_tot : abonnement_tot,
        abonnement_provider : abonnement_provider,
        maandprijs : maandprijs
    };

    if(laptopid != '' && laptopid != undefined)
        data['laptopid'] = laptopid;

    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/save_laptop.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Laptop opgeslagen.')
            {
                melding(result['message'], 'groen');
                var html = "";
                var item = result['item'];
                if(laptopid != '' && laptopid != undefined){
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(1)").html(item['soort']);
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(2)").html(item['merk']);
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(3)").html(item['type']);
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(4)").html(convertDateFormat(item['aanschafdatum']));
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(5)").html(item['employee_name']);
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(6)").html(convertDateFormat(item['abonnement_tot']));
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(7)").html(item['abonnement_provider']);
                    $(".laptop-table tr[laptoprow=" + laptopid + "] td:nth-child(8)").html("&euro;" + item['maandprijs']);
                }
                else{
                    html += "<tr laptoprow='" + item['id'] + "'>" + 
                    "<td>" + item['soort'] + "</td>" + 
                    "<td>" + item['merk'] + "</td>" + 
                    "<td>" + item['type'] + "</td>" + 
                    "<td>" + convertDateFormat(item['aanschafdatum'])  + "</td>" + 
                    "<td>" + item['employee_name'] + "</td>" + 
                    "<td>" + convertDateFormat(item['abonnement_tot']) + "</td>" + 
                    "<td>" + item['abonnement_provider'] + "</td>" + 
                    "<td>&euro;" + item['maandprijs'] + "</td>" + 
                    "<td><div onclick='editLaptop(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteLaptop(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td>" + 
                    "</tr>";
                
                $(".laptop-table tbody").append(html);
                }
                
                closeModal();
            }
        },
        error: function(e1, e2, e3){
            
        }
    })
    
}

function editLaptop(laptopid)
{
    prefillModal('Laptop + telefoons wijzigen', 'addLaptop.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_laptop.php",
            data: {
                laptopid: laptopid
            },
            dataType: "json",
            success: function(result){
                if(result != '')
                {
                    var soort = result['soort'];
                    var merk = result['merk'];
                    var type = result['type'];
                    var aanschafdatum = result['aanschafdatum'];
                    var employee = result['employee'];
                    var abonnement_tot = result['abonnement_tot'];
                    var abonnement_provider = result['abonnement_provider'];
                    var maandprijs = result['maandprijs'];
                    $("#laptopinfo #soort").val(soort);
                    $("#laptopinfo #merk").val(merk);
                    $("#laptopinfo #type").val(type);
                    $("#laptopinfo #aanschafdatum").val(aanschafdatum);
                    $("#laptopinfo #employee").val(employee);
                    $("#laptopinfo #abonnement_tot").val(abonnement_tot);
                    $("#laptopinfo #abonnement_provider").val(abonnement_provider);
                    $("#laptopinfo #maandprijs").val(maandprijs);
                    $("#laptopinfo .laptopid").val(result['id']);
                    showPrefilledModal();
                }
                
            },
            error: function(e1, e2, e3){

            }
        });
        
    });
}

function deleteLaptop(laptopid)
{
    showConfirm('Dit laptop verwijderen?', 'Verwijderen', 'red', 'deleteLaptopConfirm(' + laptopid + ')');
}

function deleteLaptopConfirm(laptopid)
{
    closeConfirm();
    $.ajax({
		type: "POST",
		url: "../php/bedrijfsvoering/delete_laptop.php",
		data: {
			'laptopid': laptopid
		},
		dataType: "html",
		success: function(result) {
			//alert(result);
			if(result == 'Laptop verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				$(".laptop-table tr[laptoprow=" + laptopid + "]").slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		}
	});
}


function addNewMateriaal()
{
    prefillModal('Materiaal wijzigen', 'addMateriaal.php').then(function() {
       
        showPrefilledModal();
    });
}


function saveMateriaalInfo()
{
    let soort = $("#materiaalinfo #soort").val();
    let merk = $("#materiaalinfo #merk").val();
    let name = $("#materiaalinfo #name").val();
    let aanschaf_datum = $("#materiaalinfo #aanschaf_datum").val();
    let waarde = $("#materiaalinfo #waarde").val();
    let nummer = $("#materiaalinfo #nummer").val();
    let employee = $("#materiaalinfo #employee").val();
    let errors = "";
    let materiaalid = $("#materiaalinfo .materiaalid").val();
    if(soort == '') {
		errors = "error";
		melding('Vul een soort in', 'Rood');
	} else if(employee == '') {
		errors = "error";
		melding('Vul een employee in', 'Rood');
	}
    else if(merk == '') {
		errors = "error";
		melding('Vul een merk in', 'Rood');
	}
    else if(name == '') {
		errors = "error";
		melding('Vul een naam in', 'Rood');
	}
    else if(aanschaf_datum == '') {
		errors = "error";
		melding('Vul een aanschaf datum in', 'Rood');
	}
    else if(waarde == '') {
		errors = "error";
		melding('Vul een waarde in', 'Rood');
	}
    else if(nummer == '') {
		errors = "error";
		melding('Vul een nummer in', 'Rood');
	}
    if(errors != '')
        return;
    var files = $('#materiaalinfo #file')[0].files;
    var fd = new FormData();
    fd.append('soort', soort);
    fd.append('employee', employee);
    fd.append('merk', merk);
    fd.append('name', name);
    fd.append('aanschaf_datum', aanschaf_datum);
    fd.append('waarde', waarde);
    fd.append('nummer', nummer);
    if(files.length > 0 ){
        fd.append('file', files[0]);
        $("#materiaalinfo .file-loading-icon").show();
        $("#materiaalinfo .file-icon").hide();
    }


    if(materiaalid != '' && materiaalid != undefined)
        fd.append('materiaalid', materiaalid);

    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/save_materiaal.php",
        data: fd,
        dataType: "json",	
        contentType: false,
        processData: false,
        success: function(result) {
            if(result['message'] == 'Materiaal opgeslagen.')
            {
                melding(result['message'], 'groen');
                var html = "";
                var item = result['item'];
                var path = item['file_path'];
                if(materiaalid != '' && materiaalid != undefined){
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(1)").html(item['soort']);
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(3)").html(item['merk']);
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(4)").html(item['name']);
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(5)").html(convertDateFormat(item['aanschaf_datum']));
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(6)").html("&euro;" + item['waarde']);
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(7)").html(item['employee_name']);
                    $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(8)").html(item['nummer']);
                    
                    if(path)
                     $(".materiaal-table tr[materiaalrow=" + materiaalid + "] td:nth-child(2)").html("<img src='" + $("#root_path").val() + "upload/" + path + "' onclick='openPrev(\"" + path + "\")' />");
                }
                else{
                    html += "<tr materiaalrow='" + item['id'] + "'>" + 
                    "<td>" + item['soort'] + "</td>" + 
                    "<td><img src='" +$("#root_path").val() + "images/users/vehicle.png' /></td>" + 
                    "<td>" + item['merk'] + "</td>" + 
                    "<td>" + item['name'] + "</td>" + 
                    "<td>" + convertDateFormat(item['aanschaf_datum'])  + "</td>" + 
                    "<td>&euro;" + item['waarde'] + "</td>" + 
                    "<td>" + item['employee_name'] + "</td>" + 
                    "<td>" + item['nummer'] + "</td>" + 
                    
                    "<td><div onclick='editMateriaal(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteMateriaal(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td>" + 
                    "</tr>";
                    
                
                $(".materiaal-table tbody").append(html);
                if(path)
                    $(".materiaal-table tr[materiaalrow=" + item['id'] + "] td:nth-child(2)").html("<img src='" + $("#root_path").val() + "upload/" + path + "' onclick='openPrev(\"" + path + "\")' />");
                }
                
                closeModal();
            }
        },
        error: function(e1, e2, e3){
            
        }
    })
    
}

function editMateriaal(materiaalid)
{
    prefillModal('Materiaal wijzigen', 'addMateriaal.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_materiaal.php",
            data: {
                materiaalid: materiaalid
            },
            dataType: "json",
            success: function(result){
                if(result != '')
                {
                    var soort = result['soort'];
                    var merk = result['merk'];
                    var name = result['name'];
                    var file_path = result['file_path'];
                    var aanschaf_datum = result['aanschaf_datum'];
                    var employee = result['employee'];
                    var waarde = result['waarde'];
                    var nummer = result['nummer'];
                    $("#materiaalinfo #soort").val(soort);
                    $("#materiaalinfo #merk").val(merk);
                    $("#materiaalinfo #name").val(name);
                    $("#materiaalinfo #aanschaf_datum").val(aanschaf_datum);
                    $("#materiaalinfo #employee").val(employee);
                    $("#materiaalinfo #waarde").val(waarde);
                    $("#materiaalinfo #nummer").val(nummer);
                    $("#materiaalinfo .materiaalid").val(result['id']);
                    if(file_path)
                      {  $("#materiaalinfo .img-user-avatar").attr('src', $("#root_path").val() + "upload/" + file_path);
                        
                    }
                   
                    showPrefilledModal();
                }
                
            },
            error: function(e1, e2, e3){

            }
        });
        
    });
}

function deleteMateriaal(materiaalid)
{
    showConfirm('Dit materiaal verwijderen?', 'Verwijderen', 'red', 'deleteMateriaalConfirm(' + materiaalid + ')');
}

function deleteMateriaalConfirm(materiaalid)
{
    closeConfirm();
    $.ajax({
		type: "POST",
		url: "../php/bedrijfsvoering/delete_materiaal.php",
		data: {
			'materiaalid': materiaalid
		},
		dataType: "html",
		success: function(result) {
			//alert(result);
			if(result == 'Materiaal verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				$(".materiaal-table tr[materiaalrow=" + materiaalid + "]").slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		}
	});
}

function saveVehicleInfo(){
    let kenteken = $("#vehicleinfo #kenteken").val();
    let employee = $("#vehicleinfo #employee").val();
    let merk = $("#vehicleinfo #merk").val();
    let uitvoering = $("#vehicleinfo #uitvoering").val();
    let zitplaatsen = $("#vehicleinfo #zitplaatsen").val();
    let apkdatum = $("#vehicleinfo #apkdatum").val();
    let wegenbelasting_bedrag = $("#vehicleinfo #wegenbelasting_bedrag").val();
    let verzekering_bedrag = $("#vehicleinfo #verzekering_bedrag").val();
    let lease_bedrag = $("#vehicleinfo #lease_bedrag").val();
    let lease_maatschappij = $("#vehicleinfo #lease_maatschappij").val();
    let lease_start = $("#vehicleinfo #lease_start").val();
    let lease_eind = $("#vehicleinfo #lease_eind").val();
    let restant_bedrag_slottijd = $("#vehicleinfo #restant_bedrag_slottijd").val();
    let tankpas_nummer = $("#vehicleinfo #tankpas_nummer").val();
    let pincode = $("#vehicleinfo #pincode").val();
    let track_jack = $("#vehicleinfo #track_jack").val();
    let vehicleid = $("#vehicleinfo .vehicleid").val();
    let errors = "";
    if(kenteken == '') {
		errors = "error";
		melding('Vul een kenteken in', 'Rood');
	} else if(employee == '') {
		errors = "error";
		melding('Vul een employee in', 'Rood');
	}  else if(merk == '') {
		errors = "error";
		melding('Vul een merk in', 'Rood');
	}  else if(uitvoering == '') {
		errors = "error";
		melding('Vul een uitvoering in', 'Rood');
	}  else if(zitplaatsen == '') {
		errors = "error";
		melding('Vul een zitplaatsen in', 'Rood');
	}  else if(apkdatum == '') {
		errors = "error";
		melding('Vul een apk datum in', 'Rood');
	}  else if(verzekering_bedrag == '') {
		errors = "error";
		melding('Vul een verzekering bedrag in', 'Rood');
	}  else if(wegenbelasting_bedrag == '') {
		errors = "error";
		melding('Vul een wegenbelasting bedrag in', 'Rood');
	}  else if(lease_bedrag == '') {
		errors = "error";
		melding('Vul een lease bedrag in', 'Rood');
	}  else if(lease_maatschappij == '') {
		errors = "error";
		melding('Vul een lease maatschappij in', 'Rood');
	}   else if(lease_start == '') {
		errors = "error";
		melding('Vul een lease start in', 'Rood');
	}   else if(lease_eind == '') {
		errors = "error";
		melding('Vul een lease eind in', 'Rood');
	}   else if(restant_bedrag_slottijd == '') {
		errors = "error";
		melding('Vul een restant bedrag slottijd in', 'Rood');
	}   else if(tankpas_nummer == '') {
		errors = "error";
		melding('Vul een tankpas nummer in', 'Rood');
	}   else if(pincode == '') {
		errors = "error";
		melding('Vul een pincode in', 'Rood');
	}   else if(track_jack == '') {
		errors = "error";
		melding('Vul een track jack in', 'Rood');
	}

    var files = $('#vehicleinfo #file')[0].files;
    var fd = new FormData();
    fd.append('kenteken', kenteken);
    fd.append('employee', employee);
    fd.append('merk', merk);
    fd.append('uitvoering', uitvoering);
    fd.append('zitplaatsen', zitplaatsen);
    fd.append('apkdatum', apkdatum);
    fd.append('verzekering_bedrag', verzekering_bedrag);
    fd.append('wegenbelasting_bedrag', wegenbelasting_bedrag);
    fd.append('lease_bedrag', lease_bedrag);
    fd.append('lease_maatschappij', lease_maatschappij);
    fd.append('lease_start', lease_start);
    fd.append('lease_eind', lease_eind);
    fd.append('restant_bedrag_slottijd', restant_bedrag_slottijd);
    fd.append('tankpas_nummer', tankpas_nummer);
    fd.append('pincode', pincode);
    fd.append('track_jack', track_jack);
    if(files.length > 0 ){
        fd.append('file', files[0]);
        $("#vehicleinfo .file-loading-icon").show();
        $("#vehicleinfo .file-icon").hide();
    }
    if(vehicleid != '' && vehicleid != undefined)
        fd.append('vehicleid', vehicleid);
    if(errors != '')
    {
        return;
    }
    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/save_vehicle.php",
        data: fd,
        dataType: "json",	
        contentType: false,
        processData: false,
        success: function(result) {
            if(result['message'] == 'Vehicle opgeslagen.')
            {
                melding(result['message'], 'groen');
                var html = "";
                var item = result['item'];//
                var path = item['file_path'];
                if(vehicleid != '' && vehicleid != undefined){
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(1)").html(item['kenteken']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(3)").html(item['employee_name']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(4)").html(item['merk']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(5)").html(item['uitvoering']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(6)").html(item['zitplaatsen']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(7)").html(convertDateFormat(item['apkdatum']));
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(8)").html(item['lease_maatschappij']);
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(9)").html(convertDateFormat(item['lease_start']));
                    $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(10)").html(convertDateFormat(item['lease_eind']));
                    if(path)
                        $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(2)").html("<img src='" + $("#root_path").val() + "upload/" + path + "'  onclick='openPrev(\"" + path + "\")'/>");

                }else{
                    html += "<tr vehiclerow='" + item['id'] + "'>" + 
                    "<td>" + item['kenteken'] + "</td>" + 
                    "<td><img src='" +$("#root_path").val() + "images/users/vehicle.png' /></td>" + 
                    "<td>" + item['employee_name'] + "</td>" + 
                    "<td>" + item['merk'] + "</td>" + 
                    "<td>" + item['uitvoering'] + "</td>" + 
                    "<td>" + item['zitplaatsen'] + "</td>" + 
                    "<td>" + convertDateFormat(item['apkdatum']) + "</td>" + 
                    "<td>" + item['lease_maatschappij'] + "</td>" + 
                    "<td>" + convertDateFormat(item['lease_start']) + "</td>" + 
                    "<td>" + convertDateFormat(item['lease_eind']) + "</td>" + 
                    "<td><div onclick='manageVehicleFileLog(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Bestanden'><i class='material-icons'>attach_file</i></div> <div onclick='vehicleNote(" + item['id'] + ")' class='actiebutton tooltipped icon_vehiclenote' data-position='top'><i class='material-icons'>insert_comment</i></div> <div onclick='editVehicle(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div> <div onclick='deleteVehicle(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td>" + 
                    "</tr>" + 
                    "<tr class='row_vehicle_note' vehicle_id=" + item['id'] + "></tr>";
                    $(".vehicle-table tbody").append(html);
                    if(path){
                        $(".vehicle-table tr[vehiclerow=" + vehicleid + "] td:nth-child(2)").html("<img src='" + $("#root_path").val() + "upload/" + path + "' onclick='openPrev(\"" + path + "\")'/>");
                    }
                       
                }
                closeModal();
            }
        },
        error: function(e1, e2, e3)
        {

        }
    })
}


function editVehicle(vehicleid)
{
    prefillModal('Voertuigen wijzigen', 'addVehicle.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_vehicle.php",
            data: {
                vehicleid: vehicleid
            },
            dataType: "json",
            success: function(result){
                if(result != '')
                {
                    $("#vehicleinfo #kenteken").val(result['kenteken']);
                    $("#vehicleinfo #employee").val(result['employee']);
                    $("#vehicleinfo #merk").val(result['merk']);
                    $("#vehicleinfo #uitvoering").val(result['uitvoering']);
                    $("#vehicleinfo #zitplaatsen").val(result['zitplaatsen']);
                    $("#vehicleinfo #apkdatum").val(result['apkdatum']);
                    $("#vehicleinfo #wegenbelasting_bedrag").val(result['wegenbelasting_bedrag']);
                    $("#vehicleinfo #verzekering_bedrag").val(result['verzekering_bedrag']);
                    $("#vehicleinfo #lease_bedrag").val(result['lease_bedrag']);
                    $("#vehicleinfo #lease_maatschappij").val(result['lease_maatschappij']);
                    $("#vehicleinfo #lease_start").val(result['lease_start']);
                    $("#vehicleinfo #lease_eind").val(result['lease_eind']);
                    $("#vehicleinfo #restant_bedrag_slottijd").val(result['restant_bedrag_slottijd']);
                    $("#vehicleinfo #tankpas_nummer").val(result['tankpas_nummer']);
                    $("#vehicleinfo #pincode").val(result['pincode']);
                    $("#vehicleinfo #track_jack").val(result['track_jack']);
                    
                    $("#vehicleinfo .vehicleid").val(result['id']);
                    var file_path = result['file_path'];
                    if(file_path){
                        $("#vehicleinfo .img-user-avatar").attr('src', $("#root_path").val() + "upload/" + file_path);
                    }
                    

                    $("#vehicleinfo select").formSelect();
                    showPrefilledModal();
                }
                
            },
            error: function(e1, e2, e3){

            }
        });
        
    });
}

function changeVacatureStatus(vacatureid, action) {
    showConfirm('Kandidaat afwijzen?', 'Afwijzen', 'red', 'changeVacatureConfirm(' + vacatureid + ', '+ action +')');
}
function changeVacatureConfirm(vacatureid, action) {
    closeConfirm();
    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/change_vacature_status.php",
        data: {
            'vacatureid': vacatureid,
            'action': action
        },
        dataType: "html",
        success: function(result) {
            //alert(result);
            if(result == 'status changed.') {
                //Alles ging goed
                melding(result, 'groen');
                if(action == 0) {
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .vacature-status").css('color', '#212121');
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .vacature-status").text('	In behandeling');

                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .reset-status").css('display', 'none');
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .default-status").css('display', 'inline-block');
                } else {
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .vacature-status").css('color', '#F44336');
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .vacature-status").text('Afgewezen');

                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .reset-status").css('display', 'inline-block');
                    $(".vacature-table tr[vacaturerow=" + vacatureid + "] .default-status").css('display', 'none');
                }
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}

function addNewVacature()
{
    showEmptyModal('Vacature wijzigen', 'addVacature.php');
}

function editVacature(vacatureid)
{
    prefillModal('Vacature wijzigen', 'addVacature.php').then(function() {
        $.ajax({
			type: "POST",
			url: "../php/bedrijfsvoering/get_vacature.php",
			data: {
				vacatureid: vacatureid
			},
			dataType: "json",
			success: function(result) {
                var root_path = $("#root_path").val();

                $("#vacatureinfo #name").val(result['full_name']);
                $("#vacatureinfo #email").val(result['email']);
                $("#vacatureinfo #phone").val(result['phone']);
                $("#vacatureinfo #description").val(result['message']);
                $("#vacatureinfo #vacature_id").val(result['ID']);
                if(result['cv'])
                {
                    $("#cv_link").attr("href", root_path + 'upload/' + result['cv']);
                    $("#cv_link").parent().parent().addClass("file-existing");
                }

                if(result['mot'])
                {
                    $("#mot_link").attr("href", root_path + 'upload/' + result['mot']);
                    $("#mot_link").parent().parent().addClass("file-existing");
                }
                showPrefilledModal();
            },
            error: function(e1, e2, e3) {

            }
        });
    });
}

function removeVacatureFile(elem)
{
    $(elem).parent().removeClass("file-existing")
}
function saveVacature()
{
    let name = $("#vacatureinfo #name").val();
	let email = $("#vacatureinfo #email").val();
	let phone = $("#vacatureinfo #phone").val();
    let message = $("#vacatureinfo #description").val();
    let errors = "";
	if(name == '') {
		errors = "error";
		melding('Vul een naam in', 'Rood');
	} else if(email == '') {
		errors = "error";
		melding('Vul een e-mailadres in', 'Rood');
	} else if(phone == '') {
		errors = "error";
		melding('Vul een telefoonummer in', 'Rood');
	}

    if(errors == '') {
		var data = $("#vacatureinfo").serializeArray();
        var fd = new FormData();
        for(var index = 0; index < data.length; index ++)
        {
            fd.append(data[index].name, data[index].value);
        }

        var files = $('#cv_file')[0].files;
        if(files.length > 0 ){
            fd.append('cv', files[0]);
        }
        files = $('#mot_file')[0].files;
        if(files.length > 0 ){
            fd.append('mot', files[0]);
        }

        showLoading();
		$.ajax({
			type: "POST",
			url: "../php/bedrijfsvoering/save_vacature.php",
			data: fd,
            contentType: false,
            processData: false,
            dataType: "json",
			success: function(result) {
                if(result['message'] == 'Vacature opgeslagen.')
                {
                    melding(result['message'], 'groen');
                    var item = result['item'];
                    if($("#vacatureinfo #vacature_id").val() == "")
                    {
                        var html = "<tr vacaturerow='" + item['ID'] + "' class='vacaturerow'><td>" + item['full_name'] + "</td><td>" + item['email'] + "</td><td>" + item['phone'] + "</td><td><span class='vacature-status'>" + item['status'] + "</span></td><td>" +
                        "<div style='display: inline-block;' onclick='changeVacatureStatus(" + item['ID'] + ", 1)' class='actiebutton tooltipped default-status' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>close</i></div>" +
                        "<div style='display: none;' onclick='changeVacatureStatus(" + item['ID'] + ", 0)' class='actiebutton tooltipped reset-status' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>refresh</i></div>" +
                        "<div onclick='editVacature(" + item['ID'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div>" +
                        "<div onclick='deleteVacature(" + item['ID'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div>" +
                        "</td></tr>";
    
                        $(".vacature-table").append(html);
                    }
                    else{
                        var red            =   '';
                        var show_reset     =   'none';
                        var show_default   =   'inline-block';
                        if(item['status'] == 'Afgewezen') {
                            red            =   'color:#F44336;';
                            show_reset     =   'inline-block';
                            show_default   =   'none';
                        }
                        var html = "<td>" + item['full_name'] + "</td><td>" + item['email'] + "</td><td>" + item['phone'] + "</td><td><span class='vacature-status' style='" + red + "'>" + item['status'] + "</span></td><td>" +
                        "<div style='display: " + show_default + ";' onclick='changeVacatureStatus(" + item['ID'] + ", 1)' class='actiebutton tooltipped default-status' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>close</i></div>" +
                        "<div style='display: " + show_reset + ";' onclick='changeVacatureStatus(" + item['ID'] + ", 0)' class='actiebutton tooltipped reset-status' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>refresh</i></div>" +
                        "<div onclick='editVacature(" + item['ID'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div>" +
                        "<div onclick='deleteVacature(" + item['ID'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div>" +
                        "</td>";
    
                        $(".vacature-table tr[vacaturerow=" + item['ID'] + "]").html(html);
                    }
                    hideLoading();
                    closeModal();
                }
			},
            error: function(e1, e2, e3)
            {
                debugger;
            }
		});
	}

}

function deleteVacature(vacatureid) {
    showConfirm('Dit vacature verwijderen?', 'Verwijderen', 'red', 'deleteVacatureConfirm(' + vacatureid + ')');
}
function deleteVacatureConfirm(vacatureid) {
    closeConfirm();
    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/delete_vacature.php",
        data: {
            'vacatureid': vacatureid
        },
        dataType: "html",
        success: function(result) {
            //alert(result);
            if(result == 'Vacature verwijderd.') {
                //Alles ging goed
                melding(result, 'groen');
                $(".vacature-table tr[vacaturerow=" + vacatureid + "]").slideUp();
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}

function fileselectedInFileLog(elem)
{
    $(elem).parent().parent().addClass("file-selected");
}
function deleteVehicle(vehicleid)
{
    showConfirm('Dit vehicle verwijderen?', 'Verwijderen', 'red', 'deleteVehicleConfirm(' + vehicleid + ')');
}

function deleteVehicleConfirm(vehicleid)
{
    closeConfirm();
    $.ajax({
		type: "POST",
		url: "../php/bedrijfsvoering/delete_vehicle.php",
		data: {
			'vehicleid': vehicleid
		},
		dataType: "html",
		success: function(result) {
			//alert(result);
			if(result == 'Vehicle verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				$(".vehicle-table tr[vehiclerow=" + vehicleid + "]").slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		}
	});
}

function fileselected()
{
    var files = $('.popup #file')[0].files;
    if(files.length > 0)
    {
        $(".popup .btn-filelog").addClass('file_selected');
    }
    else{
        $(".popup .btn-filelog").removeClass('file_selected');
    }
}

function showInnerFConfirm(clID)
{
    $("#fipc-" + clID).show();
    $("#fipo-" + clID).show();
    $("#fipc-" + clID).addClass('visible');
    $("#fipo-" + clID).addClass('visible');
}

function closeInnerFConfirm(clID)
{
    $("#fipc-" + clID).removeClass('visible');
    $("#fipo-" + clID).removeClass('visible');
    setTimeout(function() {
        $("#fipo-" + clID).hide();
        $("#fipc-" + clID).hide();
    }, 300);
}


function vehicleNote(vehicleid)
{
    var item = $(".vehicle-table tr[vehicle_id=" + vehicleid + "]");
    if(item.hasClass('display-notes'))
    {
        $(".vehicle-table tr[vehicle_id=" + vehicleid + "] .vehicle_note_container").slideUp();
        item.removeClass('display-notes');
    }
    else{
        var data = {
            vehicle_id : vehicleid
        }
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_vehicle_notes.php",
            data: data,
            dataType: "json",
            success: function(result) {
                item.addClass('display-notes');
                item.empty();
                var html = '';
                html = "<td colspan=11><div class='row vehicle_note_container'><div class='col s5 row'><div  class='col s11'><textarea type='text' class='vehicle-note-text materialize-textarea' placeholder='Typ hier je notitie.'></textarea></div><div class='col s1'><div class='file-field input-field col s1 btn-filelog'><div class='preloader-wrapper small active file-loading-icon'><div class='spinner-layer spinner-blue-only'><div class='circle-clipper left'><div class='circle'></div></div><div class='gap-patch'><div class='circle'></div></div><div class='circle-clipper right'><div class='circle'></div></div></div></div><div class='file-icon'><i class='material-icons'>attach_file</i><input type='file' class='note_file' onchange='fileselected_note_vehicle(" + vehicleid + ")'></div><div class='file-path-wrapper'><input class='file-path validate' type='text' hidden></div></div></div><div><span class='button waves-effect waves-light btn' onclick='saveVehicleNote(" + vehicleid + ")'><i class='material-icons'>add</i> Notitie toevoegen</span></div></div><div class='col s6 vehicle_note_panel'>";
                for(var index = 0; index <result.length; index ++)
                {
                    html += getVehicleNoteRowFromRawData(result[index]);
                }
                html += "</div></div></td>";
                item.append(html);
                $(".vehicle-table tr[vehicle_id=" + vehicleid + "] .vehicle_note_container").show('fast');
            },
            error(e1, e2, e3) {
    
            }
        });   
    }
}

function getVehicleNoteRowFromRawData(result, display = true)
{
    var root_path = $("#root_path").val();
    var html = "<div class='vehicle_note' id='vehicle_note_" + result['id'] + "' ";
    if(!display)
        html += "style='display: none;'";
    html += ">";
    var file_path = result['file_path'];
    if(file_path != undefined && file_path != null && file_path != '')
    {
        html += "<div class='note_prev'>";
        if(result['file_exe'] == 'pdf')
        {
            html += '<a class="img-pdf" href="' + root_path + 'upload/' + result['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
        }
        else
        {
            html += '<a class="img-preview" onclick="openPrev(\'' + result['file_path'] + '\')"><img src="' + root_path + 'upload/' + result['file_path'] + '"></a>';
        }
        html += "</div>";
    }
    html += "<div><div class='note_header'>"
        + "<span class='note_date'>" + result['created_at'] + "</span>" +
            "<span class='note-user'><i class='material-icons'>person</i> " + result['username'] + "</span>" + 
            "<div onclick='showVehicleNoteInnerConfirm(" + result['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></div>" + 
            "<div class='note_content'>" + result['data'] + "</div></div>" + 
            '<div id="vnipo-' + result['id'] + '" class="inner-popup-overlay"></div><div id="vnipc-' + result['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeVehicleNoteInnerConfirm(' + result['id'] + ')">Annuleren</span><span class="button red" onclick="deleteVehicleNoteConfirm(' + result['id'] + ')">Verwijderen</span></div></div>' + 
            "</div>";
    return html;
}

function fileselected_note_vehicle(vehicle_id)
{
    var files = $('.vehicle-table tr[vehicle_id=' + vehicle_id + '] .note_file')[0].files;
    if(files.length > 0)
    {
        $(".vehicle-table tr[vehicle_id=" + vehicle_id + "] .btn-filelog").addClass('file_selected');
    }
    else{
        $(".vehicle-table tr[vehicle_id=" + vehicle_id + "] .btn-filelog").removeClass('file_selected');
    }
}


function saveVehicleNote(vehicle_id)
{
    var user_id = $(".userid").text();
    var text = $(".vehicle-table tr[vehicle_id=" + vehicle_id + "] .vehicle-note-text").val();
    var files = $('.vehicle-table tr[vehicle_id=' + vehicle_id + '] .note_file').prop('files');
    var data = new FormData();
    data.append('vehicle_id',vehicle_id );
    data.append('user_id',user_id );
    data.append('data',text );
    if(files.length > 0)
        data.append('file',files[0]);
    else 
        data.append('file', '');
    $(".vehicle-table tr[vehicle_id=" + vehicle_id + "]  .file-loading-icon").show();
    $(".vehicle-table tr[vehicle_id=" + vehicle_id + "]  .file-icon").hide();

    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/save_vehicle_note.php",
        data: data,
        dataType: "json",
        contentType: false,
        processData: false,
        
        success: function(result) {
            if(result['message'] == 'Notitie opgeslagen.')
            {
                var html = getVehicleNoteRowFromRawData(result['data'], false);
                $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note  .vehicle_note_panel").prepend(html);
                $("#vehicle_note_" + result['data']['id']).slideDown();
                $(".vehicle-table tr[vehiclerow=" + vehicle_id + "] .icon_vehiclenote").addClass("hasnote");
                melding(result['message'], 'groen');

            }
            else{
                
                melding(result['message'], 'rood');
            }



            $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note .vehicle-note-text").val("");
            $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note .file-loading-icon").hide();
            $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note .file-icon").show();
            $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note .note_file").val("");
            $(".vehicle-table tr[vehicle_id=" + vehicle_id + "].row_vehicle_note .btn-filelog").removeClass('file_selected');

        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function showVehicleNoteInnerConfirm(vehicle_note_id)
{
    $("#vnipc-" + vehicle_note_id).show();
    $("#vnipo-" + vehicle_note_id).show();
    $("#vnipc-" + vehicle_note_id).addClass('visible');
    $("#vnipo-" + vehicle_note_id).addClass('visible');
}

function deleteVehicleNoteConfirm(vehicle_note_id)
{
    closeVehicleNoteInnerConfirm(vehicle_note_id)
    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/delete_vehicle_note.php",
        data: {
            'vehicle_note_id': vehicle_note_id
        },
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Notitie item verwijderd.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                
                if(result['note_count'] > 0)
                {
                    $(".vehicle-table tr[vehiclerow=" + result['vehicle_id'] + "] .icon_vehiclenote").addClass("hasnote");
                }
                else{
                    $(".vehicle-table tr[vehiclerow=" + result['vehicle_id'] + "] .icon_vehiclenote").removeClass("hasnote");
                }

                $("#vehicle_note_" + vehicle_note_id).slideUp();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3){
            
        }
    });
}

function closeVehicleNoteInnerConfirm(note_id)
{
    $("#vnipc-" + note_id).removeClass('visible');
    $("#vnipo-" + note_id).removeClass('visible');
    setTimeout(function() {
        $("#vnipo-" + note_id).hide();
        $("#vnipc-" + note_id).hide();
    }, 300);
}

function manageVehicleFileLog(vehicle_id)
{
    prefillModal('Bestanden bij voertuig', 'manageVehicleFile.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_vehicle_file.php",
            data: {
                vehicle_id: vehicle_id
            },
            dataType: "json",
            success: function(result) {
                var projectfilehtml = "";
                if(result)
                {
                   for(var index = 0; index < result.length; index ++)
                   {
                        projectfilehtml += getVehicleFileLogHtml(result[index]);
                   }
                }
                $(".popup.large .logs").html(projectfilehtml);
                $('.tooltipped').tooltip();

                $(".vehicleid").val(vehicle_id);
                showPrefilledModal();
            },
            error: function(e1, e2, e3)
            {

            }
        });
    });
}

function getVehicleFileLogHtml(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="flog-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper"><div class="flog-prev">';
    if(item['file_exe'] == 'pdf') 
    {
        html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
    }
    else{
        html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
    }
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name">' + item['name'] + '</span>' +
    '<div onclick="showInnerFConfirm(' + item['id'] + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' + 
    '<a href="' + root_path + 'upload/' + item['file_path'] + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' + 
    '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['uploaded_date']) + '</span>' + 
    '<span class="fc-user"><i class="material-icons">person</i>' + item['username'] + '</span>';

    html +='</div>' + 
    '<div id="fipo-' + item['id'] + '" class="inner-popup-overlay"></div>' + 
    '<div id="fipc-' + item['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerFConfirm(' + item['id'] + ')">Annuleren</span><span class="button red" onclick="deleteVehicleFConfirm(' + item['id'] + ')">Verwijderen</span></div></div>' + 
    '</div></div>';
    return html;
}

function saveVehicleFile()
{
    var vehicle_id = $(".vehicleid").val();
    var file_type = $("#type").val();
    var files = $('#file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file',files[0]);
        fd.append('file_type', file_type);
        fd.append('file_name', $("#name").val());
        fd.append('vehicle_id', vehicle_id);
        fd.append('user_id', $("#vehiclefilelog .userid").val());
        $(".file-loading-icon").show();
        $(".file-icon").hide();
    $.ajax({
        url: "../php/bedrijfsvoering/vehicle_file_upload.php",
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result){
            if (result['message'] == 'Bestand opgeslagen') {
                //Alles ging goed
                
                var fileloghtml = getVehicleFileLogHtml(result['inserted_file'], true);
                $(".file-loading-icon").hide();
                $(".file-icon").show();
                $(".popup.large .logs").prepend(fileloghtml);
				$('.tooltipped').tooltip();
				$('#flog-' + result['inserted_file']['id']).slideDown();
                melding(result['message'], 'groen');
                $("#file").val("");
                $("#type").val("");
                $("#name").val("");
                $("#type").formSelect();
                $(".popup .btn-filelog").removeClass('file_selected');
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {

        }
     });
    }else{
        melding('Selecteer eerst een bestand', 'rood');
    }
}



function deleteVehicleFConfirm(fID)
{
     //closeConfirm();
     closeInnerFConfirm(fID)
     $.ajax({
         type: "POST",
         url: "../php/bedrijfsvoering/delete_vehicle_file_log.php",
         data: {
             'file_id': fID
         },
         dataType: "html",
         success: function(result) {
             if (result == 'Bestand verwijderd') {
                 //Alles ging goed
                 melding(result, 'groen');
                 $("#flog-" + fID).slideUp();
             } else {
                 //Er ging iets mis
                 melding(result, 'rood');
             }
         }
     });
}




function manageEmployeeFileLog(employee_id)
{
    prefillModal('Personeel wijzigen', 'manageEmployeeFile.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_employee_file.php",
            data: {
                employee_id: employee_id
            },
            dataType: "json",
            success: function(result) {
                var projectfilehtml = "";
                if(result)
                {
                   for(var index = 0; index < result.length; index ++)
                   {
                        projectfilehtml += getEmployeeFileLogHtml(result[index]);
                   }
                }
                $(".popup.large .logs").html(projectfilehtml);
                $('.tooltipped').tooltip();

                $(".employeeid").val(employee_id);
                showPrefilledModal();
            },
            error: function(e1, e2, e3)
            {

            }
        });
    });
}

function getEmployeeFileLogHtml(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="flog-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper"><div class="flog-prev">';
    if(item['file_exe'] == 'pdf') 
    {
        html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
    }
    else{
        html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
    }
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name">' + item['name'] + '</span>' +
    '<div onclick="showInnerFConfirm(' + item['id'] + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' + 
    '<a href="' + root_path + 'upload/' + item['file_path'] + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' + 
    '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['uploaded_date']) + '</span>' + 
    '<span class="fc-user"><i class="material-icons">person</i>' + item['username'] + '</span>';

    html +='</div>' + 
    '<div id="fipo-' + item['id'] + '" class="inner-popup-overlay"></div>' + 
    '<div id="fipc-' + item['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerFConfirm(' + item['id'] + ')">Annuleren</span><span class="button red" onclick="deleteEmployeeFConfirm(' + item['id'] + ')">Verwijderen</span></div></div>' + 
    '</div></div>';
    return html;
}

function saveEmployeeFile()
{
    var employee_id = $(".employeeid").val();
    var file_type = $("#type").val();
    var files = $('#file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file',files[0]);
        fd.append('file_type', file_type);
        fd.append('file_name', $("#name").val());
        fd.append('employee_id', employee_id);
        fd.append('user_id', $("#employeefilelog .userid").val());
        $(".file-loading-icon").show();
        $(".file-icon").hide();
    $.ajax({
        url: "../php/bedrijfsvoering/employee_file_upload.php",
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result){
            if (result['message'] == 'Bestand opgeslagen') {
                //Alles ging goed
                
                var fileloghtml = getEmployeeFileLogHtml(result['inserted_file'], true);
                $(".file-loading-icon").hide();
                $(".file-icon").show();
                $(".popup.large .logs").prepend(fileloghtml);
				$('.tooltipped').tooltip();
				$('#flog-' + result['inserted_file']['id']).slideDown();
                melding(result['message'], 'groen');
                $("#file").val("");
                $("#type").val("");
                $("#name").val("");
                $("#type").formSelect();
                $(".popup .btn-filelog").removeClass('file_selected');
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {

        }
     });
    }else{
        melding('Selecteer eerst een bestand', 'rood');
    }
}





function deleteEmployeeFConfirm(fID)
{
     //closeConfirm();
     closeInnerFConfirm(fID)
     $.ajax({
         type: "POST",
         url: "../php/bedrijfsvoering/delete_employee_file_log.php",
         data: {
             'file_id': fID
         },
         dataType: "html",
         success: function(result) {
             if (result == 'Bestand verwijderd') {
                 //Alles ging goed
                 melding(result, 'groen');
                 $("#flog-" + fID).slideUp();
             } else {
                 //Er ging iets mis
                 melding(result, 'rood');
             }
         }
     });
}




function employeeNote(employee_id)
{
    var item = $(".row_employee_note[employee_id=" + employee_id + "]");
    if(item.hasClass('display-notes'))
    {
        $(".row_employee_note[employee_id=" + employee_id + "] .employee_note_container").slideUp();
        item.removeClass('display-notes');
    }
    else{
        var data = {
            employee_id : employee_id
        }
        $.ajax({
            type: "POST",
            url: "../php/bedrijfsvoering/get_employee_notes.php",
            data: data,
            dataType: "json",
            success: function(result) {
                item.addClass('display-notes');
                item.empty();
                var html = '';
                html = "<td class='row employee_note_container' colspan=12><div class='col s5 row'><div  class='col s11'><textarea type='text' class='employee-note-text materialize-textarea' placeholder='Typ hier je notitie.'></textarea></div><div class='col s1'><div class='file-field input-field col s1 btn-filelog'><div class='preloader-wrapper small active file-loading-icon'><div class='spinner-layer spinner-blue-only'><div class='circle-clipper left'><div class='circle'></div></div><div class='gap-patch'><div class='circle'></div></div><div class='circle-clipper right'><div class='circle'></div></div></div></div><div class='file-icon'><i class='material-icons'>attach_file</i><input type='file' class='note_file' onchange='fileselected_note_employee(" + employee_id + ")'></div><div class='file-path-wrapper'><input class='file-path validate' type='text' hidden></div></div></div><div><span class='button waves-effect waves-light btn' onclick='saveEmployeeNote(" + employee_id + ")'><i class='material-icons'>add</i> Notitie toevoegen</span></div></div><div class='col s6 employee_note_panel'>";
                for(var index = 0; index <result.length; index ++)
                {
                    html += getEmployeeNoteRowFromRawData(result[index]);
                }
                html += "</div></div>";
                item.append(html);
                $(".row_employee_note[employee_id=" + employee_id + "] .employee_note_container").show('fast');
            },
            error(e1, e2, e3) {
    
            }
        });   
    }
}



function getEmployeeNoteRowFromRawData(result, display = true)
{
    var root_path = $("#root_path").val();
    var html = "<div class='employee_note' id='employee_note_" + result['id'] + "' ";
    if(!display)
        html += "style='display: none;'";
    html += ">";
    var file_path = result['file_path'];
    if(file_path != undefined && file_path != null && file_path != '')
    {
        html += "<div class='note_prev'>";
        if(result['file_exe'] == 'pdf')
        {
            html += '<a class="img-pdf" href="' + root_path + 'upload/' + result['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
        }
        else
        {
            html += '<a class="img-preview" onclick="openPrev(\'' + result['file_path'] + '\')"><img src="' + root_path + 'upload/' + result['file_path'] + '"></a>';
        }
        html += "</div>";
    }
    html += "<div><div class='note_header'>"
        + "<span class='note_date'>" + result['created_at'] + "</span>" +
            "<span class='note-user'><i class='material-icons'>person</i> " + result['username'] + "</span>" + 
            "<div onclick='showEmployeeNoteInnerConfirm(" + result['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></div>" + 
            "<div class='note_content'>" + result['data'] + "</div></div>" + 
            '<div id="enipo-' + result['id'] + '" class="inner-popup-overlay"></div><div id="enipc-' + result['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeEmployeeNoteInnerConfirm(' + result['id'] + ')">Annuleren</span><span class="button red" onclick="deleteEmployeeNoteConfirm(' + result['id'] + ')">Verwijderen</span></div></div>' + 
            "</div>";
    return html;
}

function fileselected_note_employee(employee_id)
{
    var files = $('#employee-list .row_employee_note[employee_id=' + employee_id + '] .note_file')[0].files;
    if(files.length > 0)
    {
        $("#employee-list .row_employee_note[employee_id=" + employee_id + "] .btn-filelog").addClass('file_selected');
    }
    else{
        $("#employee-list .row_employee_note[employee_id=" + employee_id + "] .btn-filelog").removeClass('file_selected');
    }
}

function saveEmployeeNote(employee_id)
{
    var user_id = $(".userid").text();
    var text = $("#employee-list .row_employee_note[employee_id=" + employee_id + "] .employee-note-text").val();
    var files = $('#employee-list .row_employee_note[employee_id=' + employee_id + '] .note_file').prop('files');
    var data = new FormData();
    data.append('employee_id',employee_id );
    data.append('user_id',user_id );
    data.append('data',text );
    if(files.length > 0)
        data.append('file',files[0]);
    else 
        data.append('file', '');
    $("#employee-list .row_employee_note[employee_id=" + employee_id + "]  .file-loading-icon").show();
    $("#employee-list .row_employee_note[employee_id=" + employee_id + "]  .file-icon").hide();

    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/save_employee_note.php",
        data: data,
        dataType: "json",
        contentType: false,
        processData: false,
        
        success: function(result) {
            if(result['message'] == 'Notitie opgeslagen.')
            {
                var html = getEmployeeNoteRowFromRawData(result['data'], false);
                $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note  .employee_note_panel").prepend(html);
                $("#employee_note_" + result['data']['id']).slideDown();
                $("#employee-list .employee-item[employeerow=" + employee_id + "] .icon_employeenote").addClass("hasnote");
                melding(result['message'], 'groen');

            }
            else{
                
                melding(result['message'], 'rood');
            }



            $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note .employee-note-text").val("");
            $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note .file-loading-icon").hide();
            $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note .file-icon").show();
            $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note .note_file").val("");
            $("#employee-list .row_employee_note[employee_id=" + employee_id + "].row_employee_note .btn-filelog").removeClass('file_selected');

        },
        error: function(e1, e2, e3)
        {

        }
    });
}


function showEmployeeNoteInnerConfirm(employee_note_id)
{
    $("#enipc-" + employee_note_id).show();
    $("#enipo-" + employee_note_id).show();
    $("#enipc-" + employee_note_id).addClass('visible');
    $("#enipo-" + employee_note_id).addClass('visible');
}

function closeEmployeeNoteInnerConfirm(note_id)
{
    $("#enipc-" + note_id).removeClass('visible');
    $("#enipo-" + note_id).removeClass('visible');
    setTimeout(function() {
        $("#enipo-" + note_id).hide();
        $("#enipc-" + note_id).hide();
    }, 300);
}


function deleteEmployeeNoteConfirm(employee_note_id)
{
    closeEmployeeNoteInnerConfirm(employee_note_id)
    $.ajax({
        type: "POST",
        url: "../php/bedrijfsvoering/delete_employee_note.php",
        data: {
            'employee_note_id': employee_note_id
        },
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Notitie item verwijderd.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                
                if(result['note_count'] > 0)
                {
                    $("#employee-list .employee-item[employeerow=" + result['employee_id'] + "] .icon_employeenote").addClass("hasnote");
                }
                else{
                    $("#employee-list .employee-item[employeerow=" + result['employee_id'] + "] .icon_employeenote").removeClass("hasnote");
                }

                $("#employee_note_" + employee_note_id).slideUp();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3){
            
        }
    });
}


function manageEmployeeWorkingDate(employee_id)
{
    prefillModal('Personeel wijzigen','manageEmployeeWorkingSchedule.php').then(function() {
    
    
    
        $.ajax({
        
            type: "POST",
        
            url: "../php/settings/get_employee.php", 
        
            data: {employeeid:employee_id},
        
            dataType: "json",
        
            success: function(result){
                if(result)
                {
                    $("#ews_log .employeeid").val(employee_id);
                    result = result[0];
                    if(result['mo'])
                        $("#ews_log #ews_mo").prop('checked', true);
                    if(result['tu'])
                        $("#ews_log #ews_tu").prop('checked', true);
                    if(result['we'])
                        $("#ews_log #ews_we").prop('checked', true);
                    if(result['th'])
                        $("#ews_log #ews_th").prop('checked', true);
                    if(result['fr'])
                        $("#ews_log #ews_fr").prop('checked', true);
                    if(result['sa'])
                        $("#ews_log #ews_sa").prop('checked', true);
                    var title = '<span class="name">' + result['name'] + ' </span>';
                        $(".popup.large .title").html(title);
                    var html= '';
                    for(var index = 0; index < result['working_schedule'].length; index ++)
                    {
                        var item = result['working_schedule'][index];
                        html += getEmployeelogStr(employee_id, item['id'], item);
                    }

                    $(".popup.large .logs").html(html);
                    showPrefilledModal();
                }
                
            },
            error: function(e1, e2, e3){

            }
        });
    });
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



function getEmployeelogStr(employeeid, id, item, isnew = false)
{
    var employeeloghtml = '';


    var elid = id;
    var date_from = item.date_from;
    date_from = convertDateFormat(date_from);
    var date_to = item.date_to;
    date_to = convertDateFormat(date_to);
    var user = item.username;
    var desc = item.text;
    var type_Str = item.option;
    var date = date_from;
    if(date_from != date_to)
        date = date_from + " / " + date_to;

    if(isnew)
    employeeloghtml += '<div id="clog-' + elid + '" class="log-container" style="display:none;">';
    else 
    employeeloghtml += '<div id="clog-' + elid + '" class="log-container">';

       
    employeeloghtml +='<div class="log-wrapper"><div class="log-header"><span class="c-type ec-type">' + type_Str + '</span><span class="c-date ec-date">' + date + '</span><span class="c-user ec-user"><i class="material-icons">person</i> ' + user + '</span><div onclick="showInnerConfirm(' + elid + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div></div>' + '<div class="log-content">' + desc + '</div><div id="ipo-' + elid + '" class="inner-popup-overlay"></div><div id="ipc-' + elid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerConfirm(' + elid + ')">Annuleren</span><span class="button red" onclick="deleteCLogConfirm(' + employeeid + ',' + elid + ')">Verwijderen</span></div></div></div></div>';

    
    return employeeloghtml;

}


function    checkDay(ele, day_index)
{
    var employee_id = $("#ews_log .employeeid").val();
    var day_list = ['mo', 'tu', 'we', 'th', 'fr', 'sa'];
    var day = day_list[day_index];
    $.ajax({
        
        type: "POST",
    
        url: "../php/settings/set_employee_day.php", 
    
        data: {employee_id:employee_id, day:day, value: $(ele).prop('checked')},
    
        dataType: "html",
    
        success: function(result){
            if(result != 'Successfully updated')
                melding(result, 'rood');
        }
    });
}

function saveEmployeeSchedule()
{
    var user_id = $("#ews_log .userid").val();
    var employeeid = $("#ews_log .employeeid").val();
    var option = $("#ews_option").val();
    var date_from = $("#ews_log #ews_date_from").val();
    var date_to = $("#ews_log #ews_date_to").val();
    var text = $("#ews_log #ews_text").val();
    let errors = '';
    if(option == '') {
		errors = "error";
		melding('Vul een type afwezigheid in', 'Rood');
	} else if(date_from == '') {
		errors = "error";
		melding('Vul een datum van in', 'Rood');
	} else if(date_to == '') {
		errors = "error";
		melding('Vul een datum van in', 'Rood');
	} else if(text == '') {
		errors = "error";
		melding('Vul een afwezigheid in', 'Rood');
	}
    if(errors)
        return;
    var data = {
        user_id : user_id,
        employee_id : employeeid,
        option : option,
        date_from : date_from,
        date_to : date_to,
        text : text
    };
    $.ajax({
        
        type: "POST",
    
        url: "../php/settings/save_employee_log.php", 
    
        data: data,
    
        dataType: "json",
    
        success: function(result){
            if(result['message'] == 'Logboek opgeslagen.')
            {
                var item = result['new_one'];
                var html = getEmployeelogStr(item['employee_id'], item['id'], item, true);
                if(result['latest_one'].length > 0)
                {
                    $('#clog-' + result['latest_one']['id']).before(html);
                }
                else{
                    $('.popup.large .logs').append(html);
                }
                $('.tooltipped').tooltip();
	            $('#clog-' + item['id']).slideDown();
            }
            else{
                melding(result, 'rood');
            }
        },
        error: function(e1, e2, e3){

        }
    });

}


function deleteCLogConfirm(employee_id, elid)
{
    closeInnerConfirm(elid);
    $.ajax({
		type: "POST",
		url: "../php/settings/delete_employee_log.php",
		data: {
			elog: elid,
            employee_id : employee_id
		},
		dataType: "html",
		success: function(result) {
			if(result == 'Logboek item verwijderd.') {
				//Alles ging goed
				melding(result, 'groen');
				$("#clog-" + elid).slideUp();
			} else {
				//Er ging iets mis
				melding(result, 'rood');
			}
		},
        error: function(e1, e2, e3)
        {
            
        }
	});
}


function woonadres_changed()
{
    if($("#employeeinfo #ei_woonadres_nl").val() == 'Overig')
    {
        $("#employeeinfo #row_overig").show();
    }
    else{
        $("#employeeinfo #row_overig").hide();
    }
}

function editSupplier(supplierid) {	



	prefillModal('Leverancier wijzigen','addSupplier.php').then(function() {
	
	
	
	$.ajax({
	
		type: "POST",
	
		url: "../php/settings/get_supplier.php", 
	
		data: {supplierid:supplierid},
	
		dataType: "json",
	
		success: function(result){
	
	
	
		for(var i = 0; i < result.length; i++) {						
	
		
	
		var name = result[i].name;
		var id = result[i].id;
		

		$("#supplierinfo .supplierid").val(id);

		$("#supplierinfo #name").val(name);	

		$("#supplierinfo #type").val(result[i].type);	
		$("#supplierinfo #accountnumber").val(result[i].accountnumber);	
		$("#supplierinfo #krediet").val(result[i].krediet);	
		$("#supplierinfo #soort").val(result[i].soort);	
		$("#supplierinfo #email").val(result[i].email);	
		$("#supplierinfo #phone").val(result[i].phone);	
		$("#supplierinfo #rating").val(result[i].rating);	
		$("#supplierinfo #login").val(result[i].login);	
		$("#supplierinfo #wachtwoord").val(result[i].wachtwoord);	
		$("#supplierinfo #email2").val(result[i].email2);	
		$("#supplierinfo #mobile_phone").val(result[i].mobile_phone);	
		$("#supplierinfo select").formSelect();
	
		showPrefilledModal();	
	
		
	
		}
	
						
	
		}
	
		
	
	});	
	
	
	
	});
	
	
	
	}


function manageSupplierFileLog(supplier_id)
{
	prefillModal('Bestanden bij leverancier', 'manageSupplierFile.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/bedrijfsvoering/get_supplier_file.php",
			data: {
				supplier_id: supplier_id
			},
			dataType: "json",
			success: function(result) {
				var projectfilehtml = "";
				if(result)
				{
					for(var index = 0; index < result.length; index ++)
					{
						projectfilehtml += getSupplierFileLogHtml(result[index]);
					}
				}
				$(".popup.large .logs").html(projectfilehtml);
				$('.tooltipped').tooltip();

				$(".supplierid").val(supplier_id);
				showPrefilledModal();
			},
			error: function(e1, e2, e3)
			{

			}
		});
	});
}

function getSupplierFileLogHtml(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="flog-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper"><div class="flog-prev">';
    if(item['file_exe'] == 'pdf') 
    {
        html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
    }
    else{
        html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
    }
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name">' + item['name'] + '</span>' +
    '<div onclick="showInnerFConfirm(' + item['id'] + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' + 
    '<a href="' + root_path + 'upload/' + item['file_path'] + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' + 
    '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['uploaded_date']) + '</span>' + 
    '<span class="fc-user"><i class="material-icons">person</i>' + item['username'] + '</span>';

    html +='</div>' + 
    '<div id="fipo-' + item['id'] + '" class="inner-popup-overlay"></div>' + 
    '<div id="fipc-' + item['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerFConfirm(' + item['id'] + ')">Annuleren</span><span class="button red" onclick="deleteSupplierFConfirm(' + item['id'] + ')">Verwijderen</span></div></div>' + 
    '</div></div>';
    return html;
}


function saveSupplierFile()
{
    var supplier_id = $(".supplierid").val();
    var file_type = $("#type").val();
    var files = $('#file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file',files[0]);
        fd.append('file_type', file_type);
        fd.append('file_name', $("#name").val());
        fd.append('supplier_id', supplier_id);
        fd.append('user_id', $("#supplierfilelog .userid").val());
        $(".file-loading-icon").show();
        $(".file-icon").hide();
    $.ajax({
        url: "../php/bedrijfsvoering/supplier_file_upload.php",
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result){
            if (result['message'] == 'Bestand opgeslagen') {
                //Alles ging goed
                
                var fileloghtml = getSupplierFileLogHtml(result['inserted_file'], true);
                $(".file-loading-icon").hide();
                $(".file-icon").show();
                $(".popup.large .logs").prepend(fileloghtml);
				$('.tooltipped').tooltip();
				$('#flog-' + result['inserted_file']['id']).slideDown();
                melding(result['message'], 'groen');
                $("#file").val("");
                $("#type").val("");
                $("#name").val("");
                $("#type").formSelect();
                $(".popup .btn-filelog").removeClass('file_selected');
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {

        }
     });
    }else{
        melding('Selecteer eerst een bestand', 'rood');
    }
}


function addSupplier()
{
	prefillModal('Leverancier toevoegen','addSupplier.php').then(function() {
	
	
		$("#supplierinfo .supplierid").val('');

		
		showPrefilledModal();
		
	});
		
}
function deleteSupplier(supplierid)
{
	
	showConfirm('Deze leverancier verwijderen?','Verwijderen','red','deleteSupplierConfirm('+supplierid+')');	
}
function deleteSupplierConfirm(supplierid)
{
	closeConfirm();

	$.ajax({

		type: "POST",
	
		url: "../php/settings/delete_supplier.php",
	
		data: { supplierid: supplierid },
	
		dataType: "html",
	
		success: function(result){
	
		if(result == 'Leverancier verwijderd') {
	
			
	
		//Alles ging goed
	
		melding(result,'groen');
	
		$("tr[supplierrow="+supplierid+"]").slideUp();
	
		} else {
	
		melding(result,'rood');

		}			
	
		}
	
	 });


}

function saveSupplierInfo()
{
	var name = $("#supplierinfo #name").val();
	var supplierid = $(".supplierid").val();
	var type = $("#supplierinfo #type").val();
	var accountnumber = $("#supplierinfo #accountnumber").val();
	var krediet = $("#supplierinfo #krediet").val();
	var soort = $("#supplierinfo #soort").val();
	var email = $("#supplierinfo #email").val();
	var phone = $("#supplierinfo #phone").val();
	var rating = $("#supplierinfo #rating").val();
	var login = $("#supplierinfo #login").val();
	var wachtwoord = $("#supplierinfo #wachtwoord").val();
	var email2 = $("#supplierinfo #email2").val();
	var mobile_phone = $("#supplierinfo #mobile_phone").val();
	var error = '';
	
	if(name == '')
	{
		error = 'Vul een name in';		
	}	
	if(type == '')
	{
		error = 'Vul een type in';		
	}	
	if(accountnumber == '')
	{
		error = 'Vul een accountnummer in';		
	}	
	if(krediet == '')
	{
		error = 'Vul een krediet in';		
	}	
	if(soort == '')
	{
		error = 'Vul een soort in';		
	}	
	if(phone == '')
	{
		error = 'Vul een telefoon in';		
	}	
	if(email == '')
	{
		error = 'Vul een email in';		
	}	
	if(rating == '')
	{
		error = 'Vul een rating in';		
	}	

	
	if(error != ''){
		melding(error, 'Rood');
	}
	else{

		$.ajax({
			type: "POST",
			url: "../php/settings/save_supplierinfo.php",
			data: { supplierid: supplierid, name:name, type:type, accountnumber:accountnumber, krediet:krediet, email:email, phone:phone, soort:soort, rating:rating, login:login, wachtwoord:wachtwoord, mobile_phone:mobile_phone, email2:email2 },	
			dataType: "json",	
			success: function(result){
		
			if(result['message'] == 'Leverancier opgeslagen') {

			melding(result['message'],'groen');
		
			if(supplierid != '') { //Edited a user
				var item = result['supplier'];
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(1)").html(item['soort']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(2)").html(item['name']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(3)").html(item['type']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(4)").html(item['accountnumber']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(5)").html(item['email']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(6)").html(item['phone']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(7)").html(item['krediet']);
				var starhtml = "";
				for(var index = 0; index < item['rating']; index ++)
					starhtml += "<div class='clip-star'></div>";
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(8)").html(starhtml);
		
			} else { //Added a user		
				var starhtml = "";
				for(var index = 0; index < result['supplier']['rating']; index ++)
					starhtml += "<div class='clip-star'></div>";
				$("#opdracht").append("<tr supplierrow='" + result['supplier']['id'] + "'>" + 
				"<td>" + result['supplier']['soort'] + "</td>" + 
				"<td>" + result['supplier']['name'] + "</td>" + 
				"<td>" + result['supplier']['type'] + "</td>" + 
				"<td>" + result['supplier']['accountnumber'] + "</td>" + 
				"<td>" + result['supplier']['email'] + "</td>" + 
				"<td>" + result['supplier']['phone'] + "</td>" + 
				"<td>" + result['supplier']['krediet'] + "</td>" + 
				"<td>" + starhtml + "</td>" + 
				"<td style='width:110px'><div onclick='editSupplier(" + result['supplier']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div><div onclick='deleteSupplier(" + result['supplier']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td>" + "</tr>");
		
			}

			closeModal();
		
			} else {
		
			melding(result['message'],'rood');
		
			}									
		
			},
			error: function(e1, e2, e3){
				
			}
		
		 });	

	}
}

function deleteSupplierFConfirm(fID)
{
     //closeConfirm();
     closeInnerFConfirm(fID)
     $.ajax({
         type: "POST",
         url: "../php/bedrijfsvoering/delete_supplier_file_log.php",
         data: {
             'file_id': fID
         },
         dataType: "html",
         success: function(result) {
             if (result == 'Bestand verwijderd') {
                 //Alles ging goed
                 melding(result, 'groen');
                 $("#flog-" + fID).slideUp();
             } else {
                 //Er ging iets mis
                 melding(result, 'rood');
             }
         }
     });
}

function addSupplier()
{
	prefillModal('Leverancier toevoegen','addSupplier.php').then(function() {
	
	
		$("#supplierinfo .supplierid").val('');

		
		showPrefilledModal();
		
	});
		
}
function deleteSupplier(supplierid)
{
	
	showConfirm('Deze leverancier verwijderen?','Verwijderen','red','deleteSupplierConfirm('+supplierid+')');	
}

function deleteSupplierConfirm(supplierid)
{
	closeConfirm();

	$.ajax({

		type: "POST",
	
		url: "../php/settings/delete_supplier.php",
	
		data: { supplierid: supplierid },
	
		dataType: "html",
	
		success: function(result){
	
		if(result == 'Leverancier verwijderd') {
	
			
	
		//Alles ging goed
	
		melding(result,'groen');
	
		$("tr[supplierrow="+supplierid+"]").slideUp();
	
		} else {
	
		melding(result,'rood');

		}			
	
		}
	
	 });


}

function saveSupplierInfo()
{
	var name = $("#supplierinfo #name").val();
	var supplierid = $(".supplierid").val();
	var type = $("#supplierinfo #type").val();
	var accountnumber = $("#supplierinfo #accountnumber").val();
	var krediet = $("#supplierinfo #krediet").val();
	var soort = $("#supplierinfo #soort").val();
	var email = $("#supplierinfo #email").val();
	var phone = $("#supplierinfo #phone").val();
	var rating = $("#supplierinfo #rating").val();
	var login = $("#supplierinfo #login").val();
	var wachtwoord = $("#supplierinfo #wachtwoord").val();
	var email2 = $("#supplierinfo #email2").val();
	var mobile_phone = $("#supplierinfo #mobile_phone").val();
	var error = '';
	
	if(name == '')
	{
		error = 'Vul een name in';		
	}	
	if(type == '')
	{
		error = 'Vul een type in';		
	}	
	if(accountnumber == '')
	{
		error = 'Vul een accountnummer in';		
	}	
	if(krediet == '')
	{
		error = 'Vul een krediet in';		
	}	
	if(soort == '')
	{
		error = 'Vul een soort in';		
	}	
	if(phone == '')
	{
		error = 'Vul een telefoon in';		
	}	
	if(email == '')
	{
		error = 'Vul een email in';		
	}	
	if(rating == '')
	{
		error = 'Vul een rating in';		
	}	
	
	if(error != ''){
		melding(error, 'Rood');
	}
	else{

		$.ajax({
			type: "POST",
			url: "../php/settings/save_supplierinfo.php",
			data: { supplierid: supplierid, name:name, type:type, accountnumber:accountnumber, krediet:krediet, email:email, phone:phone, soort:soort, rating:rating, login:login, wachtwoord:wachtwoord, mobile_phone:mobile_phone, email2:email2 },	
			dataType: "json",	
			success: function(result){
		
			if(result['message'] == 'Leverancier opgeslagen') {

			melding(result['message'],'groen');
		
			if(supplierid != '') { //Edited a user
				var item = result['supplier'];
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(1)").html(item['soort']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(2)").html(item['name']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(3)").html(item['type']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(4)").html(item['accountnumber']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(5)").html(item['email']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(6)").html(item['phone']);
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(7)").html(item['krediet']);
				var starhtml = "";
				for(var index = 0; index < item['rating']; index ++)
					starhtml += "<div class='clip-star'></div>";
				$("#opdracht tr[supplierrow="+supplierid+"] td:nth-child(8)").html(starhtml);
		
			} else { //Added a user		
				var starhtml = "";
				for(var index = 0; index < result['supplier']['rating']; index ++)
					starhtml += "<div class='clip-star'></div>";
				$("#opdracht").append("<tr supplierrow='" + result['supplier']['id'] + "'>" + 
				"<td>" + result['supplier']['soort'] + "</td>" + 
				"<td>" + result['supplier']['name'] + "</td>" + 
				"<td>" + result['supplier']['type'] + "</td>" + 
				"<td>" + result['supplier']['accountnumber'] + "</td>" + 
				"<td>" + result['supplier']['email'] + "</td>" + 
				"<td>" + result['supplier']['phone'] + "</td>" + 
				"<td>" + result['supplier']['krediet'] + "</td>" + 
				"<td>" + starhtml + "</td>" + 
				"<td style='width:110px'><div onclick='editSupplier(" + result['supplier']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div><div onclick='deleteSupplier(" + result['supplier']['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></td>" + "</tr>");
		
			}

			closeModal();
		
			} else {
		
			melding(result['message'],'rood');
		
			}									
		
			},
			error: function(e1, e2, e3){
				
			}
		
		 });	

	}
}
