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
    height: 375px;
    position: relative;
}
#text-date{
    color : white;
    margin-left: 36px;
    padding-top : 270px;
}
.panel-first{
    width : 100%;
    height: 160px;
    
}


#table-project-info{
    width : 100%;
    text-align : left;
    margin-left : 32px;
    margin-top : 23px;
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
<th style='width : 50%'>Project</th>
<th style='width : 25%'></th>
<th style='width : 25%'></th>
</tr>
</thead>
<tbody>
<tr>
<td>
#" . $row['project_number'] . " / " . $row['address'] . "<br/>" 
. $row['email'] . "<br/>" 
. $row['phone'] . 
"
</td><td>

</td><td></td>
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
    <p  class="txt-strong">*Voornaam / Bedrijfsnaaam?*</p>';

    if(isset($_POST['gw_todo_voornaam']))
    foreach($_POST['gw_todo_voornaam'] as $item)
    {
        $html .= "<div class='item'>" . $item . "</div>";
    }

$html .=    '</div>
</div>
<htmlpagefooter name="pagefooter" style="display:none">
    <div class="fullwidth">

        
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="pagefooter" value="on" />
</body>
</html>';


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

$date = date_create();
$dt = $date->format("Y_m_d_H_i_s");
$file_name = generateRandomString(10) . ".pdf";
$mpdf->Output($file_name,'I'); 


exit();


?>