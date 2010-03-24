<h2><?=_('New tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$("#datepicker").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$('#date').attr('value', dateText);
			}
		});
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name"><?=_('Name');?></label>
    <input type="text" id="name" name="name" /><br />

    <label for="name"><?=_('Date');?></label>
    <input type="text" id="date" name="date" /><br />
    
    <div id="datepicker">&nbsp;</div>
    
    <label for="notes"><?=_('Notes');?></label><br />
    <textarea id="notes" name="notes"></textarea><br />

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" />
</form>
