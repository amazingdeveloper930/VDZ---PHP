<div class="menubalk">

	<img src="<?=$root;?>images/app-logo.png" class="app-logo">
	<img src="<?=$root;?>images/app-logo-mob.png" class="app-logo mob">
	
	<div class="menucontainer">
		<a class="<?php if($currentpage == 'funnel') { echo "actief"; } ?>" href="/funnel/">Funnel</a>
		<a class="<?php if($currentpage == 'leads' || $currentpage == 'salesplanning') { echo "actief"; } ?>" href="/leads/">Leads</a>
		<a class="<?php if($currentpage == 'opdracht') { echo "actief"; } ?>" href="/opdracht/">Opdracht</a>
		<a class="<?php if($currentpage == 'productie' || $currentpage == 'jaarplanning') { echo "actief"; } ?>" href="/productie/">Productie</a>
		<a class="<?php if($currentpage == 'plaatsing') { echo "actief"; } ?>" href="/plaatsing/">Plaatsing</a>
		<a class="<?php if($currentpage == 'aftersales') { echo "actief"; } ?>" href="/aftersales/">After sales</a>
		<a class="<?php if($currentpage == 'bedrijfsvoering') { echo "actief"; } ?>" href="/bedrijfsvoering/">Bedrijfsvoering</a>
		<div style="clear:both"></div>		
	</div>

	<div class="toolcontainer">
		<a class="btn-switch" onclick="switchTo()"><i class="material-icons">cached</i></a>
		<form method="POST" id="switch-form">
		</form>
		<a class="btn-info" target="_blank" href="/images/workflow-vdz.pdf"><i class="material-icons">info_outline</i></a>
		<a class="btn-search"><i class="material-icons">search</i></a>

	</div>

	<div style="clear:both"></div>
	
	<div class="user dropdown-trigger" data-target="user-dropdown">
		<div class="userid" style="display:none"><?=$_SESSION['id'];?></div>
		<div class="avatar">
			<?php
			if(empty($_SESSION['img_path']))
				echo '<img src="' . $root . 'images/users/' . $_SESSION['img'] . '.jpg">';
			else {
				
				echo '<img src="' . $root . 'upload/' . $_SESSION['img_path'] . '">';
			}
			?>
		</div>
		<div class="naam"><?=$_SESSION['name'];?> <i class="material-icons">keyboard_arrow_down</i></div>
		<div style="clear:both"></div>
	</div>
	
	<ul id="user-dropdown" class='dropdown-content'>   
    <li><a href="../instellingen/"><i class="material-icons">settings</i>Instellingen</a></li>
	<li class="divider"></li>
    <li><a href="../php/login/logout.php"><i class="material-icons">power_settings_new</i>Uitloggen</a></li>
	</ul>

</div>