<h2><?=_('Edit tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$("#start_date").datepicker({
			dateFormat: 'dd/mm/yy',
		});
		
		$("#end_date").datepicker({
			dateFormat: 'dd/mm/yy',
		});
		
		$("#deadline_date").datepicker({
			dateFormat: 'dd/mm/yy',
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
				if($('#teams_container .teams').length == 0)
				{
					$('#teams_container').html('');
				}
				
				$('#teams_container').append(
					'<li class="teams r-'+ui.item.value+'">' + ui.item.label + 
					'<input type="hidden" name="teams[]" value="'+ui.item.value+'" /'+'>' +
					' [<a href="#">x</a>]</li>'
				);
				
				$('#teams_container .r-'+ ui.item.value+' a').click(function (e) {
					$(e.target).parent().remove();
					
					return false;
				});
			},
			close: function() {	
				$('input[name="teams_autocomplete"]').val('');
			}
		});
		
		$('#teams_container a').click(function (e) {
			$(e.target).parent().remove();
			
			return false;
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
				if($('#players_container .players').length == 0)
				{
					$('#players_container').html('');
				}
				
				$('#players_container').append(
					'<li class="players r-'+ui.item.value+'">' + ui.item.label + 
					'<input type="hidden" name="admins[]" value="'+ui.item.value+'" /'+'>' +
					' [<a href="#">x</a>]</li>'
				);
				
				$('#players_container .r-'+ ui.item.value+' a').click(function (e) {
					$(e.target).parent().remove();
					
					return false;
				});
			},
			close: function() {	
				$('input[name="players_autocomplete"]').val('');
			}
		});
		
		$('#players_container a').click(function (e) {
			$(e.target).parent().remove();
			
			return false;
		});
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" value="<?=set_value('name', $tournament->name);?>" /><br />

    <label for="start_date"><?=_('Start date');?></label>
    <input type="text" id="start_date" name="start_date" value="<?=set_value('start_date', strftime('%d/%m/%Y', mysql_to_unix($tournament->start_date)));?>" /><br />

    <label for="end_date"><?=_('End date');?></label>
    <input type="text" id="end_date" name="end_date" value="<?=set_value('end_date', strftime('%d/%m/%Y', mysql_to_unix($tournament->end_date)));?>" /><br />

    <label for="deadline_date"><?=_('Signup deadline');?></label>
    <input type="text" id="deadline_date" name="deadline_date" value="<?=set_value('deadline_date', strftime('%d/%m/%Y', mysql_to_unix($tournament->signup_deadline)));?>" /><br />
    
    <label for="notes"><?=_('Notes');?></label>
    <textarea id="notes" name="notes" rows="20" cols="60"><?=set_value('notes', $tournament->notes);?></textarea><br />
    
    <fieldset>
    	<legend><?=_('Teams');?></legend>
    	<input type="text" name="teams_autocomplete" />
    	<ul id="teams_container">
    		<?php if($teams): ?>
    			<?php foreach($teams as $team): ?>
    				<?php if(in_array($team->id, $selected_teams)): ?>
    					<li class="teams r-<?=$team->id;?>">
    						<?=$team->name;?>
							<input type="hidden" name="teams[]" value="<?=$team->id;?>" />
							[<a href="#">x</a>]
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
    		<?php else: ?>
	    		<li>No teams selected</li>
	    	<?php endif; ?>
    	</ul>
    </fieldset>
    
    <fieldset>
    	<legend><?=_('Admins');?></legend>
    	<input type="text" name="players_autocomplete" />
    	<ul id="players_container">
    		<?php if($users): ?>
    			<?php foreach($users as $user): ?>
    				<?php if(in_array($user->id, $tournament_admins)): ?>
    					<li class="players r-<?=$user->id;?>">
    						<?=$user->username;?>
							<input type="hidden" name="admins[]" value="<?=$user->id;?>" />
							[<a href="#">x</a>]
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
    		<?php else: ?>
	    		<li>No players selected</li>
	    	<?php endif; ?>
    	</ul>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?=_('Edit');?>" />
</form>
