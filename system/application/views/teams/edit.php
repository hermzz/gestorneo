<h2><?=sprintf(_('Edit team "%s"'), $team->name);?></h2>

<?=validation_errors()?>

<form action="#" method="post">
	<fieldset>
		<div class="clearfix">
			<label for="name"><?=_('Name');?></label>
			<div class="input">
				<input type="text" id="name" name="name" value="<?=set_value('name', $team->name);?>" />
			</div>
		</div>
		
		<div class="clearfix">
			<label for="description"><?=_('Description');?></label>
			<div class="input">
				<textarea id="description" name="description" rows="8" cols="60" class="span6"><?=set_value('description', $team->description);?></textarea>
			    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>
			</div>
		</div>
		
	    <input type="submit" name="submitEditTeam" value="<?=_('Edit');?>" class="btn btn-primary" />
    </fieldset>
</form>
