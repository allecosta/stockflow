
<?php 

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $user = $conn->query("SELECT * FROM users WHERE id ='{$_GET['id']}'");

    foreach ($user->fetch_array() as $key =>$value) {
        $meta[$key] = $value;
    }
}

if ($_settings->chkFlashData('success')): ?>
	<script>
		alert_toast("<?= $_settings->flashData('success') ?>",'success')
	</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?= isset($meta['id']) ? $meta['id']: '' ?>">
				<div class="form-group col-6">
					<label>Nome
						<input type="text" name="firstname" id="firstname" class="form-control" value="<?= isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
					</label>
				</div>
				<div class="form-group col-6">
					<label>Sobrenome
						<input type="text" name="lastname" id="lastname" class="form-control" value="<?= isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
					</label>
				</div>
				<div class="form-group col-6">
					<label>Usuário
						<input type="text" name="username" id="username" class="form-control" value="<?= isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
					</label>
				</div>
				<div class="form-group col-6">
					<label>Senha
						<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?= isset($meta['id']) ? "": 'required' ?>>
					</label>
                    <?php if (isset($_GET['id'])): ?>
						<small class="text-info"><i>Deixe em branco se não quiser alterar a senha.</i></small>
                    <?php endif; ?>
				</div>
				<div class="form-group col-6">
					<label>Tipo de Usuário
						<select name="type" class="custom-select" value="<?= isset($meta['type']) ? $meta['type']: '' ?>" required>
							<option value="1" <?= isset($type) && $type == 1 ? 'selected': '' ?>>Administrador</option>
							<option value="2"> <?= isset($type) && $type == 2 ? 'selected': '' ?>Staff</option>
						</select>
					</label>
				</div>
				<div class="form-group col-6">
					<label class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Escolher arquivo</label>
		            </div>
				</div>
				<div class="form-group col-6 d-flex justify-content-center">
					<img src="<?= validateImage(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
		<div class="col-md-12">
			<div class="row">
				<button class="btn btn-sm btn-primary mr-2" form="manage-user">Salvar</button>
				<a class="btn btn-sm btn-secondary" href="./?page=user/list">Cancelar</a>
			</div>
		</div>
	</div>
</div>
<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	$(function() {
		$('.select2').select2({
			width:'resolve'
		})
	})

	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$('#manage-user').submit(function(e) {
		e.preventDefault();
		var _this = $(this)
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp) {
				if (resp == 1) {
					location.href = './?page=user/list';
				} else {
					$('#msg').html('<div class="alert alert-danger">Este nome de usuário já existe.</div>')
					$("html, body").animate({ scrollTop: 0 }, "fast");
				}
                end_loader()
			}
		})
	})
</script>