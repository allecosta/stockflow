<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Pedidos Recebidos</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="25%">
						<col width="25%">
						<col width="25%">
						<col width="20%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Data</th>
							<th>De</th>
							<th>Items</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$index = 1;
						$query = $conn->query("
							SELECT 
								* 
							FROM 
								`receiving_list` 
							ORDER BY `date_created` DESC"
						);
							
						while ($row = $query->fetch_assoc()):
							$row['items'] = explode(',',$row['stock_ids']);
								
							if ($row['from_order'] == 1) {
								$code = $conn->query("
									SELECT 
										po_code 
									FROM 
										`purchase_order_list` 
									WHERE 
										id='{$row['form_id']}' ")->fetch_assoc()['po_code'];

							} else {
								$code = $conn->query("
									SELECT 
										bo_code 
									FROM 
										`back_order_list` 
									WHERE
										 id='{$row['form_id']}' ")->fetch_assoc()['bo_code'];
							}
						?>
							
						<tr>
							<td class="text-center"><?= $index++; ?></td>
							<td><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td><?= $code ?></td>
							<td class="text-right"><?= number_format(count($row['items'])) ?></td>
							<td align="center">
								<button 
									type="button" 
									class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" 
									data-toggle="dropdown">
										Ação
									<span class="sr-only">Alternar Lista Suspensa</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a 
										class="dropdown-item" 
										href="<?= BASE_URL .'admin?page=receiving/view_receiving&id='.$row['id'] ?>" 
										data-id="<?= $row['id'] ?>">
										<span class="fa fa-eye text-dark"></span> Visualizar
									</a>
									<div class="dropdown-divider"></div>
									<a 
										class="dropdown-item" 
										href="<?= BASE_URL .'admin?page=receiving/manage_receiving&id='.$row['id'] ?>" 
										data-id="<?= $row['id'] ?>">
										<span class="fa fa-edit text-primary"></span> Editar
									</a>
									<div class="dropdown-divider"></div>
									<a 
										class="dropdown-item delete_data" 
										href="javascript:void(0)" 
										data-id="<?= $row['id'] ?>">
										<span class="fa fa-trash text-danger"></span> Excluir
									</a>
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
			_conf("Tem certeza que deseja excluir esse pedido recebido permanentemente?","deleteReceiving",[$(this).attr('data-id')])
		})
		$('.view_details').click(function() {
			uni_modal("Detalhes de Recebimento","receiving/view_receiving.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})

	function deleteReceiving($id) {
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_receiving",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=> {
				console.log(err)
				alert_toast("ocorreu um erro.",'error');
				end_loader();
			},
			success:function(resp) {
				if (typeof resp== 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("ocorreu um erro.",'error');
					end_loader();
				}
			}
		})
	}
</script>