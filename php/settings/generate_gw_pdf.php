<?php
// header('Content-Type: application/pdf');
error_reporting(E_ALL);

ini_set('display_errors', 1);
require( 'common/connection.php');


ob_start();
require_once 'vendor/autoload.php';
// include("../../vendor/MPDF/mpdf.php");



// $mpdf=new Mpdf('win-1252','A4','','',15,15,30,25,10,10);
$mpdf = new \Mpdf\Mpdf();

// $mpdf->useOnlyCoreFonts = true;    // false is default

//$mpdf->SetProtection(array('print'));

//$mpdf->allow_charset_conversion=true;  // Set by default to TRUE
//$mpdf->charset_in='windows-1252';

$mpdf->SetTitle("Vanderzeeuwbouw.nl");

//$mpdf->SetAuthor("Acme Trading Co.");
// $mpdf->setFooter('Page {PAGENO}');
$mpdf->AddPageByArray([
    'margin-left' => '0',
    'margin-right' => '0',
    'margin-top' => '0',
    'margin-bottom' => '0',
    'resetpagenum' => '1'
]);

$mpdf->SetDisplayMode('fullpage');
$todo = '<strong>To-do’s *voornaam* </strong>:<br/>';
if(isset($_POST['gw_todo_voornaam']))
{
    foreach($_POST['gw_todo_voornaam'] as $gw_todo_voornaam)
    {
        $todo .= $gw_todo_voornaam . "<br/>";
    }

}
$todo .= '<strong>To-do’s Van der Zeeuw </strong>:<br/>';
if(isset($_POST['gw_todo_zeeuw']))
{
    foreach($_POST['gw_todo_zeeuw'] as $gw_todo_zeeuw)
    {
        $todo .= $gw_todo_zeeuw . "<br/>";
    }
}

$stmt = $con -> prepare('SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, O.project_number, O.convert_date, O.startdatum, O.plaatsing 
FROM contacts C 
LEFT JOIN projects O ON (C.id = O.contact_id)
WHERE C.id = ?
');
$stmt -> bind_param('i', $_POST['gp_project']);
$stmt -> execute();
$result = $stmt -> get_result();
$row = $result->fetch_assoc();

$date = date('d-m-Y');
$html = '<html><head></head>
<style>
body{

    background-image: url("'.__DIR__.'/../../images/gesprekverslag-pdf-leeg-1.jpg");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: top left;
    background-image-resize: 4;
    background-image-resolution: from-image;
    font-size : 18px;
    font-family: Asap;
}
.panel-title{
    width : 100%;
    height: 305px;
    position: relative;
}
#text-date{
    color : #2c9ad5;
    margin-left: 38px;
    padding-top : 150px;
}
.panel-first{
    width : 100%;
    height: 160px;
    
}
.vertical-top {
    vertical-align: top;
  }

#table-project-info{
    width : 100%;
    text-align : left;
    margin-left : 32px;
    margin-top : -20px;
}
#table-project-info th{
    font-weight : bold;
    color : #1a2c5e;
    text-align : left;
}
.panel-second{
    width : 100%;
}
#txt-samenvatting{
    width : 55%;

    padding-left : 35px;
    margin-top : 60px;
    line-height : 26px;
}
</style>
<body>
<div class="panel-title">';
$html .= "<p id='text-date'>" . $date . "</p></div>";
$html .= "<div class='panel-first'>";
$html .= "<table id='table-project-info'><thead>
<tr>
<th style='width : 50%'>Voornaam Achternaam</th>
<th style='width : 40%'>&nbsp;</th>
<th style='width : 10%'>Projectnummer</th>
</tr>
</thead>
<tbody>
<tr>
<td>
". $row['address'] . "<br/>" 
. $row['email'] . "<br/>" 
. $row['phone'] . 
"
</td><td>

</td><td class='vertical-top'>#"  . $row['project_number'] . "</td>
</tr>
</tbody></table>";
$html .= "</div>";

$html .= "<div class='panel-second'>";
$html .= "<p id='txt-samenvatting'>" . nl2br($_POST['gp_samenvatting']) . "</p>";
$html .= "</div>";

// if(isset($_POST['gp_project']))
// {
//     $html .= 'Project : ' . $_POST['gp_project'];
// }

// $html .= 'Samenvatting : ' .  nl2br($_POST['gp_samenvatting']) . "<br/>" .
// 'Beslissingen : ' .  nl2br($_POST['gp_beslissingen']) . "<br/>" .
// $todo . 
$html .= '<htmlpagefooter name="pagefooter" style="display:none">
<div class="fullwidth">

    
</div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />
</body>
</html>
';


$mpdf->WriteHTML($html);




$mpdf->AddPageByArray([
    'margin-left' => '0',
    'margin-right' => '0',
    'margin-top' => '0',
    'margin-bottom' => '0',
    'resetpagenum' => '1'
]);

$html = '<html>

<head>

<style>


body{
    background-image: url("'.__DIR__.'/../../images/gesprekverslag-pdf-leeg-2.jpg");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: top left;
    background-image-resize: 4;
    background-image-resolution: from-image;
    font-size : 18px;
    font-family: Asap;

}
.panel-third{

width : 100%;
margin-left : 40px;
padding-top : 100px;
width : 55%;
height : 440px;


}

.txt-strong{
    font-weight : bold;
    color : #1a2c5e;
    font-size : 20px;
}
.panel-firth
{
    
    width : 90%;
    margin-left : 5%;
    margin-top : 40px;
}

.panel-firth .txt-strong{
}
.panel-firth-left,
.panel-firth-right{
    width : 50%;

}
.panel-firth-left{
    float: left;
}
.item{
    line-height : 25px;
    margin: 0px;
    padding : 0px;
    background-image: url("'.__DIR__.'/../../images/check.png");
    background-repeat: no-repeat;
    background-size: 50px 40px;
    background-position: 5px left;
    background-image-resize: 4;
    background-image-resolution: from-image;
    padding-left : 30px;
    padding-bottom : 5px;
    padding-right : 10px;
}

.panel-fifth{
    width : 100%;

}

.panel-fifth .person{
    line-height : 25px;
}
</style>
</head>
<body>
<div class="panel-third">
<p class="txt-strong">Wat is er besloten?</p>
<p style="line-height : 26px;">' . nl2br($_POST['gp_beslissingen']) . '</p>
</div>
<div class="panel-firth">
    <div class="panel-firth-left">
    <p  class="txt-strong">Van der Zeeuw</p>';
    if(isset($_POST['gw_todo_zeeuw']))
    foreach($_POST['gw_todo_zeeuw'] as $item)
    {
        $html .= "<div class='item'>" . $item . "</div>";
    }
$html .= '
    </div>
    <div class="panel-firth-right">
    <p  class="txt-strong">' . $row['name'] . '</p>';

    if(isset($_POST['gw_todo_voornaam']))
    foreach($_POST['gw_todo_voornaam'] as $item)
    {
        $html .= "<div class='item'>" . $item . "</div>";
    }

$html .=    '</div>';
if(isset($_POST['gw_person']))
{
    $html .= '<div class="panel-fifth">
    <p class="txt-strong">Deze personen waren aanwezig:</p>'; 
    foreach($_POST['gw_person'] as $item)
    {
        $html .= "<div class='person'>" . $item . "</div>";
    }
    $html .= '</div>';
}





$html .=
'
</div>
<htmlpagefooter name="pagefooter" style="display:none">
    <div class="fullwidth">

        
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />
</body>
</html>';


$mpdf->WriteHTML($html);


if(isset($_POST['image_path'])){
$mpdf->AddPageByArray([
    'margin-left' => '0',
    'margin-right' => '0',
    'margin-top' => '40',
    'margin-bottom' => '0'
]);

$html = '<html>

<head>

<style>


body{
    background-image: url("'.__DIR__.'/../../images/gesprekverslag-pdf-leeg-3.jpg");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: top left;
    background-image-resize: 4;
    background-image-resolution: from-image;
    font-size : 18px;
    font-family: Asap;

}
.panel-sixth{
    width : 100%;
}
.image-bijlagen{
    max-width : 90%;
    margin-left : 5%;
    margin-top : 30px;
}
</style>
</head>
<body>
';

$html .= "<div class='panel-sixth'>";

foreach($_POST['image_path'] as $img)
{
    $html .= "<img class='image-bijlagen' src='" .__DIR__.'/../../upload/' . $img .  "'/>";

    
}
$html .=
'
</div>
<htmlpagefooter name="pagefooter" style="display:none">
    <div class="fullwidth">

        
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />
</body>
</html>';


$mpdf->WriteHTML($html);
foreach($_POST['image_path'] as $img)
{
    unlink(__DIR__ . '/../../upload/' . $img);
}
}

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

$date = date_create();
$dt = $date->format("d-m-Y");
$file_name = generateRandomString(10) . "_" .  $dt . ".pdf";
$mpdf->Output('upload/'.$file_name,'F'); 
$mpdf->Output($file_name,'D'); 
// exit();

require( 'common/connection.php');

if ($stmt = $con -> prepare('INSERT projects_file (contact_id, name, file_type, file_exe, uploaded_date, file_path, user_id, klantportaal, folder_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
    
    $file_type = 5;
    $date = date_create();
    $dt = $date->format("Y-m-d H:i:s");
    $name = 'Gespreksverslag ' . ($date->format("d-m-Y"));
    $klantportaal = 1;
    $user_id = 0;
    $folder_id = 0;
    $fileEXE = 'pdf';
    $file_path = $file_name;
    $stmt -> bind_param('isisssiii', $_POST['gp_project'], $name, $file_type, $fileEXE, $dt, $file_path, $user_id, $klantportaal, $folder_id );
    $stmt -> execute();

    $contact_id = $_POST['gp_project'];
    $text = '';
    $title = "Bestand toegevoegd: " . $name;

    $stmt_new_log = $con -> prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt_new_log -> bind_param("i", $contact_id);
    $stmt_new_log -> execute();
    
    $flag = 0;
    $result_new_log = $stmt_new_log -> get_result();
  
    while($row_new_log = $result_new_log -> fetch_assoc())
    {
      if($row_new_log['c_status'] == 3 && $row_new_log['l_status'] == 1)
      {
        $flag = 1;
      }
    }
  
    if($flag)
    {
      $date = date_create();
      $date_text = $date->format("Y-m-d H:i:s");
      $entry_type = 101;
      $stmt_new_log = $con -> prepare("INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt_new_log -> bind_param("iisssi", $contact_id, $entry_type, $title, $date_text, $text, $user_id);
      $stmt_new_log -> execute();
  
    }
}

exit();


?>