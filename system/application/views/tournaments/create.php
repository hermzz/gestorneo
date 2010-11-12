<h2><?=_('New tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$('#start_calendar').click(function() {
			$('#start_datepicker').toggle();
		});
	
		$("#start_datepicker").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$('#start_date').attr('value', dateText);
				$('#start_datepicker').hide();
			}
		});
		
		$('#end_calendar').click(function() {
			$('#end_datepicker').toggle();
		});
		
		$("#end_datepicker").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$('#end_date').attr('value', dateText);
				$('#end_datepicker').hide();
			}
		});
		
		$('#deadline_calendar').click(function() {
			$('#deadline_datepicker').toggle();
		});
		
		$("#deadline_datepicker").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$('#deadline_date').attr('value', dateText);
				$('#deadline_datepicker').hide();
			}
		});
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" value="<?=set_value('name');?>" /><br />

    <label for="start_date"><?=_('Start date');?></label>
    <input type="text" id="start_date" name="start_date" value="<?=set_value('start_date');?>" />
    <a href="#" id="start_calendar"><img src="/static/images/calendar.png" /></a>
    
    <div class="datepicker" id="start_datepicker">&nbsp;</div><br />

    <label for="end_date"><?=_('End date');?></label>
    <input type="text" id="end_date" name="end_date" value="<?=set_value('end_date');?>" />
    <a href="#" id="end_calendar"><img src="/static/images/calendar.png" /></a>
    
    <div class="datepicker" id="end_datepicker">&nbsp;</div><br />

    <label for="deadline_date"><?=_('Signup deadline');?></label>
    <input type="text" id="deadline_date" name="deadline_date" value="<?=set_value('deadline_date');?>" />
    <a href="#" id="deadline_calendar"><img src="/static/images/calendar.png" /></a>
    
    <div class="datepicker" id="deadline_datepicker">&nbsp;</div><br />
    
    <label for="notes"><?=_('Notes');?></label><br />
    <textarea id="notes" name="notes" rows="20" cols="60"><?=set_value('notes');?></textarea><br />
    
    <fieldset>
    	<legend>Teams</legend>
		<?php foreach($teams as $team): ?>
			<input type="checkbox" id="team-<?=$team->id;?>" name="teams[]" value="<?=$team->id;?>" <?=set_checkbox('teams[]', $team->id);?> />
			<label for="team-<?=$team->id;?>"><?=$team->name;?></label><br />
		<?php endforeach; ?>
    </fieldset>
    
    <fieldset>
    	<legend>Admins</legend>
		<?php foreach($users as $user): ?>
			<?php if($user->level == 'user'): ?>
				<input type="checkbox" id="user-<?=$user->id;?>" name="admin_users[]" value="<?=$user->id;?>" <?=set_checkbox('admin_users[]', $user->id);?> />
				<label for="user-<?=$user->id;?>"><?=$user->username;?></label><br />
			<?php endif; ?>
		<?php endforeach; ?>
    </fieldset>
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" />
</form>
