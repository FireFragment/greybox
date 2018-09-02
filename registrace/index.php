<?php
    session_start();

    if (isset($_GET["lang"])) {
        $language = $_GET["lang"];
    } else {
        $language = "cz";
    }
    include("languages/$language.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $lang['description']; ?>">
    <title><?php echo $lang['title']; ?></title>
    
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
            <li class="pure-menu-item"><a href="?p=prihlaska" class="pure-menu-link"><?php echo $lang['home']; ?></a></li>
            <li class="pure-menu-item"><?php echo $lang['logged_in'] . ': ' . $_SESSION["email"]; ?></li>
            <li class="pure-menu-item"><a href="?p=odhlasit" class="pure-menu-link"><?php echo $lang['logout']; ?></a></li>
        </ul>
        <?php
            } else {
        ?>
        <a class="pure-menu-heading" href="<?php echo $home; ?>">greybox</a>

        <ul class="pure-menu-list">
            <li class="pure-menu-item"><a href="<?php echo $home; ?>" class="pure-menu-link"><?php echo $lang['home']; ?></a></li>
            <li class="pure-menu-item"><a href="?p=prihlaseni" class="pure-menu-link"><?php echo $lang['login']; ?></a></li>
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
        <h1 class="splash-head"><?php echo $lang['title']; ?></h1>
        <p class="splash-subhead">
            <?php echo $lang['login_to_continue']; ?>
        </p>
        <p>
            <a href="?p=registrace" class="pure-button pure-button-primary"><?php echo $lang['sign_up']; ?></a>
            <a href="?p=prihlaseni" class="pure-button pure-button-primary"><?php echo $lang['login']; ?></a>
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
        <h1 class="splash-head"><?php echo $lang['application_accepted']; ?></h1>
        <p class="splash-subhead">
            <?php echo $lang['apply_or_logout']; ?>
        </p>
        <p>
            <a href="?p=prihlaska" class="pure-button pure-button-primary"><?php echo $lang['apply_another']; ?></a>
            <a href="?p=odhlasit" class="pure-button pure-button-primary"><?php echo $lang['system_logout']; ?></a>
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
        <h2 class="content-head is-center"><?php echo $lang['login']; ?></h2>

        <div class="pure-g">
            <div class="pure-u-1 is-center">
                <p><?php echo $lang['missing_account']; ?> <a href="?p=registrace"><?php echo $lang['please_sign_up']; ?></a>. <?php echo $lang['original_credentials']; ?></p>
                <form class="pure-form pure-form-aligned" method="post">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email"><?php echo $lang['email']; ?></label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password"><?php echo $lang['password']; ?></label>
                            <input id="password" type="password" name="password" required>
                        </div>
                        <input type="hidden" name="action" value="login">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button"><?php echo $lang['login!']; ?></button>
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
        <h2 class="content-head is-center"><?php echo $lang['sign_up']; ?></h2>

        <div class="pure-g">
            <div class="pure-u-1 is-center">
                <form class="pure-form pure-form-aligned" method="post">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email"><?php echo $lang['email']; ?></label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password"><?php echo $lang['password']; ?></label>
                            <input id="password" type="password" name="password" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="password_confirmation"><?php echo $lang['password_repeat']; ?></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <input type="hidden" name="action" value="register">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button"><?php echo $lang['sign_up!']; ?></button>
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
        <h2 class="content-head is-center"><?php echo $lang['event']; ?></h2>
        <p><?php echo $lang['location']; ?>, <?php echo $lang['date']; ?></p>


        <div class="pure-g">
            <div class="l-box pure-u-1 pure-u-md-1-3">
                <h3 class="content-subhead"><?php echo $lang['team_application']; ?></h3>
                <p><?php echo $lang['team_application_details']; ?></p>
                <a href="?p=24-1-tym" class="pure-button"><?php echo $lang['apply']; ?></a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-3">
                <h3 class="content-subhead"><?php echo $lang['adjudicator_application']; ?></h3>
                <p><?php echo $lang['adjudicator_application_details']; ?></p>
                <a href="?p=24-1-rozhodci" class="pure-button"><?php echo $lang['apply']; ?></a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-3">
                <h3 class="content-subhead"><?php echo $lang['single_application']; ?></h3>
                <p><?php echo $lang['single_application_details']; ?></p>
                <a href="?p=24-1-jednotlivec" class="pure-button"><?php echo $lang['apply']; ?></a>
            </div>
        </div>
    </div>
    <?php
        }
    ?>


    <?php
        if ($page == "24-1-rozhodci" or $page == "24-1-jednotlivec") {
            if (!isset($_SESSION["token"])) {
                echo "<script> window.location.replace('$home'); </script>";
            }
            switch ($page) {
                case "24-1-rozhodci":
                    $head = $lang['adjudicator_application'];
                    break;
                case "24-1-jednotlivec":
                    $head = $lang['single_application'];
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
                            <label for="name"><?php echo $lang['name']; ?></label>
                            <input id="name" type="text" name="name" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="surname"><?php echo $lang['surname']; ?></label>
                            <input id="surname" type="text" name="surname" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="day"><?php echo $lang['birthdate']; ?></label>
                            <select id="day" name="day">
                                <?php
                                    for ($i = 1; $i <= 31; $i++) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                ?>
                            </select>
                            <select id="month" name="month">
                                <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $j = $i-1;
                                        echo "<option value=\"$i\">" . $lang['months'][$j] . "</option>";
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
                            <label for="op"><?php echo $lang['id_number']; ?></label>
                            <input id="op" type="text" name="op">
                        </div>
                        <div class="pure-control-group">
                            <label for="street"><?php echo $lang['street_number']; ?></label>
                            <input id="street" type="text" class="smartform-street-and-number" name="street" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="city"><?php echo $lang['city']; ?></label>
                            <input id="city" type="text" class="smartform-city" name="city" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="zip"><?php echo $lang['zip']; ?></label>
                            <input id="zip" type="text" class="smartform-zip" name="zip" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="comment"><?php echo $lang['note']; ?></label>
                            <textarea id="comment" type="text" name="note" placeholder="<?php echo $lang['note_example']; ?>"></textarea>
                        </div>
                        <input type="hidden" name="event" value="<?php echo $page; ?>">
                        <input type="hidden" name="action" value="application">

                        <div class="pure-controls">
                            <button type="submit" class="pure-button"><?php echo $lang['apply']; ?></button>
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
        if ($page == "24-1-tym") {
            if (!isset($_SESSION["token"])) {
                echo "<script> window.location.replace('$home'); </script>";
            }
    ?>
    <div class="content">
        <h2 class="content-head is-center"><?php echo $lang['apply']; ?></h2>

        <div class="pure-g">
            <form class="pure-form pure-form-stacked" method="post">
                <fieldset>
                    <div class="pure-u-1">
                        <label for="team-name"><?php echo $lang['team_name']; ?></label>
                        <input id="team-name" type="text" name="team-name" required>
                    </div>
                </fieldset>
                <div id="debater-line-1"></div>
                <div id="debater-line-2"></div>
                <div id="debater-line-3"></div>
                <div id="debater-line-4"></div>
                <div id="debater-line-5"></div>

                <script type="text/javascript">
                function loadDebaterLine(number, language) {
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
                                    document.getElementById("next-debater").setAttribute("onclick", "loadDebaterLine("+inc+",'"+language+"')");
                                } else {
                                    document.getElementById("next-debater").remove();
                                }
                                document.getElementById("remove-debater").setAttribute("onclick", "deleteDebaterLine("+number+",'"+language+"')");

                                smartform.rebindAllForms(true, null);
                            }
                        }
                    };

                    xmlhttp.open("GET", "team-form.php?number="+number+"&lang="+language, true);
                    xmlhttp.send();
                }

                function deleteDebaterLine(number, language) {
                    document.getElementById("debater-line-"+number).innerHTML = '';
                    dec = number - 1;
                    document.getElementById("remove-debater").setAttribute("onclick", "deleteDebaterLine("+dec+",'"+language+"')");
                    document.getElementById("next-debater").setAttribute("onclick", "loadDebaterLine("+number+",'"+language+"')");
                }
                </script>

                <input type="hidden" name="event" value="<?php echo $page; ?>">
                <input type="hidden" name="action" value="team-application">

                <div class="pure-controls">
                    <button type="button" id="next-debater" class="pure-button pure-button-primary" onclick="loadDebaterLine(1,'<?php echo $language; ?>');"><?php echo $lang['add_debater']; ?></button>
                    <button type="button" id="remove-debater" class="button-red pure-button" onclick="deleteDebaterLine(1,'<?php echo $language; ?>');" style="visibility: hidden;"><?php echo $lang['remove_debater']; ?></button>
                    <button type="submit" id="apply" class="pure-button" style="visibility: hidden;"><?php echo $lang['apply']; ?></button>
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
                    echo '<p class="is-center">' . $lang['user_exists'] . ' <a href="?p=prihlaseni">' . $lang['please_login'] . '</a>, ' . $lang['use_another'] . '.</p>';
                } elseif ($code == 422) {
                    if ($response["password"][0] == "The password confirmation does not match.") {
                        echo '<p class="is-center">' . $lang['password_mismatch'] . '</p>';
                    } elseif ($response["password"][0] == "The password format is invalid.") {
                        echo '<p class="is-center">' . $lang['wrong_format'] . '</p>';
                    } elseif ($response["password"][0] == "The password must be at least 8 characters.") {
                        echo '<p class="is-center">' . $lang['eight_characters'] . '</p>';
                    }
                }

            case 'login':
                if ($code == 200) {
                    $_SESSION["user_id"] = $response["id"];
                    $_SESSION["email"] = $response["username"];
                    $_SESSION["token"] = $response["api_token"];

                    echo "<script> window.location.replace('?p=prihlaska'); </script>";
                } elseif ($code == 401 or $code == 500) {
                    echo '<p class="is-center">' . $lang['wrong_credentials'] . '</p>';
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
