<?php
	include 'dbConn.php';
	$mapCon = createDBConn();
	$map = $_POST['map'];
	$matchID = $_POST['matchID'];
	$picktype = "banned";
	$checkMap = $mapCon->query(
		"SELECT BanOne, BanTwo, BanThree, BanFour, BanFive, BanSix, BanPickOne, BanPickTwo, BanPickThree 
		FROM `Matches` 
		WHERE MatchID = '$matchID'"
	);
	$changeTeam = $mapCon->query(
		"SELECT PickID, TeamOneID, TeamTwoID FROM Matches WHERE MatchID = '$matchID'"
	);
	$checkMatchType = $mapCon->query(
		"SELECT MatchType FROM Matches WHERE MatchID = '$matchID'"
	);
	$checkMatchType = $checkMatchType->fetch_assoc();
	$matchType = $checkMatchType['MatchType'];
	$changeTeam = $changeTeam->fetch_assoc();
	if($changeTeam['PickID'] == $changeTeam['TeamOneID']) {
		$mapCon->query("UPDATE `Matches` SET `PickID`='".$changeTeam['TeamTwoID']."' WHERE MatchID = '$matchID'");
	} elseif($changeTeam['PickID'] == $changeTeam['TeamTwoID']) {
		$mapCon->query("UPDATE `Matches` SET `PickID`='".$changeTeam['TeamOneID']."' WHERE MatchID = '$matchID'");
	}
	$checkMap = $checkMap->fetch_assoc();
	$thisFill = 0;
	foreach($checkMap as $key => $tmap) {
		if(empty($tmap)) {
			$thisFill = $key;
			break;
		}
	}
	if($matchType=="BO3" || $matchType=="BO5") {
		if(strstr($key, "Pick")) {
			$pickType = "picked";
		} else if($key=="BanFive" || $key=="BanSix") {
			$pickType="picked";
		} else {
			$pickType = "banned";
		}
	} else {
		if($key=="BanPickThree") {
			$pickType = "picked";
		} else {
			$pickType = "banned";
		}
	}
	$mapCon->query(
		"UPDATE `Matches` SET ".$thisFill."='$map' WHERE `MatchID`='$matchID'"
	);
	$checkMap = $mapCon->query(
		"SELECT BanOne, BanTwo, BanThree, BanFour, BanFive, BanSix, BanPickOne, BanPickTwo, BanPickThree 
		FROM `Matches` 
		WHERE MatchID = '$matchID'"
	);
	$checkMap = $checkMap->fetch_assoc();
	if($thisFill == "BanPickTwo") {
		$mapPool = [
			"Bank",
			"Border",
			"Chalet",
			"Club House",
			"Coastline",
			"Consulate",
			"Kafe Dostoyevsky",
			"Oregon",
			"Skyscraper"
		];
		foreach($mapPool as $findThisMap) {
			if(!in_array($findThisMap, $checkMap)) {
				$defaultMap = $findThisMap;
			}
		}
		$mapCon->query(
			"UPDATE `Matches` SET `BanPickThree`='$defaultMap' WHERE `MatchID`='$matchID'"
		);
	}
	$mapCon->close();
?>
$("td:contains('<?php echo $map; ?>')").removeClass("mapT");
$("td:contains('<?php echo $map; ?>')").addClass("<?php echo $pickType; ?>");