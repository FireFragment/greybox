<?php
/*
 * greybox
 * $Id: kluby.inc,v 1.9 2007/03/06 15:44:08 che0 Exp $
 * print out club list
 */

pg_head();

if ($GLOBALS["cps_kluby"] >= 2) cph_p(cphs_link("./?page=klub.add",$lang['add club']));

if (! cpdb_fetch_all("
	select
		klub.klub_ID, nazev, misto, count(clovek_ID) as debateri, klub.prihlaska
	from
		klub
		left join clovek
			on clovek.klub_ID = klub.klub_ID
			and clovek.debater = 1
	group by
		klub_ID, nazev, misto
	order by misto, nazev
	", array(), $kluby)) {
	
	pg_achtung($lang["no record"]);
	return;
}

cpdb_fetch_all("
	select
		klub.klub_ID, count(clovek_ID) as clenove
	from
		klub
		left join clovek
			on clovek.klub_ID = klub.klub_ID
			and clovek.clen = 1
	group by
		klub_ID
	", array(), $klub_clenove, "klub_ID");


$head = array($lang["place"], $lang["club"],$lang["debaters"],$lang["full members"]);
if ($GLOBALS["cps_lidi"] >= 3) $head[] = $lang["application"];
cph_table_head($head);

de_html($kluby);
foreach ($kluby as $klub) {
	$detail = array
	(
		$klub["misto"],
		cphs_link("./?page=klub&klub_id=" . $klub['klub_ID'],$klub['nazev']),
		$klub["debateri"],
		$klub_clenove[$klub["klub_ID"]]["clenove"]
	);
	if ($GLOBALS["cps_lidi"] >= 3) $detail[] = $klub["prihlaska"] ? $lang["yes"] : $lang["no"];
	cph_table_row($detail);
}
cph_table_end();

?>
