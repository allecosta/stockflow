<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Itens</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Adicionar</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-hovered table-striped">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="25%">
						<col width="15%">
						<col width="20%">
					</colgroup>
					<thead style="text-align: center;">
						<tr>
							<th>ID</th>
							<th>Data</th>
							<th>Nome</th>
							<th>Fornecedor</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$index = 1;
							$query = $conn->query("
								SELECT 
									i.*,s.name AS supplier 
								FROM 
									`item_list` i 
								INNER JOIN 
									supplier_list s ON i.supplier_id = s.id 
								ORDER BY i.name ASC,s.name ASC ");

							while ($row = $query->fetch_assoc()):
						?>
							<tr>
								<td class="text-center"><?= $index++; ?></td>
								<td><?= date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
								<td><?= $row['name'] ?></td>
								<td><?= $row['supplier'] ?></td>
								<td class="text-center">
									<?php if ($row['status'] == 1): ?>
										<span class="badge badge-success rounded-pill">Ativo</span>
									<?php else: ?>
										<span class="badge badge-danger rounded-pill">Inativo</span>
									<?php endif; ?>
								</td>
								<td align="center">
									<button 
										type="button" 
										class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
											Ação
										<span class="sr-only">Alternar Lista Suspensa</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-eye text-dark"></span> Visualizar</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Editar</a>
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
			_conf("Tem certeza que deseja excluir este item?","deleteCategory",[$(this).attr('data-id')])
		})
		$('#create_new').click(function() {
			uni_modal("<i class='fa fa-plus'></i> Adiconar Novo Item","maintenance/manage_item.php","mid-large")
		})
		$('.edit_data').click(function() {
			uni_modal("<i class='fa fa-edit'></i> Editar Detalhes do Item","maintenance/manage_item.php?id="+$(this).attr('data-id'),"mid-large")
		})
		$('.view_data').click(function() {
			uni_modal("<i class='fa fa-box'></i> Detalhes do Item","maintenance/view_item.php?id="+$(this).attr('data-id'),"")
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})

	function deleteCategory($id) {
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=deleteItem",
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
					location.reload();
				} else {
					alert_toast("Ocorreu um erro.",'error');
					end_loader();
				}
			}
		})
	}
</script>