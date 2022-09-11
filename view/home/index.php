<?php 
    namespace core;
    use function core\{isRol};
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="<?= Route::get('home.index') ?>"><img src="<?= Session::get('icon') ?>" width="35"><?= PROYECT_NAME ?></a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" hidden>
        <div class="input-group">
            <!--<input class="form-control" type="text" placeholder="..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>-->
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?= Route::get('home.setting') ?>">Opciones</a></li>
                <?php if (isRol()) : ?>
                    <li><a class="dropdown-item" href="<?= Route::get('home.activity') ?>">Actividades</a></li>
                <?php endif; ?>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li><a class="dropdown-item" href="<?= Route::get('login.logout') ?>">Cerrar Sesi&oacute;n</a></li>
            </ul>
        </li>
    </ul>
</nav>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link <?=Route::isCurrentView('home.index', 'bg-white text-black', '')?>" href="<?= Route::get('home.index') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt <?=Route::isCurrentView('home.index', 'text-black', '')?>"></i></div>
                        Inicio
                    </a>
                    <a class="nav-link <?=Route::isCurrentView('currentwar.index', 'bg-white text-black', '')?>" href="<?= Route::get('currentwar.index') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-dragon <?=Route::isCurrentView('currentwar.index', 'text-black', '')?>"></i></div>
                        Guerra Actual
                    </a>
                    <a class="nav-link <?=Route::isCurrentView('warlog.index', 'bg-white text-black', '')?>" href="<?= Route::get('warlog.index') ?>">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-book-open <?=Route::isCurrentView('warlog.index', 'text-black', '')?>"></i>
                        </div>
                        Registro de Guerra
                    </a>
                    <?php if (isRol()) : ?>
                        <a class="nav-link <?=Route::isCurrentView('user.index', 'bg-white text-black', '')?>" href="<?= Route::get('user.index') ?>">
                            <div class="sb-nav-link-icon">
                                <i class="fas fa-user <?=Route::isCurrentView('user.index', 'text-black', '')?>"></i>
                            </div>
                            Usuarios
                        </a>
                    <?php endif; ?>
                    <div class="sb-sidenav-menu-heading">Interface</div>
                    <a class="nav-link <?=Route::isCurrentView(['list.war', 'list.break', 'list.wait'], 'text-white', '')?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="<?=Route::isCurrentView(['list.war', 'list.break', 'list.wait'])?'true':'false'?>" aria-controls="collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Listas
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?=Route::isCurrentView(['list.war', 'list.break', 'list.wait'], 'show', '')?>" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?=Route::isCurrentView('list.war', 'bg-white text-black', '')?>" href="<?= Route::get('list.war') ?>">Lista de Guerra</a>
                            <a class="nav-link <?=Route::isCurrentView('list.break', 'bg-white text-black', '')?>" href="<?= Route::get('list.break') ?>">Lista de Descanso</a>
                            <a class="nav-link <?=Route::isCurrentView('list.wait', 'bg-white text-black', '')?>" href="<?= Route::get('list.wait') ?>">Lista de Espera</a>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logueado como:</div>
                <span style="font-size: 10px;">
                    <span class="text-white text-uppercase">
                        <?= Session::getUser('username') ?>
                    </span>
                    <span>(<?= ucfirst(Route::ROL[Session::getRol()]) ?>)</span>
                </span>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>{!!body!!}</main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; <?= PROYECT_NAME ?> 2022</div>
                    <!--<div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>-->
                </div>
            </div>
        </footer>
    </div>
</div>
<div id="loader" hidden></div>