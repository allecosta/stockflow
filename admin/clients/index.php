<?php if ($_settings->chkFlashData('success')): ?>
	<script>
		alert_toast("<?= $_settings->flashData('success') ?>",'success')
	</script>
<?php endif; ?>

<style>
    .img-avatar {
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Usuários do Sistema</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Data Registrada</th>
							<th>Avatar</th>
							<th>Nome</th>
							<th>Email</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$index = 1;
							$query = $conn->query("
								SELECT 
									*,concat(lastname,', ',firstname,' ', middlename) AS name 
								FROM 
									`users` 
								WHERE  
									`type` =2 
								ORDER BY 
									concat(lastname,', ',firstname,' ', middlename) ASC ");

							while ($row = $query->fetch_assoc()):
						?>
							<tr>
								<td class="text-center"><?= $index++; ?></td>
								<td class="text-right"><?= date("Y-m-d H:i", strtotime($row['date_added'])) ?></td>
								<td class="text-center"><img src="<?= validateImage($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
								<td><?= ucwords($row['name']) ?></td>
								<td ><p class="m-0 truncate-1"><?= $row['username'] ?></p></td>
								<td align="center">
									<button 
										type="button" 
										class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" 
										data-toggle="dropdown">Ação
										<span class="sr-only">Alternar Lista Suspensa</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item view_details" href="javascript:void(0)" data-id ="<?= $row['id'] ?>"><span class="fa fa-eye text-dark"></span> Visualizar</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Excluir</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.delete_data').click(function() {
			_conf("Tem certeza de que deseja excluir este usuário permanentemente?","delete_user",[$(this).attr('data-id')])
		})
		$('.view_details').click(function() {
			uni_modal("Detalhes do Cliente","clients/view_details.php?id="+$(this).attr('data-id'))
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})

	function deleteUser($id)
	{
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=> {
				console.log(err)
				alert_toast("Ocorreu um erro.",'error');
				end_loader();
			},
			success:function(resp) {
				if (typeof resp== 'object' && resp.status == 'success') {
					branch.reload();
				} else {
					alert_toast("Ocorreu um erro.",'error');
					end_loader();
				}
			}
		})
	}
</script>