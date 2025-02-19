<?php
/*
 * greybox
 * $Id: klub.inc,v 1.36 2006/08/23 19:58:50 che0 Exp $
 */

$domain_ck_role = array("t"=>$lang["coach"]);
$klub_id = $_REQUEST["klub_id"];

if (!cpdb_fetch_one_row("select * from klub where klub_ID = :klub_id",array(":klub_id"=>$klub_id), $klub_p)) {
	pg_achtung($lang["no record"]);
	return;
}

$klub = de_html_s($klub_p);

global $can_edit; // for get_cl_ac_array() function

$can_edit = (($GLOBALS["cps_kluby"] >= 2)
	|| (($GLOBALS["cps_kluby"] == 1) && ($klub_id == $_SESSION['user_klub_ID'])));

$GLOBALS["page"]->title = $klub['nazev'];
pg_head();
if (isset($klub["komentar"])) cph_p($klub["komentar"], array("class"=>"komentar"));

if ($can_edit) {
	$klub_f = new cpykhen;
	$klub_f->form_attr["action"] = "./?page=klub.edit.exec";
	$klub_f->form[] = new cpf_db_hidden("klub_id","klub_ID");
	$klub_f->form[] = new cpf_text($lang["club name"] . ": ");
	$klub_f->form[] = new cpf_db_textbox("nazev","nazev",array("size"=>40,"maxlength"=>255));
	$klub_f->form[] = new cpf_nl;
	$klub_f->form[] = new cpf_text($lang["short name"] . ": ");
	$klub_f->form[] = new cpf_db_textbox("kratky_nazev","kratky_nazev",array("maxlength"=>32));
	$klub_f->form[] = new cpf_nl;
	$klub_f->form[] = new cpf_text($lang["place"] . ": ");
	$klub_f->form[] = new cpf_db_textbox("misto","misto",array("size"=>40,"maxlength"=>255));
	$klub_f->form[] = new cpf_nl;
	$klub_f->form[] = new cpf_text("vedouci: ");
	$klub_f->form[] = new cpf_db_listbox("vedouci",
		array("(select clovek.clovek_ID, concat_ws(' ', jmeno, nick, prijmeni), concat_ws(' ', prijmeni, jmeno, nick) as srt from clovek_klub left join clovek using (clovek_ID) where clovek_klub.klub_ID = :klub_id) union distinct (select clovek_ID, concat_ws(' ', jmeno, nick, prijmeni), concat_ws(' ', prijmeni, jmeno, nick) as srt from clovek where klub_ID = :klub_id) order by srt", array(":klub_id"=>$klub["klub_ID"])),
		"vedouci");
	$klub_f->form[] = new cpf_nl;
	$klub_f->form[] = new cpf_text($lang["comment"] . ": ");
	$klub_f->form[] = new cpf_db_textarea("komentar","komentar",array("rows"=>4, "cols"=>60));
	$klub_f->form[] = new cpf_nl;
	if ($GLOBALS["cps_lidi"] >= 3) {
		// only admins can confirm application submission
		$klub_f->form[] = new cpf_text($lang["application"] . ": ");
		$klub_f->form[] = new cpf_db_listbox_array("prihlaska", array(1 => $lang['yes'], 0 => $lang['no']));
		$klub_f->form[] = new cpf_nl;
    } else {
		$klub_f->form[] = new cpf_db_hidden("prihlaska","prihlaska");
	}
	$klub_f->form[] = new cpf_button("save",$lang["save"]);
	//$klub_f->form[] = new cpf_button("delete",$lang["delete"]);
	$klub_f->load_this($klub_p);
	$klub_f->render();
	
	$klub_clovek = new cpykhen;
	$klub_clovek->form_attr["action"] = "./?page=klub.clovek.edit.exec";
	$klub_clovek->select = "select * from clovek_klub where klub_ID = :klub_id order by rocnik";
	$klub_clovek->select_args = array(":klub_id"=>$klub_id);
	$klub_clovek->form[] = new cpf_hidden("klub_id",$klub["klub_ID"]);
	$klub_clovek->form[] = new cpf_db_hidden("clovek_old","clovek_ID");
	$klub_clovek->form[] = new cpf_db_hidden("rocnik_old","rocnik");
	$klub_clovek->form[] = new cpf_db_listbox_array("role", $domain_ck_role);
	$klub_clovek->form[] = new cpf_db_listbox_array("rocnik", $GLOBALS["season_domain"]);
	$klub_clovek->form[] = new cpf_db_listbox("clovek_id",
		array("(select clovek.clovek_ID, concat_ws(' ', jmeno, nick, prijmeni), concat_ws(' ', prijmeni, jmeno, nick) as srt from clovek_klub left join clovek using (clovek_ID) where clovek_klub.klub_ID = :klub_id) union distinct (select clovek_ID, concat_ws(' ', jmeno, nick, prijmeni), concat_ws(' ', prijmeni, jmeno, nick) as srt from clovek where klub_ID = :klub_id) order by srt", array(":klub_id"=>$klub["klub_ID"])),
		"clovek_ID");
	$klub_clovek->form["link"] = new cpf_db_link("clovek_ID","./?page=clovek&clovek_id=%s",$lang["link"]);
	$klub_clovek->form["b1"] = new cpf_button("save",$lang["save"]);
	//$klub_clovek->form["b2"] = new cpf_button("delete",$lang["delete"]);
	$klub_clovek->render_select();
	
	$klub_clovek->form["b1"] = new cpf_button("add",$lang["add"]);
	unset($klub_clovek->form["b2"]);
	unset($klub_clovek->form["link"]);
	$klub_clovek->load_this(array("rocnik"=>$GLOBALS["current_season"]));
	$klub_clovek->render_hidden($lang["new person"]);
} else {
	
	if (isset($klub["misto"])) cph_p($lang["place"] . ": " . $klub["misto"]);
	if (isset($klub["vedouci"]))
	{
		if (!cpdb_fetch_one_row("select jmeno, nick, prijmeni, clovek_ID from clovek where clovek_ID = :vedouci",array(":vedouci"=>$klub["vedouci"]), $vedouci))
		{
			pg_achtung($lang["no record"]);
			return;
		}
		cph_p("Vedouci: " . cphs_link("./?page=clovek&clovek_id=" . $vedouci["clovek_ID"], join_name($vedouci["jmeno"], $vedouci["nick"], $vedouci["prijmeni"])),$vedouci);
	}
	
	// coaches
	if (cpdb_fetch_all("select clovek.jmeno, clovek.prijmeni, clovek.nick, clovek.clovek_ID, clovek_klub.rocnik, clovek_klub.role from clovek_klub left join clovek using (clovek_ID) where clovek_klub.klub_ID = :klub_id order by rocnik", array(":klub_id"=>$klub_id), $coaches) > 0) {
		de_html($coaches);
		cph_table_head(array($lang["role"], $lang["season"], $lang["person"]));
		foreach ($coaches as $coach) {
			cph_table_row(array(
				$domain_ck_role[$coach["role"]],
				season_to_str($coach["rocnik"]),
				cphs_link("./?page=clovek&clovek_id=" . $coach["clovek_ID"], join_name($coach["jmeno"], $coach["nick"], $coach["prijmeni"]))
			));
		}
		cph_table_end();
	}
}

// teams
if (cpdb_fetch_all("select tym.tym_ID, tym.nazev, count(debata_ID) as pocet_debat from tym left join debata_tym on tym.tym_ID = debata_tym.tym_ID where tym.klub_ID = :klub_id group by tym.tym_ID, tym.nazev",array(":klub_id"=>$klub_id),$tymy)) {

	cpdb_fetch_all("
		select
			tym.tym_ID,
			count(clovek.clovek_ID) as pocet_debateru
		from
			tym
			left join clovek_tym on tym.tym_ID = clovek_tym.tym_ID and clovek_tym.aktivni = 1
			left join clovek on clovek_tym.clovek_ID = clovek.clovek_ID and clovek.debater = 1
		where
			tym.klub_ID = :klub_id
		group by
			clovek_tym.tym_ID
		",
		array(":klub_id"=>$klub_id),
		$tym_debateri, "tym_ID");

	cph_h2($lang["teams"]);
	if ($can_edit) cph_p(cphs_link("./?page=tym.add&klub_id=" . $klub_id,$lang['add team']));

	de_html($tymy);
	cph_table_head(array($lang["team"],$lang["debaters"],$lang["debates"]));
	foreach ($tymy as $tym) {
		cph_table_row(array(
			cphs_link("./?page=tym&tym_id=" . $tym["tym_ID"], $tym["nazev"]),
			(int)$tym_debateri[$tym["tym_ID"]]["pocet_debateru"], (int)$tym["pocet_debat"]
		),
		(($tym_debateri[$tym["tym_ID"]]["pocet_debateru"] >= 3)
			&& ($tym["pocet_debat"] >= 1))
			? array() : array("class"=>"inactive"));
	}
	cph_table_end();
} else {
	if ($can_edit) cph_p(cphs_link("./?page=tym.add&klub_id=" . $klub_id,$lang['add team']));
}

// judges
if (cpdb_fetch_all("
	select
		rozhodci.misto,
		rozhodci.jazyk,
		rozhodci.format,
		clovek.clovek_ID,
		clovek.jmeno,
		clovek.nick,
		clovek.prijmeni
	from
		rozhodci
		left join clovek using(clovek_ID)
	where
		rozhodci.rocnik = :rocnik
		and clovek.klub_ID = :klub_id
	",array(":rocnik"=>$GLOBALS["current_season"],":klub_id"=>$klub_id),$r_rozhodci)) {
	
	de_html($r_rozhodci);
	cph_h2($lang["adjudicators"]);
	cph_table_head(array($lang["adjudicator"],$lang["language"],$lang["format"],$lang["place"]));
	foreach ($r_rozhodci as $rozhodci) {
		cph_table_row(array(
			cphs_link("./?page=clovek&clovek_id=" . $rozhodci["clovek_ID"], join_name($rozhodci["jmeno"], $rozhodci["nick"], $rozhodci["prijmeni"])),
			$rozhodci["jazyk"],
			$rozhodci["format"],
			$rozhodci["misto"]
		));
	}
	cph_table_end();
	
	
}

// members
cph_h2($lang['members']);

cph_p(
	(($can_edit) ?
		cphs_link("./?page=clovek.add&klub_id=".$klub_id, $lang['add person']) . " | "
		: ""
	)
	. cphs_link("./?page=klub.ib&klub_id=".$klub_id, $lang["points overview"])
	. " | " . cphs_link("./?page=klub.kidy&klub_id=".$klub_id, $lang["kidy overview"])
);

cpdb_fetch_all("
	select
		clovek.clovek_ID as a_clovek_ID,
		clovek.jmeno as a_jmeno,
		clovek.nick as a_nick,
		clovek.prijmeni as a_prijmeni,
		clovek.debater as a_debater,
		clovek.clen as a_clen,
		clovek.narozen is null as a_bez_datnar,
		tym.tym_ID as a_tym_ID,
		tym.nazev as a_tym
	from
		clovek
		left join clovek_tym on clovek.clovek_ID = clovek_tym.clovek_ID and clovek_tym.aktivni = 1
		left join tym on clovek_tym.tym_ID <=> tym.tym_ID
	where
		clovek.klub_ID = :klub_id
	order by
		a_prijmeni,
		a_jmeno,
		a_nick
	",array(":klub_id"=>$klub_id), $clenove);

de_html($clenove);
cph_table_head(array($lang['name'], $lang['team'], $lang['active debater_short'], $lang["full member"]));

function get_cl_ac_array($clen, $debater, $bez_datnar) {
	global $can_edit;
	
	$cl_prm = array();
	if (!$clen) { $cl_prm[] = "inactive"; }
	if (!$debater) { $cl_prm[] = "special"; }
	if ($can_edit && $clen && $bez_datnar) { $cl_prm[] = "small_achtung"; }
	
	return array("class" => implode(" ", $cl_prm));
}

while ($clen = current($clenove)) {
	// first result fetch
	$cl_clovek_ID = $clen['a_clovek_ID'];
	$cl_name = cphs_link("./?page=clovek&clovek_id=" . $clen['a_clovek_ID'], join_name($clen['a_jmeno'], $clen['a_nick'], $clen['a_prijmeni']));
	if ($clen['a_tym_ID']) {
		$cl_team = cphs_link("./?page=tym&tym_id=" . $clen['a_tym_ID'], $clen['a_tym']);
	} else {
		$cl_team = '';
	}
	$cl_debater = $clen['a_debater'];
	$cl_clen = $clen['a_clen'];
	$cl_bez_datnar = $clen['a_bez_datnar'];

	while ($clen = next($clenove)) {
		// check for added team, either add team, or print & fetch
		if ($clen['a_clovek_ID'] == $cl_clovek_ID) {
			$cl_team .= ", " . cphs_link("./?page=tym&tym_id=" . $clen['a_tym_ID'], $clen['a_tym']);
		} else {
			cph_table_row(array($cl_name, $cl_team, $cl_debater ? $lang['active'] : $lang['no'], $cl_clen ? $lang['yes'] : $lang['no']), get_cl_ac_array($cl_clen, $cl_debater, $cl_bez_datnar));
			
			$cl_clovek_ID = $clen['a_clovek_ID'];
			$cl_name = cphs_link("./?page=clovek&clovek_id=" . $clen['a_clovek_ID'], join_name($clen['a_jmeno'], $clen['a_nick'], $clen['a_prijmeni']));
			if ($clen['a_tym_ID']) {
				$cl_team = cphs_link("./?page=tym&tym_id=" . $clen['a_tym_ID'], $clen['a_tym']);
			} else {
				$cl_team = '';
			}
			$cl_debater = $clen['a_debater'];
			$cl_clen = $clen['a_clen'];
			$cl_bez_datnar = $clen['a_bez_datnar'];
		}
	}
	// print last one
	cph_table_row(array($cl_name, $cl_team, $cl_debater ? $lang['active'] : $lang['no'], $cl_clen ? $lang['yes'] : $lang['no']), get_cl_ac_array($cl_clen, $cl_debater, $cl_bez_datnar));
	next($clenove);
}

cph_table_end();

?>
