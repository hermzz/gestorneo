<!DOCTYPE html>
<html lang="<?= $selected_language; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?=$title?> - Gestorneo</title>
		<link rel="icon" type="image/png" href="<?=base_url();?>static/images/favicon.ico">

		<link rel="stylesheet" type="text/css" href="<?=base_url();?>static/css/bootstrap.united.min.css" />
		<link href="<?=base_url();?>static/css/flick/jquery-ui-1.10.0.custom.min.css" type="text/css" rel="stylesheet" />
		<link href="<?=base_url();?>static/css/prettyCheckable.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>static/css/default.css" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

	</head>

	<body>
		<?php $this->load->view('navbar'); ?>

		<div class="container">

			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<?php if($breadcrumbs): ?>
							<ul class="breadcrumb">
								<?php foreach($breadcrumbs as $k => $bc): ?>
									<li>
										<a href="<?=$bc['url'];?>"><?=$bc['text'];?></a>
										<?php if($k < (count($breadcrumbs) - 1)): ?>
											<span class="divider">/</span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php $this->load->view($content_view); ?>
					</div>
				</div>
			</div>

			<footer>
				<?php $this->load->view('footer'); ?>
			</footer>
		</div>
		<?php $this->load->view('footer-scripts'); ?>
	</body>
</html>
