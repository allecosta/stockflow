<?php 

if (isset($_GET['id'])) {
    $query = $conn->query("SELECT p.* FROM receiving_list p WHERE p.id = '{$_GET['id']}'");
	
    if ($query->num_rows >0) {
        foreach ($query->fetch_array() as $key => $value) {
            $$key = $value;
        }
		
        if ($fromOrder == 1) {
            $query = $conn->query("
                SELECT 
                    p.*,s.name AS supplier 
				FROM 
                    purchase_order_list p 
			    INNER JOIN 
                    supplier_list s ON p.supplier_id = s.id  
				WHERE p.id = '{$form_id}'"
			);
			
            if ($query->num_rows >0) {
                foreach ($query->fetch_array() as $key => $value) {
                    if ($key == 'id') {
                        $key = 'po_id';
                    }
                    
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
                    b.id = '{$_GET['bo_id']}'"
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
}

if (isset($_GET['po_id'])) {
    $query = $conn->query("
        SELECT 
            p.*,s.name AS supplier 
		FROM 
            purchase_order_list p 
		INNER JOIN 
            supplier_list s ON p.supplier_id = s.id  
		WHERE p.id = '{$_GET['po_id']}'"
	);
							
    if ($query->num_rows > 0) {
        foreach ($query->fetch_array() as $key => $value) {
            if ($key == 'id') {
                $key = 'po_id';
            }
            
            $$key = $value;
        }
    }
}

if (isset($_GET['bo_id'])) {
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
            b.id = '{$_GET['bo_id']}'"
	);
							
    if ($query->num_rows > 0) {
        foreach ($query->fetch_array() as $key => $value) {
            if ($key == 'id') {
                $key = 'bo_id';
            }
            
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
        <h4 class="card-title">
			<?= !isset($id) ? "Receber Pedido de ".$po_code : 'Pedido recebido atualizado' ?>
		</h4>
    </div>
    <div class="card-body">
        <form action="" id="receive-form">
            <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
            <input type="hidden" name="from_order" value="<?= isset($bo_id) ? 2 : 1 ?>">
            <input type="hidden" name="form_id" value="<?= isset($bo_id) ? $bo_id : $po_id ?>">
            <input type="hidden" name="po_id" value="<?= isset($po_id) ? $po_id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <?php if (!isset($boID)): ?>
						<div class="col-md-6">
							<label class="control-label text-info">Cod. Pedido de Compra</label>
							<input type="text" class="form-control form-control-sm rounded-0" value="<?= isset($po_code) ? $po_code : '' ?>" readonly>
						</div>
                    <?php else: ?>
                        <div class="col-md-6">
							<label class="control-label text-info">Cod. Pedido Pendente</label>
							<input type="text" class="form-control form-control-sm rounded-0" value="<?= isset($bo_code) ? $bo_code : '' ?>" readonly>
						</div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                            <select id="supplier_id" name="supplier_id" class="custom-select select2">
								<option <?= !isset($supplierId) ? 'selected' : '' ?> disabled></option>
								<?php 
								$supplier = $conn->query("
                                    SELECT 
                                        * 
									FROM 
                                        `supplier_list` 
									WHERE 
                                        status = 1 
                                    ORDER BY 
                                        `name` ASC"
								);
															
								while ($row = $supplier->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>" 
                                        <?= isset($supplierId) && $supplierId == $row['id'] ? "selected" : "" ?>>
                                        <?= $row['name'] ?>
                                    </option>
								<?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
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
						
                        if (isset($poID)):
                            if (!isset($boID))
                                $query = $conn->query("
                                    SELECT 
                                        p.*,i.name,i.description 
                                    FROM 
                                        `po_items` p 
                                    INNER JOIN 
                                        item_list i on p.item_id = i.id 
                                    WHERE 
                                        p.po_id = '{$poID}'"
                                );
                                                        
                            else
                                $query = $conn->query("
                                    SELECT 
                                        b.*,i.name,i.description 
                                    FROM 
                                        `bo_items` b 
                                    INNER JOIN 
                                        item_list i ON b.item_id = i.id 
                                    WHERE 
                                        b.bo_id = '{$boID}'"
                                    );
													
                            while ($row = $qry->fetch_assoc()):
                                $total += $row['total'];
                                $row['qty'] = $row['quantity'];
                                
                                if (isset($stockIds)) {
                                    $qty = $conn->query("
                                        SELECT 
                                            * 
                                        FROM 
                                            `stock_list` 
                                        WHERE 
                                            id IN ($stock_ids) AND item_id = '{$row['item_id']}'"
                                    );
                                                            
                                    $row['qty'] = $qty->num_rows > 0 ? $qty->fetch_assoc()['quantity'] : $row['qty'];
                                }
                        ?>
                            <tr>
                                <td class="py-1 px-2 text-center">
                                    <button 
                                        class="btn btn-outline-danger btn-sm rem_row" 
                                        type="button"><i class="fa fa-times"></i>
                                    </button>
                                </td>
                                <td class="py-1 px-2 text-center qty">
                                    <input type="number" name="qty[]" style="width:50px !important" value="<?= $row['qty']; ?>" max = "<?= $row['quantity']; ?>" min="0">
                                    <input type="hidden" name="item_id[]" value="<?= $row['item_id']; ?>">
                                    <input type="hidden" name="unit[]" value="<?= $row['unit']; ?>">
                                    <input type="hidden" name="oqty[]" value="<?= $row['quantity']; ?>">
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
                            <th class="text-right py-1 px-2" colspan="5">Desconto
								<input style="width:40px !important" name="discount_perc" class='' type="number" min="0" max="100" value="<?= isset($discountPerc) ? $discountPerc : 0 ?>">%				
                                <input type="hidden" name="discount" value="<?= isset($discount) ? $discount : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 discount">
								<?= isset($discount) ? number_format($discount) : 0 ?>
							</th>
                        </tr>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Taxa 
								<input style="width:40px !important" name="tax_perc" class='' type="number" min="0" max="100" value="<?= isset($tax_perc) ? $tax_perc : 0 ?>">%
                                <input type="hidden" name="tax" value="<?= isset($discount) ? $discount : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 tax">
								<?= isset($tax) ? number_format($tax) : 0 ?>
							</th>
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
                                <textarea 
                                    name="remarks" 
                                    rows="3" 
                                    class="form-control rounded-0"><?= isset($remarks) ? $remarks : '' ?>
                                </textarea>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-primary" type="submit" form="receive-form">Salvar</button>
        <a class="btn btn-flat btn-dark" href="<?= BASE_URL .'/admin?page=purchase_order' ?>">Cancelar</a>
    </div>
</div>
<script>
    $(function() {
        $('.select2').select2({
            placeholder:"Favor selecione aqui",
            width:'resolve',
        })
        $('#receive-form').submit(function(e) {
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_receiving",
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
						location.replace(_base_url_+"admin/?page=receiving/view_receiving&id="+resp.id);
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

        if ('<?= (isset($id) && $id > 0) || (isset($poID) && $poID > 0) ?>' == 1) {
            calc()

            $('#supplier_id').attr('readonly','readonly')
            $('table#list tbody tr .rem_row').click(function() {
                rem($(this))
            })

            console.log('test')

            $('[name="qty[]"],[name="discount_perc"],[name="tax_perc"]').on('input',function() {
                calc()
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

        $('table#list tbody tr').each(function() {
            qty = $(this).find('[name="qty[]"]').val()
            price = $(this).find('[name="price[]"]').val()
            total = parseFloat(price) * parseFloat(qty)

            $(this).find('[name="total[]"]').val(total)
            $(this).find('.total').text(parseFloat(total).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        })

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
        $('table#list tfoot .grand-total').text(parseFloat(grandTotal).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="amount"]').val(parseFloat(grandTotal))
    }
</script>