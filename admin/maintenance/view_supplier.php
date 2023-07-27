<?php 

require_once('./../../config.php');
 
$query = $conn->query("SELECT * FROM `supplier_list` WHERE  id = '{$_GET['id']}' ");

if ($qry->num_rows > 0) {
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
                        <dt class="text-info">Nome:</dt>
                        <dd class="pl-3"><?php echo $name ?></dd>
                        <dt class="text-info">Endereço:</dt>
                        <dd class="pl-3"><?= isset($address) ? $address : '' ?></dd>
                        <dt class="text-info">Contato Pessoal:</dt>
                        <dd class="pl-3"><?= isset($cperson) ? $cperson : '' ?></dd>
                        <dt class="text-info">Contato #:</dt>
                        <dd class="pl-3"><?= isset($contact) ? $contact : '' ?></dd>
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