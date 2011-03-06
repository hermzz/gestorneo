<h2><?=sprintf(_('Add trip leg for %s'), '<a href="/tournament/view/'.$tournament->id.'">'.$tournament->name.'</a>');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/javascript/jquery.timepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#departure_time').datetimepicker(
			{
				dateFormat: 'dd/mm/yy'
			}
		);
		
		$('#arrival_time').datetimepicker(
			{
				dateFormat: 'dd/mm/yy'
			}
		);
		
		$('#car_departure_time').datetimepicker(
			{
				dateFormat: 'dd/mm/yy'
			}
		);
		
		$('#trip_type').change(function(e) {
			val = $(e.target).val();
			
			if(val == "car")
			{
				$('#other_transportation').hide();
				$('#car_transportation').show();
			} else if(val != "") {
				$('#car_transportation').hide();
				$('#other_transportation').show();
				$('#trip_type_span').html(val);
			}
		});
		
		$('#trip_type').trigger('change');
	});
</script>

<style type="text/css">
	fieldset {
		display: none;
	}
</style>

<?=validation_errors()?>

<form action="#" method="post">
	
	<!-- Airplane, bus, train, boat, etc... details -->

	<label for="trip_type"><?=_('Trip type');?></label>
	<?=
		form_dropdown('trip_type', 
			array(
				'' => _('Choose one'),
				'airplane' => _('Airplane'),
				'bus' => _('Bus'),
				'train' => _('Train'),
				'boat' => _('Boat'),
				'car' => _('Car')
			), 
			set_value('trip_type', ''), 
			'id="trip_type"'
		); 
	?><br /><br />

	<fieldset id="other_transportation">
		
		<legend><?=_('Trip by <span id="trip_type_span">?</span>');?></legend>
		
		<label for="trip_name"><?=_('Trip name');?></label>
		<input type="text" id="trip_name" name="trip_name" value="<?=set_value('trip_name');?>" />
		<span class="form_help">(<?=_('ie: EZY 1234 o AVE 03112');?>)</span>
		<br />
	
		<label for="other_origin"><?=_('Origin');?></label>
		<input type="text" id="other_origin" name="other_origin" value="<?=set_value('other_origin');?>" /><br />
	
		<label for="departure_time"><?=_('Departure time');?></label>
		<input type="text" id="departure_time" name="departure_time" value="<?=set_value('departure_time');?>" /><br />
		
		<label for="other_destination"><?=_('Destination');?></label>
		<input type="text" id="other_destination" name="other_destination" value="<?=set_value('other_destination');?>" /><br />
	
		<label for="arrival_time"><?=_('Arrival time');?></label>
		<input type="text" id="arrival_time" name="arrival_time" value="<?=set_value('arrival_time');?>" /><br />
    
    	<input type="submit" name="submitTripByOther" value="Save" />
	
	</fieldset>
	
	<!-- Car details -->
	
	<fieldset id="car_transportation">
	
		<legend><?=_('Trip by car');?></legend>
	
		<label for="car_origin"><?=_('Origin');?></label>
		<input type="text" id="car_origin" name="car_origin" value="" /><br />
	
		<label for="car_destination"><?=_('Destination');?></label>
		<input type="text" id="car_destination" name="car_destination" value="" /><br />
	
		<label for="car_departure_time"><?=_('Departure time');?></label>
		<input type="text" id="car_departure_time" name="car_departure_time" value="" /><br />
    
    	<input type="submit" name="submitTripByCar" value="Save" />
    
    </fieldset>
	
</form>
