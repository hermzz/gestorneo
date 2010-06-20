<h2><?=sprintf(_('Email team for %s'), $tournament->name);?></h2>

<script>
	$(document).ready(function() {
		$('[name="submitPreviewEmail"]').click(function () {
			f = $(this).parent()[0];
			old = f.action;
			
			f.action = '/tournament/email/<?=$tournament->id;?>/preview';
			f.target = '_blank';
			f.submit();
			
			f.target = '';
			f.action = old;
			
			return false;
		});
	});
</script> 

<?=validation_errors()?>

<form action="#" method="post">
	<label for="subject"><?=_('Subject');?></label>
    <input type="text" id="subject" name="subject" /><br />
    
    <label for="message"><?=_('Message');?></label><br />
    <textarea id="message" name="message" cols="60" rows="20"></textarea><br />
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>

    <input type="submit" name="submitSendEmail" value="<?=_('Send');?>" />
    <input type="submit" name="submitPreviewEmail" value="<?=_('Preview');?>" />
</form>
