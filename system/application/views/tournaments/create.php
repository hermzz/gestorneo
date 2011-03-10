<h2><?=_('New tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$("#start_date").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$('#end_date').attr('value', dateText);
				$('#deadline_date').attr('value', dateText);
			}
		});
		
		$("#end_date").datepicker({
			dateFormat: 'dd/mm/yy'
		});
		
		$("#deadline_date").datepicker({
			dateFormat: 'dd/mm/yy'
		});
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" value="<?=set_value('name');?>" /><br />

    <label for="start_date"><?=_('Start date');?></label>
    <input type="text" id="start_date" name="start_date" value="<?=set_value('start_date');?>" /><br />

    <label for="end_date"><?=_('End date');?></label>
    <input type="text" id="end_date" name="end_date" value="<?=set_value('end_date');?>" /><br />

    <label for="deadline_date"><?=_('Signup deadline');?></label>
    <input type="text" id="deadline_date" name="deadline_date" value="<?=set_value('deadline_date');?>" /><br />
    
    <label for="notes"><?=_('Notes');?></label>
    <textarea id="notes" name="notes" rows="20" cols="60"><?=set_value('notes');?></textarea><br />
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>
    
    <fieldset>
    	<legend><?=_('Teams');?></legend>
		<?php foreach($teams as $team): ?>
			<input type="checkbox" id="team-<?=$team->id;?>" name="teams[]" value="<?=$team->id;?>" <?=set_checkbox('teams[]', $team->id);?> />
			<label for="team-<?=$team->id;?>" class="checkbox"><?=$team->name;?></label><br />
		<?php endforeach; ?>
    </fieldset>
    
    <fieldset>
    	<legend><?=_('Admins');?></legend>
		<?php foreach($users as $user): ?>
			<?php if($user->level == 'user'): ?>
				<input type="checkbox" id="user-<?=$user->id;?>" name="admin_users[]" value="<?=$user->id;?>" <?=set_checkbox('admin_users[]', $user->id);?> />
				<label for="user-<?=$user->id;?>" class="checkbox"><?=$user->username;?></label><br />
			<?php endif; ?>
		<?php endforeach; ?>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" />
</form>
