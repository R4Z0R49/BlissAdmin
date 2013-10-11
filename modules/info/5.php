<?php
include ('queries.php');

if (isset($_REQUEST['ObjectUID'])){
    $ObjectUID = $_REQUEST['ObjectUID'];
} else {
    $ObjectID = 0;
}

$res = $db->GetAll($info5, $ObjectUID);
$number = sizeof($res);

foreach($res as $row) {

	$MapCoords = worldspaceToMapCoords($row['Worldspace']);
?>	
	<div id="page-heading">
		<h3><?php echo $row['Classname']; ?> - <?php echo $row['ObjectUID']; ?></h3>
	</div>
	<!-- end page-heading -->

	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="content-table">
	<tr>
		<th rowspan="3" class="sized"><img src="images/shared/side_shadowleft.jpg" width="20" height="300" alt="" /></th>
		<th class="topleft"></th>
		<td id="tbl-border-top">&nbsp;</td>
		<th class="topright"></th>
		<th rowspan="3" class="sized"><img src="images/shared/side_shadowright.jpg" width="20" height="300" alt="" /></th>
	</tr>
	<tr>
		<td id="tbl-border-left"></td>
		<td>
		<!--  start content-table-inner ...................................................................... START -->
		<div id="content-table-inner">
		
			<!--  start table-content  -->
			<div id="table-content">
			
			<table border="0" width="100%" cellpadding="0" cellspacing="0" id="product-table">
			<tr>
				<th class="table-header-repeat line-left"><a href="">Preview</a>	</th>
				<th class="table-header-repeat line-left minwidth-1"><a href="">Info</a></th>
			</tr>
			<tr>
				<td align="center"><img src='images/vehicles/<?php echo $row['Classname']; ?>.png'></td>
				<td>
					<h2>Position:</h2><h3><?php echo "left:".$MapCoords[1]." top:".$MapCoords[2]; ?></h3>
				</td>	
			</tr>				
			</table>
			
			</div>
			<!--  end table-content  -->
	
			<div class="clear"></div>
		 
		</div>
		<!--  end content-table-inner ............................................END  -->
		</td>
		<td id="tbl-border-right"></td>
	</tr>
	<tr>
		<th class="sized bottomleft"></th>
		<td id="tbl-border-bottom">&nbsp;</td>
		<th class="sized bottomright"></th>
	</tr>
	</table>
<?php } ?>
	<div class="clear">&nbsp;</div>
