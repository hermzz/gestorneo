<h2><?=_('Tournament payments')?></h2>

<script type="text/javascript">
	$(document).ready(function (){
		$('#new_payment_dialog').dialog({
			autoOpen: false,
			modal: true,
			minWidth: 500,
			minHeight: 300,
			close: function() { location.reload(); }
		});
	
		$('#new_payment').click(function() {
			$('#new_payment_dialog').dialog('open');
		});
		
		$('input[name="player_autocomplete"]').autocomplete({
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
			select: function(event, ui) { 
				$('#payment_player_list').append(
					'<li>' + ui.item.label + ' [<a href="#">x</a>]' + 
					'<input type="hidden" name="pids[]" value="' + ui.item.id + '" /'+'>' + 
					'</li>'
				);
				
				as = $('#payment_player_list a');
				console.log(as[as.length-1]);
				$(as[as.length-1]).click(function(e) {
					$(e.target).parent().remove();
				});
			},
			close: function() {	
				$('input[name="player_autocomplete"]').val('');
			}
		});
		
		$('#new_payment_dialog form').submit(function() {
			$.ajax({
				url: '/ajax/add_payments',
				dataType: "jsonp",
				type: 'POST',
				data: {
					tid: <?=$tournament->id;?>,
					concept: $('#concept').val(),
					amount: $('#amount').val(),
					applies: $('#applies').val()
				},
				success: function(data) 
				{
					if(data.success)
					{
						//location.reload();
					} else {
						console.log('shit');
					}
				}
			});
		
			return false;
		});
		
		$('#payment_details span').editInPlace({
			url: '/ajax/edit_payment',
		});
	});
</script>

<p><a href="#" id="new_payment"><?=_('Add new payment');?></a></p>

<?php if($payment_details): ?>
	<ul id="payment_details">
		<?php foreach($payment_details as $payment): ?>
			<li>
				<?=$payment['player']->username;?>, <?=sprintf(_('owes %0.2d, paid %0.2d'), $payment['totals']['owes'], $payment['totals']['paid']);?> [<a href="#" class="toggle_deets">+</a>]
				<div class="payment_deets">
					<ul>
						<?php foreach($payment['payments'] as $detail): ?>
							<li>
								<?=$detail['concept'];?>, 
								<?=sprintf(_('owes %0.2d, paid <span id="player-%d">%0.2d</span>'), $detail['amount'], $detail['tpid'], $detail['paid']);?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No payments added yet');?>
<?php endif; ?>

<div id="new_payment_dialog">
	<form action="#" method="post">
		<input type="hidden" name="tid" value="<?=$tournament->id;?>" />
		
		<label for="concept"><?=_('Concept');?></label>
		<input type="text" id="concept" name="concept" /><br />
		
		<label for="amount"><?=_('Amount');?></label>
		<input type="text" id="amount" name="amount" />
		
		<p><?=_('Applies to');?>:</p>
		
		<input type="radio" name="applies" value="all_team" checked="checked" />
		<label for="all_team" class="radio"><?=_('All the team');?></label>
		<br />
		
		<input type="radio" name="applies" class="radio" value="individuals" />
		<label for="individuals" class="radio"><?=_('Only some players');?></label>
		<br />
		
		<input type="text" name="player_autocomplete" />
		<ul id="payment_player_list"></ul>
		
		<input type="submit" name="add_payment" value="<?=_('Add');?>" />
	</form>
</div>
