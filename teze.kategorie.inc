<?php

pg_head();

$jazyk = $_REQUEST["jazyk"];

cpdb_fetch_all("
	select
		kategorie.kategorie_ID,
		kategorie.nazev,
		count(teze_ID) as motions
	from
		kategorie
		left join kategorie_teze on kategorie.kategorie_ID = kategorie_teze.kategorie_ID
	group by
		kategorie.kategorie_ID
	",array(),$result);

de_html($result);
cph_table_head(array($lang["category"],$lang["resolutions"]));
cph_table_row(array(
	cphs_link("./?page=teze&jazyk=" . $jazyk, $lang["show all"]),
	"n/a"
));
foreach ($result as $row) {
	cph_table_row(array(
		cphs_link("./?page=teze&jazyk=" . $jazyk . "&kategorie_id=" . $row["kategorie_ID"], $row["nazev"]),
		$row["motions"]
	));
}
cph_table_end();

?>