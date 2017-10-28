<?php

if ($GLOBALS["cps_lidi"] < 0) {
	pg_achtung($lang["access denied"]);
	return;
}

$turnaj_id = $_REQUEST["turnaj_id"];
$clovek_id = $_REQUEST["clovek_id"];
$role = $_REQUEST["role"];
$komentar = $_REQUEST["komentar"];

if (isset($_REQUEST["add"])) {
	// add new
	if (cpdb_exec("insert into clovek_turnaj (clovek_ID, turnaj_ID, role, prihlasil, komentar) values (:clovek_id, :turnaj_id, :role, :prihlasil, :komentar)", array(":clovek_id"=>$clovek_id, ":turnaj_id"=>$turnaj_id, ":role"=>$role, ":prihlasil" => intval($_SESSION["user_clovek_ID"]), ":komentar"=>$komentar))) {
		pg_achtung($lang["adjudicator applied"]);
		include("turnaj.apply.inc");
	}
	
} elseif (isset($_REQUEST["save"])) {
	// update
	
	if (cpdb_exec("update clovek_turnaj set clovek_ID = :clovek_id, role = :role, mocnost = :mocnost where clovek_ID = :clovek_old and turnaj_ID = :turnaj_id", array(":clovek_id"=>$clovek_id, ":role"=>$role, ":mocnost"=>$mocnost, ":turnaj_id"=>$turnaj_id, ":clovek_old"=>$clovek_old))) {
		pg_achtung($lang["person update ok"]);
		include("turnaj.apply.inc");
	}
	
} elseif (isset($_REQUEST["delete"])) {
	// delete
	
	if (cpdb_exec("delete from clovek_turnaj where clovek_ID = :clovek_old and turnaj_ID = :turnaj_id", array(":turnaj_id"=>$turnaj_id, ":clovek_old"=>$clovek_old))) {
		pg_achtung($lang["adjudicator displied"]);
		include("turnaj.apply.inc");
	}
}

?> 
