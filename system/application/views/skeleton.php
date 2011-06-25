<html>
	<head>
		<title><?=$title?></title>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>/static/css/default.css" />
		<link href="/static/css/ui-lightness/ui.base.css" type="text/css" rel="stylesheet" />
		
		<script src="/static/javascript/jquery.min.js" language="javascript"></script>
		<script src="/static/javascript/jquery-ui.min.js" language="javascript"></script>
		<script src="/static/javascript/jquery.cookie.js" language="javascript"></script>
		
		<script>
		$(document).ready(function() {
			$('[name="language_chooser"]').change(function(e) {
				$.cookie(
					'language', 
					$(e.target).val(), 
					{
						path: '/', 
						domain: window.location.host
					}
				);
				
				location.reload();
			});
		});
		</script>
        
	</head>
	<body>
		<div id="navbar">
			<?php $this->load->view('navbar'); ?>
		</div>
		
		<div id="auth">
				<select name="language_chooser">
					<?php foreach($languages as $key => $name): ?>
						<option value="<?=$key?>" <?=$key==$selected_language?'selected="selected"':'';?>>
							<?=$name;?>
						</option>
					<?php endforeach; ?>
				</select>
			
			<?php if($this->tank_auth->is_logged_in()): ?>
				<span><?=sprintf(_('Hello %s'), $this->tank_auth->get_username());?>,</span>
				<a href="/auth/logout"><?=_('Logout');?></a>
			<?php else: ?>
				<a href="/auth/login"><?=_('Login');?></a>
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
