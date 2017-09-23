<?php
/*
 * greybox
 * $Id: clovek.edit.exec.inc,v 1.13 2005/04/15 09:08:14 che0 Exp $
 */

include_once("lib/points.inc");

$clovek_id = $_REQUEST["clovek_id"];
$klub_id = $_REQUEST["klub_id"];
$jmeno = $_REQUEST["jmeno"];
$nick = $_REQUEST["nick"];
$prijmeni = $_REQUEST["prijmeni"];
$email = $_REQUEST["email"];
$komentar = filter_comment($_REQUEST["komentar"]);
$narozen = $_REQUEST["narozen"];
$ulice = $_REQUEST["ulice"];
$mesto = $_REQUEST["mesto"];
$psc = $_REQUEST["psc"];
$prukaz = $_REQUEST["prukaz"];
$cislo = $_REQUEST["cislo"];
$debater = $_REQUEST["debater"];
$clen = $_REQUEST["clen"];

if ($GLOBALS["cps_lidi"] >= 3) {
    $clendo = $_REQUEST["clen_do"];
    $prihlaska = $_REQUEST["prihlaska"];
    $prohlaseni = $_REQUEST["prohlaseni"];
} else {
    $clendo = $prihlaska = $prohlaseni = "NULL";
}

cpdb_fetch_one_row("select klub_ID, clen from clovek where clovek_ID = :clovek_id", array(":clovek_id"=>$clovek_id), $cur);


if (!($GLOBALS["cps_lidi"] >= 2
	|| ($GLOBALS["cps_lidi"] == 1 && (
		($klub_id == $_SESSION["user_klub_ID"])
		|| ($cur["klub_ID"] == $_SESSION["user_klub_ID"])
	)))) {
		pg_achtung($lang["access denied"]);
		return;
}

if ($jmeno . $prijmeni == "" && !isset($_REQUEST["delete"]) && !isset($_REQUEST["access"])) {
	pg_achtung($lang["a person has to have at least a name"]);
	return;
}

if ($klub_id == 0) $klub_id = NULL;

if (isset($_REQUEST["add"])) {
	// add new
	
	if (cpdb_exec(
		"insert into
			clovek (klub_ID, jmeno, prijmeni, nick, email, narozen, ulice, mesto, psc, prukaz, cislo, debater, komentar, clen, clen_od, clen_do, prihlaska, prohlaseni)
		values
			(:klub_id, :prijmeni, :jmeno, :nick, :email, :narozen, :ulice, :mesto, :psc, :prukaz, :cislo, :debater, :komentar, :clen, NOW(), '".$clendo."', :prihlaska, :prohlaseni)
		",
		array(
			":klub_id"=>$GLOBALS["cps_lidi"] >= 2 ? $klub_id : $_SESSION["user_klub_ID"],
			":jmeno"=>$jmeno,
			":prijmeni"=>$prijmeni,
			":nick"=>$nick,
			":email"=>$email,
			":narozen"=>deformat_date($narozen),
			":ulice"=>$ulice,
			":mesto"=>$mesto,
			":psc"=>$psc,
			":prukaz"=>$prukaz,
			":cislo"=>$cislo,
			":debater"=>$debater,
			":clen"=> $GLOBALS["cps_lidi"] >= 3 ? $clen : 0,  
			":komentar"=>$komentar,
			":prihlaska"=>$GLOBALS["cps_lidi"] >= 3 ? $prihlaska : 0,
			":prohlaseni"=>$GLOBALS["cps_lidi"] >= 3 ? $prohlaseni : 0
		)
	)) {
		pg_achtung($lang["person insert ok"]);
		if ($klub_id) {
			include("klub.inc");
		} else {
			$_REQUEST["clovek_id"] = cpdb_insert_id();
			include("clovek.inc");
		}
	}
	
} elseif (isset($_REQUEST["save"])) {
	// update
	
	if (cpdb_exec(
		"update clovek set
			klub_ID = :klub_id,
			jmeno = :jmeno,
			prijmeni = :prijmeni,
			nick = :nick,
			email = :email,
			narozen = :narozen,
			ulice = :ulice,
			mesto = :mesto,
			psc = :psc,
			prukaz = :prukaz,
			cislo = :cislo,
			debater = :debater,
			clen = :clen,
            clen_do = '".$clendo."',
            prihlaska = :prihlaska,
            prohlaseni = :prohlaseni,
			komentar = :komentar
		where
			clovek_ID = :clovek_id
		" . ($GLOBALS["cps_lidi"] < 2 ? "and klub_ID = :user_klub_id" : ""),
		array(
			":clovek_id"=>$clovek_id,
			":klub_id"=>$GLOBALS["cps_lidi"] >= 2 ? $klub_id  : $cur["klub_ID"],
			":jmeno"=>$jmeno,
			":prijmeni"=>$prijmeni,
			":nick"=>$nick,
			":email"=>$email,
			":narozen"=>deformat_date($narozen),
			":ulice"=>$ulice,
			":mesto"=>$mesto,
			":psc"=>$psc,
			":prukaz"=>$prukaz,
			":cislo"=>$cislo,
			":debater"=>$debater,
			":clen"=>$GLOBALS["cps_lidi"] >= 3 ? $clen : $cur["clen"],
			":prihlaska"=>$GLOBALS["cps_lidi"] >= 3 ? $prihlaska : $cur["prihlaska"],
			":prohlaseni"=>$GLOBALS["cps_lidi"] >= 3 ? $prohlaseni : $cur["prohlaseni"],
			":komentar"=>$komentar,
			":user_klub_id"=>$_SESSION["user_klub_ID"]
		)
	)) {
		pg_achtung($lang["person update ok"]);
		
		if ($klub_id != $cur["klub_ID"]) {
			// recount ipoints if club changed
			ipts_recount_clovek($clovek_id);
		}
		
		include("clovek.inc");
	}
	
} elseif (isset($_REQUEST["delete"])) {
	// delete
	
	if (cpdb_exec("
		delete from
			clovek
		where
			clovek_ID = :clovek_id
		" . ($GLOBALS["cps_lidi"] < 2 ? "and klub_ID = :user_klub_id" : ""),
		array(
			":clovek_id"=>$clovek_id,
			":user_klub_id"=>$_SESSION["user_klub_ID"]
		)
	)) {
		pg_achtung($lang["person delete ok"]);
		include("klub.inc");
	}
} elseif (isset($_REQUEST["access"])) {
	// update permissions
	
	if ($GLOBALS["cps_lidi"] < 3) {
		pg_achtung($lang["access denied"]);
		return;	
	}
	
	if (cpdb_exec(
		"update clovek set
			prava_lidi = :prava_lidi,
			prava_kluby = :prava_kluby,
			prava_souteze = :prava_souteze,
			prava_debaty = :prava_debaty
		where
			clovek_ID = :clovek_id
		",
		array(
			":clovek_id"=>$clovek_id,
			":prava_lidi"=>$_REQUEST["prava_lidi"],
			":prava_kluby"=>$_REQUEST["prava_kluby"],
			":prava_souteze"=>$_REQUEST["prava_souteze"],
			":prava_debaty"=>$_REQUEST["prava_debaty"]
		)
	)) {
		pg_achtung($lang["permissions update ok"]);
		include("clovek.inc");
	}
}

?> 
