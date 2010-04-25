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
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" /><br />

    <label for="start_date"><?=_('Start date');?></label>
    <input type="text" id="start_date" name="start_date" />
    <a href="#" id="start_calendar"><img src="/static/images/calendar.png" /></a>
    
    <div class="datepicker" id="start_datepicker">&nbsp;</div><br />
    
    <label for="notes"><?=_('Notes');?></label><br />
    <textarea id="notes" name="notes" rows="20" cols="60"></textarea><br />

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" />
</form>
