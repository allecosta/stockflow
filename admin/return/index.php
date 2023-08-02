<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Devolução</h3>
        <div class="card-tools">
			<a 
                href="<?= BASE_URL ?>admin/?page=return/manage_return" 
                class="btn btn-flat btn-primary">
                <span class="fas fa-plus"></span> Adicionar
            </a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="25%">
                        <col width="25%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="text-align: center;">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Cod. Devolução</th>
                            <th>Fornecedor</th>
                            <th>Itens</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $index = 1;
                        $query = $conn->query("
                            SELECT 
                                r.*, s.name AS supplier 
                            FROM 
                                `return_list` r 
                            INNER JOIN 
                                supplier_list s ON r.supplier_id = s.id 
                            ORDER BY 
                                r.`date_created` DESC
                        ");

                        while ($row = $query->fetch_assoc()):
                            $row['items'] = count(explode(',',$row['stock_ids']));
                        ?>
                            <tr>
                                <td class="text-center"><?= $index++; ?></td>
                                <td><?= date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td><?= $row['return_code'] ?></td>
                                <td><?= $row['supplier'] ?></td>
                                <td class="text-right"><?= number_format($row['items']) ?></td>
                                <td align="center">
                                    <button 
                                        type="button" 
                                        class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" 
                                        data-toggle="dropdown"> Ação
                                        <span class="sr-only">Altenar Lista Suspensa</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a 
                                            class="dropdown-item" 
                                            href="<?= BASE_URL .'admin?page=return/view_return&id='.$row['id'] ?>" 
                                            data-id="<?= $row['id'] ?>">
                                            <span class="fa fa-eye text-dark"></span> Visualizar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a 
                                            class="dropdown-item" 
                                            href="<?= BASE_URL .'admin?page=return/manage_return&id='.$row['id'] ?>" 
                                            data-id="<?= $row['id'] ?>">
                                            <span class="fa fa-edit text-primary"></span> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a 
                                            class="dropdown-item delete_data" href="javascript:void(0)" 
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
		$('.delete_data').click(function(){
			_conf("Tem certeza que deseja excluir este registro de devolução?","deleteReturn",[$(this).attr('data-id')])
		})

		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})

	function deleteReturn($id) {
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_return",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=> {
				console.log(err)
				alert_toast("Ocorreu eu erro.",'error');
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