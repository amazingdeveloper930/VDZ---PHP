<!DOCTYPE html>
<html>

<head>
		<meta charset="utf-8">
		<title>Productie - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>
<style>
table
{
    color-adjust: exact; 
    -webkit-print-color-adjust: exact;
}
.appcontent{
    margin-top : 0px;
}
</style>

	<body class="app jaarplanning">
        		

		<div class="appcontent">
			<input hidden id="num_year" value="<?=$_GET['year']?>"/>
            <input hidden id="num_quarter" value="<?=$_GET['quarter']?>"/>		

			<div id="jaarplanning" class="tab-content active">		
                <table id="jaarplanning_table" class="full-w-table calender-quarter-table" >

                    <tbody>
                        <?php
                            for($jdex = 0; $jdex < 60; $jdex ++){
                                ?>
                                <tr>
                                <td class="td-jaarplanning-text" id="jaarplanning-text-<?=$jdex?>">
                                </td>
                                    <?php
                                
                                for($index = 1; $index <= 13; $index ++){
                                    
                        ?>
                                <td class="<?=$jdex!=0?'td-week':'td-week-header'?>" id="week-<?=$jdex.'-'.$index?>">
									<span class='week-number'></span>
									<br/>
									<span class='week-employee-info'></span>
								</td>
                        <?php
                                }
                                ?>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
		</div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	<script src="<?=$root;?>js/jaarplanning.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>