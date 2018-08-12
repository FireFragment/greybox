<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example that shows off a responsive product landing page.">
    <title>Landing Page &ndash; Layout Examples &ndash; Pure</title>
    
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
        smartform.beforeInit = function () {
            smartform.setClientId('8ndPcVUJ5B');
        }
    </script>
</head>
<body>


<?php
    $home = "/greybox/registrace/";

    $page = $_REQUEST["p"];
?>




<header>
    <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="<?php echo $home; ?>">greybox</a>

        <ul class="pure-menu-list">
            <li class="pure-menu-item pure-menu-selected"><a href="<?php echo $home; ?>" class="pure-menu-link">Domů</a></li>
            <li class="pure-menu-item"><a href="?p=prihlaseni" class="pure-menu-link">Přihlášení</a></li>
        </ul>
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
            <a href="?p=registrace" class="pure-button ">Registrace</a>
            <a href="?p=prihlaseni" class="pure-button ">Přihlášení</a>
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
                <p>Pokud ještě nemáte účet, <a href="?p=registrace">zaregistrujte se</a>.</p>
                <form class="pure-form pure-form-aligned">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" placeholder="Email">
                        </div>

                        <div class="pure-control-group">
                            <label for="password">Heslo</label>
                            <input id="password" type="password" placeholder="Heslo">
                        </div>

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
                <form class="pure-form pure-form-aligned">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" placeholder="Email">
                        </div>

                        <div class="pure-control-group">
                            <label for="password">Heslo</label>
                            <input id="password" type="password" placeholder="Heslo">
                        </div>

                        <div class="pure-control-group">
                            <label for="password_confirmation">Zopakujte heslo</label>
                            <input id="password_confirmation" type="password" placeholder="Heslo">
                        </div>

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
    ?>
    <div class="content">
        <h2 class="content-head is-center">Zahájení XXIV. ročníku Debatní ligy</h2>
        <p>Babice, 21.-23. září 2018</p>


        <div class="pure-g">
            <div class="l-box pure-u-1 pure-u-md-1-4">
                <h3 class="content-subhead">Školení debatérů</h3>
                <a href="?p=skoleni-debateru" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-4">
                <h3 class="content-subhead">Školení rozhodčích</h3>
                <a href="?p=skoleni-rozhodcich" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-4">
                <h3 class="content-subhead">Turnaj Open Gate Open - tým</h3>
                <a href="?p=tym" class="pure-button">Přihlásit</a>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-4">
                <h3 class="content-subhead">Turnaj Open Gate Open - rozhodčí</h3>
                <a href="?p=rozhodci" class="pure-button">Přihlásit</a>
            </div>
        </div>
    </div>
    <?php
        }
    ?>


    <?php
        if ($page == "skoleni-debateru" or $page == "skoleni-rozhodcich" or $page == "rozhodci") {
            $head = "Přihláška ";
            switch ($page) {
                case "skoleni-debateru":
                    $head .= "na školení debatérů";
                    break;
                case "skoleni-rozhodcich":
                    $head .= "na školení rozhodčích";
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
                <form class="pure-form pure-form-aligned">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="name">Jméno</label>
                            <input id="name" type="text" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="surname">Příjmení</label>
                            <input id="surname" type="text" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="day">Datum narození</label>
                            <select id="day">
                                <?php
                                    for ($i = 1; $i <= 31; $i++) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                ?>
                            </select>
                            <select id="month">
                                <?php
                                    $months = ["leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec"];
                                    for ($i = 1; $i <= 12; $i++) {
                                        $j = $i-1;
                                        echo "<option value=\"$i\">$months[$j]</option>";
                                    }
                                ?>
                            </select>
                            <select id="year">
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
                            <input id="op" type="text" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="street">Ulice a číslo</label>
                            <input id="street" type="text" class="smartform-street-and-number" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="city">Město</label>
                            <input id="city" type="text" class="smartform-city" required>
                        </div>
                        <div class="pure-control-group">
                            <label for="zip">PSČ</label>
                            <input id="zip" type="text" class="smartform-zip" required>
                        </div>
                        <input id="event" type="hidden" value="page">

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

    <footer class="footer l-box is-center">
        2018 Asociace debatních klubů, z.s.
    </footer>

</div>




</body>
</html>
