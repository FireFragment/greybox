<?php

function cpdb_achtung($achtung) {
	$achtung = "Database error: $achtung";
	if (isset($GLOBALS["page"])) {
		pg_achtung($achtung);
	} else {
		echo "<br />" . $achtung;
	}
}

function cpdb_bind($query, $args = array()) {
	$repl_from = $repl_to = array();
	foreach ($args as $key => $value) {
		$repl_from[] = $key;
		
		if (!is_int($value)) {
			if ($value == "" || is_null($value)) {
				$repl_to[] = "NULL";
			} else {
				$repl_to[] = "'" . mysqli_real_escape_string($GLOBALS["dblink"], $value) . "'";
			}
		} else {
			$repl_to[] = $value;
		}
	}
	
	// cpdb_achtung(str_replace($repl_from, $repl_to, $query));
	return str_replace($repl_from, $repl_to, $query);
}

function cpdb_fetch_one_value ($query, $args = array()) {
	$res = mysqli_query($GLOBALS["dblink"], cpdb_bind($query, $args));
	if ($res) {
		if ($row = mysqli_fetch_row($res)) {
			return $row[0];
		} else {
			return false;
		}
	} else {
		cpdb_achtung(mysqli_error());
		return false;
	}	
}

function cpdb_fetch_all ($query, $args, & $result, $primary_key = "__") {
	$result = array();
	$numres = 0;
	
	$my_res = mysqli_query($GLOBALS["dblink"], cpdb_bind($query, $args));
	if ($my_res) {
		while ($tres = mysqli_fetch_array($my_res)) {
			if ($primary_key == "__") {
				// normal behaviour
				$result[] = $tres;
			} else {
				// create associative array of rows
				$result[$tres[$primary_key]] = $tres;
			}
			$numres++;
		}
	} else {
		cpdb_achtung(mysqli_error($GLOBALS["dblink"]));
	}
	
	return $numres;
}

function cpdb_fetch_one_row ($query, $args, & $row) {
	$my_res = mysqli_query($GLOBALS["dblink"], cpdb_bind($query, $args));
	if (! $my_res) {
		cpdb_achtung(mysqli_error());
		return false;
	}
	if ($row = mysqli_fetch_array($my_res)) {
		if (mysqli_fetch_array($my_res)) {
			pg_achtung("Internal database fetch problem: unexpected rows");
		}
		return true;
	} else {
		$row = NULL;
		return false;
	}
	
}

function cpdb_exec($query, $args) {
	$my_res = mysqli_query($GLOBALS["dblink"], cpdb_bind($query, $args));
	if (!! $my_res) {
		return true;
	} else {
		cpdb_achtung(mysqli_error());
		return false;
	}
}


function cpdb_transaction() {
	return mysqli_query($GLOBALS["dblink"], "start transaction");
}

function cpdb_commit() {
	return mysqli_query($GLOBALS["dblink"], "commit");
}

function cpdb_rollback() {
	return mysqli_query($GLOBALS["dblink"], "rollback");
}

function cpdb_insert_id() {
	return mysqli_insert_id($GLOBALS["dblink"]);
}

?>
