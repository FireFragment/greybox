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
            Zahájení XXIV. ročníku Debatní ligy
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



    <!--
    <div class="content">
        <h2 class="content-head is-center">Excepteur sint occaecat cupidatat.</h2>

        <div class="pure-g">
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">

                <h3 class="content-subhead">
                    <i class="fa fa-rocket"></i>
                    Get Started Quickly
                </h3>
                <p>
                    Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                </p>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                <h3 class="content-subhead">
                    <i class="fa fa-mobile"></i>
                    Responsive Layouts
                </h3>
                <p>
                    Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                </p>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                <h3 class="content-subhead">
                    <i class="fa fa-th-large"></i>
                    Modular
                </h3>
                <p>
                    Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                </p>
            </div>
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                <h3 class="content-subhead">
                    <i class="fa fa-check-square-o"></i>
                    Plays Nice
                </h3>
                <p>
                    Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                </p>
            </div>
        </div>
    </div>
    -->


    <footer class="footer l-box is-center">
        2018 Asociace debatních klubů, z.s.
    </footer>

</div>




</body>
</html>
