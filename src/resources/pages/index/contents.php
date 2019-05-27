<?PHP \DynamicalWeb\Runtime::import('Example'); ?>
<!doctype html>
<html lang="<?PHP \DynamicalWeb\HTML::print(APP_LANGUAGE_ISO_639); ?>">
    <head>
        <?PHP \DynamicalWeb\HTML::importSection('header'); ?>
        <title><?PHP \DynamicalWeb\HTML::print(TEXT_PAGE_TITLE); ?></title>
    </head>

    <body>

        <header>
            <?PHP \DynamicalWeb\HTML::importSection('navigation'); ?>
        </header>

        <main role="main" class="container">
            <h1 class="mt-5"><?PHP \DynamicalWeb\HTML::print(TEXT_HEADER); ?></h1>
            <p class="lead"><?PHP \DynamicalWeb\HTML::print(TEXT_CONTENT); ?></p>

            <hr/>
            <?PHP \DynamicalWeb\HTML::importMarkdown('example'); ?>
            <?PHP
                $ExampleLibrary = new \Example\ExampleLibrary();
                $ExampleLibrary->getPrintFunctions()->SayName('John Smith');
                $ExampleLibrary->getPrintFunctions()->sayAge(12);
            ?>
        </main>

        <?PHP \DynamicalWeb\HTML::importSection('footer'); ?>

        <?PHP \DynamicalWeb\HTML::importSection('js_scripts'); ?>

    </body>
</html>
