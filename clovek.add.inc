<?php
/*
 * greybox
 * $Id: clovek.add.inc,v 1.9 2005/10/24 09:04:47 che0 Exp $
 */

$klub_id = $_REQUEST["klub_id"];

pg_head();

cph_p($lang['clovek.add.achtung']); // warning about adding already existing people

if ($GLOBALS["cps_lidi"] < 1) {
	pg_achtung($lang['access denied']);
	return;
}

$clovek_f = new cpykhen;
$clovek_f->form_attr["action"] = "./?page=clovek.edit.exec";
$clovek_f->form[] = new cpf_text($lang["name"] . ": ");
$clovek_f->form[] = new cpf_db_textbox("prijmeni","prijmeni",array("maxlength"=>60));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["surname"] . ": ");
$clovek_f->form[] = new cpf_db_textbox("jmeno","jmeno",array("maxlength"=>40));
$clovek_f->form[] = new cpf_nl; 
$clovek_f->form[] = new cpf_text($lang["nick"] . ": ");
$clovek_f->form[] = new cpf_db_textbox("nick","nick",array("maxlength"=>60));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("email: ");
$clovek_f->form[] = new cpf_db_textbox("email","email",array());
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["club"] . ": ");
$clovek_f->form[] = new cpf_db_listbox("klub_id",array(
	"select klub_ID, nazev from klub union select 0, :no_club as nazev", array(":no_club"=>$lang["no club"])),"klub_ID");
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["born"] . ": ");
$clovek_f->form[] = new cpf_db_textbox("narozen","narozen",array("maxlength"=>10));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("ulice a cislo: ");
$clovek_f->form[] = new cpf_db_textbox("ulice","ulice",array("class"=>'smartform-street-and-number'));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("mesto: ");
$clovek_f->form[] = new cpf_db_textbox("mesto","mesto",array("class"=>'smartform-city'));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("psc: ");
$clovek_f->form[] = new cpf_db_textbox("psc","psc",array("class"=>'smartform-zip'));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("prukaz: ");
$clovek_f->form[] = new cpf_db_listbox_array("prukaz",array('op' => 'obcansky prukaz', 'pas' => 'cestovni pas', 'jiny' => 'jiny'));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text("cislo: ");
$clovek_f->form[] = new cpf_db_textbox("cislo","cislo",array());
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["comment"] . ": ");
$clovek_f->form[] = new cpf_db_textarea("komentar","komentar",array("rows"=>4, "cols"=>60));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["active debater"] . ": ");
$clovek_f->form[] = new cpf_db_listbox_array("debater",array(1 => $lang['yes'], 0 => $lang['no']));
$clovek_f->form[] = new cpf_nl;
$clovek_f->form[] = new cpf_text($lang["full member"] . ": ");
if ($GLOBALS["cps_lidi"] >= 3) {
	// only admins can edit membership
	$clovek_f->form[] = new cpf_db_listbox_array("clen", array(1 => $lang['yes'], 0 => $lang['no']));
} else {
	$clovek_f->form[] = new cpf_db_text("clen", array(), array(1 => $lang['yes'], 0 => $lang['no']));
}
if ($GLOBALS["cps_lidi"] >= 3) {
	// only admins can edit membership
	$clovek_f->form[] = new cpf_nl;
    $clovek_f->form[] = new cpf_text($lang["member until"] . ": ");
    $clovek_f->form[] = new cpf_db_textbox("clen_do","clen_do",array("maxlength"=>4));
}
$clovek_f->form[] = new cpf_nl;

$clovek_f->form[] = new cpf_button("add",$lang["add"]);
$clovek_f->load_this(array("klub_ID"=>$klub_id, "debater"=>1, "clen"=>0, "clen_do"=>null));
$clovek_f->render();


?>