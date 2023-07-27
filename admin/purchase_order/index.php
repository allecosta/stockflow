<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Pedido de Compra</h3>
        <div class="card-tools">
			<a href="<?= BASE_URL ?>admin/?page=purchase_order/manage_po" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Criar Nova</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Cod. Pedido de Compra</th>
                            <th>Fornecedor</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $index = 1;
                        $query = $conn->query("
                            SELECT 
                                p.*, s.name AS supplier 
                            FROM 
                                `purchase_order_list` p 
                            INNER JOIN 
                                supplier_list s ON p.supplier_id = s.id 
                            ORDER BY p.`date_created` DESC");

                        while ($row = $query->fetch_assoc()):
                            $row['items'] = $conn->query("
                                SELECT 
                                    count(item_id) AS `items` 
                                FROM 
                                    `po_items` WHERE po_id = '{$row['id']}' ")->fetch_assoc()['items'];
                        ?>
                            <tr>
                                <td class="text-center"><?= $index++; ?></td>
                                <td><?= date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td><?= $row['po_code'] ?></td>
                                <td><?= $row['supplier'] ?></td>
                                <td class="text-right"><?= number_format($row['items']) ?></td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 0): ?>
                                        <span class="badge badge-primary rounded-pill">Pendente</span>
                                    <?php elseif ($row['status'] == 1): ?>
                                        <span class="badge badge-warning rounded-pill">Recebido Parcialmente</span>
                                        <?php elseif ($row['status'] == 2): ?>
                                        <span class="badge badge-success rounded-pill">Recebido</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger rounded-pill">N/A</span>
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
                                    <?php if ($row['status'] == 0): ?>
                                        <a class="dropdown-item" href="<?= BASE_URL.'admin?page=receiving/manage_receiving&po_id='.$row['id'] ?>" data-id="<?= $row['id'] ?>"><span class="fa fa-boxes text-dark"></span> Recebido</a>
                                        <div class="dropdown-divider"></div>
                                    <?php endif; ?>
                                        <a class="dropdown-item" href="<?= BASE_URL.'admin?page=purchase_order/view_po&id='.$row['id'] ?>" data-id="<?= $row['id'] ?>"><span class="fa fa-eye text-dark"></span> Visualizar</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= BASE_URL.'admin?page=purchase_order/manage_po&id='.$row['id'] ?>" data-id="<?= $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Editar</a>
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
			_conf("Tem certeza que deseja excluir este pedido de compra permanentemente?","delete_po",[$(this).attr('data-id')])
		})
		$('.view_details').click(function() {
			uni_modal("Detalhes de Pagamento","transaction/view_payment.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})

	function deletePurchaserOrder($id) {
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_po",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
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