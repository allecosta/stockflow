<?php 

require_once('./../../config.php');

 $query = $conn->query("
    SELECT 
        i.*,s.name AS supplier 
    FROM 
        `item_list` i 
    INNER JOIN 
        supplier_list s ON i.supplier_id = s.id WHERE  i.id = '{$_GET['id']}' ");

 if ($query->num_rows > 0) {
     foreach ($query->fetch_assoc() as $key => $value) {
         $$key = $value;
     }
 }

?>

<style>
    #uni_modal .modal-footer {
        display:none;
    }
</style> 
<div class="container-fluid" id="print_out">
    <div id='transaction-printable-details' class='position-relative'>
        <div class="row">
            <fieldset class="w-100">
                <div class="col-12">
                    <dl>
                        <dt class="text-info">Nome do Item:</dt>
                        <dd class="pl-3"><?= $name ?></dd>
                        <dt class="text-info">Descrição:</dt>
                        <dd class="pl-3"><?= isset($description) ? $description : '' ?></dd>
                        <dt class="text-info">Custo:</dt>
                        <dd class="pl-3"><?= isset($cost) ? number_format($cost,2) : '' ?></dd>
                        <dt class="text-info">Fornecedor:</dt>
                        <dd class="pl-3"><?= isset($supplier) ? $supplier : '' ?></dd>
                        <dt class="text-info">Status:</dt>
                        <dd class="pl-3">
                            <?php if ($status == 1): ?>
                                <span class="badge badge-success rounded-pill">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-danger rounded-pill">Inativo</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-dark btn-flat" type="button" id="cancel" data-dismiss="modal">Fechar</button>
        </div>
    </div>
</div>
<script>
    $(function() {
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
    })
</script>