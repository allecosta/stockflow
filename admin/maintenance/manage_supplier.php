<?php

require_once('../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $query = $conn->query("SELECT * FROM `supplier_list` WHERE id = '{$_GET['id']}' ");
    if ($query->num_rows > 0) {
        foreach ($query->fetch_assoc() as $key => $value) {
            $$key = $value;
        }
    }
}

?>

<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="container-fluid">
	<form action="" id="supplier-form">
		<input type="hidden" name ="id" value="<?= isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label class="control-label">Nome
				<input name="name" id="name" class="form-control rounded-0" value="<?= isset($name) ? $name : ''; ?>">
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Endereço
				<textarea 
					name="address" cols="30" rows="2" 
					class="form-control form no-resize"><?php echo isset($address) ? $address : ''; ?>
				</textarea>
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Contato Pessoal
				<input name="cperson" class="form-control rounded-0" value="<?= isset($cperson) ? $cperson : ''; ?>">
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Contato #
				<input name="contact" class="form-control rounded-0" value="<?= isset($contact) ? $contact : ''; ?>">	
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Status
				<select name="status" class="custom-select select">
					<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Ativo</option>
					<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inativo</option>
				</select>
			</label>
		</div>
	</form>
</div>
<script>
	$(document).ready(function() {
		$('#supplier-form').submit(function(e) {
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_supplier",
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