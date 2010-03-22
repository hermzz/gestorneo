<html>
	<head>
		<title><?=$title?></title>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>/static/css/default.css" />
		<link href="/static/css/base/ui.base.css" type="text/css" rel="stylesheet" />
		<link href="/static/css/base/ui.theme.css" type="text/css" rel="stylesheet" />
		
		<script src="http://www.google.com/jsapi" language="javascript"></script> 
		<script language="javascript"> 
			google.load("jquery","1.4.0");
			google.load("jqueryui", "1.7.2");
		</script>
        
	</head>
	<body>
		<div id="navbar">
			<?php if($this->tank_auth->is_logged_in()): ?>
				<?php $this->load->view('navbar'); ?>
			<?php endif; ?>
		</div>
		
		<div id="auth">
			<?php if($this->tank_auth->is_logged_in()): ?>
				<span>Hello <?=$this->tank_auth->get_username();?>,</span>
				<a href="/auth/logout">Logout</a>
			<?php else: ?>
				<a href="/auth/login">Login</a>
			<?php endif; ?>
		</div>
		
		<div id="content">
		    <?php $this->load->view($content_view); ?>
		</div>
		
		<div id="footer">
			<?php $this->load->view('footer'); ?>
		</div>
	</body>
</html>
