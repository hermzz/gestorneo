<h2><?=_('New team');?></h2>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" value="<?=set_value('name');?>" /><br />
    
    <label for="description"><?=_('Description');?></label><br />
    <textarea id="description" name="description" rows="10" cols="60"><?=set_value('description');?></textarea><br />
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>

    <input type="submit" name="submitNewTeam" value="<?=_('Add');?>" />
</form>
