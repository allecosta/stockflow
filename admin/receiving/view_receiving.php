<?php 

$query = $conn->query("SELECT * FROM receiving_list WHERE id = '{$_GET['id']}'");

if ($query->num_rows > 0) {
    foreach ($query->fetch_array() as $key => $value) {
        $$key = $value;
    }

    if ($from_order == 1) {
        $po_qry = $conn->query("
            SELECT 
                p.*,s.name AS supplier 
            FROM 
                `purchase_order_list` p 
            INNER JOIN 
                `supplier_list` s ON p.supplier_id = s.id 
            WHERE 
                p.id= '{$form_id}' "
        );

        if ($po_qry->num_rows > 0) {
            foreach ($po_qry->fetch_array() as $key => $value) {
                if (!isset($$key)) {
                    $$key = $value;
                }
            }
        }
    } else {
        $query = $conn->query("
            SELECT 
                b.*,s.name AS supplier,p.po_code 
            FROM 
                back_order_list b 
            INNER JOIN 
                supplier_list s ON b.supplier_id = s.id 
            INNER JOIN 
                purchase_order_list p ON b.po_id = p.id  
            WHERE
                 b.id = '{$form_id}'"
        );

        if ($query->num_rows > 0) {
            foreach ($query->fetch_array() as $key => $value) {
                if ($key == 'id') {
                    $key = 'bo_id';
                }
                
                if (!isset($$key)) {
                    $$key = $value;
                }
            }
        }
    }
}

?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Detalhes do Pedido Recebido - <?= $po_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Cod. Pedido de Compra</label>
                    <div><?= isset($po_code) ? $po_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                        <div><?= isset($supplier) ? $supplier : '' ?></div>
                    </div>
                </div>
                <?php if (isset($boID)): ?>
                    <div class="col-md-6">
                        <label class="control-label text-info">Cod. Pedido Pendente</label>
                        <div><?= isset($backOrderCode) ? $backOrderCode : '' ?></div>
                    </div>    
                <?php endif; ?>
            </div>
            <h4 class="text-info">Ordens</h4>
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
                        <th class="text-center py-1 px-2">Quant.</th>
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
                            s.id in ({$stock_ids})"
                    );

                    while ($row = $query->fetch_assoc()):
                        $total += $row['total']
                    ?>
                        <tr>
                            <td class="py-1 px-2 text-center"><?= number_format($row['quantity'],2) ?></td>
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
                        <th class="text-right py-1 px-2" colspan="4">Sub Total</th>
                        <th class="text-right py-1 px-2 sub-total"><?= number_format($total,2)  ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Desconto <?= isset($discount_perc) ? $discount_perc : 0 ?>%</th>
                        <th class="text-right py-1 px-2 discount"><?= isset($discount) ? number_format($discount,2) : 0 ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Taxa <?= isset($tax_perc) ? $tax_perc : 0 ?>%</th>
                        <th class="text-right py-1 px-2 tax"><?= isset($tax) ? number_format($tax,2) : 0 ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Total</th>
                        <th class="text-right py-1 px-2 grand-total"><?= isset($amount) ? number_format($amount,2) : 0 ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Observações</label>
                        <p><?= isset($remarks) ? $remarks : '' ?></p>
                    </div>
                </div>
                <?php if ($status > 0): ?>
                    <div class="col-md-6">
                        <span class="text-info"><?= ($status == 2)? "Recebido" : "Parcialmente recebido" ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Imprimir</button>
        <a class="btn btn-flat btn-primary" href="<?= BASE_URL .'/admin?page=receiving/manage_receiving&id='.(isset($id) ? $id : '') ?>">Editar</a>
        <a class="btn btn-flat btn-dark" href="<?= BASE_URL .'/admin?page=receiving' ?>">Retornar</a>
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
            _head.find('title').text("Pedido Recebido - Visualização de Impressão")
            var p = $('#print_out').clone()
            p.find('tr.text-light').removeClass("text-light bg-navy")
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?= validateImage($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?= $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Received Order</h4>'+
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