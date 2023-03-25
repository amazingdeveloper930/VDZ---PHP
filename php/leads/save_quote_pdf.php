<?php



error_reporting(E_ALL);

ini_set('display_errors', 1);
require( '../../common/connection.php');


ob_start();
require_once '../../vendor/autoload.php';
// include("../../vendor/MPDF/mpdf.php");



// $mpdf=new Mpdf('win-1252','A4','','',15,15,30,25,10,10);
$mpdf = new \Mpdf\Mpdf();

// $mpdf->useOnlyCoreFonts = true;    // false is default

//$mpdf->SetProtection(array('print'));

//$mpdf->allow_charset_conversion=true;  // Set by default to TRUE
//$mpdf->charset_in='windows-1252';

$mpdf->SetTitle("Vanderzeeuwbouw.nl - Quote");

//$mpdf->SetAuthor("Acme Trading Co.");
// $mpdf->setFooter('Page {PAGENO}');
$mpdf->AddPageByArray([
    'margin-left' => '15mm',
    'margin-right' => '15mm',
    'margin-top' => '10mm',
    'margin-bottom' => '30mm',
    'resetpagenum' => '1'
]);

$mpdf->SetDisplayMode('fullpage');



$name2 = 'id4735_infdb';

$login2 = 'id4735_infdb';

$pass2 = 'qROSf8L3L';

$hostname = "localhost";


//$QUOTE_ID = 46;
//connect to database

//$mysqli = new mysqli($hostname, $login2, $pass2, $name2);

$intro = '';
$voor = '';
$stmt = $con -> prepare("SELECT meta, text FROM quote_text_default");
$stmt->execute();
    // Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if($row['meta'] == 'QUOTE_INTRO')
        $intro = $row['text'];
    if($row['meta'] == 'QUOTE_VOOR')
        $voor = $row['text'];
}


$imagecss = "";


$stmt = $con -> prepare("SELECT quotes.intro, quotes.quote_date, quotes.reference, quotes.arbeid_pdf, quotes.materiaal_pdf, contacts.address, contacts.name AS contact_name, quotes.file_path, contacts.email, contacts.phone,  contacts.city FROM quotes LEFT JOIN contacts ON quotes.contact_id = contacts.id WHERE quotes.id = ?");
$stmt->bind_param('s', $QUOTE_ID);
$stmt->execute();
    // Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

$date = '';
$address = '';
$address_2 = '';
$reference_text = '';
$reference_text_1 = '';
$arbeid_pdf = 0;
$materiaal_pdf = 0;
$file_path = '';
while ($row = $result->fetch_assoc()) {
    if($row['intro'] != null)
        $intro = $row['intro'];
    $date = $row['quote_date'];
    $address = $row['address'] . ",<br/>" . $row['city'];
    $address_2 = $row['address'];
    $email = $row['email'];
    $phone = $row['phone'];
    $contact_name = $row['contact_name'];
    $arbeid_pdf = $row['arbeid_pdf'];
    $materiaal_pdf = $row['materiaal_pdf'];
    if($row['reference'] != null && $row['reference']!='')
        {
            $reference_text = "<strong>Kenmerk: </strong>" . $row['reference'];
            $reference_text_1 = "<strong>Kenmerk &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong> " . $row['reference'];
        }
    if($row['file_path'] != '' && $row['file_path'] != null)
        $file_path = $row['file_path'];
}

$date=date_create($date);
$date_text = date_format($date,"d-m-Y"); 
$date= date_format($date,"d F Y");

if($file_path != "")
    $imagecss = "
    .image_file{
        background-image: url('" . __DIR__ . "/../../upload/" . $file_path . "');
        width: 383px;
        height: 263px;
        background-repeat: no-repeat;
        background-size: cover;
        transform: rotate(90deg);
            /* All browsers support */
            -moz-transform: rotate(90deg);
            -webkit-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
        transform-origin: 50%;

        position: absolute;
        left: 379px;
        top: 334px;
        z-index: 10;

    }
    .border_img{

        background-image: url('" . __DIR__ . "/../../images/border_img.png');
        width: 410px;
        height: 283px;
        // background-color:#ff000030;
        background-repeat: no-repeat;
        background-size: cover;
        position: absolute;
        left: 359px;
        top: 319px;
        z-index: 20;
        
    }
    
    "; 



$result = null;
if ($stmt = $con->prepare('SELECT id as chapter_id, chapter_name FROM quote_chapters WHERE quote_id = ? ORDER BY sort_order ASC')) {

    $stmt->bind_param('s', $QUOTE_ID);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
}
    
$result_array_pdf = [];
$pdf_file = '';

while ($row = $result->fetch_assoc()) {

    if($stmt1 = $con -> prepare('SELECT Q.*, T1.name AS s_tag_name, T2.name AS f_tag_name FROM quote_lines Q 
    LEFT JOIN (SELECT * FROM tags WHERE type = "STANDARD") T1 ON Q.standard_tag_id = T1.id  
    LEFT JOIN (SELECT * FROM tags WHERE type = "FASE") T2 ON Q.fase_tag_id = T2.id
    WHERE Q.quote_id = ? AND Q.chapter_id = ? 
     ORDER BY Q.sort_order ASC'))
    {
    $stmt1->bind_param('ss', $QUOTE_ID, $row['chapter_id']);
    $stmt1->execute();
    // Store the result so we can check if the account exists in the database.
    $result1 = $stmt1->get_result();
    }
    $line_data = [];
    while ($row1 = $result1->fetch_assoc()) {
        $line_data []= $row1;
    }
    $row += ['line_data' => $line_data];
    $result_array_pdf[] = $row;
}

if ($stmt = $con->prepare('SELECT pdf_file FROM quotes WHERE id = ? ORDER BY id ASC')) {

    $stmt->bind_param('s', $QUOTE_ID);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();

    $pdf_file = '';
    while ($row = $result->fetch_assoc()) {
        $pdf_file = $row['pdf_file'];
    }



    if(isset($pdf_file) && $pdf_file != '' && file_exists('../../pdf/' . $pdf_file))
    {
        unlink('../../pdf/' . $pdf_file);
    }

    
}



$table_data = '';
$total = 0;
$vat_9 = 0;
$vat_21 = 0;

$chapternumber = 0;

foreach($result_array_pdf as $chapter_value)
{	
	$chapternumber++;
    $table_data .= "<tr class='tr-chapter-header'><td><strong>" . $chapter_value['chapter_name'] . "</strong></td><td>Aantal</td><td>Eenheid</td><td>Prijs</td><td>Subtotaal</td><td style='width : 70px;'>BTW</td></tr>";
    $subtotal = 0;
    $subtotal_exVat = 0;
    $flag = false;
	$linenumber = 0;	
	
	//if($chapternumber == '32') { /*$linenumber++; if($linenumber == '5'){ break; }*/ continue; }
	
    for($jdex = 0; $jdex < count($chapter_value['line_data']); $jdex ++)
    {		
        $line_data = $chapter_value['line_data'][$jdex];		
		
        $line_data['subtotal'] = str_replace(".", "", $line_data['subtotal']);
        $line_data['subtotal'] = str_replace("&euro;", "", $line_data['subtotal']);
        $temp_price = $line_data['price'];
        $temp_price = str_replace(".", "", $temp_price);
        $temp_price = str_replace(",", ".", $temp_price);
        $subtotal_exVat_item = floatval($temp_price) * floatval(str_replace(",", ".", $line_data['quanitty']));
        $subtotal_exVat += $subtotal_exVat_item;


        $table_data .= "<tr><td>" . $line_data['line_title'] . '</td><td style="text-align:center;">' . $line_data['quanitty'] . '</td><td style="text-align:center;">' . $line_data['unit'] . '</td><td><p class="one-line-text">&euro; ' . $line_data['price'] . '</p></td><td><p class="one-line-text">&euro; ' . number_format($subtotal_exVat_item, 2, ',', '.') . '</p></td><td style="text-align:right; width : 70px; ">Hoog<br/>' . ($line_data['vat'] * 100) . '%</td><tr>';		
		
        $table_data .= "<tr><td>" . nl2br($line_data['line_descr']) . "</td><td></td><td></td><td></td><td></td><td></td></tr>";		
       
        

        if($line_data['vat'] == 0.09)
            $vat_9 += floatval(str_replace(",", ".", $line_data['subtotal'])) * 0.09 / 1.09;
        if($line_data['vat'] == 0.21)
            $vat_21 += floatval(str_replace(",", ".", $line_data['subtotal'])) * 0.21 / 1.21;
        if(!($line_data['subtotal'] == null || $line_data['subtotal'] == ''))
           {
            $subtotal += floatval(str_replace(",", ".", $line_data['subtotal']));
            
           } 
        // var_dump($line_data['subtotal']);
        if($materiaal_pdf && ($line_data['line_am_option'] == 'ja'))
        {
            $text = '<strong>Materiaal : </strong>';
            $stmt_2 = $con -> prepare('SELECT * FROM quote_materiaals WHERE line_id = ?');
            $stmt_2 -> bind_param('i', $line_data['id']);
            $stmt_2 -> execute();
            $m_result = $stmt_2 -> get_result();
            $temp_text = [];
            while ($m_row = $m_result->fetch_assoc()) {
                $temp_text []= $m_row['materiaal_title'];
            }
            $text .= join($temp_text, ', ');
            $table_data .= "<tr class='am_row'><td>";
            $table_data .= $text;
            $table_data .= "</td><td></td><td></td><td></td><td></td><td></td></tr>";
        }

        if($arbeid_pdf && ($line_data['line_am_option'] == 'ja'))
        {
            $text = '<strong>Arbeid : </strong>';
            $stmt_2 = $con -> prepare('SELECT * FROM quote_arbeids WHERE line_id = ?');
            $stmt_2 -> bind_param('i', $line_data['id']);
            $stmt_2 -> execute();
            $m_result = $stmt_2 -> get_result();
            $temp_text = [];
            while ($m_row = $m_result->fetch_assoc()) {
                $temp_text []= $m_row['arbeid_title'];
            }
            $text .= join($temp_text, ', ');
            $table_data .= "<tr class='am_row'><td>";
            $table_data .= $text;
            $table_data .= "</td><td></td><td></td><td></td><td></td><td></td></tr>";
        }

        if(isset($line_data['s_tag_name']))
        {
            $text = '<strong>Locatie : </strong>';
            $text .= $line_data['s_tag_name'];
            $table_data .= "<tr class='am_row'><td>";
            $table_data .= $text;
            $table_data .= "</td><td></td><td></td><td></td><td></td><td></td></tr>";
        }

        if(isset($line_data['f_tag_name']))
        {
            $text = '<strong>Fase : </strong>';
            $text .= $line_data['f_tag_name'];
            $table_data .= "<tr class='am_row'><td>";
            $table_data .= $text;
            $table_data .= "</td><td></td><td></td><td></td><td></td><td></td></tr>";
        }

    }
    

    $total += $subtotal;
    $table_data .= "<tr><td></td><td></td><td></td><td><strong>Subtotaal</strong></td><td><p class='one-line-text'><strong>&euro; " . number_format($subtotal_exVat, 2, ',', '.') . "</strong></p></td><td></td></tr>";
    $table_data .= "<tr><td></td><td></td><td></td><td><strong>Subtotaal incl.BTW<br/>&nbsp;</strong></td><td><p class='one-line-text'><strong>&euro; " . number_format($subtotal, 2, ',', '.') . "<br/>&nbsp;</strong></p></td><td></td></tr>";
}
if($total === '---')
{
    $vat_9 = 0;
    $vat_21 = 0;
}

$total_btw = '---';
if($total !== '---')
    $total_btw = $total - $vat_9 - $vat_21;


$table_data .= "<tr><td></td><td></td><td></td><td>Totaal excl. BTW</td><td><p class='one-line-text'>&euro; " . number_format($total_btw, 2, ',', '.') . "</p></td><td></td></tr>";

if($vat_9 != 0)
    $table_data .= "<tr><td></td><td></td><td></td><td>BTW Hoog &nbsp;9%</td><td><p class='one-line-text'>&euro; " . number_format($vat_9, 2, ',', '.')  . "</p></td><td></td></tr>";

if($vat_21 != 0)
    $table_data .= "<tr><td></td><td></td><td></td><td>BTW Hoog 21%</td><td><p class='one-line-text'>&euro; " . number_format($vat_21, 2, ',', '.') . "</p></td><td></td></tr>";


$table_data .= "<tr><td></td><td></td><td></td><td><strong>Totaal</strong></td><td><p class='one-line-text'><strong>&euro; " . number_format($total, 2, ',', '.') . "</strong></p></td><td></td></tr>";




















$html = '<html><head></head>
<style>
body{

    background-image: url("'.__DIR__.'/../../images/start_cover.jpg");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: top left;
    background-image-resize: 4;
    background-image-resolution: from-image;
}

#address-text{
    font-family: Asap;
    color: #42A7D5;
    font-size: 20px;
    margin-left: 5mm;
    padding-top: 43mm;

}
#name-text{
    font-family: Asap;
    color: #1D3050;
    font-size: 18px;
    margin-left: 5mm;
    font-weight: 900;
    line-height: 2px;
}
#email-text, #phone-text, #id-text, #date-text, #reference-text{
    font-family: Asap;
    color: #1D3050;
    margin-left: 5mm;
    line-height: 2px;
    font-size: 12px;
}
#id-text{
    padding-top: 15px;
}

.am_row td{
    padding-left: 10mm;
}
.am_row m_title, .am_row a_title, .am_row t_title{
    font-weight: bold;
}

.tr-chapter-header{
    background-color : red;
    color: white;
    
    background-color : #002c5c;
    
}

.tr-chapter-header td { 
    color : white;
    font-weight : bold;
    padding-top : 12px;
    padding-bottom : 12px;
}

p.one-line-text {

    white-space:pre;
    overflow:hidden;
}


' . $imagecss .'
</style>
<body>
<p id="address-text">' . $address . '</p>
<p id="name-text"><b>' . $contact_name . '</b></p>
<p id="email-text">' . $email . '</p>
<p id="phone-text">' . $phone . '</p>
<p id="id-text">Offertenummer ' . $QUOTE_ID . '</p>
<p id="date-text">' . $date_text . '</p>
<p id="reference-text">' . $reference_text . '</p>
<div class="image_file"></div>
<div class="border_img"></div>
<htmlpagefooter name="pagefooter" style="display:none">
    <div class="fullwidth">

        
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />

</body>
</html>';

$mpdf->WriteHTML($html);

$mpdf->AddPageByArray([
    'margin-left' => '15mm',
    'margin-right' => '15mm',
    'margin-top' => '10mm',
    'margin-bottom' => '45mm',
    'resetpagenum' => '1'
]);

$html = '<html>

<head>

<style>

@page {	

    margin-top: 1cm;

	font-size: 9px !important;

	// background-color:#ffffff;

}



@page :first {	

    margin-top: 1cm;	

    font-size: 9px !important;
    // background: url("../../images/Untitled-1.jpg");
	// // background-color:#ffffff;
    // background-repeat: no-repeat;
    // background-size: cover;


}
body{
    font-family: asap;
    font-size: 9pt;
    
}
h1 {

	font-family:asap;

	font-weight:normal;

	margin-bottom:-5px;

	font-size:25px;

	color:#2c3255;

	line-height:25px;

}

h3 {

	font-family:asap;

	line-height:20px;

}

p.underline{
    text-decoration: underline;
}
a{
    color:black;
}

.quote_table{
    font-family: Asap;
}
.quote_table tbody td{
    padding: 0.5em 5px 0.5em;
}

.quote_table tr.table-header td{
    padding: 5px 10px;
    font-weight: bold;
}

.text-right{
    text-align:right;
}
.text-gray {
    color: #a5a5a5;
}

.paragraph{
	margin-bottom: 2rem;
}

#main-title{
	font-size: 3.5rem;
    font-weight: bold;
    margin-bottom: 0px;
}

.logo-image{
    text-align: right;
    width: 35%;
    float: right;
    display: block;
    margin-top: -2.5rem;
    margin-right: 1rem;

}
.half-width{
    width: 50%;
    display: inline-block;
    float: left;
}
.sign-box{
    margin-top: 5rem;
}

.underline-box {
    width: 100%;
    background: black;
    height: 1px;
    margin-bottom: 0.5rem;
}
p{
    // margin-bottom: 0.5rem;
    margin: 0px;
}
p.margin-bottom-1{
    margin-bottom: 0.5rem;
}
.width-25{
    width: 25px;
    display: inline-block;
}

.width-70{
    width: 70px;
    display: inline-block;
}
p{
    // line-height: 
}
.line-height-small{
    margin-top: 0px;
    margin-bottom: 2px;
}

body{

    background-image: url("' . __DIR__ . '/../../images/quote_back.jpg");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: top left;
    background-image-resize: 4;
    background-image-resolution: from-image;
}
.text-mark{
    position: absolute;
    right: 70px;
    font-size: 50px;
    top: 30px;
    font-family: Asap;
    color: #0A2A53;
}
</style>
<body>

<htmlpagefooter name="pagefooter" style="display:none">
    <div class="fullwidth">

        <div class="col-12" style="padding: 0px 5mm 20mm;">
             <p style="float:right; text-align:right; " class="footer-text">Page {PAGENO} / {nbpg}<br/>&nbsp;</p>
        </div>
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />
<p class="text-mark"><b>OFFERTE</b></p>
<div class="paragraph" style="border-style: dashed;border-color:#24A9E2; border-width: 2px; padding: 2rem; width: 40%;">
<p id="line-height-small" style="font-size: 2em; color: #1C2F50"><b>' . $contact_name . '</b></p>
<p id="line-height-small" style="margin-top: 1rem;">' . $email . '</p>
<p id="line-height-small">' . $phone . '</p>
<p id="line-height-small" style="margin-top: 2rem;">Offertenummer ' . $QUOTE_ID . '</p>
<p id="line-height-small">' . $date_text . '</p>
<p id="line-height-small">' . $reference_text . '</p>
</div>
<div class="photoimage"></div>
<div class="paragraph">
    <p>Geachte heer/mevrouw,</p>
</div>' . $intro . '
<div style="width: 100%;">
    <div class="half-width">
        <p class="margin-bottom-1">Met vriendelijke groet,</p><br/>
        <p class="margin-bottom-1">Verkoopteam Van der Zeeuw Bouw</p>
    </div>
    <div class="half-width">
        <p class="margin-bottom-1">voor akkoord,</p>
        <div class="sign-box">
        <p class="margin-bottom-1">Datum:</p>
        <div class="underline-box"></div>
        <p class="margin-bottom-1">Plaats:</p>
        <div class="underline-box"></div>
        <p class="margin-bottom-1">Handtekening:</p>
        </div>
    </div>
</div>
<pagebreak />
<h2 style="margin: 0px; text-decoration: underline;">Omschrijving werkzaamheden</h2>
<br/>
<table style="width:100%;margin: 0;padding: 0;border-spacing:0" class="quote_table">

    <tbody>
        <tr class="table-header">
            <td style="text-align: left; width: 45%"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    ' .
        $table_data .
    '</tbody>
</table>
<pagebreak />'
.'<h2 style="margin: 0px; text-decoration: underline;">Voorwaarden</h2> <br/>'
.$voor.
'</body>
</html>';





//$stylesheet = file_get_contents('stijlvoorpdf.css'); // external css

//$mpdf->WriteHTML($stylesheet,1);

$mpdf->WriteHTML($html);




function generateRandomString($length = 10, $hasNumber = false, $hasLowercase = true, $hasUppercase = false): string
{
    $string = '';
    if ($hasNumber)
        $string .= '0123456789';
    if ($hasLowercase)
        $string .= 'abcdefghijklmnopqrstuvwxyz';
    if ($hasUppercase)
        $string .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($x = $string, ceil($length / strlen($x)))), 1, $length);
}

// $file_name = generateRandomString(15).'.pdf';
$address_2 = str_replace("  ", " ", $address_2);
$address_2 = str_replace(" ", "-", $address_2);
$address_2 = str_replace("/", "-", $address_2);

$date = date_create();
$dt = $date->format("Y_m_d_H_i_s");
$file_name = $address_2 . "-" . $QUOTE_ID . "_" . generateRandomString(3) . ".pdf";
$mpdf->Output('../../pdf/'.$file_name,'F'); 

ob_end_clean();


if($stmt = $con ->prepare('UPDATE quotes SET pdf_file = ? WHERE id = ?')){

    $stmt->bind_param('si', $file_name, $QUOTE_ID);
    $stmt->execute();
}





function mergePDFFiles(Array $filenames, $outFile) {

    $mpdf = new mPDF();

    if ($filenames) {

        //  print_r($filenames); die;

        $filesTotal = sizeof($filenames);

        $fileNumber = 1;

        $mpdf->SetImportUse();

        if (!file_exists($outFile)) {

            $handle = fopen($outFile, 'w');

            fclose($handle);

        }

        foreach ($filenames as $fileName) {

            if (file_exists($fileName)) {

                $pagesInFile = $mpdf->SetSourceFile($fileName);

                //print_r($fileName); die;

                for ($i = 1; $i <= $pagesInFile; $i++) {

                    $tplId = $mpdf->ImportPage($i);

                    $mpdf->UseTemplate($tplId);

                    if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {

                        $mpdf->WriteHTML('<pagebreak />');

                    }

                }

            }

            $fileNumber++;

        }

        $mpdf->Output($outFile, 'F');

    }

}





// $bestanden = array('voor_pdf.pdf','GroterWonen_'.$randomstring.'.pdf','na_pdf.pdf');

// mergePDFFiles($bestanden,'GroterWonen_'.$randomstring.'_compleet.pdf');








?>
