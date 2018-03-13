<?php
/*
 * greybox
 * $Id: teze.inc,v 1.13 2005/03/23 20:05:48 che0 Exp $
 * vypis vsech tezi
 */

if (isset($_REQUEST["teze_id"])) {
	include("teze.detaily.inc");
	return;
}

$lang_choice = (isset ($_REQUEST["jazyk"])) ? $_REQUEST["jazyk"] : "cz";
$category_choice = (isset ($_REQUEST["kategorie_id"])) ? $_REQUEST["kategorie_id"] : null;

pg_head();
if ($GLOBALS["cps_teze"] >= 1) cph_p(cphs_link("./?page=teze.add&jazyk=" . $lang_choice,$lang["add resolution"]));

if (is_null($category_choice)) {
	cph_switch($lang["language"], "./?page=teze&jazyk=%s", array("cz"=>"cz", "en"=>"en", "de"=>"de", "fr"=>"fr"), $lang_choice);
} else {
	cph_switch($lang["language"], "./?page=teze&jazyk=%s&kategorie_id=" . $category_choice, array("cz"=>"cz", "en"=>"en", "de"=>"de", "fr"=>"fr"), $lang_choice);
}

cph_p(cphs_link("./?page=teze.kategorie&jazyk=" . $lang_choice,$lang["choose category"]));

if (is_null($category_choice)) {
	cpdb_fetch_all("
		select
			teze.teze_ID,
			teze.tx,
			count(debata_ID) as debates
		from
			teze
			left join debata on teze.teze_ID = debata.teze_ID
		where
			jazyk = :lang
		group by
			teze.teze_ID,
			teze.tx
		order by
			teze.tx
		",
		array(":lang"=>$lang_choice),$result);
} else {
	if (!cpdb_fetch_one_row("select nazev from kategorie where kategorie_ID = :kategorie_id", array(":kategorie_id" => $category_choice), $kategorie_p)) {
		pg_achtung($lang["no record"]);
		return;
	}
	$kategorie = de_html_s($kategorie_p);
	cph_h2($kategorie["nazev"]);
	cpdb_fetch_all("
		select
			teze.teze_ID,
			teze.tx,
			count(debata_ID) as debates
		from
			teze
			left join debata on teze.teze_ID = debata.teze_ID
			left join kategorie_teze on teze.teze_ID = kategorie_teze.teze_ID
		where
			jazyk = :lang
		and
			kategorie_teze.kategorie_ID = :category
		group by
			teze.teze_ID,
			teze.tx
		order by
			teze.tx
		",
		array(":lang"=>$lang_choice,":category"=>$category_choice),$result);	
}

de_html($result);
cph_table_head(array($lang["resolution"],$lang["debates"]));
foreach ($result as $row) {
	cph_table_row(array(
		cphs_link("./?page=teze.detaily&teze_id=" . $row["teze_ID"], $row["tx"]),
		$row["debates"]
	));
}
cph_table_end();
?>
