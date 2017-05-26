<?php
/*
 * greybox
 * $Id: clovek.self.exec.inc,v 1.3 2005/01/09 00:47:32 che0 Exp $
 */

$clovek_id = $_REQUEST["clovek_id"];
$nick = $_REQUEST["nick"];
$email = $_REQUEST["email"];
$komentar = $_REQUEST["komentar"];
$narozen = $_REQUEST["narozen"];
$ulice = $_REQUEST["ulice"];
$mesto = $_REQUEST["mesto"];
$psc = $_REQUEST["psc"];
$prukaz = $_REQUEST["prukaz"];
$cislo = $_REQUEST["cislo"];

if ($_SESSION["user_clovek_ID"] != $clovek_id) {
		pg_achtung($lang["access denied"]);
		return;
}

if (isset($_REQUEST["save"])) {
	// update
	
	if (cpdb_exec("
		update clovek set
			nick = :nick,
			email = :email,
			komentar = :komentar,
			ulice = :ulice,
			mesto = :mesto,
			psc = :psc,
			prukaz = :prukaz,
			cislo = :cislo
		where
			clovek_ID = :clovek_id
		",
		array(
			":clovek_id"=>$clovek_id,
			":nick"=>$nick,
			":email"=>$email,
			":komentar"=>$komentar,
			":ulice"=>$ulice,
			":mesto"=>$mesto,
			":psc"=>$psc,
			":prukaz"=>$prukaz,
			":cislo"=>$cislo
		)
	)) {
		pg_achtung($lang["person update ok"]);
		include("clovek.inc");
	}
	
} elseif (isset($_REQUEST["born"])) {
	// delete
	
	if (cpdb_exec("
		update clovek set
			narozen = :narozen
		where
			clovek_ID = :clovek_id
			and isnull(narozen)
		",
		array(
			":clovek_id"=>$clovek_id,
			":narozen"=>deformat_date($narozen),
		)
	)) {
		pg_achtung($lang["person update ok"]);
		include("clovek.inc");
	}
}

?> 
