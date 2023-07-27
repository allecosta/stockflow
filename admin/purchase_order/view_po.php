<?php 

$query = $conn->query("
    SELECT 
        p.*,s.name AS supplier 
    FROM 
        purchase_order_list p 
    INNER JOIN 
        supplier_list s ON p.supplier_id = s.id  
    WHERE 
        p.id = '{$_GET['id']}'");

if ($query->num_rows >0) {
    foreach ($query->fetch_array() as $key => $value) {
        $$key = $value;
    }
}

?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Detalhes de Pedido de Compra - <?= $po_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Cod. Pedido de Compra</label>
                    <div><?php echo isset($po_code) ? $po_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                        <div><?php echo isset($supplier) ? $supplier : '' ?></div>
                    </div>
                </div>
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
                        <th class="text-center py-1 px-2">Quantidade</th>
                        <th class="text-center py-1 px-2">Unidade</th>
                        <th class="text-center py-1 px-2">Item</th>
                        <th class="text-center py-1 px-2">Custos</th>
                        <th class="text-center py-1 px-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    $query = $conn->query("
                        SELECT 
                            p.*,i.name,i.description 
                        FROM 
                            `po_items` p 
                        INNER JOIN 
                            item_list i ON p.item_id = i.id 
                        WHERE 
                            p.po_id = '{$id}'");

                    while ($row = $qry->fetch_assoc()):
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
                        <p><?php echo isset($remarks) ? $remarks : '' ?></p>
                    </div>
                </div>
                <?php if ($status > 0): ?>
                    <div class="col-md-6">
                        <span class="text-info"><?= ($status == 2) ? "Recebido" : "Recebido Parcialmente" ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Imprimir</button>
        <a class="btn btn-flat btn-primary" href="<?= BASE_URL.'/admin?page=purchase_order/manage_po&id='.(isset($id) ? $id : '') ?>">Edit</a>
        <a class="btn btn-flat btn-dark" href="<?= BASE_URL.'/admin?page=purchase_order' ?>">Voltar à Lista</a>
    </div>
</div>
<table id="clone_list" class="d-none">
    <tr>
        <td class="py-1 px-2 text-center">
            <button 
                class="btn btn-outline-danger btn-sm rem_row" 
                type="button"><i class="fa fa-times"></i>
            </button>
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
                _head.find('title').text("Detalhes de Pedido de compra - Visualização de Impressão")
            var p = $('#print_out').clone()
            p.find('tr.text-light').removeClass("text-light bg-navy")
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?= validateImage($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?= $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Purchase Order</h4>'+
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