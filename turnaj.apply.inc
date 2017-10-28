<?php

include_once("i_turnaj.inc");

if ($GLOBALS["cps_lidi"] < 0)
{
	pg_achtung($lang["access denied"]);
	return;
}

$turnaj_id = $_REQUEST["turnaj_id"];
$prihlaseny_id = intval($_SESSION["user_clovek_ID"]);

if (!cpdb_fetch_one_row("select turnaj.*, soutez.nazev as soutez, liga.nazev as liga from turnaj left join soutez on turnaj.soutez_ID = soutez.soutez_ID left join liga on turnaj.liga_ID = liga.liga_ID where turnaj_ID = :turnaj_id", array(":turnaj_id" => $turnaj_id),$turnaj_p)) {
	pg_achtung($lang["no record"]);
	return;
}

$turnaj = de_html_s($turnaj_p);
$GLOBALS["page"]->title = $turnaj["nazev"];
pg_head();

if (isset($turnaj["komentar"])) cph_p($turnaj["komentar"], array("class"=>"komentar"));

cph_p(
	$lang["date_from"] . ": " . format_date($turnaj["datum_od"]) . cphs_br()
	. $lang["date_to"] . ": " . format_date($turnaj["datum_do"]) . cphs_br()
	. $lang["competition"] . ": " . cphs_link("./?page=soutez&soutez_id=" . $turnaj["soutez_ID"], $turnaj["soutez"])
	. ((isset($turnaj["liga_ID"])) 
		? (cphs_br() . $lang["league"] . ": " . cphs_link("./?page=liga&liga_id=" . $turnaj["liga_ID"], $turnaj["liga"]))
		: ""
	)
);

$role_domain = array("o"=>$lang["organizer"]);
if (cpdb_fetch_all("select clovek_turnaj.role as role, mocnost, clovek.clovek_ID, clovek.jmeno, clovek.nick, clovek.prijmeni from clovek_turnaj left join clovek using (clovek_ID) where turnaj_ID = :turnaj_id and clovek_turnaj.role = 'o'", array(":turnaj_id"=>$turnaj_id), $r_clovek) > 0) {
	de_html($r_clovek);
	cph_table_head(array($lang["role"],$lang["person"],$lang["factor"]));
	foreach ($r_clovek as $clovek) {
		cph_table_row(array(
			$role_domain[$clovek["role"]],
			cphs_link("./?page=clovek&clovek_id=" . $clovek["clovek_ID"], join_name($clovek["jmeno"],$clovek["nick"],$clovek["prijmeni"])),
			$clovek["mocnost"]
		));
	}
	cph_table_end();
}


// before deadline
if (strtotime($turnaj["deadline"]) >= time())
{
	$turnaj_clovek = new cpykhen;
	$turnaj_clovek->form_attr["action"] = "./?page=turnaj.rozhodci.edit.exec";
	$turnaj_clovek->form["tid"] = new cpf_db_hidden("turnaj_id","turnaj_ID");
	$turnaj_clovek->form["cid"] = new cpf_db_hidden("clovek_old","clovek_ID");
	$turnaj_clovek->form[] = new cpf_db_listbox_array("role", array("r"=>$lang["adjudicator"]));
	if ($GLOBALS["cps_lidi"] >= 3)
	{
		$turnaj_clovek_select = array("select clovek.clovek_ID, concat_ws(' ', prijmeni, nick, jmeno) from rozhodci left join clovek using(clovek_ID) where rozhodci.rocnik = :rocnik order by prijmeni, jmeno, nick", array(":rocnik"=>$GLOBALS["current_season"]));
	}
	else
	{
		$turnaj_clovek_select = array("select clovek_ID, concat_ws(' ', prijmeni, nick, jmeno) from clovek where clovek_ID = :clovek_id", array(":clovek_id"=>$prihlaseny_id));
	}
	$turnaj_clovek->form[] = new cpf_db_listbox("clovek_id", $turnaj_clovek_select, "clovek_id");

	$turnaj_clovek->form["tid"] = new cpf_hidden("turnaj_id",$turnaj["turnaj_ID"]);
	unset($turnaj_clovek->form["cid"]);
	$turnaj_clovek->form[] = new cpf_text($lang["comment"] . ": ");
	$turnaj_clovek->form[] = new cpf_db_textarea("komentar","komentar",array("rows"=>1, "cols"=>60));
	$turnaj_clovek->form["b1"] = new cpf_button("add",$lang["add"]);
	unset($turnaj_clovek->form["b2"]);
	unset($turnaj_clovek->form["link"]);
	$turnaj_clovek->load_this(array("mocnost"=>1));
	$turnaj_clovek->render_hidden($lang["apply adjudicator"]);
}
else
{
	cph_p($lang["application ended"], array("class"=>"komentar"));
}
// show adjudicators who applied
$rozhodci_select = "select clovek_turnaj.role as role, clovek_turnaj.komentar as komentar, mocnost, clovek.clovek_ID, jmeno, nick, prijmeni, narozen, ulice, mesto, psc, cislo from clovek_turnaj left join clovek using (clovek_ID) where turnaj_ID = :turnaj_id and clovek_turnaj.role = 'r'";
if ($GLOBALS['cps_lidi'] < 2) $rozhodci_select .= " and prihlasil = :prihlasil";

if (cpdb_fetch_all($rozhodci_select, array(":turnaj_id"=>$turnaj_id, ":prihlasil"=>$prihlaseny_id), $r_clovek) > 0) {
	$role_domain = array("r"=>$lang["adjudicator"]);
	de_html($r_clovek);
	cph_table_head(array($lang["role"],$lang["person"],$lang["born"],$lang["postal"],$lang["id number"],$lang["comment"]));
	foreach ($r_clovek as $clovek) {
		cph_table_row(array(
			$role_domain[$clovek["role"]],
			cphs_link("./?page=clovek&clovek_id=" . $clovek["clovek_ID"], join_name($clovek["jmeno"],$clovek["nick"],$clovek["prijmeni"])),
			$clovek["narozen"] ? $clovek["narozen"] : $lang["please add"],
			$clovek["ulice"] ? $clovek["ulice"] . ", " . $clovek["psc"] . " " . $clovek["mesto"] : $lang["please add"],
			$clovek["cislo"] ? $clovek["cislo"] : $lang["please add"],
			$clovek["komentar"]
		));
	}
	cph_table_end();
}




?>
