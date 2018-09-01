<?php
    session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Přihlašování na akce pořádané Asociací debatních klubů, z.s.">
    <title>Přihlašování na turnaje Debatní ligy</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
    
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-old-ie-min.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
    <!--<![endif]-->
    
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    
        <!--[if lte IE 8]>
            <link rel="stylesheet" href="css/style-old-ie.css">
        <![endif]-->
        <!--[if gt IE 8]><!-->
            <link rel="stylesheet" href="css/style.css">
        <!--<![endif]-->

    <script type="text/javascript" src="https://secure.smartform.cz/api/v1/smartform.js" async></script>
    <script type="text/javascript">
        var smartform = smartform || {};
        smartform.beforeInit = function initialize() {
            smartform.setClientId('8ndPcVUJ5B');
        }
    </script>
</head>
<body>


<?php
    $home = "/greybox/registrace/";
    $url = "localhost:8000/api/";

    $page = $_REQUEST["p"];

    if ($page == "odhlasit") {
        $ch = curl_init();

        $urlFinal = $url."logout";
        $data = array("api_token" => $_SESSION["token"]);

        curl_setopt($ch, CURLOPT_URL, $urlFinal);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);

        if ($code == 200) {
            session_unset();
            session_destroy();
            unset($page);
        }
    }
?>




<header>
    <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <?php
            if (isset($_SESSION["token"])) {
        ?>
        <a class="pure-menu-heading" href="?p=prihlaska">greybox</a>

        <ul class="pure-menu-list">
            <li class="pure-menu-item"><a href="?p=prihlaska" class="pure-menu-link">Domů</a></li>
            <li class="pure-menu-item">přihlášen/a: <?php echo $_SESSION["email"]; ?></li>
            <li class="pure-menu-item"><a href="?p=odhlasit" class="pure-menu-link">Odhlásit</a></li>
        </ul>
        <?php
            } else {
        ?>
        <a class="pure-menu-heading" href="<?php echo $home; ?>">greybox</a>

        <ul class="pure-menu-list">
            <li class="pure-menu-item"><a href="<?php echo $home; ?>" class="pure-menu-link">Domů</a></li>
            <li class="pure-menu-item"><a href="?p=prihlaseni" class="pure-menu-link">Přihlášení</a></li>
        </ul>
        <?php   
            }
        ?>
    </div>
</header>

<?php
    if (empty($page)) {
?>
<div class="splash-container">
    <div class="splash">
        <h1 class="splash-head">Registrace</h1>
        <p class="splash-subhead">
            Pro pokračování se musíte přihlásit.
        </p>
        <p>
            <a href="?p=registrace" class="pure-button pure-button-primary">Registrace</a>
            <a href="?p=prihlaseni" class="pure-button pure-button-primary">Přihlášení</a>
        </p>
    </div>
</div>
<?php
    }
?>

<?php
    if ($page == "prijata") {
        if (!isset($_SESSION["token"])) {
            echo "<script> window.location.replace('$home'); </script>";
        }
?>
<div class="splash-container">
    <div class="splash">
        <h1 class="splash-head">Přihláška přijata</h1>
        <p class="splash-subhead">
            Nyní můžete přihlásit další osobu, nebo se odhlásit ze systému.
        </p>
        <p>
            <a href="?p=prihlaska" class="pure-button pure-button-primary">Přihlásit další osobu</a>
            <a href="?p=odhlasit" class="pure-button pure-button-primary">Odhlásit ze systému</a>
        </p>
    </div>
</div>
<?php
    }
?>

<div class="content-wrapper">

    <?php
        if ($page == "prihlaseni") {
    ?>
    <div class="content">
        <h2 class="content-head is-center">Přihlášení</h2>

        <div class="pure-g">
            <div class="pure-u-1 is-center">
                <p>Pokud ještě nemáte účet, <a href="?p=registrace">zaregistrujte se</a>. Přihlašovací údaje k původní aplikaci greybox zde nefungují a je třeba se znovu zaregistrovat.</p>
                <form class="pure-form pure-form-aligned" method="post">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password">Heslo</label>
                            <input id="password" type="password" name="password" required>
                        </div>
                        <input type="hidden" name="action" value="login">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button">Přihlásit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

    </div>
    <?php
        }
    ?>


    <?php
        if ($page == "registrace") {
    ?>
    <div class="content">
        <h2 class="content-head is-center">Registrace</h2>

        <div class="pure-g">
            <div class="pure-u-1 is-center">
                <form class="pure-form pure-form-aligned" method="post">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password">Heslo</label>
                            <input id="password" type="password" name="password" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password_confirmation">Zopakujte heslo</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <input type="hidden" name="action" value="register">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button">Registrovat</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

    </div>
    <?php
        }
    ?>


    <?php
        if ($page == "prihlaska") {
            if (!isset($_SESSION["token"])) {
                echo "<script> window.location.replace('$home'); </script>";
            }
    ?>
    <div class="content">
        <h2 class="content-head is-center">Zahájení XXIV. ročníku Debatní ligy</h2>
        <p>Babice, 21.-23. září 2018</p>


        <div class="pure-g">
            <div class="l-box pure-u-1 pure-u-md-1-5">
                <h3 class="content-subhead">Školení debatérů</h3>
                <p>Je jedinečnou příležitostí si pod vedením metodiků Ředitelství soutěží osvojit nové debatní schopnosti a dovednosti. Začínající debatéry čekají celkem čtyři školící bloky a dvě debaty, v nichž budou moci otestovat své nově nabyté zkušenosti.</p>
                <a href="?p=skoleni-debateru" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-5">
                <h3 class="content-subhead">Školení rozhodčích</h3>
                <p>Je otevřeno všem zájemcům, kteří by chtěli rozšířit řady akreditovaných rozhodčích, jedinou podmínkou je dosažení 18. roku věku v této debatní sezóně, tj. do 28. dubna 2019. Předchozí zkušenosti s formou KPDP ani akademickým debatováním nejsou nutné, v pátek proběhne jednotící seminář s úvodem do formy KPDP a ukázkovou debatou.</p>
                <a href="?p=skoleni-rozhodcich" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-5">
                <h3 class="content-subhead">Školení koučů</h3>
                <p>Letošní novinkou je školení koučů zaměřené především na učitele a všechny ostatní, kteří by se rádi dozvěděli, jak vypadá debatování, jak vést debatní klub nebo k čemu může debatování pomoct ve výuce. Budete moct shlédnout reálnou debatu a také Vás seznámíme s fungováním ADK i možnostmi, které nabízíme.</p>
                <a href="?p=skoleni-koucu" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-5">
                <h3 class="content-subhead">Turnaj Open Gate Open - tým</h3>
                <p>Proběhnou čtyři kola turnaje a dva bloky školení, které se budou věnovat nejen debatní teorii ale i přípravě na teze a okruhy XXIV. ročníku Debatní ligy.</p>
                <a href="?p=tym" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-5">
                <h3 class="content-subhead">Turnaj Open Gate Open - rozhodčí</h3>
                <p>Proběhnou čtyři kola turnaje a dva bloky školení, které se budou věnovat nejen debatní teorii ale i přípravě na teze a okruhy XXIV. ročníku Debatní ligy.</p>
                <a href="?p=rozhodci" class="pure-button">Přihlásit</a>
            </div>
        </div>
    </div>
    <?php
        }
    ?>


    <?php
        if ($page == "skoleni-debateru" or $page == "skoleni-rozhodcich" or $page == "skoleni-koucu" or $page == "rozhodci") {
            if (!isset($_SESSION["token"])) {
                echo "<script> window.location.replace('$home'); </script>";
            }
            $head = "Přihláška ";
            switch ($page) {
                case "skoleni-debateru":
                    $head .= "na školení debatérů";
                    break;
                case "skoleni-rozhodcich":
                    $head .= "na školení rozhodčích";
                    break;
                case "skoleni-koucu":
                    $head .= "na školení koučů";
                    break;
                case "rozhodci":
                    $head .= "rozhodčích";
                    break;
            }
    ?>
    <div class="content">
        <h2 class="content-head is-center"><?php echo $head; ?></h2>

        <div class="pure-g">
            <div class="pure-u-1 is-center">
                <form class="pure-form pure-form-aligned" method="post">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="name">Jméno</label>
                            <input id="name" type="text" name="name" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="surname">Příjmení</label>
                            <input id="surname" type="text" name="surname" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="day">Datum narození</label>
                            <select id="day" name="day">
                                <?php
                                    for ($i = 1; $i <= 31; $i++) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                ?>
                            </select>
                            <select id="month" name="month">
                                <?php
                                    $months = ["leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec"];
                                    for ($i = 1; $i <= 12; $i++) {
                                        $j = $i-1;
                                        echo "<option value=\"$i\">$months[$j]</option>";
                                    }
                                ?>
                            </select>
                            <select id="year" name="year">
                                <?php
                                    for ($i = 0; $i <= 99; $i++) {
                                        $j = 2018-$i;
                                        echo "<option value=\"$j\">$j</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="pure-control-group">
                            <label for="op">Číslo občanského průkazu</label>
                            <input id="op" type="text" name="op">
                        </div>
                        <div class="pure-control-group">
                            <label for="street">Ulice a číslo</label>
                            <input id="street" type="text" class="smartform-street-and-number" name="street" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="city">Město</label>
                            <input id="city" type="text" class="smartform-city" name="city" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="zip">PSČ</label>
                            <input id="zip" type="text" class="smartform-zip" name="zip" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="comment">Poznámka</label>
                            <textarea id="comment" type="text" name="note" placeholder="například čas příjezdu/odjezdu, stravovací omezení apod."></textarea>
                        </div>
                        <input type="hidden" name="event" value="<?php echo $page; ?>">
                        <input type="hidden" name="action" value="application">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button">Přihlásit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <?php
        }
    ?>

    <?php
        if ($page == "tym") {
            if (!isset($_SESSION["token"])) {
                echo "<script> window.location.replace('$home'); </script>";
            }
    ?>
    <div class="content">
        <h2 class="content-head is-center">Přihláška týmů</h2>

        <div class="pure-g">
            <form class="pure-form pure-form-stacked" method="post">
                <fieldset>
                    <div class="pure-u-1">
                        <label for="team-name">Název týmu</label>
                        <input id="team-name" type="text" name="team-name" required>
                    </div>
                </fieldset>
                <div id="debater-line-1"></div>
                <div id="debater-line-2"></div>
                <div id="debater-line-3"></div>
                <div id="debater-line-4"></div>
                <div id="debater-line-5"></div>

                <script type="text/javascript">
                function loadDebaterLine(number) {
                    var xmlhttp = new XMLHttpRequest();

                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
                            if (xmlhttp.status == 200) {
                                document.getElementById("debater-line-"+number).innerHTML = xmlhttp.responseText;
                                if (number < 5) {
                                    if (number == 1) {
                                        document.getElementById("remove-debater").removeAttribute("style");
                                        document.getElementById("apply").removeAttribute("style");
                                    }
                                    inc = number + 1;
                                    document.getElementById("next-debater").setAttribute("onclick", "loadDebaterLine("+inc+")");
                                } else {
                                    document.getElementById("next-debater").remove();
                                }
                                document.getElementById("remove-debater").setAttribute("onclick", "deleteDebaterLine("+number+")");

                                smartform.rebindAllForms(true, null);
                            }
                        }
                    };

                    xmlhttp.open("GET", "team-form.php?number="+number, true);
                    xmlhttp.send();
                }

                function deleteDebaterLine(number) {
                    document.getElementById("debater-line-"+number).innerHTML = '';
                    dec = number - 1;
                    document.getElementById("remove-debater").setAttribute("onclick", "deleteDebaterLine("+dec+")");
                    document.getElementById("next-debater").setAttribute("onclick", "loadDebaterLine("+number+")");
                }
                </script>

                <input type="hidden" name="event" value="<?php echo $page; ?>">
                <input type="hidden" name="action" value="team-application">

                <div class="pure-controls">
                    <button type="button" id="next-debater" class="pure-button pure-button-primary" onclick="loadDebaterLine(1);">Přidat debatéra</button>
                    <button type="button" id="remove-debater" class="button-red pure-button" onclick="deleteDebaterLine(1);" style="visibility: hidden;">Odebrat posledního debatéra</button>
                    <button type="submit" id="apply" class="pure-button" style="visibility: hidden;">Přihlásit</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        }
    ?>

<?php
    if (isset($_POST["action"])) {
        $ch = curl_init();

        switch ($_POST["action"]) {
            case 'register':
                $urlFinal = $url."user";
                $data = array(
                    "username" => $_POST["email"],
                    "password" => $_POST["password"],
                    "password_confirmation" => $_POST["password_confirmation"],
                    "person_id" => null
                );
                break;

            case 'login':
                $urlFinal = $url."login";
                $data = array(
                    "username" => $_POST["email"],
                    "password" => $_POST["password"]
                );
                break;

            case 'application':
                $urlFinal = $url."registration";
                $data = array(
                    "api_token" => $_SESSION["token"],
                    "name" => $_POST["name"],
                    "surname" => $_POST["surname"],
                    "birthdate" => $_POST["year"]."-".$_POST["month"]."-".$_POST["day"],
                    "id_number" => $_POST["op"],
                    "street" => $_POST["street"],
                    "city" => $_POST["city"],
                    "zip" => $_POST["zip"],
                    "note" => !empty($_POST["note"]) ? $_POST["note"] : null,
                    "event" => $_POST["event"]
                );               
                break;

            case 'team-application':
                $urlFinal = $url."team";
                $data = array (
                    "api_token" => $_SESSION["token"],
                    "name" => $_POST["team-name"],
                    "event" => $_POST["event"]
                );
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $urlFinal);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);


        switch ($_POST["action"]) {
            case 'register':
                if ($code == 201) {
                    $ch = curl_init();

                    $data = array(
                        "username" => $_POST["email"],
                        "password" => $_POST["password"]
                    );

                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_URL, $url."login");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $response = json_decode(curl_exec($ch), true);
                    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

                    curl_close($ch);
                } elseif ($code == 409) {
                    echo '<p class="is-center">Uživatel s tímto emailem již existuje. <a href="?p=prihlaseni">Přihlaste se</a>, nebo použijte jiný.</p>';
                } elseif ($code == 422) {
                    if ($response["password"][0] == "The password confirmation does not match.") {
                        echo '<p class="is-center">Zadaná hesla se neshodují.</p>';
                    } elseif ($response["password"][0] == "The password format is invalid.") {
                        echo '<p class="is-center">Heslo musí obsahovat alespoň tři z následujících kategorií malá písmena, velká písmena, čísla a znaky.</p>';
                    } elseif ($response["password"][0] == "The password must be at least 8 characters.") {
                        echo '<p class="is-center">Heslo musí obsahovat alespoň 8 znaků.</p>';
                    }
                }

            case 'login':
                if ($code == 200) {
                    $_SESSION["user_id"] = $response["id"];
                    $_SESSION["email"] = $response["username"];
                    $_SESSION["token"] = $response["api_token"];

                    echo "<script> window.location.replace('?p=prihlaska'); </script>";
                } elseif ($code == 401 or $code == 500) {
                    echo '<p class="is-center">Nesprávný email nebo heslo.</p>';
                }
                break;

            case 'application':
                if ($code == 201) {
                    echo "<script> window.location.replace('?p=prijata'); </script>";
                }
                break;

            case 'team-application':
                if ($code == 201) {
                    $i = 1;
                    $team = $response["id"];

                    while (isset($_POST["name-$i"])) {
                        $ch = curl_init();

                        $data = array(
                            "api_token" => $_SESSION["token"],
                            "name" => $_POST["name-$i"],
                            "surname" => $_POST["surname-$i"],
                            "birthdate" => $_POST["year-$i"]."-".$_POST["month-$i"]."-".$_POST["day-$i"],
                            "id_number" => $_POST["op-$i"],
                            "street" => $_POST["street-$i"],
                            "city" => $_POST["city-$i"],
                            "zip" => $_POST["zip-$i"],
                            "note" => !empty($_POST["note-$i"]) ? $_POST["note-$i"] : null,
                            "event" => $_POST["event"],
                            "team" => $team
                        );

                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($ch, CURLOPT_URL, $url."registration");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $debater = json_decode(curl_exec($ch), true);
                        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

                        curl_close($ch);

                        $i++;
                    }

                    if ($code == 201) {
                        echo "<script> window.location.replace('?p=prijata'); </script>";
                    }
                }

                break;
            default:
                # code...
                break;
        }
    }
?>

    <footer class="footer l-box is-center">
        2018 Asociace debatních klubů, z.s.
    </footer>

</div>


</body>
</html>
