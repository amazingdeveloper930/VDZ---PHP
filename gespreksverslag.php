<?php 


if(isset($_POST['gp_form_data']))
{
    include('php/settings/generate_gw_pdf.php');
}


require 'common/sessie_check.php';
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

		

</head>



<?php 



require( 'common/connection.php');


?>



<body class="app">

	
		
<?php include 'common/navigatie.php'; ?>



<div class="appcontent">



    <div class="titlebar">

        <div class="titlebarcontainer">

            <h2>Dashboard</h2>

            <div class="submenu">

                <a class="<?php if($currentpage == 'gesprekvrslag') { echo "actief"; } ?>" href="/gespreksverslag/">gespreksverslag</a>

            </div>

            <div style="clear:both"></div>

        </div>


        <div style="clear:both"></div>

    </div>
    <div class="tab-content active">
        <div class="gespre-title">
            <h3>Gesprekverslag</h3>
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
                            <select id="gp_project" name="gp_project" required>
                            <option disabled selected >
                                Kies het projectnummer
                            </option>
                            <?php                 
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value=" . $row['contact_id']  . ">#" . $row['project_number'] . " / " . $row['address'] . "</option>";
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
                            <label>To-do’s *voornaam*</label>
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
        </div>

        <div class="gespre-footer">
            <button type="submit" class=" button gespre-button waves-effect waves-light btn">Opslaan & .pdf maken</button>
        </div>
        </form>
    </div>
    



</div>		



<script src="<?=$root;?>js/gesprekvrslag.js" type="text/javascript"></script>			



<?php include 'common/footer.php'; ?>