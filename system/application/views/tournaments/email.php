<h2><?=_('Email team');?></h2>

<?=validation_errors()?>

<form action="#" method="post">
	 <label for="subject"><?=_('Subject');?></label>
    <input type="text" id="subject" name="subject" /><br />
    
    <label for="message"><?=_('Message');?></label><br />
    <textarea id="message" name="message" cols="60" rows="20"></textarea><br />

    <input type="submit" name="submitSendEmail" value="<?=_('Send');?>" />
</form>
