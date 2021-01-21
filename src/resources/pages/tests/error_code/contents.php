<?PHP
    use DynamicalWeb\HTML;

    $response_code = (int)DynamicalWeb\Request::getDefinedDynamicParameters()["code"];
    \DynamicalWeb\Response::setResponseCode($response_code);
?>
<!doctype html>
<html lang="<?PHP HTML::print(APP_LANGUAGE_ISO_639); ?>">
    <head>
        <?PHP HTML::importSection('header'); ?>
        <title>Dynamical Parameters Example</title>
    </head>

    <body>

        <header>
            <?PHP HTML::importSection('navigation'); ?>
        </header>

        <main role="main" class="container">
            <h1 class="mt-5"><?PHP HTML::print($response_code . " Response page"); ?></h1>
            <p class="lead">Change the response code via the URL!</p>
        </main>

        <?PHP HTML::importSection('footer'); ?>
        <?PHP HTML::importSection('js_scripts'); ?>

    </body>
</html>
