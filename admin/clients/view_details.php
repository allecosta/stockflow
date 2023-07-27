<?php

require_once('./../../config.php');

$query = $conn->query("
    SELECT *,concat(lastname,', ',firstname,' ', middlename) AS name 
    FROM 
        `users` 
    WHERE  id = '{$_GET['id']}' ");

if ($query->num_rows > 0) {
    foreach ($query->fetch_assoc() as $key => $value) {
        $$key = $value;
    }

    $metaQuery = $conn->query("SELECT * FROM `user_meta` WHERE user_id = '{$id}'");

    while ($row = $metaQuery->fetch_assoc()) {
        $meta[$row['meta_field']] = $row['meta_value'];
    }
}
?>

<style>
    #uni_modal .modal-footer {
        display:none;
    }

    img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
 <div class="container-fluid" id="print_out">
    <div id='transaction-printable-details' class='position-relative'>
        <div class="row">
            <fieldset class="w-100">
                <legend class="text-info">Informação</legend>
                <div class="col-12">
                    <div class="form-group text-center">
                        <img src="<?= validateImage(isset($avatar) ? $avatar :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                    </div>
                    <hr class="border-light">
                    <dl>
                        <dt class="text-info">Nome:</dt>
                        <dd class="pl-3"><?= $name ?></dd>
                        <dt class="text-info">Gênero:</dt>
                        <dd class="pl-3"><?= isset($meta['gender']) ? $meta['gender'] : '' ?></dd>
                        <dt class="text-info">Data de Nascimento:</dt>
                        <dd class="pl-3"><?= isset($meta['dob']) ? date("M d, Y",strtotime($meta['dob'])) : '' ?></dd>
                        <dt class="text-info">Contato #:</dt>
                        <dd class="pl-3"><?= isset($meta['contact']) ? $meta['contact'] : '' ?></dd>
                        <dt class="text-info">Endereço:</dt>
                        <dd class="pl-3"><?= isset($meta['address']) ? $meta['address'] : '' ?></dd>
                        <dt class="text-info">Email:</dt>
                        <dd class="pl-3"><?= isset($username) ? $username : '' ?></dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-light btn-flat" type="button" id="cancel" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $(function() {
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('#print').click(function() {
            start_loader()
            var _el = $('<div>')
            var _head = $('head').clone()
                _head.find('title').text("Detalhes de Pagamento - Visualização de Impressão")
            var p = $('#print_out').clone()
            p.find('hr.border-light').removeClass('.border-light').addClass('border-dark')
            p.find('.btn').remove()
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?= validateImage($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?= $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Detalhes de Pagamento</h4>'+
                      '</div>'+
                      '<div class="col-1 text-right">'+
                      '</div>'+
                      '</div><hr/>')
            _el.append(p.html())
            var nw = window.open("","","width=1200,height=900,left=250,location=no,titlebar=yes")
            nw.document.write(_el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                nw.close()
                end_loader()
                }, 200);
            }, 500);

        })
    })
</script>