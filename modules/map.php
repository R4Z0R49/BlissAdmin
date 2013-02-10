<?php
if (isset($_SESSION['user_id']))
{
	switch($show) {
	case 0:
		$title = 'Recent Players';
		break;
	case 1:
		$title = 'Alive Players';
		break;
	case 2:
		$title = 'Dead Players';
		break;
	case 3:
		$title = 'All Players';
		break;
	case 4:
		$title = 'Vehicles';
		break;
	case 5:
		$title = 'Vehicle Spawns';
		break;
	case 6:
		$title = 'Tents';
		break;
	case 7:
		$title = 'Deployables';
		break;
	case 8:
		$title = 'Recent Players, Vehicles and Deployables';
		break;
	}
	echo '<div id="page-heading"><title>'.$title.' - '.$sitename.'</title><h2>'.$title.'&nbsp;(<span id="count">0</span>)</h2></div>';

	include('modules/leaf.php');
}
else
{
	header('Location:' .$security.'.php');
}
?>
