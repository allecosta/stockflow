<?php

require_once('../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $query = $conn->query("
		SELECT 
			* 
		FROM 
			`item_list` 
		WHERE id = '{$_GET['id']}' ");

    if ($query->num_rows > 0) {
        foreach ($query->fetch_assoc() as $key => $value) {
            $$key = $value;
        }
    }
}

?>

<div class="container-fluid">
	<form action="" id="item-form">
		<input type="hidden" name ="id" value="<?= isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="name" class="control-label">Nome</label>
			<input type="text" name="name" id="name" class="form-control rounded-0" value="<?= isset($name) ? $name : ''; ?>">
		</div>
		<div class="form-group">
			<label class="control-label">Descrição
				<textarea 
					name="description" cols="30" rows="2" 
					class="form-control form no-resize"><?= isset($description) ? $description : ''; ?>
				</textarea>	
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Custo
				<input type="number" name="cost" step="any" class="form-control rounded-0 text-end" value="<?= isset($cost) ? $cost : ''; ?>">
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Fornecedor
				<select name="supplier_id" class="custom-select select2">
					<option <?= !isset($supplier_id) ? 'selected' : '' ?> disabled></option>
					<?php 
						$supplier = $conn->query("
							SELECT 
								* 
							FROM 
								`supplier_list` 
							WHERE 
								status = 1 
							ORDER BY `name` ASC");

						while ($row = $supplier->fetch_assoc()):
					?>
						<option value="<?= $row['id'] ?>" <?= isset($supplier_id) && $supplier_id == $row['id'] ? "selected" : "" ?> ><?= $row['name'] ?></option>
					<?php endwhile; ?>
				</select>
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Status
				<select name="status" class="custom-select selevt">
					<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Ativo</option>
					<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inativo</option>
				</select>
			</label>
		</div>
	</form>
</div>
<script>
	$(document).ready(function() {
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#item-form').submit(function(e) {
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_item",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("Ocorreu um erro",'error');
					end_loader();
				},
				success:function(resp) {
					if (typeof resp =='object' && resp.status == 'success') {
						location.reload();
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
				}
			})
		})
	})
</script>