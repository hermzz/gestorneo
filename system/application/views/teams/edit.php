<h2><?=sprintf(_('Edit team "%s"'), $team->name);?></h2>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" value="<?=set_value('name', $team->name);?>" /><br />
    
    <label for="description"><?=_('Description');?></label><br />
    <textarea id="description" name="description" rows="10" cols="60"><?=set_value('description', $team->description);?></textarea><br />
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>

    <input type="submit" name="submitEditTeam" value="<?=_('Edit');?>" />
</form>
