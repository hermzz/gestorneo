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
	});
</script>

<?=validation_errors()?>

<form action="#" id="tournament_form" method="post">
	<fieldset>
		<div class="clearfix">
			<label for="name"><?=_('Name');?></label>
			<div class="input">
				<input type="text" id="name" name="name" class="span6" value="<?=set_value('name');?>" />
			</div>
		</div>

		<div class="clearfix">
			<label for="start_date"><?=_('Start date');?></label>
			<div class="input">
				<input type="text" id="start_date" name="start_date" class="span2" value="<?=set_value('start_date');?>" />
			</div>
		</div>	

		<div class="clearfix">
			<label for="end_date"><?=_('End date');?></label>
			<div class="input">
				<input type="text" id="end_date" name="end_date" class="span2" value="<?=set_value('end_date');?>" />
			</div>
		</div>	

		<div class="clearfix">
			<label for="deadline_date"><?=_('Signup deadline');?></label>
			<div class="input">
				<input type="text" id="deadline_date" name="deadline_date" class="span2" value="<?=set_value('deadline_date');?>" />
			</div>
		</div>	

		<div class="clearfix">
			<label for="notes"><?=_('Notes');?></label>
			<div class="input">
				<textarea id="notes" name="notes" rows="8" cols="60" class="span12"><?=set_value('notes');?></textarea>
				<p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>
			</div>
		</div>
	</fieldset>
    
    <fieldset>
    	<legend><?=_('Teams');?></legend>
    	
    	<div class="clearfix">
			<div class="input">
				<input type="text" name="teams_autocomplete" />
				
				<ul id="teams_container">
    				<li><?=_('No teams selected');?></li>
		    	</ul>
			</div>
		</div>	
    </fieldset>
    
    <fieldset>
    	<legend><?=_('Admins');?></legend>
    	
    	<div class="clearfix">
			<div class="input">
				<input type="text" name="players_autocomplete" />
				
				<ul id="players_container">
					<li><?=_('No players selected');?></li>
				</ul>
			</div>
		</div>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" class="btn large primary offset2" />
</form>
