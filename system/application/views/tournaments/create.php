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
				if(team_ids.length == 0)
				{
					$('#teams_container').html('');
				}
				
				$('#teams_container').append('<li class="r-'+ui.item.value+'">'+ui.item.label+' [<a href="#">x</a>]</li>');
				
				$('#teams_container .r-'+ ui.item.value+' a').click(function (e) {
					team_id = $(e.target).parent().attr('class').match(/r-([0-9]+)/)[1];
					team_ids.splice(team_ids.indexOf(team_id), 1);
					$(e.target).parent().remove();
					
					return false;
				});
				
				team_ids.push(ui.item.value);
			},
			close: function() {	
				$('input[name="teams_autocomplete"]').val('');
			}
		});
		
		$('input[name="players_autocomplete"]').autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '/ajax/player_autocomplete',
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
				if(admin_ids.length == 0)
				{
					$('#players_container').html('');
				}
				
				$('#players_container').append('<li class="r-'+ui.item.value+'">'+ui.item.label+' [<a href="#">x</a>]</li>');
				
				$('#players_container .r-'+ ui.item.value+' a').click(function (e) {
					player_id = $(e.target).parent().attr('class').match(/r-([0-9]+)/)[1];
					admin_ids.splice(admin_ids.indexOf(player_id), 1);
					$(e.target).parent().remove();
					
					return false;
				});
				
				admin_ids.push(ui.item.value);
			},
			close: function() {	
				$('input[name="players_autocomplete"]').val('');
			}
		});
		
		$('#tournament_form').submit(function() {
			$.each(team_ids, function (i, v) {
				$('#tournament_form').append('<input type="hidden" name="teams[]" value="'+v+'" /'+'>');
			});
			$.each(admin_ids, function (i, v) {
				$('#tournament_form').append('<input type="hidden" name="admins[]" value="'+v+'" /'+'>');
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
    	<ul id="teams_container">
    		<li>No teams selected</li>
    	</ul>
    </fieldset>
    
    <fieldset>
    	<legend><?=_('Admins');?></legend>
    	<input type="text" name="players_autocomplete" />
    	<ul id="players_container">
    		<li>No players selected</li>
    	</ul>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" />
</form>
