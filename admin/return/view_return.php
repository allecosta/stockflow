<?php 

$query = $conn->query("
    SELECT 
        r.*,s.name AS supplier 
    FROM 
        return_list r 
    INNER JOIN 
        supplier_list s ON r.supplier_id = s.id  
    WHERE 
        r.id = '{$_GET['id']}'
");

if ($query->num_rows > 0) {
    foreach ($query->fetch_array() as $key => $value) {
        $$key = $value;
    }
}

?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Registro de Devolução - <?= $return_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Cod. Devolução</label>
                    <div><?= isset($return_code) ? $return_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                        <div><?= isset($supplier) ? $supplier : '' ?></div>
                    </div>
                </div>
            </div>
            <h4 class="text-info">Items</h4>
            <table class="table table-striped table-bordered" id="list">
                <colgroup>
                    <col width="10%">
                    <col width="10%">
                    <col width="30%">
                    <col width="25%">
                    <col width="25%">
                </colgroup>
                <thead>
                    <tr class="text-light bg-navy">
                        <th class="text-center py-1 px-2">Quantidade</th>
                        <th class="text-center py-1 px-2">Unidade</th>
                        <th class="text-center py-1 px-2">Item</th>
                        <th class="text-center py-1 px-2">Custo</th>
                        <th class="text-center py-1 px-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    $query = $conn->query("
                        SELECT 
                            s.*,i.name,i.description 
                        FROM 
                            `stock_list` s 
                        INNER JOIN 
                            item_list i ON s.item_id = i.id 
                        WHERE 
                            s.id in ({$stockIds})
                    ");

                    while ($row = $query->fetch_assoc()):
                        $total += $row['total']
                    ?>
                        <tr>
                            <td class="py-1 px-2 text-center"><?= number_format($row['quantity']) ?></td>
                            <td class="py-1 px-2 text-center"><?= ($row['unit']) ?></td>
                            <td class="py-1 px-2">
                                <?= $row['name'] ?> <br>
                                <?= $row['description'] ?>
                            </td>
                            <td class="py-1 px-2 text-right"><?= number_format($row['price']) ?></td>
                            <td class="py-1 px-2 text-right"><?= number_format($row['total']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Total</th>
                        <th 
                            class="text-right py-1 px-2 grand-total">
                            <?= isset($amount) ? number_format($amount,2) : 0 ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Devolução</label>
                        <p><?= isset($remarks) ? $remarks : '' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Imprimir</button>
        <a 
            class="btn btn-flat btn-primary" 
            href="<?= BASE_URL .'/admin?page=return/manage_return&id='.(isset($id) ? $id : '') ?>">Editar
        </a>
        <a class="btn btn-flat btn-dark" href="<?= BASE_URL .'/admin?page=return' ?>">Retornar</a>
    </div>
</div>
<table id="clone_list" class="d-none">
    <tr>
        <td class="py-1 px-2 text-center">
            <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="py-1 px-2 text-center qty">
            <span class="visible"></span>
            <input type="hidden" name="item_id[]">
            <input type="hidden" name="unit[]">
            <input type="hidden" name="qty[]">
            <input type="hidden" name="price[]">
            <input type="hidden" name="total[]">
        </td>
        <td class="py-1 px-2 text-center unit"></td>
        <td class="py-1 px-2 item"></td>
        <td class="py-1 px-2 text-right cost"></td>
        <td class="py-1 px-2 text-right total"></td>
    </tr>
</table>
<script>
    $(function() {
        $('#print').click(function() {
            start_loader()
            var _el = $('<div>')
            var _head = $('head').clone()
                _head.find('title').text("Registro de Devolução - Visualização de Impresssão")
            var p = $('#print_out').clone()
            p.find('tr.text-light').removeClass("text-light bg-navy")
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                '<div class="col-1 text-right">'+
                '<img src="<?= validateImage($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                '</div>'+
                '<div class="col-10">'+
                '<h4 class="text-center"><?= $_settings->info('name') ?></h4>'+
                '<h4 class="text-center">Return Record</h4>'+
                '</div>'+
                '<div class="col-1 text-right">'+
                '</div>'+
                '</div><hr/>')
            _el.append(p.html())
            var nw = window.open("","","width=1200,height=900,left=250,location=no,titlebar=yes")
            nw.document.write(_el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                nw.close()
                end_loader()
                }, 200);
            }, 500);
        })
    })
</script>