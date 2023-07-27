<section class="page-section bg-dark" id="home">
	<div class="container">
		<h2 class="text-center">Pacotes de Viagem</h2>
		<div class="d-flex w-100 justify-content-center">
			<hr class="border-warning" style="border:3px solid" width="15%">
		</div>
		<div class="d-flex w-100">
			<?php
				$packages = $conn->query("SELECT * FROM `packages` ORDER BY rand() ");

				while ($row = $packages->fetch_assoc() ):
					$cover='';

					if (is_dir(BASE_APP.'uploads/package_'.$row['id'])) {
						$img = scandir(BASE_APP.'uploads/package_'.$row['id']);
						$key = array_search('.',$img);

						if($key !== false) {
							unset($img[$key]);
						}
							
						$key = array_search('..',$img);

						if ($key !== false) {
							unset($img[$key]);
						}
							
						$cover = isset($img[2]) ? 'uploads/package_'.$row['id'].'/'.$img[2] : "";
					}

					$row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
			?>
					<div class="card w-100 rounded-0">
						<img class="card-img-top" src="<?= validateimage($cover) ?>" alt="<?= $row['title'] ?>" height="200rem" style="object-fit:cover">
						<div class="card-body">
							<h5 class="card-title truncate-1"><?= $row['title'] ?></h5>
							<p class="card-text truncate"><?= $row['description'] ?></p>
						<div class="w-100 d-flex justify-content-end">
							<a href="./?page=packages&id=<?= password_hash($row['id'], PASSWORD_BCRYPT) ?>" class="btn btn-sm btn-flat btn-warning">Ver Pacotes <i class="fa fa-arrow-right"></i></a>
						</div>
						</div>
					</div>
				<?php endwhile; ?>
		</div>
		<div class="d-flex w-100 justify-content-end">
			<a href="./?page=packages" class="btn btn-flat btn-warning mr-4">Explorar Pacotes <i class="fa fa-arrow-right"></i></a>
		</div>
	</div>
</section>