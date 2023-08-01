</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <a href="<?= BASE_URL ?>admin" class="brand-link bg-primary text-sm">
        <img src="<?= validateImage($_settings->info('logo'))?>" alt="Store Logo" class="brand-image img-circle elevation-3 bg-black" style="width: 1.8rem;height: 1.8rem;max-height: unset">
        <span class="brand-text font-weight-light"><?= $_settings->info('short_name') ?></span>
    </a>
    <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
        <div class="os-resize-observer-host observed">
            <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
        </div>
        <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
            <div class="os-resize-observer"></div>
        </div>
        <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
        <div class="os-padding">
            <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
                <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
                    <div class="clearfix"></div>
                    <nav class="mt-4">
                        <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item dropdown">
                                <a href="./" class="nav-link nav-home">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Paínel</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=purchase_order" class="nav-link nav-purchase_order">
                                    <i class="nav-icon fas fa-th-list"></i>
                                    <p>Pedido de Compra</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=receiving" class="nav-link nav-receiving">
                                    <i class="nav-icon fas fa-boxes"></i>
                                    <p>Recebimento</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=back_order" class="nav-link nav-back_order">
                                    <i class="nav-icon fas fa-exchange-alt"></i>
                                    <p>Pedido Pendente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=return" class="nav-link nav-return">
                                    <i class="nav-icon fas fa-undo"></i>
                                    <p>Retorno</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=stocks" class="nav-link nav-stocks">
                                    <i class="nav-icon fas fa-table"></i>
                                    <p>Estoque</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= BASE_URL ?>admin/?page=sales" class="nav-link nav-sales">
                                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    <p>Vendas</p>
                                </a>
                            </li>

                            <?php if ($_settings->userData('type') == 1): ?>
                                <li class="nav-header">Manutenção</li>
                                <li class="nav-item dropdown">
                                    <a href="<?= BASE_URL ?>admin/?page=maintenance/supplier" class="nav-link nav-maintenance_supplier">
                                        <i class="nav-icon fas fa-truck-loading"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="<?= BASE_URL ?>admin/?page=maintenance/item" class="nav-link nav-maintenance_item">
                                        <i class="nav-icon fas fa-boxes"></i>
                                        <p>Item</p>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="<?= BASE_URL ?>admin/?page=user/list" class="nav-link nav-user_list">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="<?= BASE_URL ?>admin/?page=system_info" class="nav-link nav-system_info">
                                        <i class="nav-icon fas fa-cogs"></i>
                                        <p>Configurações</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>  
                </div>
            </div>
        </div>
        <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
                <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
            </div>
        </div>
        <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
                <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
            </div>
        </div>
        <div class="os-scrollbar-corner"></div>
    </div>    
</aside>
<script>
    var page;

    $(document).ready(function() {
    page = '<?= isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    page = page.replace(/\//gi,'_');

    if ($('.nav-link.nav-'+page).length > 0) {
        $('.nav-link.nav-'+page).addClass('active')

        if ($('.nav-link.nav-'+page).hasClass('tree-item') == true) {
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
            $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }

        if ($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true) {
            $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }
    }

    $('#receive-nav').click(function() {
    $('#uni_modal').on('shown.bs.modal',function() {
        $('#find-transaction [name="tracking_code"]').focus();
    })
        uni_modal("Enter Tracking Number","transaction/find_transaction.php");
        
        })
    })
</script>