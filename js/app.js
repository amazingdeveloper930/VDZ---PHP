var color_list = ['COLOR1', 'COLOR2', 'COLOR3', 'COLOR4', 'COLOR5', 'COLOR6', 'COLOR7', 'COLOR8'];


function melding(tekst, kleur) {



if(kleur == 'rood')	{ kleur = '#be1522' }

if(kleur == 'groen') { kleur = '#4c9921' }



$.toast({    

    text: tekst,    

    loader: true,

	loaderBg: kleur,

	stack: 5,

    position: 'top-right'

})	

	

}





function showConfirm(text,buttontext,buttoncolor,buttonfunction, showcancelbutton = true, button_class = []) {



$(".popup.confirm .text").html(text);

$(".popup.confirm .button:nth-child(2)").html(buttontext);

$(".popup.confirm .button:nth-child(2)").removeClass();
$(".popup.confirm .buttons span:nth-child(2)").addClass('button');
$(".popup.confirm .button:nth-child(2)").addClass(buttoncolor);
for(var index = 0; index < button_class.length;index ++)
    $(".popup.confirm .button:nth-child(2)").addClass(button_class[index]);

$(".popup.confirm .button:nth-child(2)").attr('onClick', buttonfunction);



$(".popup.confirm").show();

$(".popup-overlay").show();



$(".popup.confirm").addClass('visible');

$(".popup-overlay").addClass('visible');

if(!showcancelbutton)
    $(".popup.confirm .button:nth-child(1)").hide();
else
    $(".popup.confirm .button:nth-child(1)").show();

}

$('body').keypress(function(event){
    if(event.keyCode == 13 && $(".popup.confirm").hasClass('visible')){
        $(".popup.confirm .button:nth-child(2)").click();
    }
  });


function closeConfirm() {

	

$(".popup.confirm").removeClass('visible');

$(".popup-overlay").removeClass('visible');



 setTimeout(function(){ 

$(".popup.confirm").hide();

$(".popup-overlay").hide();

 }, 300);

	

}







function showEmptyModal(title,contents) {





$(".popup.large .title").html(title);

$(".popup.large .logs").html('');

$(".popup.large .contents").load('../php/popups/'+contents, function(responseTxt, statusTxt, xhr){

	

    if(statusTxt == "success") { 



	M.updateTextFields();	

	$(".popup.large select").formSelect();



	$(".popup.large").show();

	$(".popup-overlay").show();	

    $("body").addClass('popup-active');

	$(".popup.large").addClass('visible');

	$(".popup-overlay").addClass('visible');
    $(".popup-large-container").show();

    $(".popup-large-container").addClass('visible');



	}    

    if(statusTxt == "error") { melding('Kon de popup niet openen..','rood'); }

    

});



}





function prefillModal(title,contents) {



return new Promise(function(resolve,reject) {

$(".popup.large .title").html(title);

$(".popup.large .logs").html('');

$(".popup.large .contents").load('../php/popups/'+contents, function(responseTxt, statusTxt, xhr){

	

    if(statusTxt == "success") { resolve(); }    

    if(statusTxt == "error") { reject(); }

    

});

});

  

}



function prefillVeryLargeModal(title,contents) {



	return new Promise(function(resolve,reject) {
	
	$(".popup.very-large .title").html(title);
	
	$(".popup.very-large .logs").html('');

    $(".popup.very-large .popupheader .btn-modal-close").attr('onclick', 'closeVeryLargeModal()');
	
	$(".popup.very-large .contents").load('../php/popups/'+contents, function(responseTxt, statusTxt, xhr){
	
		
	
		if(statusTxt == "success") { resolve(); }    
	
		if(statusTxt == "error") { reject(); }
	
		
	
	});
	
	});
	
	  
	
}

Date.prototype.getWeek = function (dowOffset) {
    /*getWeek() was developed by Nick Baicoianu at MeanFreePath: http://www.meanfreepath.com */
    
        dowOffset = typeof(dowOffset) == 'number' ? dowOffset : 0; //default dowOffset to zero
        var newYear = new Date(this.getFullYear(),0,1);
        var day = newYear.getDay() - dowOffset; //the day of week the year begins on
        day = (day >= 0 ? day : day + 7);
        var daynum = Math.floor((this.getTime() - newYear.getTime() - 
        (this.getTimezoneOffset()-newYear.getTimezoneOffset())*60000)/86400000) + 1;
        var weeknum;
        //if the year starts before the middle of a week
        if(day < 4) {
            weeknum = Math.floor((daynum+day-1)/7) + 1;
            if(weeknum > 52) {
                nYear = new Date(this.getFullYear() + 1,0,1);
                nday = nYear.getDay() - dowOffset;
                nday = nday >= 0 ? nday : nday + 7;
                /*if the next year starts before the middle of
                  the week, it is week #1 of that year*/
                weeknum = nday < 4 ? 1 : 53;
            }
        }
        else {
            weeknum = Math.floor((daynum+day-1)/7);
        }
        return weeknum;
    };


function showPrefilledModal(mode = 'normal') {



setTimeout(function(){

M.updateTextFields();	

$(".popup.large select").formSelect();

},100);



setTimeout(function(){

$(".popup-large-container").show();
if(mode == 'wide')
{
    $(".popup-large-container .popup").addClass('wide');
}
else{
    $(".popup-large-container .popup").removeClass('wide');
}


$(".popup-overlay").show();	
$("body").addClass('popup-active');



$(".popup-large-container").addClass('visible');

$(".popup-overlay").addClass('visible');

},200);	

	

}




function showPrefilledVeryLargeModal(mode = "normal") {



	setTimeout(function(){
	
	M.updateTextFields();	
	
	$(".popup.very-large select").formSelect();
	
	},100);
	
	
	
	setTimeout(function(){
        $(".popup-very-large-container").show();
	$(".popup.very-large").show();
    $("body").addClass('popup-active');
        if (mode == 'max') {
            $(".popup.very-large").addClass("max-large");
            $(".popup.very-large").removeClass('ext-large');
            $(".popup.very-large").removeClass('normal-large');
        }
        if (mode == 'normal') {
            $(".popup.very-large").addClass("normal-large");
            $(".popup.very-large").removeClass('ext-large');
            $(".popup.very-large").removeClass('max-large');
        }
        if(mode =='ext') {
            $(".popup.very-large").addClass("ext-large");
            $(".popup.very-large").removeClass('normal-large');
            $(".popup.very-large").removeClass('max-large');
        }
        $(".popup-overlay-very-large").show();
        // $('.project-task-panel .submenu .tabs').tabs('destroy');
        // setTimeout(function(){
                // $('.project-task-panel .submenu .tabs').tabs();
            // }, 1000);


        $(".popup-very-large-container").addClass('visible');

        $(".popup-overlay-very-large").addClass('visible');

        $('.line_completed input').attr('disabled', true);
        $('.line_skipped input').attr('disabled', true); // this is for project page

    }, 200);



}


function closeModal() {	

$(".popup.large").removeClass('visible');

$(".popup-large-container").removeClass('visible');

$(".popup-overlay").removeClass('visible');

$("body").removeClass('popup-active');

 setTimeout(function(){ 

$(".popup-large-container").hide();
$(".popup-overlay").hide();

 }, 300);

}

function showModal() {	

	$(".popup.large").addClass('visible');	
	$(".popup-overlay").addClass('visible');
    $(".popup-large-container").addClass('visible');
	
	setTimeout(function(){
		$(".popup.large").show();		
		$(".popup-overlay").show();
        $(".popup-large-container").show();
        $("body").addClass('popup-active');
	}, 100);
	
}


function showLoading() {
    // $(".popup-overlay").addClass('visible');
    $(".spin-container").show();
    $(".spin-loader").show();
}

function hideLoading() {
    // $(".popup-overlay").removeClass('visible');
    $(".spin-container").hide();
    $(".spin-loader").hide();
}



function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

function getRealNumber(str)
{
	str = str.replace(/\./g, '');
    str = str.replace(/\,/g, '.');
	if($.isNumeric(str))
		return parseFloat(str);
	else return 0;
}

function convertNumber(ele, mode = 1){
	var value = $(ele).val();
    value = value.replace(/\€/g, '');
	value = value.replace(/\./g, '');
	value = value.replace(/\,/g, '.');
    if(mode == 1){
        value = addCommas(parseFloat(value).toFixed(2)); //PRICE
    }
	   
    else if(mode == 2)
        value = addCommas(value); //QUANTITY
    // if(mode == 1)
    // {
    //     value = '�' + value;
    // }
	$(ele).val(value);
}

function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function convertDateFormat(date_data)
{
    if(date_data){
        var dateAr = date_data.split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0]
        return newDate;
    }
    else{
        return null;
    }
    
}

function getOriginalDate(date_data)
{
    var dateAr = date_data.split('-');
    var newDate = date_data;
    if(dateAr.length >= 3)
        newDate = dateAr[2] + '-' + ("0" + dateAr[1]).slice(-2) + '-' + ("0" + dateAr[0]).slice(-2);
    return newDate;
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}


function openPrev(prevfile)
{
    var modal = document.getElementById("img-modal");
    var modalImg = document.getElementById("img01");
    modal.style.display = "block";
    modalImg.src = '../upload/' + prevfile;
}

function closePrevModal()
{
    var modal = document.getElementById("img-modal");
    modal.style.display = "none";
}




function sortTable(table, col, reverse) {
    var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
        tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
        i;
    var theaderCol = table.tHead.rows[0].cells[col];
    reverse = -((+reverse) || -1);
    tr = tr.sort(function (a, b) { // sort rows
        if(!$(theaderCol).hasClass('datum-column'))
            return reverse // `-1 *` if want opposite order
                * (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
                    .localeCompare(b.cells[col].textContent.trim())
                );
        else{
            return reverse // `-1 *` if want opposite order
                * (getOriginalDate(a.cells[col].textContent.trim()) // using `.textContent.trim()` for test
                    .localeCompare(getOriginalDate(b.cells[col].textContent.trim()))
                );
        }
    });
    for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
}

function makeSortable(table) {
    var th = table.tHead, i;
    th && (th = th.rows[0]) && (th = th.cells);
    if (th) i = th.length;
    else return; // if no `<thead>` then do nothing
    while (--i >= 0) (function (i) {
        var dir = 1;

        th[i].addEventListener('click', function () {
            if(!$(th[i]).hasClass('no-sort')){
                sortTable(table, i, (dir = 1 - dir));
                for(j = 0; j < th.length; j ++ ){
                    $(th[j]).removeClass("sort0");
                    $(th[j]).removeClass("sort1");
                }
                
                $(th[i]).addClass("sort" + (1 - dir));
                
            }
            
        });
       
    }(i));
}

function makeAllSortable(parent, tableClass) {
    parent = parent || document.body;
    var t = parent.getElementsByClassName(tableClass), i = t.length;
    while (--i >= 0) makeSortable(t[i]);
}


function sortTableInternal(table, col, reverse) {
    var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
        tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
        i, j;
    var itemtr = [];
    //this is for only table-alltaken table
    
    for(i = 0, j=0; i < tr.length; i++)
    {
        if($(tr[i]).hasClass('contactrow') || $(tr[i]).hasClass('vehiclerow') || $(tr[i]).hasClass('employee-item'))
            { 
                itemtr[j] = [];
                itemtr[j]['head'] = tr[i];
                itemtr[j]['data'] = [];
                j++;
            }
        else{
            itemtr[j - 1]['data'].push(tr[i]);
        }
    }
    var theaderCol = table.tHead.rows[0].cells[col];
    reverse = -((+reverse) || -1);
    itemtr = itemtr.sort(function (a, b) { // sort rows
        if(!$(theaderCol).hasClass('datum-column'))
            return reverse // `-1 *` if want opposite order
                * (a['head'].cells[col].textContent.trim() // using `.textContent.trim()` for test
                    .localeCompare(b['head'].cells[col].textContent.trim())
                );
        else{
            return reverse // `-1 *` if want opposite order
                * (getOriginalDate(a['head'].cells[col].textContent.trim()) // using `.textContent.trim()` for test
                    .localeCompare(getOriginalDate(b['head'].cells[col].textContent.trim()))
                );
        }
    });
    for(i = 0; i < itemtr.length; ++i){
        tb.appendChild(itemtr[i]['head']); // append each row in order
        for(j = 0; j < itemtr[i]['data'].length; j++)
        {
            tb.appendChild(itemtr[i]['data'][j]);
        }
    } 
}

function makeSortableInternal(table) {
    var th = table.tHead, i;
    th && (th = th.rows[0]) && (th = th.cells);
    if (th) i = th.length;
    else return; // if no `<thead>` then do nothing
    while (--i >= 0) (function (i) {
        var dir = 1;

        th[i].addEventListener('click', function () {
            if(!$(th[i]).hasClass('no-sort')){
                sortTableInternal(table, i, (dir = 1 - dir));
                for(j = 0; j < th.length; j ++ ){
                    $(th[j]).removeClass("sort0");
                    $(th[j]).removeClass("sort1");
                }
                
                $(th[i]).addClass("sort" + (1 - dir));
                
            }
            
        });
       
    }(i));
}

function makeAllSortableInternal(parent, tableClass) {
    parent = parent || document.body;
    var t = parent.getElementsByClassName(tableClass), i = t.length;
    while (--i >= 0) makeSortableInternal(t[i]);
}

$(document).ready(function() {



    $('.dropdown-trigger').dropdown({ 'coverTrigger': false, 'constrainWidth': false, 'alignment': 'right' });

$("select").formSelect();

$('.tooltipped').tooltip();


$('.submenu .tabs').tabs();

// $('#table_id').DataTable();

// $('.contacten-table').DataTable(
//     {
//         "paging":   false,
//         "searching": false,
//         "info":     false
//     }
// );

    makeAllSortable(null, 'sort-table');
    makeAllSortableInternal(null, 'sort-table-group');
    var row_name=$(".row_name").val();
    var row_id = $(".row_table_id").val();
    if(row_id != '' && row_id != undefined)
    {
        $('html, body').animate({
						
            scrollTop: $("#" + row_name + row_id).offset().top - 100
        }, 1000);
    }
    

});

$(".page-info").on('click', function(){
    showConfirm($(".page-info").attr('data-page-info'), 'OK!', 'green', "closeConfirm()", false, ['waves-effect', 'waves-light', 'btn']);
});

$(".btn-search").on('click', function(){
    
    prefillVeryLargeModal('Zoeken', 'searchContacts.php').then(function() {
        $(".popup.very-large .popupheader i").attr('onclick', 'closeSearchModal()');
        $(".popup.very-large").addClass('searchbox');
        doSearch();
        showPrefilledVeryLargeModal('normal');
    });
});

function closeSearchModal()
{
    $(".popup.very-large").removeClass('visible');
    $(".popup-very-large-container").removeClass('visible');
    $("body").removeClass('popup-active');
    setTimeout(function() {
        $(".popup.very-large").hide();
        $(".popup.very-large").removeClass('searchbox');
        $(".popup-very-large-container").hide();
        $(".popup-overlay-very-large").hide();
        $(".popup.very-large .popupheader i").attr('onclick', 'closeVeryLargeModal()');
    }, 300);
}

function closeVeryLargeModal() {
    $(".popup.very-large").removeClass('visible');
    $(".popup-very-large-container").removeClass('visible');
    $("body").removeClass('popup-active');
    setTimeout(function() {
        $(".popup-very-large-container").hide();
        $(".popup-overlay-very-large").hide();
        trggerFullscreen(2);
    }, 300);
}

var timer;

function keyEntered() {

    clearTimeout(timer);
    timer = setTimeout(doSearch.bind(this), 200);
}

function doSearch() {
    $(".searchResult-panel table tbody tr").each(function(index, item){
        var key = $("#keyword").val().toLowerCase();
        if(key == '')
            $(item).hide();
        else{
            var name = $(item).attr('c-name');
            var address = $(item).attr('c-address');
            var city = $(item).attr('c-city');
            var email = $(item).attr('c-email');
            var number = $(item).attr('p-number');

            if(name.includes(key) || 
            address.includes(key) || 
            city.includes(key) || 
            email.includes(key) || 
            number.includes(key))
                $(item).show();
            else 
                $(item).hide();
        }
    })
}

function gotoPage(page)
{
    window.location.href = page;
}




function calcPriceInc(ele)
{
    var price = $(ele).parent().parent().find(".st_price").val();
    price = price.replace(/\€/g, '');
    price = price.replace(/\./g, '');
    price = price.replace(/\,/g, '.');
    var vat = $(ele).parent().parent().find(".st_vat").val();
    var price_inc = price * (1 + parseFloat(vat));


    price_inc = price_inc.toFixed(2).toString().replace(/\./g, ',');
    price_inc = addCommas(price_inc);
    $(ele).parent().parent().find(".st_price_inc").val(price_inc);
}

function calcPriceIncFromSTID(st_id)
{
    var ele = $("#tab_chapter_meer tr[st_row=" + st_id + "]");
    if(st_id == 0)
        ele = $("#tab_chapter_meer tr.st_newrow");
    var price = ele.find(".st_price").val();
    price = price.replace(/\€/g, '');
    price = price.replace(/\./g, '');
    price = price.replace(/\,/g, '.');
    var vat = ele.find(".st_vat").val();
    var price_inc = price * (1 + parseFloat(vat));


    price_inc = price_inc.toFixed(2).toString().replace(/\./g, ',');
    price_inc = addCommas(price_inc);
    ele.find(".st_price_inc").val(price_inc);
}

function getRawHtmlFromSpeicalTask(item)
{
    var html = '';


    html += '<td class="td_text">' + item.text + '</td>' +
    '<td class="td_price">&euro;' + item.price + '</td>' +
    '<td class="td_vat">' + (item.vat * 100) + '%</td>' +
    '<td class="td_price_inc">&euro;' + item.price_inc + '</td>' +
    '<td class="td_option">' + item.option + '</td>' +
    '<td class="td_date">' + convertDateFormat(item.date) + '</td>' +
    '<td><div onclick="editSpecialTask(' + item.contact_id + ', ' + item.id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Update"><i class="material-icons">create</i></div></td>';
    return html;
}

function getRawHtmlFromTaskPlanning(item)
{
    var html = '';
    html += '<td class="pt_name">' + item.name + '</td>' + 
    '<td class="pt_week">' + item.week + ' weken</td>' + 
    '<td class="pt_sort_order">' + item.sort_order + '</td>' + 
    '<td class="pt_medewerkers">' + item.medewerkers + '</td>' + 
    '<td class="pt_uitbesteed">' + item.uitbesteed + '</td>' + 
    '<td class="pt_color">' + item.color_widget + '</td>' + 
    '<td class="pt_action"><div onclick="editTaskPlanning(' + item['contact_id'] + ', ' + item['id'] +  ')" class="actiebutton tooltipped" data-position="top" data-tooltip="edit"><i class="material-icons">edit</i></div><div onclick="deleteConfirmTaskPlanning(' + item['contact_id'] + ', ' + item['id'] +  ')" class="actiebutton tooltipped" data-position="top" data-tooltip="delete"><i class="material-icons">delete</i></div></td>';
    return html;
}

function getRawHtmlFromTaskPlanningEdition(contact_id, item, color = null)
{
    var html = '';
    if(item != null)
        html += '<td class="pt_name"><input class="ptb_name" value="' + item.name + '" type="text" placeholder="Omschrijving"/></td>' + 
        '<td class="pt_week"><input class="ptb_week" value="' + item.week + '" type="text" placeholder="0"/></td>' + 
        '<td class="pt_sort_order"><input class="ptb_sort_order" value="' + item.sort_order + '" type="text" placeholder="0"/></td>' +
        '<td class="pt_medewerkers"><input class="ptb_medewerkers" value="' + item.medewerkers + '" type="number" placeholder="0" min = 0 oninput="medewerkersChanged(' + item['id'] + ')"/></td>' +
        '<td class="pt_uitbesteed"><select class="ps_uitbesteed" onchange="uitbesteedChanged(' + item['id'] + ')"><option value="Ja">Ja</option><option value="Nee" ' + (item.uitbesteed =='Nee' ? 'selected' : '' ) + '>Nee</option></select></td>' +
        '<td class="pt_color">' + '<span class="new badge pt-' + item.color + '" data-badge-caption=""  data-badge-caption="" onclick="changeColor(this)" color="'+ item.color +'"></span>' + '</td>' + 
        '<td class="pt_action"><div onclick="saveTaskPlanning(' + contact_id + ', ' + item['id'] +  ')" class="actiebutton tooltipped" data-position="top" data-tooltip="save"><i class="material-icons">save</i></div></td>';
    else{
        
        html += '<td class="pt_name"><input class="ptb_name"  type="text" placeholder="Omschrijving"/></td>' + 
        '<td class="pt_week"><input class="ptb_week" type="text" placeholder="0"/></td>' + 
        '<td class="pt_sort_order"><input class="ptb_sort_order" type="text" placeholder="0"/></td>' + 
        '<td class="pt_medewerkers"><input class="ptb_medewerkers" value=0 type="number" placeholder="0" min = 0 onchange="medewerkersChanged()"/></td>' +
        '<td class="pt_uitbesteed"><select class="ps_uitbesteed" onchange="uitbesteedChanged()"><option value="Ja">Ja</option><option value="Nee">Nee</option></select></td>' +
        '<td class="pt_color">' + '<span class="new badge pt-' + color + '" data-badge-caption="" onclick="changeColor(this)" color="'+ color +'"></span>' + '</td>' + 
        '<td class="pt_action"><div onclick="saveTaskPlanning(' + contact_id +  ')" class="actiebutton tooltipped" data-position="top" data-tooltip="save"><i class="material-icons">save</i></div></td>';
    }
    return html;
}
function changeColor(ele)
{
    ele = $(ele);
    var color = ele.attr('color');
    ele.removeClass('pt-' + color);
    var color_index = color_list.indexOf(color);
    color = color_list[(color_index + 1) % (color_list.length)];
    ele.attr('color', color);
    ele.addClass('pt-' + color);
}


function getRawHtmlFromSpeicalTaskEdition(item)
{
    var html = '';
    html = '<td><input placeholder="Omschrijving" value="' + item['text'] + '" class="st_text" type="text"/></td>' + 
            '<td>&euro;<input placeholder="0.00" class="special_number st_price" type="text" value="' + item['price'] + '" onchange="convertNumber(this)" oninput="calcPriceInc(this)"/></td>' + 
            '<td><select class="st_vat" onchange="calcPriceIncFromSTID(' + item['id'] + ')"><option value="0.09">9%</option><option value="0.21" ' +  (item['vat'] == 0.21 ? 'selected':'') + '>21%</option></select></td>' +
            '<td>&euro;<input placeholder="0.00" value="' + item['price_inc'] + '" class="special_number st_price_inc" type="text" readonly/></td>' + 
            '<td><input placeholder="Wijze akkoord" class="st_option" value="' + item['option'] + '" type="text"/></td>' + 
                    
            '<td><input placeholder="dd-mm-jjjj" class="st_datum" value="' + item['date'] + '" type="date"/></td>' + 
            '<td><div onclick="saveSpecialTask(' + item['contact_id'] + ', ' + item['id'] + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Save"><i class="material-icons">save</i></div></td>';
    return html; 
}
function editSpecialTask(contact_id, st_id){
    var data = {
        st_id: st_id
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_special_task.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result){
                var parent_ele = "#tab_chapter_meer tr[st_row=" + result['id'] + "]";
                $(parent_ele).empty();
                var html = getRawHtmlFromSpeicalTaskEdition(result);
                $(parent_ele).html(html);
                $(parent_ele + " .st_vat").formSelect();
            }
        }
    });
}
function saveSpecialTask(contactid, st_id = 0)
{
    var row_ele = '';
    if(st_id == 0)
        row_ele = "#tab_chapter_meer .st_newrow ";
    else 
        row_ele ="#tab_chapter_meer tr[st_row=" + st_id + "] ";
    var text = $(row_ele + ".st_text").val();
    var price = $(row_ele + ".st_price").val();
    var vat = $(row_ele + ".st_vat").val();
    var price_inc = $(row_ele + ".st_price_inc").val();
    var option = $(row_ele + ".st_option").val();
    var date = $(row_ele + ".st_datum").val();

    let errors = "";
    if(text == '') {
		errors = "error";
		melding('Vul een Omschrijving in', 'Rood');
	} else if(price == '') {
        errors = "error";
		melding('Vul een Prijs.ex.BTW in', 'Rood');
    } else if(option == '') {
        errors = "error";
		melding('Vul een Wijze akkoord in', 'Rood');
    } else if(date == '') {
        errors = "error";
		melding('Vul een Datum akkoord in', 'Rood');
    }

    var data = {
        text : text,
        price : price,
        vat : vat,
        price_inc : price_inc,
        option : option,
        date : date,
        contact_id :  contactid,
        st_id : st_id
    };
    if(errors == '')
    {
        $.ajax({
			type: "POST",
			url: "../php/opdracht/save_special_task.php",
			data: data,
			dataType: "json",
			success: function(result) {
                if(result.mode == 'CREATE')
                {
                    $("#tab_chapter_meer .st_newrow input").val('');
                    $("#tab_chapter_meer .st_newrow .st_vat").val('0.21');
                    $("#tab_chapter_meer .st_newrow .st_vat").formSelect();
                    var html = "<tr st_row=" + result['id'] + ">" + getRawHtmlFromSpeicalTask(result) + "</tr>";
                    $(html).insertBefore("#tab_chapter_meer .st_newrow");
                }
                if(result.mode == 'UPDATE')
                {
                    var html = getRawHtmlFromSpeicalTask(result);
                    $("#tab_chapter_meer tr[st_row=" + st_id + "]").html(html);
                }
            },
            error: function(e1, e2, e3){

            }
        });
    }
}

function saveNewTask(contactid, chapterid)
{
    var userid = $(".userid").text();
    var name = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_name").val();
    var supplier = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverancier").val();
    var order_date = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_besteldatum").val();
    var supply_date = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverdatum").val();
    var timer = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_timer").val();
    let errors = "";
    if(name == '') {
		errors = "error";
		melding('Vul een Naam taak in', 'Rood');
	}else if(timer == '') {
        errors = "error";
		melding('Vul een Timer in', 'Rood');
    }
    var isspecial_task = 0;
    if(chapterid == 1)
    isspecial_task = $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_isspecial_task").val();
    var data = {
        userid:userid,
        name:name,
        supplier:supplier,
        order_date:order_date,
        supply_date:supply_date,
        timer: timer,
        isspecial_task: 0,
        not_necessary: 'false',
        chapter: chapterid,
        custom_contact_id: contactid
    };
    if(chapterid == 1 && isspecial_task == 1)
    {
        data['isspecial_task'] = 1;
    }
    
    
    if(errors == ''){
        $.ajax({
			type: "POST",
			url: "../php/opdracht/save_new_task.php",
			data: data,
			dataType: "json",
			success: function(result) {
                if(result['message'] == 'new task created')
                {
                    melding("Taak toegevoegd!", 'groen');
                    if(chapterid == 1 && isspecial_task == 1)
                    {
                        $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverancier").prop('disabled', false);
                        $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_besteldatum").prop('disabled', false);
                        $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverdatum").prop('disabled', false);//
                        $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_isspecial_task").val(0);
                        
                        for(var index = 0; index < result['created_task'].length; index ++)
                        {
                            var html = getTaskRowFromRawData(contactid, result['created_task'][index], result['supplier']);
                            var line_class = 'line_init';
                            if(result['created_task'][index]['status'] == 'PROCESSING')
                                line_class = 'line_processing';

                            html = "<tr task_id='" + result['created_task'][index]['id'] + "' class='project_task " + line_class + "'>" + html + "</tr><tr task_id='" + result['created_task'][index]['id'] + "' class='project_task_note'></tr>";
                            $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task").before(html);
                        }

                    }
                    else{
                        
                        
                        var html = getTaskRowFromRawData(contactid, result['created_task'], result['supplier']);
                        var line_class = 'line_init';
                        if(result['created_task']['status'] == 'PROCESSING')
                            line_class = 'line_processing';

                        html = "<tr task_id='" + result['created_task']['id'] + "' class='project_task " + line_class + "'>" + html + "</tr><tr task_id='" + result['created_task']['id'] + "' class='project_task_note'></tr>";
                        $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task").before(html);
                        
                    }

                    $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_name").val('');
                    $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverancier").val('true');
                    $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_besteldatum").val('true');
                    $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_leverdatum").val('true');
                    $("#tab_chapter" + chapterid + " .project-task-table .tr_new_task .new_task_timer").val(48);
                    $("#tab_chapter" + chapterid + " .project-task-table select").formSelect();
                }
            },
            error: function(e1, e2, e3){

            }
        });
    }

}

function getFormatedDateTime(date) {
    let fdate = new Date(date);
    return ("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear() + ' ' + ("0" + fdate.getHours()).slice(-2) + ':' + ("0" + fdate.getMinutes()).slice(-2);
}


function editTaskPlanning(contact_id, plan_id)
{
    var data = {
        contact_id : contact_id,
        plan_id : plan_id
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_task_planning.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result)
            {
                var html = getRawHtmlFromTaskPlanningEdition(contact_id, result);
                $("#tab_chapter_planning tr[pt_row=" + result['id']+ "]").html(html);
                $("#tab_chapter_planning tr[pt_row=" + result['id']+ "] select").formSelect();
            }
        },
        error: function(e1, e2, e3){

        }
    });
}

function saveTaskPlanning(contact_id, plan_id = null)
{
    var data = {};
    data = {
        contact_id: contact_id
    }
    var name = '';
    var week = '';
    var sort_order = '';
    var color = '';
    var medewerkers = 0;
    var uitbesteed = 'Nee';
    if(plan_id){
        name = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .ptb_name").val();
        week = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .ptb_week").val();
        sort_order = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .ptb_sort_order").val();
        color = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .pt_color span").attr('color');
        medewerkers = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .ptb_medewerkers").val();
        uitbesteed = $("#tab_chapter_planning tr[pt_row=" + plan_id + "] .ps_uitbesteed").val();
    }
    else{
        name = $("#tab_chapter_planning tr.pt_new .ptb_name").val();
        week = $("#tab_chapter_planning tr.pt_new .ptb_week").val();
        sort_order = $("#tab_chapter_planning tr.pt_new .ptb_sort_order").val();
        color = $("#tab_chapter_planning tr.pt_new .pt_color span").attr('color');
        medewerkers = $("#tab_chapter_planning tr.pt_new .ptb_medewerkers").val();
        uitbesteed = $("#tab_chapter_planning tr.pt_new .ps_uitbesteed").val();
    }
    let errors = '';
    if(name == ''){
        errors = "error";
		melding('Vul een Omschrijving in', 'Rood');
    }
    if(week == ''){
        errors = "error";
		melding('Vul een Week in', 'Rood');
    }
    if(sort_order == ''){
        errors = "error";
		melding('Vul een Sort Order in', 'Rood');
    }
    if(medewerkers == ''){
        errors = "error";
		melding('Vul een Medewerkers in', 'Rood');
    }
    if(plan_id)
    data['plan_id'] = plan_id;
    if(errors == '')
    {
        data['name'] = name;
        data['week'] = week;
        data['sort_order'] = sort_order;
        data['color'] = color;
        data['medewerkers'] = medewerkers;
        data['uitbesteed'] = uitbesteed;

        $.ajax({
            type: "POST",
            url: "../php/opdracht/save_new_planning.php",
            data: data,
            dataType: "json",
            success: function(result) {
                var total_week = result['total_week'];
                result = result['result'];
                var html = getRawHtmlFromTaskPlanning(result);
                if(plan_id)
                    $("#tab_chapter_planning tr[pt_row=" + result['id'] + "]").html(html);
                else{
                    html = "<tr pt_row='" + result['id'] + "'>" + html + "</tr>";
                    $("#tab_chapter_planning tr.pt_new").before(html);
                    var color_index = color_list.indexOf(result['color']);
                    color = color_list[(color_index + 1) % (color_list.length)];
                    html = getRawHtmlFromTaskPlanningEdition(contact_id, null, color);
                    $("#tab_chapter_planning tr.pt_new").html(html);
                    $("#tab_chapter_planning tr.pt_new select").formSelect();
                }
                if($(".popup #startdatum").val() != "")
                {
                    var startdatum = new Date($(".popup #startdatum").val());
                    var days = parseInt(total_week, 10);
                    days *= 7;
                    startdatum.setDate(startdatum.getDate() + days);
                    $(".popup #opleverdatum").val(formatDate(startdatum));

                }
                showConfirm("De duur van dit project veranderd, de opleverdatum wordt verschoven", 'OK!', 'red', "closeConfirm()", false, ['waves-effect', 'waves-light', 'btn']);

                if($.isFunction(displayQuarterData))
                {
                    displayQuarterData();
                }
            },
            error: function(e1, e2, e3) {
    
            }
        });
    }
    

}

function deleteConfirmTaskPlanning(contact_id, plan_id)
{
    showConfirm('Dit projectplanning verwijderen?', 'Verwijderen', 'red', 'deleteTaskPlanning(' + contact_id + ", "+ plan_id+ ')');
}

function deleteTaskPlanning(contact_id, plan_id)
{
    closeConfirm();
    var data = {
        contact_id : contact_id,
        plan_id : plan_id
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_planning.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Projectplanning verwijderd'){

                melding(result['message'], 'groen');
                $("#tab_chapter_planning tr[pt_row=" + plan_id + "]").slideUp();

            }
            else{
                melding(result['message'], 'rood');
            }
        }
    });
}

function changedTaskOption(contact_id)
{
    if($(".tr_new_task[chapter_id=1] .new_task_isspecial_task").val() == 1)
    {
        $(".tr_new_task[chapter_id=1] .new_task_leverancier").val('true');
        $(".tr_new_task[chapter_id=1] .new_task_besteldatum").val('true');
        $(".tr_new_task[chapter_id=1] .new_task_leverdatum").val('true');

        $(".tr_new_task[chapter_id=1] .new_task_leverancier").prop('disabled', true);
        $(".tr_new_task[chapter_id=1] .new_task_besteldatum").prop('disabled', true);
        $(".tr_new_task[chapter_id=1] .new_task_leverdatum").prop('disabled', true);
        
    }
    else{
        $(".tr_new_task[chapter_id=1] .new_task_leverancier").prop('disabled', false);
        $(".tr_new_task[chapter_id=1] .new_task_besteldatum").prop('disabled', false);
        $(".tr_new_task[chapter_id=1] .new_task_leverdatum").prop('disabled', false);
    }
    $(".tr_new_task[chapter_id=1] select").formSelect();
}

// Thisis for ticket



function trggerFullscreen(mode = 0)
{
    if(typeof fullscreentrigger !== 'undefined')
    {
        if(mode == 0)
        {
            if(fullscreentrigger)
            {
                fullscreentrigger = false;
                $(".popup.very-large .popupheader .btn-quote-fullscreen i.material-icons").html('fullscreen');
                $(".popup.very-large").removeClass('fullscreen');
            }
            else{
                fullscreentrigger = true;
                $(".popup.very-large .popupheader .btn-quote-fullscreen i.material-icons").html('fullscreen_exit');
                $(".popup.very-large").addClass('fullscreen');
            }
        }
        if(mode == 1)
        {
            fullscreentrigger = true;
            $(".popup.very-large .popupheader .btn-quote-fullscreen i.material-icons").html('fullscreen_exit');
            $(".popup.very-large").addClass('fullscreen');
        }
        if(mode == 2)
        {
            fullscreentrigger = false;
            $(".popup.very-large .popupheader .btn-quote-fullscreen i.material-icons").html('fullscreen');
            $(".popup.very-large").removeClass('fullscreen');
        }
    }
    else{
        return;
    }
}

function getTagSelectOptionsHtml(mode = 'STANDARD')
{
	var html = "";
	if(mode == 'STANDARD')
        html += "<option value=''>Geen tag</option>";
	if(mode == 'FASE')
		html += "<option value=''>Geen fase</option>";
    
    for(var index = 0; index < tags.length ; index ++)
    {
        if(mode == tags[index]['type'])
            html += "<option value=" + tags[index]['id'] + ">" + tags[index]['name'] + "</option>";
    }
    return html;
}


(function($){
    var oldFormSelect = $.fn.formSelect;
    $.fn.formSelect = function()
    {
        $(this).addClass("browser-default");
        var ret = oldFormSelect.apply(this, arguments);
        
        return ret;
    }
})(jQuery);
function propLogInKlant(contact_log_id)
{
    $.ajax({
        type: "POST",
        url: "../php/funnel/prop_log_klant.php",
        data: {
            'contact_log_id': contact_log_id
        },
        dataType: 'json',
        success: function(result){
            var klanten = result['klanten'];
            if(klanten)
            {
                $("#clog-" + contact_log_id).addClass('show-klanten');
            }
            else
                $("#clog-" + contact_log_id).removeClass('show-klanten');
        },
        error: function(e1, e2, e3){
    
        }
    })
}

function getContactlogStr(contactid, id, item, isnew = false)
{
    var file_path = item.file_path;
    var contactloghtml = '';


    var clid = id;
    var type = item.entry_type;
    var date = item.entry_date;
    var user = item.username;
    var desc = item.entry_description;
    var type_Str = item.entry_title;

    if(type != 101)
    {
        type_Str = contact_type[type];
        if (item.entry_title == 'lead')
            type_Str = lead_type[type];
    }


    var class_str = "log-container";
    if(item.klanten != undefined && item.klanten)
        class_str += " show-klanten";
    if(isnew)
    contactloghtml += '<div id="clog-' + clid + '" class="' + class_str + '" style="display:none;">';
    else 
    contactloghtml += '<div id="clog-' + clid + '" class="' + class_str + '">';

    if(file_path == undefined || file_path == null || file_path == ''){
       
    contactloghtml +='<div class="log-wrapper"><div class="log-header"><span class="c-type">' + type_Str + '</span><span class="c-date">' + getFormatedDateTime(date) + '</span><span class="c-user"><i class="material-icons">person</i> ' + user + '</span><div onclick="propLogInKlant(' + clid + ')" class="actiebutton tooltipped btn_klantenprop" data-position="top" data-tooltip="In klantportaal"><i class="material-icons">assignment_ind</i></div>';
    if(type != 101)
        contactloghtml += '<div onclick="showInnerConfirm(' + clid + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>';
    contactloghtml += '</div>' + '<div class="log-content">' + desc + '</div><div id="ipo-' + clid + '" class="inner-popup-overlay"></div><div id="ipc-' + clid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerConfirm(' + clid + ')">Annuleren</span><span class="button red" onclick="deleteCLogConfirm(' + contactid + ',' + clid + ')">Verwijderen</span></div></div></div></div>';
    }
    else{
        contactloghtml += '<div class="log-wrapper"><div class="flog-prev">';


        if(item.file_exe == 'pdf') 
        {
            contactloghtml += '<a class="img-pdf" href="../upload/' + file_path + '" target="_blank"><img  src="../images/pdf.png"></a>';
        }
        else{
            contactloghtml += '<a class="img-preview" onclick="openPrev(\'' + file_path + '\')"><img src="upload/' + file_path + '"></a>';
        }

        contactloghtml += '</div>' + 
        '<div class="flog-header">' +
        '<span class="fc-name">' + type_Str + '</span>' +
'<div onclick="propLogInKlant(' + clid + ')" class="actiebutton tooltipped btn_klantenprop" data-position="top" data-tooltip="In klantportaal"><i class="material-icons">assignment_ind</i></div>' + 
        '<div onclick="showInnerConfirm(' + clid + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' + 
        '<a href="../upload/' + file_path + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' + 
        '</div>' +
        '<div class="flog-container">' + 
        '<span class="fc-date">' + getFormatedDateTime(date) + '</span>' + 
        '<span class="fc-user"><i class="material-icons">person</i>' + user + '</span>' +
        '<p class="fc-desc">' + desc + '</p>' 
        + 
        '</div>' ;
        contactloghtml += '<div id="ipo-' + clid + '" class="inner-popup-overlay"></div><div id="ipc-' + clid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerConfirm(' + clid + ')">Annuleren</span><span class="button red" onclick="deleteCLogConfirm(' + contactid + ',' + clid + ')">Verwijderen</span></div></div></div></div></div></div>';

    }
    return contactloghtml;

}

function closeTicketNoteInnerConfirm(note_id)
{
    $("#nipc-" + note_id).removeClass('visible');
    $("#nipo-" + note_id).removeClass('visible');
    setTimeout(function() {
        $("#nipo-" + note_id).hide();
        $("#nipc-" + note_id).hide();
    }, 300);
}


function deleteTicketNoteConfirm(note_id)
{
    $.ajax({
        type: "POST",
        url: "../php/aftersales/delete_ticket_note.php",
        data: {
            'note_id': note_id
        },
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Notitie item verwijderd.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                
                if(result['note_count'] > 0)
                {
                    // $("tr[ticket_row=" + result['ticket_id'] + "].project_task .icon_ticketnote").addClass("hasnote");
                }
                else{
                    $("tr[ticket_row=" + result['ticket_id'] + "].project_ticket  .icon_ticketnote").removeClass("hasnote");
                }

                $("#ticket_note_" + note_id).slideUp();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        }
    });
}

function getTicketRawHtmlFromItem(item, mode='normal', header = true)
{
    var html = "";
    if(mode == 'normal')
    {
        html += "<td>" + item['title'] + "</td>";
    }
    else if(mode == 'edit')
    {
        html += "<td><input class='txt_ticket_title' value='" + item['title'] + "' type='text'/></td>";
    }
    html += "<td><span class='button btn' onclick='closeTicket(" + item['id'] + ")' " + (item['status'] == 'OPENED' ? '':' disabled ') + "><i class='material-icons'>done</i> Sluiten</span>";
    if(item['status'] == 'CLOSED')
        html += '<span class="user-info"><i class="material-icons">person</i>' + item['username'] + '</span>';
    html += "</td>"; 
    html += "<td>" + convertDateFormat(item['datum']) + "</td>" + "<td>" + (item['name'] == null? 'N.v.t' : item['name']) + "</td>";
    if(item['plan_datum'] != undefined)
        html += "<td>" + convertDateFormat(item['plan_datum']) + "</td>";
    else 
        html += "<td>" + 'N.v.t' + '</td>';
    if(mode == 'normal')
    {
        if(item['inkoop_besteld'] != undefined)
            html += "<td>" + convertDateFormat(item['inkoop_besteld']) + "</td>";
        else 
            html += "<td>" + 'N.v.t' + '</td>';
        html += "<td>" + (item['bedrag_open'] == null? 'N.v.t' : item['bedrag_open']) + "</td>";
    }
    else if(mode == 'edit')
    {
        html += "<td>" + "<input type='date' value='" + item['inkoop_besteld'] + "' class='txt_ticket_inkoop_besteld datepicker' />" + "</td>";

        html += "<td><input type='text' value='" + (item['bedrag_open']? item['bedrag_open'] : '') + "' class='txt_bedrag_open'/></td>";
    }


    html += '<td><div class="actiebutton btn-ticket-refresh" onclick="restartTicket(' + item['id'] + ')"><i class="material-icons">refresh</i></div></td>';
    html += "<td>" + item['timer_widget'] + "</td>" + 
    "<td>";
    if(mode == 'normal')
    html += "<div class='actiebutton tooltipped' data-position='top' onclick='editTicket(" + item['id'] + ")'><i class='material-icons'>edit</i></div>";
    else{
        html += "<div class='actiebutton tooltipped' data-position='top' onclick='updateTicket(" + item['id'] + ")'><i class='material-icons'>save</i></div>";
    }
    html += "<div class='actiebutton tooltipped icon_ticketnote " + (item['note_count'] > 0 ? 'hasnote' : '') + "' data-position='top' onclick='ticketNotes(" + item['id'] + ")'><i class='material-icons'>insert_comment</i></div>" + 
    
    "</td>";
    if(header)
    {
        html = "<tr class='project_ticket " + (item['status'] == 'OPENED' ? 'ticket_opened' : 'ticket_closed') +  "' ticket_row=" + item['id']+ ">" + html + "</tr>";
        html += "<tr class='project_ticket_note' ticket_row=" + item['id']+ ">" + "<td colspan='9'>"
    
        + "</td></tr>";
    }
    
    
    
    return html;
}

function manageTickets (contactid)
{
    prefillVeryLargeModal('Report wijzigen', 'manageProjectTickets.php').then(function() {
		$.ajax({
			type: "POST",
			url: "../php/aftersales/get_tickets.php",
			data: {
				contactid: contactid
			},
			dataType: "json",
			success: function(result) {
				updateTicketTimerWiget(contactid);

                $("#ticketinfo #ticket_datum").val(formatDate(new Date()));
                $("#ticketinfo .contactid").val(contactid);
                $("#ticketinfo #ticket_employee").formSelect();
                for(var index = 0; index < result.length; index ++)
                {
                    var html = getTicketRawHtmlFromItem(result[index]);
                    if(result[index]['status'] == 'OPENED')
                        $(html).insertBefore(".popup .project-ticket-table .blank_row");
                    else{
                        $(".popup .project-ticket-table tbody").append(html);
                    }
                }
                showPrefilledVeryLargeModal('max');
			}
		});
	});
}

function saveTicket()
{
    var employee = $("#ticketinfo #ticket_employee").val();
    var datum = $("#ticketinfo #ticket_datum").val();
    var title = $("#ticketinfo #ticket_title").val();
    var inkoop_besteld = $("#ticketinfo #inkoop_besteld").val();
    var bedrag_open = $("#ticketinfo #bedrag_open").val();
    var contact_id = $("#ticketinfo .contactid").val();
    var user_id = $("#ticketinfo .userid").val();
    $.ajax({
        type: "POST",
			url: "../php/aftersales/save_ticket.php",
			data: {
                employee: employee,
                datum: datum,
                title: title,
                contact_id: contact_id,
                user_id: user_id,
                bedrag_open: bedrag_open,
                inkoop_besteld: inkoop_besteld
			},
			dataType: "json",
			success: function(result) {
                if(result['message'] == 'Ticket opgeslagen.')
                {
                    melding(result['message'], 'groen');
                    $("#ticketinfo #ticket_datum").val(formatDate(new Date()));
                    $("#ticketinfo #ticket_title").val("");
                    $("#ticketinfo #ticket_employee").val("");
                    $("#ticketinfo #ticket_employee").formSelect();

                    if(result['item'].length > 0)
                    {
                        var item = result['item'][0];
                        $(".popup .project_ticket[ticket_row=" + item['id'] + "]").remove();
                            var html = getTicketRawHtmlFromItem(item) + "</tr>";
                        if(item['status'] == 'OPENED')
                            $(html).insertBefore(".popup .project-ticket-table .blank_row");
                        else{
                            $(".popup .project-ticket-table tbody").append(html);
                        }
                    }
                    
                    updateTicketTimerWiget(contact_id);
                }
                else{
                    melding(result['message'], 'rood');
                }
            },
            error: function(e1, e2, e3)
            {

            }
    });
}


function restartTicket(ticket_id)
{
    $.ajax({
        type: "POST",
			url: "../php/aftersales/restart_ticket.php",
			data: {
                ticket_id: ticket_id
			},
			dataType: "json",
			success: function(result) {
                if(result['message'] == 'Ticket sluiten.')
                {
                    melding(result['message'], 'groen');
                    if(result['item'].length > 0)
                    {
                        var item = result['item'][0];
                        $(".popup .project_ticket[ticket_row=" + item['id'] + "]").remove();
                        $(".popup .project_ticket_note[ticket_row=" + item['id'] + "]").remove();
                            var html = getTicketRawHtmlFromItem(item);
                        if(item['status'] == 'OPENED')
                            $(html).insertBefore(".popup .project-ticket-table .blank_row");
                        else{
                            $(".popup .project-ticket-table tbody").append(html);
                        }
                        updateTicketTimerWiget(item['contact_id']);
                    }

                }
                else{
                    melding(result['message'], 'rood');
                }
            },
            error: function(e1, e2, e3)
            {

            }
    });
}

function closeTicket(ticket_id)
{
    var user = $("#ticketinfo .userid").val();
    $.ajax({
        type: "POST",
			url: "../php/aftersales/close_ticket.php",
			data: {
				user: user,
                ticket_id: ticket_id
			},
			dataType: "json",
			success: function(result) {
                if(result['message'] == 'Ticket sluiten.')
                {
                    melding(result['message'], 'groen');
                    if(result['item'].length > 0)
                    {
                        var item = result['item'][0];
                        $(".popup .project_ticket[ticket_row=" + item['id'] + "]").remove();
                        $(".popup .project_ticket_note[ticket_row=" + item['id'] + "]").remove();
                            var html = getTicketRawHtmlFromItem(item);
                        if(item['status'] == 'OPENED')
                            $(html).insertBefore(".popup .project-ticket-table .blank_row");
                        else{
                            $(".popup .project-ticket-table tbody").append(html);
                        }
                        updateTicketTimerWiget(item['contact_id']);
                    }
                }
                else{
                    melding(result['message'], 'rood');
                }
            },
            error: function(e1, e2, e3)
            {

            }
    });
}

function updateTicketRowHtml(ticket_id, mode)
{
    $.ajax({
        type: "POST",
        url: "../php/aftersales/get_tickets.php",
        data: {
            ticket_id: ticket_id
        },
        dataType: "json",
        success: function(result) {
        
            for(var index = 0; index < result.length; index ++)
            {
                $(".popup .project-ticket-table tbody .project_ticket[ticket_row=" + result[index]['id'] + "]").html(getTicketRawHtmlFromItem(result[index], mode , false));
            }

            
        }
    });
}
function editTicket (ticket_id)
{
    updateTicketRowHtml(ticket_id, 'edit');
}

function updateTicket(ticket_id)
{
    var container = ".popup .project-ticket-table tbody .project_ticket[ticket_row=" + ticket_id + "]";
    var data = {
        ticket_id : ticket_id,
        title : $(container + " .txt_ticket_title").val(),
        inkoop_besteld : $(container + " .txt_ticket_inkoop_besteld").val(),
        bedrag_open : $(container + " .txt_bedrag_open").val()
    };

    $.ajax({
        type: "POST",
        url: "../php/aftersales/save_ticket.php",
        data: data,
        dataType: "json",
        success: function(result) {
        
            if(result['status'] == 'success')
            {
                melding(result['message'], 'groen');
                updateTicketRowHtml(ticket_id, 'normal');
            }
            else{
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function ticketNotes(ticket_id){
    var item = $(".project_ticket_note[ticket_row=" + ticket_id + "]");
    if(item.hasClass('display-notes'))
    {
        
        $(".project_ticket_note[ticket_row=" + ticket_id + "] .ticket_note_container").slideUp();
        item.removeClass('display-notes');
    }
    else{

        var data = {
            ticket_id: ticket_id
        };
        $.ajax({
            type: "POST",
            url: "../php/aftersales/get_ticket_notes.php",
            data: data,
            dataType: "json",
            success: function(result) {
                item.addClass('display-notes');
                item.empty();
                var html = '';
                html = "<td colspan=10><div class='row ticket_note_container'><div class='col s6 row'><div  class='col s10'><textarea type='text' class='ticket-note-text materialize-textarea' placeholder='Typ hier je notitie.'></textarea></div><div class='col s2'><div class='file-field input-field col btn-filelog'><div class='preloader-wrapper small active file-loading-icon'><div class='spinner-layer spinner-blue-only'><div class='circle-clipper left'><div class='circle'></div></div><div class='gap-patch'><div class='circle'></div></div><div class='circle-clipper right'><div class='circle'></div></div></div></div><div class='file-icon'><i class='material-icons'>attach_file</i><input type='file' class='note_file' onchange='fileselected_ticket_note(" + ticket_id + ")'></div><div class='file-path-wrapper'><input class='file-path validate' type='text' hidden></div></div></div><div><span class='button waves-effect waves-light btn' onclick='saveticketNote(" + ticket_id + ")'><i class='material-icons'>add</i> Notitie toevoegen</span></div></div><div class='col s6 ticket_note_panel'>";
                for(var index = 0; index <result.length; index ++)
                {
                    html += getTicketNoteRowFromRawData(result[index]);
                }
                html += "</div></div></td>";
                
                item.append(html);
                $(".project_ticket_note[ticket_row=" + ticket_id + "] .ticket_note_container").show('fast');;
                
                
            },
            error(e1, e2, e3) {
    
            }
        });   
        

    }
}

function saveticketNote(ticket_id)
{
    var user_id = $(".userid").text();
    var text = $("tr[ticket_row=" + ticket_id + "].project_ticket_note .ticket-note-text").val();
    var files = $('tr[ticket_row=' + ticket_id + '].project_ticket_note .note_file').prop('files');
    var data = new FormData();

    data.append('ticket_id',ticket_id );
    data.append('user_id',user_id );
    data.append('data',text );
    if(files.length > 0)
        data.append('file',files[0]);
    else 
        data.append('file', '');
    $("tr[ticket_row=" + ticket_id + "].project_ticket_note .file-loading-icon").show();
    $("tr[ticket_row=" + ticket_id + "].project_ticket_note .file-icon").hide();

    $.ajax({
        type: "POST",
        url: "../php/aftersales/save_ticket_note.php",
        data: data,
        dataType: "json",
        contentType: false,
        processData: false,
        
        success: function(result) {
            if(result['message'] == 'Notitie opgeslagen.')
            {
                var html = getTicketNoteRowFromRawData(result['data'], false);
                $("tr[ticket_row=" + ticket_id + "].project_ticket_note .ticket_note_panel").prepend(html);
                $("#ticket_note_" + result['data']['id']).slideDown();
                $("tr[ticket_row=" + ticket_id + "].project_ticket  .icon_ticketnote ").addClass("hasnote");
                melding(result['message'], 'groen');

            }
            else{
                
                melding(result['message'], 'rood');
            }



            $("tr[ticket_row=" + ticket_id + "].project_ticket_note .task-note-text").val("");
            $("tr[ticket_row=" + ticket_id + "].project_ticket_note .file-loading-icon").hide();
            $("tr[ticket_row=" + ticket_id + "].project_ticket_note .file-icon").show();
            $("tr[ticket_row=" + ticket_id + "].project_ticket_note .note_file").val("");
            $("tr[ticket_row=" + ticket_id + "].project_ticket_note .btn-filelog").removeClass('file_selected');

        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function getTicketNoteRowFromRawData(item, display = true)
{
    var html = "<div class='task_note' id='ticket_note_" + item['id'] + "' ";
    if(!display)
        html += "style='display: none;'";
    html += ">";
    var file_path = item['file_path'];
    var root_path = $("#root_path").val();
    if(file_path != undefined && file_path != null && file_path != '')
    {
        html += "<div class='note_prev'>";
        if(item['file_exe'] == 'pdf')
        {
            html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
        }
        else
        {
            html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
        }
        html += "</div>";
    }
    html += "<div><div class='note_header'>"
        + "<span class='note_date'>" + item['created_at'] + "</span>" +
            "<span class='note-user'><i class='material-icons'>person</i> " + item['username'] + "</span>" + 
            "<div onclick='showNoteInnerConfirm(" + item['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></div>" + 
            "<div class='note_content'>" + item['data'] + "</div></div>" + 
            '<div id="nipo-' + item['id'] + '" class="inner-popup-overlay"></div><div id="nipc-' + item['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeTicketNoteInnerConfirm(' + item['id'] + ')">Annuleren</span><span class="button red" onclick="deleteTicketNoteConfirm(' + item['id'] + ')">Verwijderen</span></div></div>' + 
            "</div>";
    return html;
}



function updateTicketTimerWiget(contact_id)
{
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_ticket_project.php",
        data: {
            contactid: contact_id
        },
        dataType: "json",
        success: function(result)
        {
            if(result.length > 0)
            {
                result = result[0];
                var name = result.name;
                var email = result.email;
                var phone = result.phone;
                var timer_widget = result.timer_ticket;
                var title = '<span class="name">Tickets - ' + name + ' </span>' + ((timer_widget) ? timer_widget : '') + '<span class="city">' + email + " - " + phone + '</span>';
                $(".popup.large .title").html(title);
                $(".popup.very-large .title").html(title);
                $("#project_opened tr[ticket_contact_row=" + contact_id + "]  td:nth-child(7)").html(timer_widget);
                if(result['ticket_count'] > 0)
                {
                    if( $("#project_opened tr[ticket_contact_row=" + contact_id + "]").length == 0)
                    {
                        var html = "<tr ticket_contact_row='" + contact_id + "'>" +
                        "<td>" + result.project_number + "</td>" + 
                        "<td>" + name + "</td>" + 
                        "<td>" + result.city + "</td>" + 
                        "<td>" + result.address + "</td>" + 
                        "<td>" + result.email + "</td>" + 
                        "<td>" + result.phone + "</td>" + 
                        "<td>" + result.timer_ticket + "</td>" + 
                        "<td>" + '<div onclick="manageTickets(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Tickets"><i class="material-icons">report_problem</i></div> ' + 
                        '<div onclick="manageProjectFileLog(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div> ' +    
                        '<div onclick="manageProjectTask(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div> ' + 
                        '<div onclick="manageContactLog(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div> ' + 
                        '<div onclick="editContact(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div> ' + 
                        '<div onclick="deleteContact(' + contact_id + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' + "</td> " + 
                        "</tr>";
                        $("#project_opened tbody").append(html);
                    }
                }
                else{
                    $("#project_opened tr[ticket_contact_row=" + contact_id + "]").remove();
                }
            }
        },
        error: function(e1, e2, e3)
        {

        }
    });
}
function fileselected_ticket_note(ticket_id)
{
    var files = $('.project-ticket-table tr[ticket_row=' + ticket_id + '].project_ticket_note .note_file')[0].files;
    if(files.length > 0)
    {
        $(".project-ticket-table tr[ticket_row=" + ticket_id + "].project_ticket_note .btn-filelog").addClass('file_selected');
    }
    else{
        $(".project-ticket-table tr[ticket_row=" + ticket_id + "].project_ticket_note .btn-filelog").removeClass('file_selected');
    }
}


function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

function setCookie(cname, cvalue, exdays = 1) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function gotoWeekplanning(contact_id, ticket_id)
{
    setCookie("contact_id", contact_id);
    setCookie("ticket_id", ticket_id);
    setCookie("page", "WERKPLANNING");
    var root_path = $("#root_path").val();
    location.href = root_path + "weekplanning/";
}

function uitbesteedChanged(task_id = null)
{
    var container = '.popup #tab_chapter_planning';
    if(task_id != null)
    {
        container += " tr[pt_row=" + task_id + "]";
    }
    else{
        container += " tr.pt_new";
    }
    var uitbesteed = $(container + " .ps_uitbesteed").val();
    if(uitbesteed == 'Ja')
        $(container + " .ptb_medewerkers").val(0);
}

function medewerkersChanged(task_id = null)
{
    var container = '.popup #tab_chapter_planning';
    if(task_id != null)
    {
        container += " tr[pt_row=" + task_id + "]";
    }
    else{
        container += " tr.pt_new";
    }
    var medewerkers = $(container + " .ptb_medewerkers").val();
    if(medewerkers > 0)
       {
        $(container + " .ps_uitbesteed").val("Nee");
        $(container + " .ps_uitbesteed").formSelect();
       }
}

function projectStartDatumChanged()
{
    
    var data = {
        contact_id : $(".popup .contactid").val()
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_totalweek.php",
        data: data,
        dataType: "json",
        success: function(result) {
            var total_week = result['week'];
            var startdatum = new Date($(".popup #startdatum").val());
            var days = parseInt(total_week, 10);
            days *= 7;
            startdatum.setDate(startdatum.getDate() + days);
            $(".popup #opleverdatum").val(formatDate(startdatum));
            showConfirm("Het hele project wordt verplaatst, dus de oplevertdatum wijzigt ook", 'OK!', 'red', "closeConfirm()", false, ['waves-effect', 'waves-light', 'btn']);
        }
    });
    
}

function projectEndDatumChanged()
{
    
    var data = {
        contact_id : $(".popup .contactid").val()
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_totalweek.php",
        data: data,
        dataType: "json",
        success: function(result) {
            var total_week = result['week'];
            var opleverdatum = new Date($(".popup #opleverdatum").val());
            var days = parseInt(total_week, 10);
            days *= 7;
            opleverdatum.setDate(opleverdatum.getDate() - days);
            $(".popup #startdatum").val(formatDate(opleverdatum));
            showConfirm("Het hele project wordt verplaatst, dus de startdatum wijzigt ook", 'OK!', 'red', "closeConfirm()", false, ['waves-effect', 'waves-light', 'btn']);
        }
    });
    
}


function sortTableByPrio(table) {
    var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
        tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
        i;
    tr = tr.sort(function (a, b) { // sort rows
        let a_v = parseInt($(a).find(".select-prio").val());
        let b_v = parseInt($(b).find(".select-prio").val());
        a_v = (a_v == 0 ? 1000 : a_v)
        b_v = (b_v == 0 ? 1000 : b_v)
        return a_v > b_v ? 1 : (a_v == b_v ? 0 : -1)

    });
    for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
}


function changedPrio(contact_id){
    $.ajax({
        type: "POST",
        url: "../php/leads/changed_prio.php",
        data: {
            'contact_id': contact_id,
            'prio' : $(".contacten-table tr[contactrow=" + contact_id + "] .select-prio").val()
        },
        async: false,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                $("tr[contactrow=" + contact_id + "]").parent().parent().find("th").removeClass("sort0").removeClass("sort1");
                sortTableByPrio($("tr[contactrow=" + contact_id + "]").parent().parent()[0])
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}

function switchTo()
{
    const path_name = window.location.pathname;
    $.ajax({
        type: "POST",
        url: "../php/login/get_token.php",
        
        async: false,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
              $("#switch-form input").remove();
              $("#switch-form").append("<input hidden name='EL'/><input hidden name='SS'/><input hidden name='TL'/><input hidden name='PATH'/>");
              $("#switch-form input[name='EL']").val(result['EL']);
              $("#switch-form input[name='TL']").val(result['TL']);
              $("#switch-form input[name='SS']").val(result['SS']);
              $("#switch-form input[name='PATH']").val(path_name);
              $("#switch-form").attr("action", "https://orders2.wowprefab.nl/php/login/auth2.php");
              $("#switch-form").submit();
            }
        },
        error: function(e1, e2, e3) {

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