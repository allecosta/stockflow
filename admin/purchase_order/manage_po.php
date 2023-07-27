<?php 

if (isset($_GET['id'])) {
    $query = $conn->query("
        SELECT 
            p.*,s.name AS supplier 
        FROM 
            purchase_order_list p 
        INNER JOIN 
            supplier_list s ON p.supplier_id = s.id  
        WHERE p.id = '{$_GET['id']}'");

    if ($query->num_rows >0) {
        foreach ($query->fetch_array() as $key => $value) {
            $$key = $value;
        }
    }
}

?>

<style>
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title"><?= isset($id) ? "Detalhes do Pedido de Compra - ".$po_code : 'Criar Novo Pedido de Compra' ?></h4>
    </div>
    <div class="card-body">
        <form action="" id="po-form">
            <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">Cod. Pedido de Compra</label>
                        <input type="text" class="form-control form-control-sm rounded-0" value="<?= isset($po_code) ? $po_code : '' ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                            <select name="supplier_id" id="supplier_id" class="custom-select select2">
                                <option <?= !isset($supplier_id) ? 'selected' : '' ?> disabled></option>
                                <?php 
                                    $supplier = $conn->query("
                                        SELECT 
                                            * 
                                        FROM 
                                            `supplier_list` 
                                        WHERE 
                                            status = 1 
                                        ORDER BY 
                                            `name` ASC");

                                    while ($row = $supplier->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= isset($supplier_id) && $supplier_id == $row['id'] ? "selected" : "" ?> ><?= $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <fieldset>
                    <legend class="text-info">Formulário de Item</legend>
                    <div class="row justify-content-center align-items-end">
                        <?php 
                        $itemArr = [];
                        $costA = [];
                        $item = $conn->query("
                            SELECT 
                                * 
                            FROM 
                                `item_list` 
                            WHERE 
                                status = 1 
                            ORDE BY 
                                `name` ASC");

                        while ($row = $item->fetch_assoc()):
                            $itemArr[$row['supplier_id']][$row['id']] = $row;
                            $costArr[$row['id']] = $row['cost'];
                        endwhile;
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Item
                                    <select class="custom-select ">
                                        <option disabled selected></option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Unidade
                                    <input type="text" class="form-control rounded-0">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Quantidade
                                    <input type="number" step="any" class="form-control rounded-0">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                                <button type="button" class="btn btn-flat btn-sm btn-primary" id="add_to_list">Adicionado a Lista</button>
                            </div>
                        </div>
                </fieldset>
                <hr>
                <table class="table table-striped table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                    </colgroup>
                    <thead>
                        <tr class="text-light bg-navy">
                            <th class="text-center py-1 px-2"></th>
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

                        if (isset($id)):
                            $query = $conn->query("
                                SELECT 
                                    p.*,i.name,i.description 
                                FROM 
                                    `po_items` p 
                                INNER JOIN 
                                    item_list i ON p.item_id = i.id 
                                WHERE p.po_id = '{$id}'");

                            while ($row = $qry->fetch_assoc()):
                                $total += $row['total']
                            ?>
                                <tr>
                                    <td class="py-1 px-2 text-center">
                                        <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
                                    </td>
                                    <td class="py-1 px-2 text-center qty">
                                        <span class="visible"><?= number_format($row['quantity']); ?></span>
                                        <input type="hidden" name="item_id[]" value="<?= $row['item_id']; ?>">
                                        <input type="hidden" name="unit[]" value="<?= $row['unit']; ?>">
                                        <input type="hidden" name="qty[]" value="<?= $row['quantity']; ?>">
                                        <input type="hidden" name="price[]" value="<?= $row['price']; ?>">
                                        <input type="hidden" name="total[]" value="<?= $row['total']; ?>">
                                    </td>
                                    <td class="py-1 px-2 text-center unit">
                                        <?= $row['unit']; ?>
                                    </td>
                                    <td class="py-1 px-2 item">
                                        <?= $row['name']; ?> <br>
                                        <?= $row['description']; ?>
                                    </td>
                                    <td class="py-1 px-2 text-right cost">
                                        <?= number_format($row['price']); ?>
                                    </td>
                                    <td class="py-1 px-2 text-right total">
                                        <?= number_format($row['total']); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Sub Total</th>
                            <th class="text-right py-1 px-2 sub-total">0</th>
                        </tr>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Desconto <input style="width:40px !important" name="discount_perc" class='' type="number" min="0" max="100" value="<?= isset($discount_perc) ? $discount_perc : 0 ?>">%
                                <input type="hidden" name="discount" value="<?= isset($discount) ? $discount : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 discount"><?= isset($discount) ? number_format($discount) : 0 ?></th>
                        </tr>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Taxa <input style="width:40px !important" name="tax_perc" class='' type="number" min="0" max="100" value="<?= isset($tax_perc) ? $tax_perc : 0 ?>">%
                                <input type="hidden" name="tax" value="<?= isset($discount) ? $discount : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 tax"><?= isset($tax) ? number_format($tax) : 0 ?></th>
                        </tr>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Total
                                <input type="hidden" name="amount" value="<?= isset($discount) ? $discount : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 grand-total">0</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-info control-label">Observações
                                <textarea name="remarks" rows="3" class="form-control rounded-0"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                            </label>           
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-primary" type="submit" form="po-form">Salvar</button>
        <a class="btn btn-flat btn-dark" href="<?= BASE_URL.'/admin?page=purchase_order' ?>">Cancelar</a>
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
    var items = $.parseJSON('<?php echo json_encode($itemArr) ?>')
    var costs = $.parseJSON('<?php echo json_encode($costArr) ?>')
    
    $(function() {
        $('.select2').select2({
            placeholder:"Favor selecione aqui",
            width:'resolve',
        })
        $('#item_id').select2({
            placeholder:"Favor selecione primeiro o fornecedor",
            width:'resolve',
        })

        $('#supplier_id').change(function() {
            var supplierId = $(this).val()

            $('#item_id').select2('destroy')

            if (!!items[supplierId]) {
                $('#item_id').html('')
                var listItem = new Promise(resolve=> {
                    Object.keys(items[supplierId]).map(function(key) {
                        var row = items[supplierId][key]
                        var opt = $('<option>')
                            opt.attr('value',row.id)
                            opt.text(row.name)
                        $('#item_id').append(opt)
                    })
                    resolve()
                })
                listItem.then(function() {
                    $('#item_id').select2({
                        placeholder:"Favor selecione o item aqui",
                        width:'resolve',
                    })
                })
            } else {
                listItem.then(function() {
                    $('#item_id').select2({
                        placeholder:"Nenhum item listado ainda",
                        width:'resolve',
                    })
                })
            }

        })

        $('#add_to_list').click(function() {
            var supplier = $('#supplier_id').val()
            var item = $('#item_id').val()
            var qty = $('#qty').val() > 0 ? $('#qty').val() : 0;
            var unit = $('#unit').val()
            var price = costs[item] || 0
            var total = parseFloat(qty) * parseFloat(price)
            var itemName = items[supplier][item].name || 'N/A';
            var itemDescription = items[supplier][item].description || 'N/A';
            var tr = $('#clone_list tr').clone()

            if (item == '' || qty == '' || unit == '' ) {
                alert_toast('Os campos de texto do item de formulário são obrigatórios.','Aviso');
                
                return false;
            }

            if ($('table#list tbody').find('tr[data-id="'+item+'"]').length > 0) { 
                alert_toast('O item já existe na lista.','Error');

                return false;
            }
            tr.find('[name="item_id[]"]').val(item)
            tr.find('[name="unit[]"]').val(unit)
            tr.find('[name="qty[]"]').val(qty)
            tr.find('[name="price[]"]').val(price)
            tr.find('[name="total[]"]').val(total)
            tr.attr('data-id',item)
            tr.find('.qty .visible').text(qty)
            tr.find('.unit').text(unit)
            tr.find('.item').html(itemName +'<br/>'+ itemDescription)
            tr.find('.cost').text(parseFloat(price).toLocaleString('en-US'))
            tr.find('.total').text(parseFloat(total).toLocaleString('en-US'))
            $('table#list tbody').append(tr)
            calc()
            $('#item_id').val('').trigger('change')
            $('#qty').val('')
            $('#unit').val('')
            tr.find('.rem_row').click(function() {
                rem($(this))
            })
            
            $('[name="discount_perc"],[name="tax_perc"]').on('input',function() {
                calc()
            })
            $('#supplier_id').attr('readonly','readonly')
        })
        $('#po-form').submit(function(e) {
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_po",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=> {
					console.log(err)
					alert_toast("Ocorreu um erro",'error');
					end_loader();
				},
				success:function(resp) {
					if (resp.status == 'success') {
						location.replace(_base_url_+"admin/?page=purchase_order/view_po&id="+resp.id);
					} else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    } else {
						alert_toast("Ocorreu um erro",'error');
						end_loader();
                        console.log(resp)
					}
                    $('html,body').animate({scrollTop:0},'fast')
				}
			})
		})

        if ('<?= isset($id) && $id > 0 ?>' == 1) {
            calc()
            $('#supplier_id').trigger('change')
            $('#supplier_id').attr('readonly','readonly')
            $('table#list tbody tr .rem_row').click(function(){
                rem($(this))
            })
        }
    })

    function rem(_this) {
        _this.closest('tr').remove()
        calc()

        if ($('table#list tbody tr').length <= 0) {
            $('#supplier_id').removeAttr('readonly')
        }
    }

    function calc() {
        var subTotal = 0;
        var grandTotal = 0;
        var discount = 0;
        var tax = 0;

        $('table#list tbody input[name="total[]"]').each(function() {
            subTotal += parseFloat($(this).val())
            
        })
        $('table#list tfoot .sub-total').text(parseFloat(subTotal).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        var discount =   subTotal * (parseFloat($('[name="discount_perc"]').val()) /100)
        subTotal = subTotal - discount;
        var tax =   subTotal * (parseFloat($('[name="tax_perc"]').val()) /100)
        grandTotal = subTotal + tax
        $('.discount').text(parseFloat(discount).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="discount"]').val(parseFloat(discount))
        $('.tax').text(parseFloat(tax).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="tax"]').val(parseFloat(tax))
        $('table#list tfoot .grand-total').text(parseFloat(grand_total).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="amount"]').val(parseFloat(grandTotal))
    }
</script>