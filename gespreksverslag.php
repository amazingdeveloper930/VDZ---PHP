<?php 


if(isset($_POST['gp_form_data']))
{
    include('php/settings/generate_gw_pdf.php');
}


require 'common/global.php';
require( 'common/connection.php');


$currentpage = 'gespreksverslag';



// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;

if ($stmt = $con->prepare(
    'SELECT P.*, C.name, C.address FROM projects P JOIN contacts C ON P.contact_id = C.id ORDER BY C.address')) {	

    //$stmt->bind_param('i', 3); // only lead
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
}

$stmt = $con -> prepare("SELECT WA.* FROM werkplanning_activity WA GROUP BY text");
$stmt -> execute();
$result_activity = $stmt -> get_result();



?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>gespreksverslag - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>
        <link href="<?=$root;?>css/gespreksverslag.css" rel="stylesheet" type="text/css">	
		

</head>



<?php 



require( 'common/connection.php');


?>



<body class="gespreksverslag">

<div class="gespreksverslag-menubalk">

	<img src="https://orders.vanderzeeuwbouw.nl/images/app-logo.png" class="app-logo">
	<img src="https://orders.vanderzeeuwbouw.nl/images/app-logo-mob.png" class="app-logo mob">
	
	
	

</div>

<div class="gespreksverslag-content">



    
    <div class="tab-content active">
        <div class="gespre-title">
            <h2>Gesprekverslag</h2>
            <p>Maak hier een verslag van het gesprek met de klant. De ingevulde velden krijg je automatisch in een pdf. Deze staat ook direct als bestand in het klantportaal klaar.</p>
        </div>
        <form method="POST" action="" id="gp-form">
            <input hidden name="gp_form_data" value=""/>
        <div class="gespre-container">
            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header gespre-widget-header-light">Klantgegevens</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>Project</label>
                            <select id="gp_project" name="gp_project" required onchange="changedGpProject()">
                            <option disabled selected >
                                Kies het projectnummer
                            </option>
                            <?php                 
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option data-name=\"" . $row['name'] . "\" value=" . $row['contact_id']  . ">#" . $row['project_number'] . " / " . $row['address'] . "</option>";
                                }

                            ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header">Samenvatting</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>Beschrijf van er algemeen besproken is</label>
                            <textarea  id="gp_samenvatting" name="gp_samenvatting"  placeholder="Type hier het verslag" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header">Beslissingen</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>Wat is er besloten?</label>
                            <textarea  id="gp_beslissingen" name="gp_beslissingen" placeholder="Type hier het verslag" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header">To-do’s</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>To-do’s <span id="gw-voornaam">*voornaam</span></label>
                            <div class="gw-todo-list-box gw-todo-voor"></div>
                            <button class="gp-add-todo" type="button" onclick="addTodoVoor()"><span class="gp-add-todo-label">Veld toevoegen</span><i class="material-icons">add_circle</i></button>
                        </div>
                        <div class="gw-form-container">
                            <label>To-do’s Van der Zeeuw</label>
                            <div class="gw-todo-list-box gw-todo-zeeuw"></div>
                            <button class="gp-add-todo" type="button" onclick="addTodoZeeuw()"><span class="gp-add-todo-label">Veld toevoegen</span><i class="material-icons">add_circle</i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header">Personen</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>Deze personen waren aanwezig</label>
                            <div class="gw-todo-list-box gw-personen"></div>
                            <button class="gp-add-todo" type="button" onclick="addPerson()"><span class="gp-add-todo-label">Persoon toevoegen</span><i class="material-icons">add_circle</i></button>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="gespre-item">
                <div class="gespre-widget">
                    <h4 class="gespre-widget-header">Afbeeldingen toevoegen</h4>
                    <div class="gespre-widget-body">
                        <div class="gw-form-container">
                            <label>Upload hier de afbeeldingen</label>
                            <div class="input-div">
                                <p>Sleep hier je bestanden naar toe of klik om te uploaden</p>
                                <input type="file" class="file image-input" multiple="multiple" accept="image/jpeg, image/png, image/jpg" onchange="fileChanged()" ondrop="fileDroped(event)">
                            </div>

                            <output></output>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

        <div class="gespre-footer">
            <button type="button" class=" button gespre-button waves-effect waves-light btn" onclick="clearAllInputs()">Opslaan & .pdf maken</button>
        </div>
        </form>
    </div>
    



</div>		



<script src="<?=$root;?>js/gesprekvrslag.js" type="text/javascript"></script>			



<?php include 'common/footer.php'; ?>