<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Estoque</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="10%">
						<col width="20%">
						<col width="30%">
						<col width="20%">
					</colgroup>
					<thead style="text-align: center;">
						<tr>
							<th>ID</th>
							<th>Item</th>
							<th>Fornecedor</th>
							<th>Descrição</th>
							<th>Disponível</th>
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
							ORDER BY 
								`name` DESC
						");

						while ($row = $query->fetch_assoc()):
							$in = $conn->query("
								SELECT 
									SUM(quantity) AS total 
								FROM 
									stock_list 
								WHERE 
									item_id = '{$row['id']}' AND type = 1")->fetch_array()['total'];

							$out = $conn->query("
								SELECT 
									SUM(quantity) AS total 
								FROM 
									stock_list 
								WHERE 
									item_id = '{$row['id']}' AND type = 2")->fetch_array()['total'];

							$row['available'] = $in - $out;
						?>
							<tr>
								<td class="text-center"><?= $index++; ?></td>
								<td><?= $row['name'] ?></td>
								<td><?= $row['supplier'] ?></td>
								<td><?= $row['description'] ?></td>
								<td class="text-right"><?= number_format($row['available']) ?></td>
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
			_conf("Tem certeza que deseja excluir este pedido recebido permanentemente?","deleteReceiving",[$(this).attr('data-id')])
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