<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">

    <img style="max-width:24px; margin-top: 2px; margin-right: 5px;" src="/assets/images/logo.svg">

    <a class="navbar-brand" href="#">
        <?PHP \DynamicalWeb\HTML::print(TEXT_NAVBAR_BRAND); ?>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?PHP if(APP_CURRENT_PAGE == 'index'){ \DynamicalWeb\HTML::print("active"); } ?>">
                <a class="nav-link" href="#"><?PHP \DynamicalWeb\HTML::print(TEXT_NAVBAR_PAGE_HOME); ?></a>
            </li>
            <li class="nav-item <?PHP if(APP_CURRENT_PAGE == 'example'){ \DynamicalWeb\HTML::print("active"); } ?>">
                <a class="nav-link" href="#"><?PHP \DynamicalWeb\HTML::print(TEXT_NAVBAR_PAGE_EXAMPLE); ?></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?PHP \DynamicalWeb\HTML::print(TEXT_NAVBAR_LANGUAGE_DROPDOWN); ?></a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="#">English</a>
                    <a class="dropdown-item" href="#">中文</a>
                </div>
            </li>
        </ul>
    </div>

</nav>