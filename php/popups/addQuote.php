<?php 
    require '../../common/sessie_check.php';
	require '../../common/global.php';

?>
	<form id="quoteinfo" class="col s12">
		<input type="hidden" name="contactid" class="contactid" value="">
        <input type="hidden" name="userid" class="userid" value="">
        <input type="hidden" name="quoteid" class="quoteid" value="">
        <input type="hidden" name="cloned" class="cloned" value="0">
        <div id="quote-toolbar">
            <div class="offerte_toolbar_box">
                <div class="offerte_header_container">
                    <label>Winst & risico factor %</label>
                    <input type="text" onchange="convertNumber(this, 2)" oninput = "calculate(true)" id="offerte_factor"/>
                </div>
                <div class="offerte_header_container">
                    <label>Algemene kosten %</label>
                    <input type="text" onchange="convertNumber(this, 2)" oninput = "calculate()" id="offerte_kosten"/>
                </div>
                <div class="offerte_header_container">
                    <label>Arbeid kosten</label>
                    <input type="text" onchange="convertNumber(this)" oninput = "calculate(true)" id="offerte_rate"/>
                </div>
                <div class="offerte_header_container">
                    <label>Arbeid inkoop</label>
                    <input type="text" onchange="convertNumber(this)" oninput = "calculate(true)" id="offerte_inkoop"/>
                </div>
                <div class="offerte_checkbox">
                    <span>Toon arbeid in PDF</span>
                    <label>
                        <input type="checkbox" class="filled-in" id="offerte_arbeid_pdf">
                        <span>&nbsp;</span>                       
                    </label>
                </div>
                
                <div class="offerte_checkbox">
                    <span>Toon materiaal in PDF</span>
                    <label>
                        <input type="checkbox" class="filled-in" id="offerte_materiaal_pdf">
                        <span>&nbsp;</span>                        
                    </label>
                </div>
                <div class="offerte_header_container offerte_header_file_container">
                    <label>Foto op cover</label>
                    <div class="file-field input-field col s1 btn-filelog">
                        <div class="file-icon">
                            <i class="material-icons">attach_file</i>
                            <input type="file" id="file" onchange="fileselected()">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" hidden>
                        </div>
                    </div>
                    <img onclick="">
                </div>
                
            
            </div>
        </div>
        <div id="intro-text-panel">
            <textarea id="intro-text" >

            </textarea>
        </div>
        
        <div class="chapters">
            
        </div>
        

	</form>

    <div class="calculating-box row">
            <div class="col s3">
                    <div class="footer-button"> <span class="button waves-effect waves-light" onclick="addChapter()"><i class="material-icons">add</i> Hoofdstuk toevoegen</span> </div>
            </div>
            <div class="col s9" style="padding-left: 10%">
            <div class="col s4">
                <div class="row">
                    <div class="col s6">
                        <span>Uren arbeid</span>
                    </div>
                    <div class="col s6">
                        <span class="uren_arbeid">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Verkoop arbeid</span>
                    </div>
                    <div class="col s6">
                        € <span class="verkoop_arbeid">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Inkoop arbeid</span>
                    </div>
                    <div class="col s6">
                        € <span class="inkoop_arbeid">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Inkoop materiaal</span>
                    </div>
                    <div class="col s6">
                        € <span class="inkoop_materiaal">---</span>
                    </div>
                </div>
            </div>
            <div class="col s4">
               
                <div class="row">
                    <div class="col s6">
                        <span>Marge op uren</span>
                    </div>
                    <div class="col s6">
                        € <span class="marge_uren">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Marge w&r</span>
                    </div>
                    <div class="col s6">
                        € <span class="marge_w">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Brutowinst</span>
                    </div>
                    <div class="col s6">
                        € <span class="totaal_marge">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Algemene kosten</span>
                    </div>
                    <div class="col s6">
                        € <span class="algemene_kosten">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <span>Netto winst</span>
                    </div>
                    <div class="col s6">
                        € <span class="netto_winst">---</span>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s8 text-right">
                        <span>Totaal excl. BTW </span>
                    </div>
                    <div class="col s4">
                        € <span class="total-excel">---</span>
                    </div>
                </div>
                <div class="row btw_row_1">
                    <div class="col s8 text-right">
                        <span>BTW Hoog <span class="vat-percent"></span></span>
                    </div>
                    <div class="col s4">
                        € <span class="vat-excel">---</span>
                    </div>
                </div>
                <div class="row btw_row_2" style="display: none;">
                    <div class="col s8 text-right">
                        <span>BTW Hoog <span class="vat-percent"></span></span>
                    </div>
                    <div class="col s4">
                        € <span class="vat-excel">---</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s8 text-right">
                        <span style="font-weight: bold">Totaal </span>
                    </div>
                    <div class="col s4">
                        € <span class="total">---</span>
                    </div>
                </div>
            </div>
            </div>
        </div>