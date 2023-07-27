<script>
    $(document).ready(function() {
        window.viewer_modal = function($src = '') {
        start_loader()

        var t = $src.split('.')
        t = t[1]

        if (t =='mp4') {
            var view = $("<video src='"+$src+"' controls autoplay></video>")
        } else {
            var view = $("<img src='"+$src+"' />")
        }

        $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
        $('#viewer_modal .modal-content').append(view)
        $('#viewer_modal').modal({
                show:true,
                backdrop:'static',
                keyboard:false,
                focus:true
                })

                end_loader()  
    }
        window.uni_modal = function($title = '' , $url='',$size="") {
            start_loader()

            $.ajax({
                url:$url,
                error:err=> {
                    console.log()
                    alert("An error occured")
                },
                success:function(resp) {
                    if (resp) {
                        $('#uni_modal .modal-title').html($title)
                        $('#uni_modal .modal-body').html(resp)
                        if ($size != '') {
                            $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                        } else {
                            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                        }
                        $('#uni_modal').modal({
                        show:true,
                        backdrop:'static',
                        keyboard:false,
                        focus:true
                        })

                        end_loader()
                    }
                }
            })
        }
        window._conf = function($msg='',$func='',$params = []) {
            $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
            $('#confirm_modal .modal-body').html($msg)
            $('#confirm_modal').modal('show')
        }
    })
</script>

<footer class="main-footer text-sm">
        <strong>Copyright © <?= date('Y') ?>.</strong> Todos os direitos reservados
        <div class="float-right d-none d-sm-inline-block">
          <strong><?= $_settings->info('short_name') ?> (por: <a href="" target="blank">e-Maker Web</a>)</strong>
        </div>
      </footer>
    </div>
    
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- Bootstrap 4 -->
    <script src="<?= BASE_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- ChartJS -->
    <script src="<?= BASE_URL ?>plugins/chart.js/Chart.min.js"></script>

    <!-- Sparkline -->
    <script src="<?= BASE_URL ?>plugins/sparklines/sparkline.js"></script>

    <!-- Select2 -->
    <script src="<?= BASE_URL ?>plugins/select2/js/select2.full.min.js"></script>

    <!-- JQVMap -->
    <script src="<?= BASE_URL ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

    <!-- jQuery Knob Chart -->
    <script src="<?= BASE_URL ?>plugins/jquery-knob/jquery.knob.min.js"></script>

    <!-- daterangepicker -->
    <script src="<?= BASE_URL ?>plugins/moment/moment.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/daterangepicker/daterangepicker.js"></script>

    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= BASE_URL ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Summernote -->
    <script src="<?= BASE_URL ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= BASE_URL ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <!-- overlayScrollbars -->
    <script src="<?= BASE_URL ?>dist/js/adminlte.js"></script>

    <div class="daterangepicker ltr show-ranges opensright">
        <div class="ranges">
            <ul>
                <li data-range-key="Today">Hoje</li>
                <li data-range-key="Yesterday">Ontem</li>
                <li data-range-key="Last 7 Days">Úlltimos 07 dias</li>
                <li data-range-key="Last 30 Days">Úlltimos 30 dias</li>
                <li data-range-key="This Month">Este mês</li>
                <li data-range-key="Last Month">Último mês</li>
                <li data-range-key="Custom Range">Custom Range</li>
            </ul>
        </div>
        <div class="drp-calendar left">
            <div class="calendar-table"></div>
            <div class="calendar-time" style="display: none;"></div>
        </div>
        <div class="drp-calendar right">
            <div class="calendar-table"></div>
            <div class="calendar-time" style="display: none;"></div>
        </div>
        <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
<div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;">Sergipe</div>