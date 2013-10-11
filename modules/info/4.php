<?php
include ('queries.php');

if (isset($_REQUEST['ObjectID'])){
	$ObjectID = $_REQUEST['ObjectID'];
} else {
    $ObjectID = 0;
}

if (isset($_REQUEST['submit_inv']) && isset($_REQUEST['inv'])) {
    $db->Execute("UPDATE Object_DATA SET Inventory = ? WHERE ObjectID = ?", array($_REQUEST['inv'], $ObjectID));
    $db->Execute("INSERT INTO `logs`(`action`, `user`, `timestamp`) VALUES (CONCAT('Set inventory of vehicle: ',?,' (',?,') to ',?),?,NOW())", array($row['Classname'], $ObjectID, $_REQUEST['inv'], $_SESSION['login']));
}

if (isset($_REQUEST['submit_loc']) && isset($_REQUEST['loc'])){
	$loc =  mysql_real_escape_string($_POST['loc']);
	$db->Execute("UPDATE Object_DATA SET Worldspace = ? WHERE ObjectID = ?", array($loc, $ObjectID));
	$db->Execute("INSERT INTO `logs`(`action`, `user`, `timestamp`) VALUES (CONCAT('Edited location of Object: ',?),?,NOW())", array($ObjectID, $_SESSION['login']));
} 

$res = $db->GetAll($info4, array($ObjectID, $iid));

foreach($res as $row) {

    $MapCoords = worldspaceToMapCoords($row['Worldspace']);
	
	$Backpack  = $row['Inventory'];
	$Backpack = str_replace("|", ",", $Backpack);
	$Backpack  = json_decode($Backpack);

	$owner = "";
    $ownerid = "";
    $owneruid = "";
	
	$Hitpoints = $row['Hitpoints'];
	$Hitpoints = str_replace("|", ",", $Hitpoints);
	$Hitpoints = json_decode($Hitpoints);
	
	$xml = file_get_contents('items.xml', true);
	require_once('modules/xml2array.php');
	$items_xml = XML2Array::createArray($xml);
	
	$xml = file_get_contents('vehicles.xml', true);
	require_once('modules/xml2array.php');
	$vehicles_xml = XML2Array::createArray($xml);
?>	
	<div id="page-heading">
		<center>
			<h3><?php echo "<title>".$row['Classname']." - ".$sitename."</title>"; ?></h3>
			<h3 class="custom-h3"><?php echo $row['Classname']; ?> - <?php echo $row['ObjectID']; ?> - Last save: <?php echo $row['last_updated']; ?></h3>
		</center>
	</div>
	<!-- end page-heading -->

	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="content-table">
	<tr>
		<td>
		<!--  start content-table-inner ...................................................................... START -->
		<div id="content-table-inner">
		
			<!--  start table-content  -->
			<div id="table-content">
				<div id="gear_vehicle" style="margin-left: 64px; margin-bottom: 10px;">	
					<div class="gear_info">
						<img class="vehiclemodel" src='images/vehicles/<?php echo $row['Classname']; ?>.png'/>
						<div id="gps" style="margin-left:120px;margin-top:323px">
							<div class="gpstext" style="font-size: 22px;width:60px;text-align: left;margin-left:47px;margin-top:13px">
							<?php
								echo $MapCoords[0];
							?>
							</div>
							<div class="gpstext" style="font-size: 22px;width:60px;text-align: left;margin-left:47px;margin-top:34px">
							<?php
								echo $MapCoords[3];
							?>
							</div>
							<div class="gpstext" style="width:120px;margin-left:13px;margin-top:61px">
							<?php
								if ($accesslvls[0][3] != 'false') {
									echo sprintf("%03d%03d",$MapCoords[1],$MapCoords[2]);
								} else {
									echo '<h4 style="margin-top: 2px">Classified!</h4>';
								}
							?>
							</div>							
						</div>

						<div class="statstext" style="width:180px;margin-left:280px;margin-top:-85px">
							<?php echo 'Damage:&nbsp;'.sprintf("%d%%", round($row['Damage'] * 100));?>
						</div>
						<div class="statstext" style="width:180px;margin-left:280px;margin-top:-65px">
							<?php echo 'Fuel:&nbsp;'.sprintf("%d%%", round($row['Fuel'] * 100));?>
						</div>
					</div>
					<!-- Backpack -->
					<div class="vehicle_gear">	
						<div id="vehicle_inventory">	
						<?php
							
							$maxmagazines = 24;
							$maxweaps = 3;
							$maxbacks = 0;
							$freeslots = 0;
							$freeweaps = 0;
							$freebacks = 0;
							$BackpackName = $row['Classname'];
							if(array_key_exists('s'.$row['Classname'],$vehicles_xml['vehicles'])){
								$maxmagazines = $vehicles_xml['vehicles']['s'.$row['Classname']]['transportmaxmagazines'];
								$maxweaps = $vehicles_xml['vehicles']['s'.$row['Classname']]['transportmaxweapons'];
								$maxbacks = $vehicles_xml['vehicles']['s'.$row['Classname']]['transportmaxbackpacks'];
								$BackpackName = $vehicles_xml['vehicles']['s'.$row['Classname']]['Name'];
							}
							if (count($Backpack) >0){
							$bpweaponscount = count($Backpack[0][0]);
							$bpweapons = array();
							for ($m=0; $m<$bpweaponscount; $m++){
									for ($mi=0; $mi<$Backpack[0][1][$m]; $mi++){
										$bpweapons[] = $Backpack[0][0][$m];
									}
							}							

							
							$bpitemscount = count($Backpack[1][0]);
							$bpitems = array();
							for ($m=0; $m<$bpitemscount; $m++){
								for ($mi=0; $mi<$Backpack[1][1][$m]; $mi++){
									$bpitems[] = $Backpack[1][0][$m];
								}
							}
							
							$bpackscount = count($Backpack[2][0]);
							$bpacks = array();
							for ($m=0; $m<$bpackscount; $m++){
								for ($mi=0; $mi<$Backpack[2][1][$m]; $mi++){
									$bpacks[] = $Backpack[2][0][$m];
								}
							}
							
							$Backpack = (array_merge($bpweapons, $bpacks, $bpitems));
							$freebacks = $maxbacks;
							$backpackslots = 0;
							$backpackitem = array();
							$bpweapons = array();
							for ($i=0; $i<count($Backpack); $i++){
								if(array_key_exists('s'.$Backpack[$i],$items_xml['items'])){
									switch($items_xml['items']['s'.$Backpack[$i]]['Type']){
										case 'binocular':
											$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										case 'rifle':
											$bpweapons[] = array('image' => '<img style="max-width:84px;max-height:84px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										case 'pistol':
											$bpweapons[] = array('image' => '<img style="max-width:84px;max-height:84px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										case 'backpack':
											$bpweapons[] = array('image' => '<img style="max-width:84px;max-height:84px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											$freebacks = $freebacks - 1;
											break;
										case 'heavyammo':
											$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										case 'smallammo':
											$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										case 'item':
											$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
											break;
										default:
											$s = '';
									}
								}
							}	
							
							$weapons = count($bpweapons);
							$magazines = $maxmagazines;
							$freeslots = $magazines;
							$freeweaps = $maxweaps;
							$jx = 1;
							$jy = 0;
							$jk = 0;
							$jl = 0;
							$numlines = 0;
							for ($j=0; $j< $weapons; $j++){
								if ($jk > 3){ $jk = 0;$jl++;}
								echo '<div class="gear_slot" style="margin-left:'.($jx+(86*$jk)).'px;margin-top:'.($jy+(86*$jl)).'px;width:84px;height:84px;">'.$bpweapons[$j]['image'].'</div>';
								//$magazines = $magazines - $bpweapons[$j]['slots'];	
								$freeweaps = $freeweaps - 1;
								$jk++;
							}
							
							if ($jl > 0){
								$numlines = $jl+1;
							}
							if ($jl == 0){
								if ($weapons > 0){
									$numlines++;
								}
							}
							//if ($weapons == 1){$numlines = 1;}
							$jx = 1;
							$jy = (86*$numlines);
							$jk = 0;
							$jl = 0;

							for ($j=0; $j<$magazines; $j++){
								if ($jk > 6){ $jk = 0;$jl++;}
								if ($j<count($backpackitem)){
									echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$backpackitem[$j]['image'].'</div>';
									//$jk = $jk - 1 + $backpackitem[$j]['slots'];
									//$backpackslots = $backpackslots + $backpackitem[$j]['slots'];
									$freeslots = $freeslots - 1;
								} else {
									//if($backpackslots==$maxmagazines){
										//break;
									//}
									//$backpackslots++;
									echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;"></div>';
								}								
								$jk++;
							}	
							}
							//$freeslots = $freeslots - $magazines;							
						?>
						</div>
						<div class="backpackname">
						<?php
							echo 'Mags:&nbsp;'.$freeslots.'&nbsp;/&nbsp;'.$maxmagazines.'&nbsp;Weaps:&nbsp;'.$freeweaps.'&nbsp;/&nbsp;'.$maxweaps.'&nbsp;Backs:&nbsp;'.$freebacks.'&nbsp;/&nbsp;'.$maxbacks.'&nbsp;';
						?>
						</div>
					</div>
					<!-- Backpack -->
					
					<!-- Hitpoints -->
					<div class="vehicle_hitpoints">	
						<?php
							$jx = 1;
							$jy = 48;
							$jk = 0;
							$jl = 0;
							for ($i=0; $i<count($Hitpoints); $i++){
								if ($jk > 3){ $jk = 0;$jl++;}
								$hit = '<img style="max-width:90px;max-height:90px;" src="images/hits/'.$Hitpoints[$i][0].'.png" title="'.$Hitpoints[$i][0].' - '.round(100 - ($Hitpoints[$i][1]*100)).'%" alt="'.$Hitpoints[$i][0].' - '.round(100 - ($Hitpoints[$i][1]*100)).'%"/>';
								//$hit = $Hitpoints[$i][0].' - '.$Hitpoints[$i][1];
								echo '<div class="hit_slot" style="margin-left:'.($jx+(93*$jk)).'px;margin-top:'.($jy+(93*$jl)).'px;width:91px;height:91px;background-color: rgba(100,'.round((255/100)*(100 - ($Hitpoints[$i][1]*100))).',0,0.8);">'.$hit.'</div>';
								$jk++;
							}							
						?>						
						<div class="backpackname">
						<?php
							echo 'Hitpoints';
						?>
						</div>
					</div>
					<!-- Hitpoints -->
			
				</div>
			</div>
<div id="medical">
	<table id="medical">
		<tr>
			<th class="custom-th">
			Options
			</th>
		</tr>
		<tr>
			<td>
				<a href="admin.php?view=actions&repairVehicle=<?php echo $row['ObjectID']; ?>">Repair Vehicle</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href="admin.php?view=actions&destroyVehicle=<?php echo $row['ObjectID']; ?>">Destroy Vehicle</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href="admin.php?view=actions&refuelVehicle=<?php echo $row['ObjectID']; ?>">Refuel Vehicle</a>
			</td>
		</tr>
	</table>
</div>

<div id="vehicleString">
    <form method="POST">
    <h2 class="custom-h2-string">Inventory String</h2>
        <textarea name="inv">
<?php
echo $row['Inventory'];
?>
        </textarea><br>
    <br><input name="submit_inv" class="btn btn-default" type="submit" value="Submit" />
    </form>

	<form method="post">
	<br><h2 class="custom-h2-string">Location String</h2>
		<textarea name="loc" action="">
<?php 
	if ($accesslvls[0][3] != 'false') {
		echo $row['Worldspace'];
	} else {
		echo 'Classified!';
	}
?>
		</textarea><br>
	<br><input name="submit_loc" class="btn btn-default" type="submit" value="Submit" />
	</form>
</div>
		 
		</div>
		<!--  end content-table-inner ............................................END  -->
		</td>
		<td id="tbl-border-right"></td>
	</tr>
	</table>
<?php } ?>
	<div class="clear">&nbsp;</div>
