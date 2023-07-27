<style>

  .user-img {
        position: absolute;
        height: 27px;
        width: 27px;
        object-fit: cover;
        left: -7%;
        top: -12%;
  }

  .btn-rounded {
        border-radius: 50px;
  }
</style>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark border border-light border-top-0  border-left-0 border-right-0 navbar-light text-sm bg-lightblue">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		</li>
		<li class="nav-item d-none d-sm-inline-block">
			<a href="<?= BASE_URL ?>" class="nav-link"><?= (!isMobileDevice()) ? $_settings->info('name'):$_settings->info('short_name'); ?> - Administrador</a>
		</li>
	</ul>
	<ul class="navbar-nav ml-auto">
		<li class="nav-item">
			<div class="btn-group nav-link">
				<button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
					<span><img src="<?= validateImage($_settings->userData('avatar')) ?>" class="img-circle elevation-2 user-img" alt="User Image"></span>
					<span class="ml-3"><?= ucwords($_settings->userData('firstname').' '.$_settings->userData('lastname')) ?></span>
					<span class="sr-only">Alternar Menu Suspenso</span>
				</button>
				<div class="dropdown-menu" role="menu">
					<a class="dropdown-item" href="<?= BASE_URL.'admin/?page=user' ?>"><span class="fa fa-user"></span> Minha Conta</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?= BASE_URL.'/classes/Login.php?f=logout' ?>"><span class="fas fa-sign-out-alt"></span> Sair</a>
				</div>
			</div>
		</li>
		<li class="nav-item"></li>
	</ul>
</nav>
