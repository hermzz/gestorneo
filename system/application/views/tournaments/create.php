<h2><?=_('New tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	team_ids = [];
	admin_ids = [];
	
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
		
		$('input[name="teams_autocomplete"]').autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '/ajax/team_autocomplete',
					dataType: "jsonp",
					data: {
						term: request.term
					},
					success: function(data) 
					{
						if(data.success)
						{
							response($.map(data.results, function(item) 
							{
								return {
									label: item.name,
									value: item.id
								}
							}));
						} else {
							console.log('fail');
						}
					}
				});
			},
			select: function(event, ui) 
			{
				if(team_ids.length > 0)
				{
					$('#teams_container').append(', ');
				} else {
					$('#teams_container').html('');
				}
				
				$('#teams_container').append('<span class="r-'+ui.item.value+'">'+ui.item.label+' [<a href="#">x</a>]</span>');
				
				$('#teams_container .r-'+ ui.item.value+' a').click(function (e) {
					team_id = $(e.target).parent().attr('class').match(/r-([0-9]+)/)[1];
					team_ids.splice(team_ids.indexOf(team_id), 1);
					$(e.target).parent().remove();
				});
				
				team_ids.push(ui.item.value);
			},
			close: function() {	
				$('input[name="teams_autocomplete"]').val('');
			}
		});
		
		$('#tournament_form').submit(function() {
			$.each(team_ids, function (i, v) {
				$('#tournament_form').append('<input type="hidden" name="teams[]" value="'+v+'" /'+'>');
			});
		});
	});
</script>

<?=validation_errors()?>

<form action="#" id="tournament_form" method="post">
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
    	<input type="text" name="teams_autocomplete" />
    	<p id="teams_container">No teams selected</p>
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
