<script src="<?=base_url();?>static/javascript/jquery.cookie.js"></script>
<script src="<?=base_url();?>static/javascript/jquery.ba-hashchange.min.js"></script>
<script src="<?=base_url();?>static/javascript/jquery.tablesorter.min.js"></script>
<script src="<?=base_url();?>static/javascript/jquery.ui.datepicker-es.js"></script>
<script src="<?=base_url();?>static/javascript/prettyCheckable.js"></script>
<script src="<?=base_url();?>static/javascript/custom.js"></script>
<script src="<?=base_url();?>static/javascript/bootstrap.min.js"></script>
<?php for($i=0; isset($extra_js_files) && $i<count($extra_js_files); $i++) : ?>
<script src="<?=$extra_js_files[$i]?>"></script>
<?php endfor; ?>

<script>
	jQuery(document).ready(function($) {
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
