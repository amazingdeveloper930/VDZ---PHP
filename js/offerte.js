var root = $("#root_path").val();
let myEditor = null;
let fullscreentrigger = false;

var tags = [];

var contacts_list = [];

var defaultChapterList = [];



function manageQuoteList(contactid) {
    prefillModal('Lead Offerte', 'manageQuoteList.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_quote_list.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {

                var timer_log = result.timer;
                contacts_list = result.contacts;
                result = result.result;
                
                $(".popup.large .logs").empty();
                for (var i = 0; i < result.length; i++) {
                    var qdate = result[i].qdate;
                    var qtime = result[i].qtime;
                    var qid = result[i].id;
                    var user = result[i].username;
                    var pdf_url = root + "pdf/" + result[i].pdf_file;
                    var reference = result[i].reference;

                    if(reference == null)
                        reference = '';
                    var quotelisthtml = "<div id='quote" + qid + "' class='log-container'><div class='log-wrapper'><div class='log-header'>" +
                        "<span class='q-date'>" + qdate + "</span>" +
                        "<a class='q-pdf' href='" + pdf_url + "' target='_blank'><img class='pdf-icon' src = '" + root + "images/pdf.svg'/></a>" +
                        "<span class='q-time'>" + qtime + "</span>" +
                        "<span class='q-user'><i class='material-icons'>person</i> " + user + "</span>" +
                        "<span class='q-reference'>" + reference + "</span>" +
                        "<div onclick='showQuoteInnerConfirm(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div>";
                        if(result[i].version != 1)
                        quotelisthtml += "<div onclick='editQuote(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div>";
                        quotelisthtml += 
                        "<div onclick='showQuoteEmailConfirm(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Email'><i class='material-icons'>email</i></div>";

                        if(result[i].version != 1)
                        quotelisthtml += 
                        "<div onclick='cloneQuote(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Clone'><i class='material-icons'>content_copy</i></div>";
                        quotelisthtml += 
                        "</div>" + '<div id="qipo-' + qid + '" class="inner-popup-overlay"></div><div id="qipc-' + qid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeQuoteInnerConfirm(' + qid + ')">Annuleren</span><span class="button red" onclick="deleteQuoteConfirm(' + qid + ')">Verwijderen</span></div></div>' +
                        '<div id="qepo-' + qid + '" class="inner-popup-overlay"></div><div id="qepc-' + qid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeQuoteEmailConfirm(' + qid + ')">Annuleer</span><span class="button green" onclick="sendQuoteEmail(' + qid + ')">Verstuur email</span></div></div>' +
                        '<div id="qcpo-' + qid + '" class="inner-popup-overlay"></div><div id="qcpc-' + qid + '" class="popup inner-confirm"><div class="buttons"><select id="qcps-' + qid + '"><option value=' + contactid + '>Dit project</option>' + getSelectHtmlFromContactList(contactid) + '</select><span class="button white" onclick="closeCloneQuoteConfirm(' + qid + ')">Annuleer</span><span class="button green" onclick="cloneQuoteConfirm(' + qid + ')">Kopieer</span></div></div>' +
                        
                        "</div></div>";
                    $(".popup.large .logs").append(quotelisthtml);

                    ;
                }
                var title = '';
                for (i = 0; i < timer_log.length; i++) {
                    var name = timer_log[i].name;
                    var timer = timer_log[i].timer;
                    var phone = timer_log[i].phone;
                    var email = timer_log[i].email;
                    title = '<span class="name">' + name + ' </span>' + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';


                }

                $(".popup.large .title").html(title);
                showPrefilledModal('wide');
                $("#quotelist .contactid").val(contactid);

            },
            error(e1, e2, e3) {

            }
        });
    });
}


function deleteQuoteConfirm(quote_id) {
    closeQuoteInnerConfirm(quote_id);
    $.ajax({
        type: "POST",
        url: "../php/leads/delete_quote.php",
        data: {
            'quote_id': quote_id
        },
        dataType: "html",
        success: function(result) {
            //alert(result);
            if (result == 'Offerte verwijderd.') {
                //Alles ging goed
                melding(result, 'groen');
                $("#quote" + quote_id).slideUp();
                var contact_id = $("#quotelist .contactid").val();
                $.ajax({
                    type: "POST",
                    url: "../php/leads/get_last_quote.php",
                    data: {
                        contact_id: contact_id
                    },
                    dataType: "json",
                    success: function(result) {
                        if (result && result.length > 0) {
                            var qdate = result[0].qdate;
                            var qtime = result[0].qtime;
                            var qid = result[0].id;
                            var user = result[0].username;
                            var pdf_url = root + "pdf/" + result[0].pdf_file;
                            $("table tr[contactrow=" + contact_id + "] td:nth-child(6)").html(qdate + "<a class='q-pdf' href='" + pdf_url + "' target='_blank'><img class='pdf-icon' src='" + root + "images/pdf.svg'></a>");

                        } else {
                            $("table tr[contactrow=" + contact_id + "] td:nth-child(6)").html("Nog te versturen");
                            $("#quoteinfo .quoteid").val("");
                        }
                    },
                    error: function(e1, e2, e3) {

                    }
                });




            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}
function openIntro()
{
    if($("#intro-text-panel").css('display') == 'block')
    {
        $("#intro-text-panel").slideUp();
    }
    else{
        $("#intro-text-panel").slideDown();
    }

}


function initEditor()
{
    ClassicEditor.create(document.querySelector('#intro-text'), {
                
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
}
function addNewQuote(version = 1) {
    var contactid = $("#quotelist .contactid").val();
    var userid = $("#quotelist .userid").val();
    closeModal();
    trggerFullscreen(2);
    prefillVeryLargeModal('Offerte toevoegen', 'addQuote.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_lead_log.php",
            data: {
                contactid: contactid
            },
            dataType: "html",
            success: function(result) {
                if (result) {
                    $("#quoteinfo .contactid").val(contactid);
                    $("#quoteinfo .userid").val(userid);
                    $("#quoteinfo .quoteid").val('');
                   
                    if (!Array.isArray(result)) result = $.parseJSON(result);
                    var title = '';
                    if (result.length > 0) {

                        let timer = result[0].timer;
                        let name = result[0].name;
                        let email = result[0].email;
                        let phone = result[0].phone;
                        title = name + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';

                    }

                
                    title += '<input type="text" placeholder="Kenmerk offerte" class="txt-quote-reference" value="" />';
                    title += '<div class="btn-quote-fullscreen tooltipped" onclick="trggerFullscreen()"  class="actiebutton tooltipped" data-position="top" data-tooltip="Fullscreen"><i class="material-icons">fullscreen</i></div>';
                    title += '<div class="open-intro-button tooltipped" onclick="openIntro()"  class="actiebutton tooltipped" data-position="top" data-tooltip="Introductie tekst"><i class="material-icons">subject</i></div>';
                    title += '<div class="delete-all-chapter-button" onclick="deleteAllChapter()"  class="actiebutton tooltipped" data-position="top" ><i class="material-icons">delete</i></div>';

                    $(".popup.very-large .title").html(title);
                    // $("#chapters").append("<div id='chapters'></div>");
                    $(".popup.very-large .popupheader .btn-modal-close").attr('onclick', 'closeVeryLargeModalForQuote()');





                    $.ajax({
                        type: "POST",
                        url: "../php/settings/get_default_offerte_details.php",
                        data:{
                            version : version
                        },
                        dataType: "json",
                        success: function(result) {
                           
                           var factor = result['factor'];
                           var rate = result['rate'];
                           var inkoop = result['inkoop'];
                           
                           var kosten = result['kosten'];
                            if(factor != '')
                                                        {
                                factor = getRealNumber(factor);
                            }
                            else 
                                factor = 0;
                            factor = factor * 100;
                            factor = addCommas(parseFloat(factor).toFixed(2));
                            $("#offerte_factor").val(factor);
                            $("#offerte_rate").val(rate);
                            $("#offerte_inkoop").val(inkoop);
                            $("#offerte_kosten").val(kosten);
                            $("#intro-text").html(result['intro']);
                            tags = result['tags'];
                           result = result['chapter_list'];
                           defaultChapterList = result;
                            var title = '<div class="save-button"><span class="button waves-effect waves-light btn" onclick="saveQuote(' + version + ')">Offerte opslaan</span></div>';
                            $(".popup.very-large .title").append(title);
                            
                            for (var index = 0; index < result.length; index++) {
                                buildChapter(result[index], false, false);
                            }   
                            
                            
                            initEditor();
                            calculate();
                            $('.chapter-line-vat').formSelect();
                            $('.tooltipped').tooltip();
                        },
                        error: function(e1, e2, e3)
                        {
                            
                        }

                    });

                }
                showPrefilledVeryLargeModal('max');
            },
            error(e1, e2, e3) {

            }
        });
    });




}

function changedChapter(id)
{
    $(".popup #chapter_" + id + " .chapter-header select[name='chapter_title'] option[original_item = 'true']").remove();
    $(".popup #chapter_" + id + " .chapter-content").empty();
    var default_chapter_id = $(".popup #chapter_" + id + " .chapter-header select[name='chapter_title']") . val();
    var lineDataList = [];
    for(var index = 0; index < defaultChapterList.length; index ++ )
    {
        if(defaultChapterList[index]['chapter_id'] == default_chapter_id)
        {
            lineDataList = defaultChapterList[index]['line_data'];
            for(var jdex = 0; jdex < lineDataList.length ; jdex ++)
            {
                buildLine(id, lineDataList[jdex]);
            }
            $(".popup.very-large select").formSelect();
            calculate(true);
            break;
        }
    }
}

function changedLine(chapter_id, line_id)
{
    var line_com = ".popup #line_" + line_id;
    $(line_com + " select.chapter-line-header option[original_item = 'true']").remove();
    var item = $(line_com + " select.chapter-line-header option:selected");

    var unit = item.attr('line_unit');
    $(line_com + " .chapter-line-quantity").val(1);
    $(line_com + " .chapter-line-unit").text(unit);
    $(line_com + " .chapter-line-vat").val("0.21");
   
  

    $(".popup.very-large select").formSelect();
   

    var index = item.attr('default_chapter_index');
    var jdex = item.attr('default_line_index');
    $(line_com + " .chapter-line-description").val(defaultChapterList[index]['line_data'][jdex]['line_description']);

    buildAM(chapter_id, line_id, defaultChapterList[index]['line_data'][jdex]);
    setOfferteLabel(line_id);
    getTotal(chapter_id, line_id);
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
function changedAM(id, line_id)
{
    var container = "#line_" + line_id;

    var line_data = [];
    line_data['line_am_option'] = $(container + " .chapter-line-am-option").val();
    buildAM(id, line_id, line_data);
    setOfferteLabel(line_id);
    getTotal(id, line_id, true);
}

function setProfit(line_id)
{
    var container = "#line_" + line_id;
    var profit = $(container + " .chapter-line-profit").val();
    if(profit == "0")
    {
        $(container).removeClass("line-profit");
    }
    else{
        $(container).addClass("line-profit");
    }
    // $(container + " .chapter-line-quantity").val("1");
}

function changedProfit(id, line_id)
{
    setProfit(line_id);
    setOfferteLabel(line_id);
    var profitAmount = getProfitAmount();
    setProfitAmount(profitAmount, "line_" + line_id);
    getTotal(id, line_id, true);
}

function getLineProfitSelectOptionsHtml()
{
	var html = "<option value='0'>Nee</option>";
    html += "<option value='1'>Ja, variabel</option>";
    html += "<option value='0.003'>0,3%</option>";
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

function buildAM(id, line_id, lineData)
{

    var linehtml = "";
    linehtml += "<div class='row line-row-amoption'>"+
        "<div class='col s3'>Arbeid/Materiaal?</div>" +
        "<div class='col s1'><select class='chapter-line-am-option browser-default' onchange='changedAM(\"" + id + "\", \"" + line_id + "\")'><option value='ja'>Ja</option><option value='nee'>Nee</option></select></div>" +
    "</div>";
    if(lineData['line_am_option'] == 'ja')
    {
        linehtml += "<div class='row line-row-arbeid-box'>" +
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
		"<div class='col s2'>&nbsp;</div>" +
		"<div class='col s1'><p class='label-materiaal-total'></p></div>" + 
		"</div>" + 
		"<div class='row line-row-winst'>" + 
		"<div class='col s3'><p class='label-winst-header'></p></div>" + 
		"<div class='col s2'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-winst-total'></p></div>" + 
		"</div>" +
		"<div class='row line-row-total'>" + 
		"<div class='col s3'><p class='label-total-header'></p></div>" +
		"<div class='col s2'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-total-value'></p></div>"
		"</div>";
        $("#line_" + line_id + " .am_content").html(linehtml);
        if($.isEmptyObject(lineData['arbeid_data']))
        {
            addArbeid(id, line_id);
        }
        else
        for(var kdex = 0; kdex < lineData['arbeid_data'].length; kdex++)
        {
            addArbeid(id, line_id, lineData['arbeid_data'][kdex]);
        }

        if($.isEmptyObject(lineData['materiaal_data']))
        {
            addMateriaal(id, line_id);
        }
        else
        for(kdex = 0; kdex < lineData['materiaal_data'].length; kdex++)
        {
            addMateriaal(id, line_id, lineData['materiaal_data'][kdex]);
        }

        $("#line_" + line_id).removeClass('line-am-arbeid');

    }
    else{
        linehtml += 
        "<div class='row line-row-profit'>"+
        "<div class='col s3'><p>Brutowinstregel?</p></div>" +
        "<div class='col s1'><select class='chapter-line-profit browser-default' onchange='changedProfit(\"" + id + "\", \"" + line_id + "\")'>" + getLineProfitSelectOptionsHtml() + "</select></div>" +
        "</div>" + 
        "<div class='row line-row-price'>" +
        "<div class='col s3'><p class='label-price-header'></p></div>" + 
        "<div class='col s2'>&nbsp;</div>" + 
        
        "<div class='col s1'><input type='text' class='chapter-line-arbeid-price' onchange='convertNumber(this)' oninput='getTotal(\"" + id + "\", \"" + line_id + "\")'></input></div>" + "</div>" +
		"<div class='row line-row-winst'>" + 
		"<div class='col s3'><p class='label-winst-header'></p></div>" + 
		"<div class='col s2'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-winst-total'></p></div>" + 
		"</div>" +
		"<div class='row line-row-total'>" + 
		"<div class='col s3'><p class='label-total-header'></p></div>" +
		"<div class='col s2'>&nbsp;</div>" + 
		"<div class='col s1'><p class='label-total-value'></p></div>"
		"</div>";
        $("#line_" + line_id + " .am_content").html(linehtml);
        $("#line_" + line_id + " .chapter-line-arbeid-price").val(lineData['line_total']);
        $("#line_" + line_id).addClass('line-am-arbeid');
        $("#line_" + line_id + " .chapter-line-profit").val(lineData['line_profit']);
        if($("#line_" + line_id + " .chapter-line-profit").val() != "0")
        {
            $("#line_" + line_id).addClass('line-profit-mode');
        }
        setProfit(line_id);
    }
    $("#line_" + line_id + " .chapter-line-am-option").val(lineData['line_am_option']);
    
}
function buildLine(chapter_id, lineData, existing = false)
{
    var linehtml = '';
    var line_id = makeid(15);
    var optionHtml = '';
    if(existing)
    {
        optionHtml += "<option selected original_item = 'true' line_am_option='" + lineData['line_am_option'] + "' line_unit='" + lineData['unit'] + "' line_total='" + lineData['line_total'] + "' materiaal_total='" + lineData['materiaal_total'] + "' arbeid='" + lineData['arbeid'] + "' >" + lineData['line_title'] + "</option>";
    }
    var default_chapter_id = lineData['default_chapter_id'];

    for(var index = 0; index < defaultChapterList.length; index ++ )
    {
        if(defaultChapterList[index]['chapter_id'] == default_chapter_id)
        {
            lineDataList = defaultChapterList[index]['line_data'];
            for(var jdex = 0; jdex < lineDataList.length ; jdex ++)
            {
                var item = lineDataList[jdex];
                optionHtml += "<option ";

                if(existing == false && lineData['id'] == item['id'])
                    optionHtml += " selected ";
                // if(existing == false)
                // {
                //     optionHtml += " default_chapter_index=" + index + " default_line_index=" + jdex; 
                // }
                optionHtml += " default_chapter_index=" + index + " default_line_index=" + jdex; 
                optionHtml += " line_am_option='" + item['line_am_option'] + "' line_unit='" + item['line_unit'] + "' line_total='" + item['line_total'] + "' materiaal_total='" + item['materiaal_total'] + "' arbeid='" + item['arbeid'] + "' >" + item['line_title'] + "</option>";
            }
            
            break;
        }
    }

    linehtml = "<div class='chapter-line' id='line_" + line_id + "'>" +
    "<div class='input-field col s4'><input type='hidden' class='chapter-line-id' value='" + line_id + "' /><select  class='browser-default chapter-line-header' onchange='changedLine(\"" + chapter_id + "\", \"" + line_id + "\")'>" + optionHtml + "</select>" + 
    "<div class='actiebutton btn-editline' onclick='editLine(\"" + chapter_id + "\", \"" + line_id + "\")'><i class='material-icons'>edit</i></div>"+
    "<div onclick='editLineTag(\"" + line_id + "\")' class='actiebutton btn-edittag' data-position='top' data-tooltip=''><i class='material-icons'>label_outline</i></div>" +
    "<div class='actiebutton btn-closetext' onclick='closeText(this)'><i class='material-icons'>format_align_justify</i></div>" +
    "<div class='actiebutton btn-uploadline' onclick='uploadLine(\"" + chapter_id + "\", \"" + line_id + "\")'><i class='material-icons'>file_upload</i></div>" +
    "</div>" +
    "<div class='input-field col s1'><input type='text' class='chapter-line-quantity special-number' onchange='convertNumber(this, 2)' oninput = 'calculate(true)' min='0' ";
    if(existing)
        linehtml += " value= '" + lineData['quanitty'] + "'";
    else 
        linehtml += " value= 1";
    linehtml += " /></div>" ;
    linehtml += "<div class='input-field col s1'><p class='chapter-line-unit'>" + lineData['line_unit'] + "</p></div>" +
    "<div class='col s1'><p class='chapter-line-price'></p></div>" + 
    "<div class='input-field col s1'><select class='chapter-line-vat'  onchange = 'calculate(true)'><option value='0' >0 %</option><option value='0.09' >9 %</option><option value='0.21' selected>21 %</option></select></div>" +
    "<div class='col s1'><p class='chapter-line-price-inc'></p></div>" + "<div class='col s2'><p class='chapter-line-subtotal'></p></div>" +
    "<div class='col icon-list' style='margin-top: -20px; margin-left: 27px;'>" +
        "<div onclick='deleteLine(\"" + chapter_id + "\", \"" + line_id + "\")' class='actiebutton' data-position='top' ><i class='material-icons'>delete</i></div>" +
        "<div onclick='moveupLine(\"" + chapter_id + "\", \"" + line_id + "\")' class='actiebutton  moveup-btn' data-position='top' ><i class='material-icons'>arrow_upward</i></div></div>" +
        "<div class='col s4' style='clear: both;'><textarea type='text' class='chapter-line-description materialize-textarea' placeholder='Beschrijving van deze regel.'></textarea></div>";
    linehtml += "<div class='row line-row-tag-box'>" + 
            "<div class='line-row-tag-standard'>" +
                "<div class='col s2'><p class='label-tag-standard-header'>Kies een standaard tag</p></div>" +
                "<div class='input-field col s1'><select class='browser-default chapter-line-tag-standard'>" + getTagSelectOptionsHtml('STANDARD') + "</select></div>" + 
            "</div>" + 
            "<div class='line-row-tag-fase'>" + 
                "<div class='col s2'><p class='label-tag-fase-header'>Kies een fase tag</p></div>" +
                "<div class='input-field col s1'><select class='browser-default chapter-line-tag-fase'>" + getTagSelectOptionsHtml('FASE') + "</select></div>" + 
            "</div>" + 
        "</div>" ;
    linehtml += "<div class='am_content'></div>";


    linehtml += "</div>";
    $("#chapter_" + chapter_id + " .chapter-content").append(linehtml);

    if(existing)
    {
        $("#chapter_" + chapter_id + " #line_" + line_id + " .chapter-line-description").val(lineData['line_descr']);
        $("#chapter_" + chapter_id + " #line_" + line_id + " .chapter-line-vat").val(lineData['vat']);


        
    }
    else 
    {
        $("#chapter_" + chapter_id + " #line_" + line_id + " .chapter-line-description").val(lineData['line_description']);
    }
        
    $("#chapter_" + chapter_id + " #line_" + line_id + " .chapter-line-tag-standard").val(lineData['standard_tag_id']);

    $("#chapter_" + chapter_id + " #line_" + line_id + " .chapter-line-tag-fase").val(lineData['fase_tag_id']);
    
    $(".popup #chapter_" + chapter_id + " .chapter-subtotal").show();
    buildAM(chapter_id, line_id, lineData);

    if(lineData['line_edit_mode'] == 1)
        editLine(chapter_id, line_id);

    setOfferteLabel(line_id);
    getTotal(chapter_id, line_id, false);
}



function buildChapter(chapterData, existing = false, shouldBuildLine = true)
{
    var chapterhtml = '';
    var optionHtml = '';
    var id = makeid(15);
    if(existing)
    {
        optionHtml += "<option selected value=" + chapterData['default_chapter_id'] + " original_item = 'true'>" + chapterData['chapter_name'] + "</option>";
    }
    for(var index = 0; index < defaultChapterList.length; index ++)
    {
        optionHtml += "<option ";
        if(existing == false && defaultChapterList[index]['chapter_id'] == chapterData['chapter_id'])
            optionHtml += " selected ";
        optionHtml += "value=" + defaultChapterList[index]['chapter_id'] + ">" + defaultChapterList[index]['chapter_title'] + "</option>";
    }

    chapterhtml = "<div class='chapter-container' id='chapter_" + id + "'>" +
    "<input type='hidden' name='chapter_id[]' value='" + id + "'>" +
    "<div class='chapter-wrapper'><div class='chapter-header row'><div class='col s4'><select class='browser-default' name='chapter_title' onchange='changedChapter(\"" + id + "\")'>" + optionHtml + "</select></div>";
    
    chapterhtml += "<div class='col s1'><p>Aantal</p></div><div class='col s1'></div><div class='col s1'><p>Prijs ex.BTW</p></div><div class='col s1'><p>BTW</p></div><div class='col s1'><p>Prijs inc.BTW</p></div><div class='col s1'><p>Subtotaal</p></div><div class='col s1'><p>W&R % <input type='text' onchange='convertNumber(this, 2)' oninput='calculate(true)' class='chapter_factor' name='chapter_factor'/></p></div><div class='col icon-list'>" +
    "<div onclick='deleteChapter(\"" + id + "\")' class='actiebutton ' data-position='top' ><i class='material-icons'>delete</i></div>" +
    "<div onclick='moveupChapter(\"" + id + "\")' class='actiebutton  moveup-btn' data-position='top' data-tooltip=''><i class='material-icons'>arrow_upward</i></div>" +
    "<div onclick='addLine(\"" + id + "\")' class='actiebutton ' data-position='top' data-tooltip=''><i class='material-icons'>add</i></div>" +
    "</div></div>" +
    "<div class='chapter-content row'></div>" +
    "<div class='chapter-subtotal'>" + 
    "<div class='label-subtotal-ex'><div class='label-subtotal'>Subt ex.BTW</div>&euro; <span class='label-subtotal-value'></span></div>" +
    "<div class='label-subtotal-in'><div class='label-subtotal'>Subt in.BTW</div>&euro; <span class='label-subtotal-value'></span></div>" +
    "</div>" +
    "</div></div>";
    $('.popup .chapters').append(chapterhtml);
    if(existing && chapterData['chapter_factor'] != '')
    {
        $("#chapter_" + id + " .chapter_factor").val(chapterData['chapter_factor']);
    }
    if(shouldBuildLine)
    {
        for(var index = 0; index < chapterData['line_data'].length; index ++)
        buildLine(id, chapterData['line_data'][index], existing);
    }
    
}

function moveupChapter(id) {
    var currentItem = $("#chapter_" + id);
    currentItem.insertBefore(currentItem.prev());
}


function deleteChapter(id) {

    showConfirm('Hoofdstuk en alle regels verwijderen?', 'Verwijderen', 'red', "deleteChapterConfirm('" + id + "')");

}

function deleteChapterConfirm(id) {
    closeConfirm();

    $("#chapter_" + id).remove();
    calculate(true);
}

function deleteAllChapter() {
    showConfirm('Weet je zeker dat je alle regels wilt verwijderen?', 'Ja, verwijderen', 'red', "deleteAllChapterConfirm()");
}

function deleteAllChapterConfirm() {
    closeConfirm();
    $(".chapter-container").remove();
    calculate();
}

function deleteLine(chapter_id, line_id) {
    $("#chapter_" + chapter_id + " #line_" + line_id).remove();
    if($("#chapter_" + chapter_id + " .chapter-line").length == 0)
        $("#chapter_" + chapter_id + " .chapter-subtotal").hide();
    calculate(true);
}

function moveupLine(chapter_id, line_id) {
    var currentItem = $("#chapter_" + chapter_id + " #line_" + line_id);
    currentItem.insertBefore(currentItem.prev());
}


function addChapter(){
    if(defaultChapterList.length > 0)
    buildChapter(defaultChapterList[0]);
    calculate(true);
}

function addLine(chapter_id)
{
    var default_chapter_id = $(".popup #chapter_" + chapter_id + " select[name='chapter_title']").val();
    for(var index = 0; index < defaultChapterList.length ; index ++)
    {
        if(defaultChapterList[index]['chapter_id'] == default_chapter_id)
        {
            if(defaultChapterList[index]['line_data'].length > 0)
            buildLine(chapter_id, defaultChapterList[index]['line_data'][0]);
            break;
        }
    }
    calculate(true);
    $(".popup #chapter_" + chapter_id + " select").formSelect();
}


function setOfferteLabel(line_id)
{
	var item = "#line_" + line_id; 
	var text = $(item + " .chapter-line-header option:selected").text();
	if(text == undefined)
	text = "";
    $(item + " .line-row-price .label-price-header").text(text);
	var num = text.split(' ')[0].replace(/[^\d.-]/g, '');
	text = text.substring(num.length);
	text = text.toLowerCase();
	$(item + " .line-row-arbeid .label-arbeid-header").text(num + " Arbeid " + text);
	$(item + " .line-row-materiaal .label-materiaal-header").text(num + " Materiaal " + text);
	$(item + " .line-row-winst .label-winst-header").text(num + " Winst & risico");
    var unit = $(item + " .chapter-line-unit").text();	
    var am_option =  $(item + " .chapter-line-am-option").val();
    $(item + " .line-row-total .label-total-header").text('Totaal per ' + unit);
    if(am_option == 'nee')
    {
        var profit = $(item + " .chapter-line-profit").val();
        if(profit != '0')
            $(item + " .line-row-total .label-total-header").text('Totaal');
    }
    
	
    
}


function calculate(flag_c_profit = false)
{
    var profitAmount = getProfitAmount();
    if(flag_c_profit)
    {
        
        setProfitAmount(profitAmount);
    }
    var factor = $(".popup #offerte_factor").val();
    var rate = $(".popup #offerte_rate").val();
    var inkoop = $(".popup #offerte_inkoop").val();
    var kosten = $(".popup #offerte_kosten").val();
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
    if(inkoop != '')
    {
        inkoop = getRealNumber(inkoop);
    }
    else
        inkoop = 0;
    if(kosten != '')
    {
        kosten = getRealNumber(kosten);
    }
    else
        kosten = 0;

    var total_excl = 0;
    var vat_21 = 0;
    var vat_9 = 0;
    var total = 0;
    var t_arbeid = 0;
    var t_materiaal = 0;
    var t_wr = 0;
    var t_profit = 0;
    for(var kdex = 0; kdex < $(".chapters .chapter-container").length ; kdex ++)
    {
        var chapter_item = $(".chapters .chapter-container")[kdex];
        var chapter_subtotal_ex = 0;
        var chapter_subtotal_in = 0;
        var chapter_factor = factor;
        
        if($(".chapters #" + chapter_item.id+ " .chapter_factor").val() != '')
        {
            chapter_factor = $(".chapters #" + chapter_item.id+ " .chapter_factor").val();
            chapter_factor = getRealNumber(chapter_factor);
            chapter_factor = chapter_factor / 100;
        }

    
        for(var index = 0; index < $(".chapters #" + chapter_item.id+ " .chapter-line").length ; index ++)
        {
            var item = $(".chapters #" + chapter_item.id+ " .chapter-line")[index];
            var line_com = "#" + item.id;
            var selected_line = $(line_com + " select.chapter-line-header option:selected");
            
            var line_am_option = $(line_com + " .chapter-line-am-option").val();
            var line_unit = $(line_com + " .chapter-line-unit").text();
            var line_total = "";
            var materiaal_total = 0;
            var arbeid = 0;
            var quantity_val = $(line_com + " .chapter-line-quantity").val();
            quantity_val = getRealNumber(quantity_val);
            
            if(line_am_option == 'ja')
                {
                    arbeid = $(line_com + " .chapter-line-arbeid").val();
                    materiaal_total = $(line_com + " .label-materiaal-total").text();
                }
                else{
                    line_total = $(line_com + " .chapter-line-arbeid-price").val();
                }
            line_total = (line_total != '' ? getRealNumber(line_total) : 0);
            materiaal_total = (materiaal_total != '' ? getRealNumber(materiaal_total) : 0);
            arbeid = (arbeid != '' ? getRealNumber(arbeid) : 0);
            
            
            var price = 0;
            var sub_winst_total = 0;
            var line_profit = $(line_com + " .chapter-line-profit").val();

            
            if(line_am_option == 'ja'){
                price = (arbeid * rate + materiaal_total) * (1 + chapter_factor);
                t_arbeid += arbeid * quantity_val;
                t_materiaal += materiaal_total * quantity_val;
                sub_winst_total = (arbeid * rate + materiaal_total) * quantity_val * chapter_factor;
            }
                
            else {
                if(line_profit == "0")
                {
                    price = line_total * (1 + chapter_factor);
                    sub_winst_total = line_total * chapter_factor * quantity_val;
                }
                else{
                    price = line_total;
                    sub_winst_total = 0;
                    quantity_val = 1;
                    t_profit += price;
                }
                
            }
            var price_inc = 0;
            var vat = 0;
            vat = price * parseFloat($(line_com + " .chapter-line-vat").val());
            price_inc = price + vat;
            var subtotal = 0;
            subtotal = price_inc * quantity_val;
            $(line_com + " .chapter-line-price").text(addCommas(parseFloat(price).toFixed(2)));
            $(line_com + " .chapter-line-price-inc").text(addCommas(parseFloat(price_inc).toFixed(2)));
            $(line_com + " .chapter-line-subtotal").text(addCommas(parseFloat(subtotal).toFixed(2)));
            //
            if(sub_winst_total == '')
            sub_winst_total = 0;
            $(line_com + " .label-winst-total").text(addCommas(parseFloat(sub_winst_total).toFixed(2)));
            
            t_wr += sub_winst_total;
            $(line_com + " .label-total-value").text(addCommas(parseFloat(price).toFixed(2)));

            //


            total_excl += price * quantity_val;
            total += subtotal;
            if($(line_com + " .chapter-line-vat").val() == "0.09")
                vat_9 += vat  * quantity_val;
            else 
                vat_21 += vat  * quantity_val;
            chapter_subtotal_ex += price * quantity_val;
            chapter_subtotal_in += subtotal;
        }
        $(".chapters #" + chapter_item.id + " .label-subtotal-ex .label-subtotal-value").text(addCommas(parseFloat(chapter_subtotal_ex).toFixed(2)));
        $(".chapters #" + chapter_item.id + " .label-subtotal-in .label-subtotal-value").text(addCommas(parseFloat(chapter_subtotal_in).toFixed(2)));
    }
    $(".calculating-box .total").html("---");
    $(".calculating-box .vat-percent").html("");
    $(".calculating-box .vat-excel").html("0");
    $(".calculating-box .total-excel").html("---");
    $(".calculating-box .uren_arbeid").html("---");
	$(".calculating-box .verkoop_arbeid").html("");
	$(".calculating-box .inkoop_arbeid").html("---");
	$(".calculating-box .inkoop_materiaal").html("---");
	$(".calculating-box .marge_uren").html("---");
	$(".calculating-box .marge_w").html("---");
    $(".calculating-box .totaal_marge").html("---");
    $(".calculating-box .totaal_verkoop").html("---");
    $(".calculating-box .algemene_kosten").html("---");
    $(".calculating-box .netto_winst").html("---");
    $(".calculating-box .btw_row_2").hide();
    $(".calculating-box .total").html(addCommas(total.toFixed(2).replace(/\./g, ',')));
    var row = 1;
    if (vat_9 > 0) {
        $(".calculating-box .btw_row_" + row + " .vat-percent").html('&nbsp;&nbsp;' + "9%");
        $(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_9.toFixed(2).replace(/\./g, ',')));
        row++;
    }
    if (vat_21 > 0) {
        $(".calculating-box .btw_row_" + row + " .vat-percent").append("21%");
        if (vat_9 > 0)
            $(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_21.toFixed(2).replace(/\./g, ',')));
        else
            $(".calculating-box .btw_row_" + row + " .vat-excel").html(addCommas(vat_21.toFixed(2).replace(/\./g, ',')));
        if (row == 2)
            $(".calculating-box .btw_row_2").show();
    }
    $(".calculating-box .total-excel").html(addCommas((total_excl).toFixed(2).replace(/\./g, ',')));
    $(".calculating-box .uren_arbeid").html(addCommas(t_arbeid.toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .verkoop_arbeid").html(addCommas((t_arbeid * rate).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .inkoop_arbeid").html(addCommas((t_arbeid * inkoop).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .inkoop_materiaal").html(addCommas((t_materiaal).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .marge_uren").html(addCommas((t_arbeid * (rate - inkoop)).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .marge_w").html(addCommas(t_wr.toFixed(2).replace(/\./g, ',')));
    $(".calculating-box .totaal_marge").html(addCommas((t_arbeid * (rate - inkoop) + t_wr + t_profit).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .algemene_kosten").html(addCommas((profitAmount * kosten / 100).toFixed(2).replace(/\./g, ',')));

    $(".calculating-box .netto_winst").html(addCommas((t_arbeid * (rate - inkoop) + t_wr + t_profit - profitAmount * kosten / 100).toFixed(2).replace(/\./g, ',')));

}

function getProfitAmount()
{
    var amount = 0;
    $(".chapter-content .chapter-line").each(function(item){
        var container = "#" + $(this).attr("id");
        var am_option = $(container + " .chapter-line-am-option").val();
        var quantity = $(container + " .chapter-line-quantity").val();
        var price = $(container + " .label-total-value").text();
        quantity = getRealNumber(quantity);
        price = getRealNumber(price);
        if(am_option == 'nee')
        {
            var profit = $(container + " .chapter-line-profit").val();
            if(profit != '0')
                quantity = 0;
        }
        
        amount += quantity * price;
    });
    return amount;
}
function setProfitAmount(amount, line_id = null)
{
    if(line_id == null)
    {
        $(".chapter-content .chapter-line").each(function(item){
            
            var line = $(this).attr("id");
            setProfitAmount(amount, line);
        });
    }
    else{
        var container = "#" + line_id;
        var am_option = $(container + " .chapter-line-am-option").val();
        if(am_option == 'nee')
        {
            var profit = $(container + " .chapter-line-profit").val();
            if(profit != '0' && profit != '1')
            {
                profit = parseFloat(profit);
                var profitAmount = profit * amount;
                $(container + " .chapter-line-arbeid-price").val(addCommas(profitAmount.toFixed(2)));
                
            }
        }
    }
    
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




function deleteArbeid(chapter_id, line_id, arbeid_id)
{
	$("#arbeid-item-" + arbeid_id).remove();
	getTotal(chapter_id, line_id);
}

function deleteMateriaal(chapter_id, line_id, materiaal_id)
{
	$("#material-item-" + materiaal_id).remove();
	getTotal(chapter_id, line_id);
}

function getTotal(chapter_id = null, line_id = null, shouldcalculate = true) {

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
			getTotal(null, item_line_id, shouldcalculate);
		}
	}
	else{
		var container = "#line_" + line_id;
		var total = 0;
		var materiaal_total = 0;
		var winst_total = 0;
		if($(container + " .chapter-line-am-option").val() == 'nee')
		{
			var ar_value = getRealNumber($(container + " .chapter-line-arbeid-price").val());
			var profit = $(container + " .chapter-line-profit").val();
            if(profit == '0')
			    winst_total = ar_value  * factor;
            else
                winst_total = 0;
			total = ar_value + winst_total;
			
		}
		else{
			var ar_value_total = 0;
			for(var index = 0; index < $(container + " .line-row-arbeid-box .arbeid-item").length; index ++)
			{
				var item_str = $(container + " .line-row-arbeid-box .arbeid-item")[index];
                item_str = $(item_str).attr('id');
				var quantity = $("#" + item_str + " .arbeid_quantity").val();

				quantity = (stuks != '' ? getRealNumber(quantity) : 0);
				var a_t = quantity * rate;
				ar_value_total += quantity;
				$("#" + item_str + " .label-arbeid-item-total").text(addCommas(parseFloat(a_t).toFixed(2)));
			}

			$(container + " .chapter-line-arbeid").val(addCommas(parseFloat(ar_value_total)));
			var arbeid_value = $(container + " .chapter-line-arbeid").val();
			var arbeid_total = getRealNumber(arbeid_value) * rate;
			$(container + " .label-arbeid-total").text(addCommas(parseFloat(arbeid_total).toFixed(2)));

			
			for(var index = 0; index < $(container + " .line-row-materiaal-box .material-item").length; index ++)
			{
				var item_str = $(container + " .line-row-materiaal-box .material-item")[index];
                item_str = $(item_str).attr('id');
				var stuks = $("#" + item_str + " .materiaal_stuks").val();
				var price = $("#" + item_str + " .materiaal_price").val();

				stuks = (stuks != '' ? getRealNumber(stuks) : 0);
				price = (price != '' ? getRealNumber(price) : 0);
				var m_t = stuks* price;
				materiaal_total += m_t;
				$("#" + item_str + " .label-materiaal-item-total").text(addCommas(parseFloat(m_t).toFixed(2)));
			}
			$(container + " .label-materiaal-total").text(addCommas(parseFloat(materiaal_total).toFixed(2)));
			winst_total  = (materiaal_total + arbeid_total) * factor;
			total = arbeid_total + materiaal_total + winst_total;
		}
		$(container + " .label-winst-total").text(addCommas(parseFloat(winst_total).toFixed(2)));
		$(container + " .label-total-value").text(addCommas(parseFloat(total).toFixed(2)));

        $(container + " .chapter-line-price").text(addCommas(parseFloat(total).toFixed(2)));
        if(shouldcalculate)
        {
            var flag = true;
            var am_option = $(container + " .chapter-line-am-option").val();
            if(am_option == "nee")
            {
                var profit =  $(container + " .chapter-line-profit").val();
                if(profit != "0")
                    flag = false;
            }
            calculate(flag);
        }
        

	}

}

function saveQuote(version = 1) {
    var contact_id = $("#quoteinfo .contactid").val();
    var userid = $("#quoteinfo .userid").val();
    var quoteid = $("#quoteinfo .quoteid").val();
    var factor = $(".popup #offerte_factor").val();
    if(factor != '')
	{
		factor = getRealNumber(factor);
	}
	else factor = 0;
	factor = addCommas(factor / 100);

    var rate = $(".popup #offerte_rate").val();
    var inkoop = $(".popup #offerte_inkoop").val();
    var kosten = $(".popup #offerte_kosten").val();
    var arbeid_pdf = $(".popup #offerte_arbeid_pdf").prop('checked');
    var materiaal_pdf = $(".popup #offerte_materiaal_pdf").prop('checked');

    if(arbeid_pdf)
        arbeid_pdf = 1;
    else 
        arbeid_pdf = 0;
    if(materiaal_pdf)
        materiaal_pdf = 1;
    else 
        materiaal_pdf = 0;    

    var reference = $(".txt-quote-reference").val();
    var intro = myEditor.getData();
    var quoteData = {};
    var chapterData = [];
    for (var index = 0; index < $(".chapter-container input[name='chapter_id[]']").length; index++) {

        var chapter_id = $(".chapter-container input[name='chapter_id[]']").eq(index).val();
        var chapter_factor = $(".chapters #chapter_" + chapter_id + " .chapter_factor").val();
        var chapter_name = $(".chapters #chapter_" + chapter_id + " select[name='chapter_title'] option:selected").text();
        var line_datas = [];
        for (var jdex = 0; jdex < $("#chapter_" + chapter_id + " .chapter-line-id").length; jdex++) {
            var line_id = $("#chapter_" + chapter_id + " .chapter-line-id").eq(jdex).val();
            var line_total = '';

            var line_am_option = $("#line_" + line_id  + " .chapter-line-am-option").val();


            var arbeid = 0;
            var materiaal_total = 0;
            if(line_am_option == 'ja')
            {
                arbeid = $("#line_" + line_id + " .chapter-line-arbeid").val();
                materiaal_total = $("#line_" + line_id + " .label-materiaal-total").text();
            }
            else{
                line_total = $("#line_" + line_id + " .chapter-line-arbeid-price").val();
            }

            if(line_total == undefined)
                line_total = '';
            if(arbeid == undefined)
                arbeid = '';
            if(materiaal_total == undefined)
                materiaal_total = '';


            var line_edit_mode = 0;
            
            if($("#line_" + line_id).hasClass('line-edit-mode'))
                line_edit_mode = 1;

            var line_data = {
                line_title: $("#line_" + line_id + " .chapter-line-header option:selected").text(),
                line_edit_mode : line_edit_mode,
                line_descr: $("#line_" + line_id + " .chapter-line-description").val(),
                quanitty: $("#line_" + line_id + " .chapter-line-quantity").val(),
                unit: $("#line_" + line_id + " .chapter-line-unit").text(),
                price: $("#line_" + line_id + " .chapter-line-price").text(),
                price_inc: $("#line_" + line_id + " .chapter-line-price-inc").text(),
                vat: $("#line_" + line_id + " .chapter-line-vat").val(),
                subtotal: $("#line_" + line_id + " .chapter-line-subtotal").text(),
                line_am_option : line_am_option,
                line_total : line_total,
                arbeid : arbeid,
                materiaal_total : materiaal_total,
                sort_order: jdex,
                line_profit : $("#line_" + line_id + " .chapter-line-profit").val(),
                standard_tag_id : $("#line_" + line_id + " .chapter-line-tag-standard").val(),
				fase_tag_id : $("#line_" + line_id + " .chapter-line-tag-fase").val()
            };
            var arbeid_data = [];
            var materiaal_data = [];
            for(var kdex = 0; kdex < $("#line_" + line_id + " .am_content .arbeid-content .arbeid-item").length; kdex ++)
            {
                var arbeid_content_div = $("#line_" + line_id + " .am_content .arbeid-content .arbeid-item").eq(kdex);
                var arbeid_item = {
                    arbeid_title : 
                    arbeid_content_div.find('.arbeid_title').val(),
                    quantity : arbeid_content_div.find('.arbeid_quantity').val(),
                    sort_order : kdex
                };
                arbeid_data.push(arbeid_item);
            }

            for(kdex = 0; kdex < $("#line_" + line_id + " .am_content .materiaal-content .material-item").length; kdex ++)
            {
                var materiaal_content_div = $("#line_" + line_id + " .am_content .materiaal-content .material-item").eq(kdex);
                var materiaal_item = {
                    materiaal_title : 
                    materiaal_content_div.find('.materiaal_title').val(),
                    stuks : materiaal_content_div.find('.materiaal_stuks').val(),
                    price : materiaal_content_div.find('.materiaal_price').val(),
                    sort_order : kdex
                };
                materiaal_data.push(materiaal_item);
            }


            line_data['arbeid_data'] = arbeid_data;
            line_data['materiaal_data'] = materiaal_data;
            line_datas.push(line_data);
        }

        chapterData.push({
            chapter_name: chapter_name,
            default_chapter_id: $(".chapters #chapter_" + chapter_id + " select[name='chapter_title']").val(),
            line_data: line_datas,
            chapter_factor : chapter_factor
        });
    }
    quoteData = {
        contact_id: contact_id,
        userid: userid,
        quoteid: quoteid,
        chapterData: chapterData,
        intro: intro,
        reference : reference,
        factor: factor,
        rate: rate,
        inkoop : inkoop,
        kosten : kosten,
        materiaal_pdf : materiaal_pdf,
        arbeid_pdf : arbeid_pdf,
        version : version
    };
    var files = $('#file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file', files[0]);
    }
    fd.append('cloned', $(".popup .cloned").val());
    var filename = $(".popup .btn-filelog .file-path").val();
    if(filename == undefined)
            filename = '';
    fd.append('file_path', filename);
    showLoading();

    $.ajax({
        type: "POST",
        url: "../php/leads/save_quote_file.php",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result) {
            var filename = $(".popup .btn-filelog .file-path").val();
            if(result['message'] == 'success')
                filename = result['file_name'];
            if(filename == undefined)
            filename = '';
            quoteData['file_path'] = filename;
            $.ajax({
                type: "POST",
                url: "../php/leads/save_quote.php",
                data: quoteData,
                dataType: "json",
                success: function(result) {
                    hideLoading();
                    if (result['message'] == 'Offerte opgeslagen.') {
                        //Alles ging goed
                        melding(result['message'], 'groen');
                        if (quoteid != '') {
                            updateQuotePopup(quoteid);
        
                        } else {
                            addNewQuoteRow(contact_id);
                        }
                        if (result['quote'].length > 0) {
                            var pdf_url = root + "pdf/" + result['quote'][0].pdf_file;
                            $("table.lead-table tr[contactrow=" + contact_id + "] td:nth-child(6)").html(result['quote'][0]['qdate'] + "<a class='q-pdf' href='" + pdf_url + "' target='_blank'><img class='pdf-icon' src='" + root + "images/pdf.svg'></a>");
                            $("#quoteinfo .quoteid").val(result['quote'][0].id);
        
                        } else {
                            $("table.lead-table tr[contactrow=" + contact_id + "] td:nth-child(6)").html("Nog te versturen");
                            $("#quoteinfo .quoteid").val("");
                        }
        
        
        
                    } else {
                        //Er ging iets mis
                        melding(result['message'], 'rood');
                    }
                    closeVeryLargeModalForQuote();
                },
                error: function(e1, e2, e3) {
                    hideLoading();
                }
            });
        }
    });

    

}


function addNewQuoteRow(contact_id) {
    $.ajax({
        type: "POST",
        url: "../php/leads/get_last_quote.php",
        data: {
            contact_id: contact_id
        },
        dataType: "json",
        success: function(result) {
            if (result) {
                var qdate = result[0].qdate;
                var qtime = result[0].qtime;
                var qid = result[0].id;
                var user = result[0].username;
                var pdf_url = root + "pdf/" + result[0].pdf_file;
                var reference = result[0].reference;
                if(reference == null)
                        reference = '';
                var quotelisthtml = "<div id='quote" + qid + "' class='log-container'><div class='log-wrapper'><div class='log-header'>" +
                    "<span class='q-date'>" + qdate + "</span>" +
                    "<a class='q-pdf'  href='" + pdf_url + "' target='_blank'><img class='pdf-icon' src = '" + root + "images/pdf.svg'/></a>" +
                    "<span class='q-time'>" + qtime + "</span>" +
                    "<span class='q-user'><i class='material-icons'>person</i> " + user + "</span>" +
                    "<span class='q-reference'>" + reference + "</span>" + 
                    "<div onclick='showQuoteInnerConfirm(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div>" +
                    "<div onclick='editQuote(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>edit</i></div>" +
                    "<div onclick='showQuoteEmailConfirm(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Email'><i class='material-icons'>email</i></div>" +
                    "<div onclick='cloneQuote(" + qid + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Clone'><i class='material-icons'>content_copy</i></div></div>" +
                    '<div id="qipo-' + qid + '" class="inner-popup-overlay"></div><div id="qipc-' + qid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeQuoteInnerConfirm(' + qid + ')">Annuleren</span><span class="button red" onclick="deleteQuoteConfirm(' + qid + ')">Verwijderen</span></div></div>' +
                    '<div id="qepo-' + qid + '" class="inner-popup-overlay"></div><div id="qepc-' + qid + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeQuoteEmailConfirm(' + qid + ')">Annuleer</span><span class="button green" onclick="sendQuoteEmail(' + qid + ')">Verstuur email</span></div></div>' + 
                    '<div id="qcpo-' + qid + '" class="inner-popup-overlay"></div><div id="qcpc-' + qid + '" class="popup inner-confirm"><div class="buttons"><select id="qcps-' + qid + '"><option value=' + contact_id + '>Dit project</option>' + getSelectHtmlFromContactList(contact_id) + '</select><span class="button white" onclick="closeCloneQuoteConfirm(' + qid + ')">Annuleer</span><span class="button green" onclick="cloneQuoteConfirm(' + qid + ')">Kopieer</span></div></div>' +
                    "</div>";;
                $(".popup.large .logs").prepend(quotelisthtml);
            }

        }
    });
}


function editQuote(quote_id) {
    var contact_id = $("#quotelist .contactid").val();
    var userid = $("#quotelist .userid").val();
    closeModal();
    trggerFullscreen(2);
    prefillVeryLargeModal('Offerte toevoegen', 'addQuote.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_lead_log.php",
            data: {
                contactid: contact_id

            },
            dataType: "html",
            success: function(result) {
                if (result) {
                    $("#quoteinfo .contactid").val(contact_id);
                    $("#quoteinfo .userid").val(userid);
                    $("#quoteinfo .quoteid").val(quote_id);
                    
                    if (!Array.isArray(result)) result = $.parseJSON(result);
                    var title = '';
                    if (result.length > 0) {

                        let timer = result[0].timer;
                        let name = result[0].name;
                        let email = result[0].email;
                        let phone = result[0].phone;
                        title = name + ((timer) ? timer : '') + '<span class="city">' + email + " - " + phone + '</span>';

                    }
                    

                    title += '<input type="text" placeholder="Kenmerk offerte" class="txt-quote-reference" value="" />';
                    title += '<div class="btn-quote-fullscreen tooltipped" onclick="trggerFullscreen()"  class="actiebutton tooltipped" data-position="top" data-tooltip="Fullscreen"><i class="material-icons">fullscreen</i></div>';
                    title += '<div class="open-intro-button tooltipped" onclick="openIntro()"  class="actiebutton tooltipped" data-position="top" data-tooltip="Introductie tekst"><i class="material-icons">subject</i></div>';
                    title += '<div class="save-button"><span class="button waves-effect waves-light btn" onclick="saveQuote()">Offerte opslaan</span></div>';
                    $(".popup.very-large .title").html(title);
                    $(".popup.very-large .popupheader .btn-modal-close").attr('onclick', 'closeVeryLargeModalForQuote()');
                    


                    $("#quoteinfo .chapters").empty();
                    

                    $.ajax({
                        type: "POST",
                        url: "../php/leads/get_quote_details.php",
                        data: {
                            quote_id: quote_id
                        },
                        dataType: "html",
                        success: function(result) {
                            if (!Array.isArray(result)) result = $.parseJSON(result);
                            var intro = result['intro'];
                            $(".txt-quote-reference").val(result['reference']);
                            var factor = result['factor'];
                            var rate = result['rate'];
                            var inkoop = result['inkoop'];
                            var kosten = result['kosten'];
                            var materiaal_pdf = result['materiaal_pdf'];
                            var arbeid_pdf = result['arbeid_pdf'];
                            var version = result['version'];
                            if(factor != '')
                            {
                                factor = getRealNumber(factor);
                            }
                            else factor = 0;
                            factor = addCommas(factor * 100);
                            
                            var file_path = result['file_path'];
                            if(file_path != null && file_path != '')
                            {
                                $(".popup .offerte_header_file_container img").attr('src', root + "upload/" + file_path);
                                $(".popup .offerte_header_file_container img").attr('onclick', 'openPrev("' + file_path + '")');
                                $(".popup .offerte_header_file_container img").show();
                                $(".popup .offerte_header_file_container .btn-filelog").addClass("file_selected");
                                $(".popup .btn-filelog .file-path").val(file_path);
                            }
                            
                            result = result['result'];
                            $("#intro-text").html(intro);
                            
                            $("#offerte_factor").val(factor);
                            $("#offerte_rate").val(rate);
                            $("#offerte_inkoop").val(inkoop);
                            $("#offerte_kosten").val(kosten);

                            $("#offerte_arbeid_pdf").prop('checked', arbeid_pdf);
                            $("#offerte_materiaal_pdf").prop('checked', materiaal_pdf);

                            $.ajax({
                                type: "POST",
                                url: "../php/settings/get_default_offerte_details.php",
                                data : {
                                    version : version
                                },
                                dataType: "json",
                                success: function(detailed_result) {
                                    tags = detailed_result['tags'];
                                    defaultChapterList = detailed_result['chapter_list'];

                                    for (var index = 0; index < result.length; index++) {
                                        buildChapter(result[index], true);
                                    }
                                    initEditor();
                                    calculate();
                                    $('.chapter-line-vat').formSelect();
                                    $('.tooltipped').tooltip();
                                    showPrefilledVeryLargeModal('max');
                                },
                                error(e1, e2, e3) {
                                    
                                }
                            });
                            
                            
                        },
                        error: function(e1, e2, e3)
                        {
                           
                        }

                    });


                }
               
            }
        });

    });
}





function cloneQuote(qID){
    $("#qcpc-" + qID).show();
    $("#qcpo-" + qID).show();
    $("#qcpc-" + qID).addClass('visible');
    $("#qcpo-" + qID).addClass('visible');
    $("#qcps-" + qID).formSelect();
}

function closeCloneQuoteConfirm(qID) {
    $("#qcpc-" + qID).removeClass('visible');
    $("#qcpo-" + qID).removeClass('visible');
    setTimeout(function() {
        $("#qcpo-" + qID).hide();
        $("#qcpc-" + qID).hide();
    }, 300);
}

function cloneQuoteConfirm(quoteid)
{
    var contact_id = $("#qcps-" + quoteid).val();
    var userid = $("#quotelist .userid").val();
    showLoading();
    $.ajax({
        type: "POST",
        url: "../php/leads/clone_quote.php",
        data: {
            contact_id: contact_id,
            userid : userid,
            quoteid : quoteid
        },
        dataType: "json",
        success: function(result) {
            hideLoading();
            melding(result['message'], 'groen');
            closeCloneQuoteConfirm(quoteid);
            var org_contact_id = $("#quotelist .contactid").val();
            
            
            if (result['quote'].length > 0) {
                if(org_contact_id == contact_id)
                {
                    addNewQuoteRow(contact_id);
                    $(".popup #quote" + result['quote'][0].id).hide();
                    $(".popup #quote" + result['quote'][0].id).slideDown();
                }

                var pdf_url = root + "pdf/" + result['quote'][0].pdf_file;
                $("table.lead-table tr[contactrow=" + contact_id + "] td:nth-child(6)").html(result['quote'][0]['qdate'] + "<a class='q-pdf' href='" + pdf_url + "' target='_blank'><img class='pdf-icon' src='" + root + "images/pdf.svg'></a>");
                $("#quoteinfo .quoteid").val(result['quote'][0].id);

            } else {
                $("table.lead-table tr[contactrow=" + contact_id + "] td:nth-child(6)").html("Nog te versturen");
                $("#quoteinfo .quoteid").val("");
            }

        },
        error: function(e1, e2, e3) {
            alert();
            debugger;
        }
    });
}


function showQuoteInnerConfirm(qID) {
    $("#qipc-" + qID).show();
    $("#qipo-" + qID).show();
    $("#qipc-" + qID).addClass('visible');
    $("#qipo-" + qID).addClass('visible');
}

function closeQuoteInnerConfirm(qID) {
    $("#qipc-" + qID).removeClass('visible');
    $("#qipo-" + qID).removeClass('visible');
    setTimeout(function() {
        $("#qipo-" + qID).hide();
        $("#qipc-" + qID).hide();
    }, 300);
}


function showQuoteEmailConfirm(qID) {
    $("#qepc-" + qID).show();
    $("#qepo-" + qID).show();
    $("#qepc-" + qID).addClass('visible');
    $("#qepo-" + qID).addClass('visible');
}

function closeQuoteEmailConfirm(qID) {
    $("#qepc-" + qID).removeClass('visible');
    $("#qepo-" + qID).removeClass('visible');
    setTimeout(function() {
        $("#qepo-" + qID).hide();
        $("#qepc-" + qID).hide();
    }, 300);
}


function sendQuoteEmail(qID) {
    closeQuoteEmailConfirm(qID);

    $.ajax({
        type: "POST",
        url: "../php/leads/send_email.php",
        data: {
            'qID': qID
        },
        dataType: "html",
        success: function(result) {
            if (result == 'E-mail verstuurd naar de klant.') {
                //Alles ging goed
                melding(result, 'groen');
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });


}

function updateQuotePopup(quote_id) {
    $.ajax({
        type: "POST",
        url: "../php/leads/get_quote.php",
        data: {
            quote_id: quote_id
        },
        dataType: "json",
        success: function(result) {
            if (result) {
                $("#quote" + quote_id + " .q-date").html(result[0].qdate);
                $("#quote" + quote_id + " .q-time").html(result[0].qtime);
                $("#quote" + quote_id + " .q-user").html("<i class='material-icons'>person</i>" + result[0].username);
                $("#quote" + quote_id + " .q-pdf").attr('href', root + "pdf/" + result[0].pdf_file);
                var ref = result[0].reference;
                if(ref == null)
                ref = '';
                $("#quote" + quote_id + " .q-reference").html(ref);

            }
        }
    });
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

function editLine(id, line_id)
{
    var container = "#line_" + line_id;
    if($(container).hasClass('line-edit-mode'))
    {
        $(container).removeClass('line-edit-mode');
        $(container + " .input-chapter-line-header").remove();
        $(container + " .select-chapter-line-unit").remove();
        $(container + " select.chapter-line-header option[original_item = 'true']").remove();
        // changedLine(id, line_id);
    }
    else{
        $(container).addClass('line-edit-mode');
        $(container + " .chapter-line-header").after("<input type='text' class='input-chapter-line-header' oninput='changedLineHeader(\"" + id + "\", \"" + line_id + "\")'/>");
        $(container + " .input-chapter-line-header").val($(container + " .chapter-line-header option:selected").text());
        var selected_item = $(container + " .chapter-line-header option:selected");
        var optionHtml = "<option original_item='true' line_am_option='nee' selected>" + selected_item.text() + "</option>";


        var unit_selecthtml = "<select class='select-chapter-line-unit browser-default' onchange='changedLineUnit(\"" + id + "\", \"" + line_id + "\")'><option value='stuk'>stuk</option><option value='m1'>m1</option><option value='m2'>m2</option><option value='m3'>m3</option></select>";

        //remove origin
        $(container + " select.chapter-line-header option[original_item = 'true']").remove();
        //add origin
        $(container + " select.chapter-line-header").prepend(optionHtml);
        $(container + " .chapter-line-unit").after(unit_selecthtml);

        $(container + " .select-chapter-line-unit").val( $(container + " .chapter-line-unit").text());
        $(container + " .select-chapter-line-unit").formSelect();
    }
}

function changedLineHeader(id, line_id)
{
    var container = "#line_" + line_id;
    var text = $(container + " .input-chapter-line-header").val();
    $(container + " select.chapter-line-header option:selected").text(text);
    setOfferteLabel(line_id);
}

function changedLineUnit(id, line_id)
{
    var container = "#line_" + line_id;
    var text = $(container + " .select-chapter-line-unit").val();
    $(container + " .chapter-line-unit").text(text);
    setOfferteLabel(line_id);
}


function editLineTag(line_id)
{
	var container = "#quoteinfo #line_" + line_id + " ";
	if($(container).hasClass('tag-highlighted'))
	{
		$(container).removeClass('tag-highlighted')
	}
	else {
		$(container).addClass('tag-highlighted')
	}
}

function getSelectHtmlFromContactList(contact_id)
{
    var html = "";
    for(var index = 0; index < contacts_list.length; index ++)
    {
        if(contacts_list[index]['id'] != contact_id)
         html += "<option value='" + contacts_list[index]['id'] + "'>" + contacts_list[index]['address'] + "</option>";
    }
    return html;
}

function uploadLine(chapter_id, line_id)
{
    var default_chapter_id = $(".popup #chapter_" + chapter_id + " select[name='chapter_title']").val();


    var line_total = '';

            var line_am_option = $("#line_" + line_id  + " .chapter-line-am-option").val();


            var arbeid = 0;
            var materiaal_total = 0;
            if(line_am_option == 'ja')
            {
                arbeid = $("#line_" + line_id + " .chapter-line-arbeid").val();
                materiaal_total = $("#line_" + line_id + " .label-materiaal-total").text();
            }
            else{
                line_total = $("#line_" + line_id + " .chapter-line-arbeid-price").val();
            }

            if(line_total == undefined)
                line_total = '';
            if(arbeid == undefined)
                arbeid = '';
            if(materiaal_total == undefined)
                materiaal_total = '';


            var line_edit_mode = 0;
            
            if($("#line_" + line_id).hasClass('line-edit-mode'))
                line_edit_mode = 1;

            var line_data = {
                default_chapter_id : default_chapter_id,
                line_title: $("#line_" + line_id + " .chapter-line-header option:selected").text(),
                line_edit_mode : line_edit_mode,
                line_descr: $("#line_" + line_id + " .chapter-line-description").val(),
                quanitty: $("#line_" + line_id + " .chapter-line-quantity").val(),
                unit: $("#line_" + line_id + " .chapter-line-unit").text(),
                price: $("#line_" + line_id + " .chapter-line-price").text(),
                price_inc: $("#line_" + line_id + " .chapter-line-price-inc").text(),
                vat: $("#line_" + line_id + " .chapter-line-vat").val(),
                subtotal: $("#line_" + line_id + " .chapter-line-subtotal").text(),
                line_am_option : line_am_option,
                line_total : line_total,
                arbeid : arbeid,
                materiaal_total : materiaal_total,
                line_profit : $("#line_" + line_id + " .chapter-line-profit").val(),
                standard_tag_id : $("#line_" + line_id + " .chapter-line-tag-standard").val(),
				fase_tag_id : $("#line_" + line_id + " .chapter-line-tag-fase").val(),
            };
            var arbeid_data = [];
            var materiaal_data = [];
            for(var kdex = 0; kdex < $("#line_" + line_id + " .am_content .arbeid-content .arbeid-item").length; kdex ++)
            {
                var arbeid_content_div = $("#line_" + line_id + " .am_content .arbeid-content .arbeid-item").eq(kdex);
                var arbeid_item = {
                    arbeid_title : 
                    arbeid_content_div.find('.arbeid_title').val(),
                    quantity : arbeid_content_div.find('.arbeid_quantity').val(),
                    sort_order : kdex
                };
                arbeid_data.push(arbeid_item);
            }

            for(kdex = 0; kdex < $("#line_" + line_id + " .am_content .materiaal-content .material-item").length; kdex ++)
            {
                var materiaal_content_div = $("#line_" + line_id + " .am_content .materiaal-content .material-item").eq(kdex);
                var materiaal_item = {
                    materiaal_title : 
                    materiaal_content_div.find('.materiaal_title').val(),
                    stuks : materiaal_content_div.find('.materiaal_stuks').val(),
                    price : materiaal_content_div.find('.materiaal_price').val(),
                    sort_order : kdex
                };
                materiaal_data.push(materiaal_item);
            }


            line_data['arbeid_data'] = arbeid_data;
            line_data['materiaal_data'] = materiaal_data;
    showLoading();
    $.ajax({
        type: "POST",
        url: "../php/leads/upload_quote_line.php",
        data: line_data,
        dataType: "json",
        success: function(result) {
            melding(result['message'], 'groen');
            hideLoading();
        },
        error: function(e1, e2, e3) {
            hideLoading()
        }
    });
    
}