<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="<?=$assetDir?>/../../../../frontend/web/images/Icon_Projeto.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Produtos Ginásio</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [
                    ['label' => 'Yii2 PROVIDED', 'header' => true],
                    ['label' => 'Gii',  'icon' => 'file-code', 'url' => ['/gii'], 'target' => '_blank'],
                    ['label' => 'Users',  'icon' => 'file-code', 'url' => ['/user/index'], 'target' => '_blank'],
                    ['label' => 'Profiles',  'icon' => 'file-code', 'url' => ['/profile/index'], 'target' => '_blank'],
                    ['label' => 'Produtos', 'header' => true],
                    [
                        'label' => 'Ver Produtos',
                        'items' => [
                            ['label' => 'Produtos', 'icon' => 'file-code', 'url' => ['/produto/index'], 'target' => '_blank'],
                            ['label' => 'Categorias', 'icon' => 'file-code', 'url' => ['/categoria/index'], 'target' => '_blank'],
                            ['label' => 'Marcas', 'icon' => 'file-code', 'url' => ['/marca/index'], 'target' => '_blank'],
                            ['label' => 'Géneros', 'icon' => 'file-code', 'url' => ['/genero/index'], 'target' => '_blank'],
                            ['label' => 'Ivas', 'icon' => 'file-code', 'url' => ['/iva/index'], 'target' => '_blank'],
                            ['label' => 'Tamanhos', 'icon' => 'file-code', 'url' => ['/tamanho/index'], 'target' => '_blank'],
                            ['label' => 'Avaliações', 'icon' => 'file-code', 'url' => ['/avaliacoes/index'], 'target' => '_blank'],
                        ],
                    ],
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>