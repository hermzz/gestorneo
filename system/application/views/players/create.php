<h2>New player</h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$("#datepicker").datepicker({
			onSelect: function(dateText, inst) {
				$('#joined').attr('value', dateText);
			}
		});
	});
</script>

<?=validation_errors()?>

<form action="#" method="post">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" /><br />

    <label for="name">Joined</label>
    <input type="text" id="joined" name="joined" /><br />
    
    <div id="datepicker">&nbsp;</div>

    <input type="submit" name="submitNewPlayer" value="Add" />
</form>
