<html>
	<head>
		<title><?=$title?></title>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>/static/css/default.css" />
	</head>
	<body>
		<div id="navbar">
			<?php $this->load->view('navbar'); ?>
		</div>
		
		<div id="content">
		    <?php $this->load->view($content_view); ?>
		</div>
		
		<div id="footer">
			<?php $this->load->view('footer'); ?>
		</div>
	</body>
</html>
