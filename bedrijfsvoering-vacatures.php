<?php
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'bedrijfsvoering';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bedrijfsvoering - Van der Zeeuw Bouw Ordersysteem</title>

    <?php include 'common/header.php'; ?>

</head>

<?php

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$stmt  =   $con->prepare("SELECT * from vacatures");
$stmt->execute();
$result = $stmt->get_result();


?>
<body class="app">

<?php include 'common/navigatie.php'; ?>

<div class="appcontent">

    <div class="titlebar">
        <div class="titlebarcontainer withbutton">
            <h2>Bedrijfsvoering

                <div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
                     data-page-info="Dit scherm toont de overzichten voor het beheren van personeel, voertuigen, materiaal, leveranciers en elektronica."
                >
                    <img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
                </div>
            </h2>
            <div class="submenu">
                <div class="row">
                    <div class="col s12">
                        <ul class="tabs">
                            <?php
                            if($_SESSION['ac_level'] == 1)
                            {
                                echo '<li class=" col"><a href="/bedrijfsvoering">Personeel</a></li>';
                            }
                            ?>
                            <li class="col"><a href="/bedrijfsvoering-voertuigen">Voertuigen</a></li>
                            <li class=" col"><a href="/bedrijfsvoering-materiaal">Materiaal</a></li>
                            <li class=" col"><a href="/bedrijfsvoering-leveranciers">Leveranciers</a></li>
                            <li class=" col"><a href="/bedrijfsvoering-laptops">Laptops + telefoons</a></li>
                            <li class="tab col"><a class="active" href="#">Vacatures</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
        <span class="titlebarbutton button waves-effect waves-light btn" onclick="addNewVacature()"><i class="material-icons">add</i> Toevoegen</span>
        <div style="clear:both"></div>
    </div>

    <div id="vacature-panel" class="tab-content active">
        <table class="vacature-table full-w-table sort-table-group">
            <thead>
            <tr>
                <th>Naam</th>
                <th>E-mailadres</th>
                <th>Telefoonnummer</th>
                <th>Status</th>
                <th style="width:200px" class="no-sort"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($result) {
                while ($row = $result->fetch_assoc()) {
                    $red            =   '';
                    $show_reset     =   'none';
                    $show_default   =   'inline-block';
                    if($row['status'] == 'Afgewezen') {
                        $red            =   'color:#F44336;';
                        $show_reset     =   'inline-block';
                        $show_default   =   'none';
                    }
                    ?>
                    <tr vacaturerow="<?=$row['ID'];?>" class="vacaturerow">
                        <td><?=$row['full_name'];?></td>
                        <td><?=$row['email'];?></td>
                        <td><?=$row['phone'];?></td>
                        <td><span class="vacature-status" style="<?php echo $red; ?>"><?=$row['status'];?></span></td>
                        <td>
                            <div style="display: <?php echo $show_default; ?>;" onclick="changeVacatureStatus(<?=$row['ID'];?>, 1)" class="actiebutton tooltipped default-status" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">close</i></div>
                            <div style="display: <?php echo $show_reset; ?>;" onclick="changeVacatureStatus(<?=$row['ID'];?>, 0)" class="actiebutton tooltipped reset-status" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">refresh</i></div>
                            <div onclick="editVacature(<?=$row['ID'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
                            <div onclick="deleteVacature(<?=$row['ID'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?=$root;?>js/bedrijfsvoering.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
