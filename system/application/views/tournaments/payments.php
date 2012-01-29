<?php if($this->tank_auth->is_admin()): ?>
	<ul class="tabs">
		<li class="dropdown pull-right" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin</a>
			<ul class="dropdown-menu">
				<li><a href="#" id="new_payment" data-controls-modal="new_payment_dialog" data-backdrop="static"><?=_('Add new payment');?></a></li>
			</ul>
		</li>
	</ul>
<?php endif; ?>

<h2><?=_('Tournament payments')?></h2>

<script type="text/javascript">
	var PAYED_TXT = "<?=_('Payed');?>";
	var NOT_PAYED_TXT = "<?=_('Not payed');?>";
	
	$(document).ready(function (){
		$('#new_payment_dialog').modal();
		
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
					'<input type="hidden" name="pids[]" value="' + ui.item.value + '" /'+'>' + 
					'</li>'
				);
				
				as = $('#payment_player_list a');
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
					applies: $('[name="applies"][checked="checked"]').val(),
					pids: function() {
						a = [];
						$('[name="pids[]"]').each(function(i,e) {a.push($(e).val()); });
						return a;
					}
				},
				success: function(data) 
				{
					if(data.success)
					{
						location.reload();
					}
				}
			});
		
			return false;
		});
		
		$('.payment_links').click(function(e) {
			$.ajax({
				url: '/ajax/set_payed',
				dataType: "jsonp",
				type: 'POST',
				data: {
					tpid: $(e.target).attr('tpid'),
					plid: $(e.target).attr('plid'),
					payed: parseInt($(e.target).attr('payed')) ? 0 : 1
				},
				success: function(data) 
				{
					if(data.success)
					{
						if($(e.target).attr('payed') == 1)
						{
							$(e.target).html(NOT_PAYED_TXT);
							$(e.target).attr('payed', '0');
						} else {
							$(e.target).html(PAYED_TXT);
							$(e.target).attr('payed', '1');
						}
					}
				}
			});
			
			return false;
		});
	});
</script>

<?php if($payments): ?>
	<table class="span<?=(count($payments) + 2) * 3;?> zebra-striped">
		<thead>
			<tr>
				<th class="span3"><?=_('Players');?></th>
				<?php foreach($payments as $payment): ?>
					<th class="span3"><?=$payment->concept;?> - €<?=$payment->amount;?></th>
				<?php endforeach; ?>
				<th class="span3"><?=_('Owes');?></th>
			</th>
		</thead>
		<tbody>
			<?php foreach($players as $player): ?>
				<tr>
					<td class="span3"><?=$player->username;?></td>
					<?php foreach($payments as $payment): ?>
						<td class="span3">
							<?php
								foreach($payment->players as $p)
									if($p->plid == $player->id)
										if($this->tank_auth->is_admin())
										{
											echo '<a href="#" payed="'.$p->payed.'" class="payment_links" tpid="'.$payment->tpid.'" plid="'.$p->plid.'">' 
												. ($p->payed ? _('Payed') : _('Not payed')) . '</a>';
										} else {
											echo $p->payed ? _('Payed') : _('Not payed');
										}
							?>
						</td>
					<?php endforeach; ?>
					<td><?=$player->amount_owed;?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<p><?=_('No payments added yet');?>
<?php endif; ?>

<div id="new_payment_dialog" class="modal hide fade" style="display: block; ">
	<div class="modal-header">
		<a href="#" class="close">×</a>
		<h3><?=_('Add new payment');?></h3>
	</div>
	<div class="modal-body">
		<form action="#" method="post">
			<input type="hidden" name="tid" value="<?=$tournament->id;?>" />
		
			<div class="clearfix">
				<label for="concept"><?=_('Concept');?></label>
				<div class="input">
					<input type="text" id="concept" name="concept" />
				</div>
			</div>
			
			<div class="clearfix">
				<label for="amount"><?=_('Amount');?></label>
				<div class="input">
					<input type="text" id="amount" name="amount" />
				</div>
			</div>
		
			<div class="clearfix">
				<label><?=_('Applies to');?>:</label>
				<div class="input">
					<ul class="inputs-list">
						<li>
							<label>
								<input type="radio" name="applies" value="all_team" checked="checked" />
								<span><?=_('All the team');?></span>
							</label>
						</li>
						<li>
							<label>
								<input type="radio" name="applies" class="radio" value="individuals" />
								<span>
									<?=_('Only some players');?><br />
									<input type="text" name="player_autocomplete" />
									<ul id="payment_player_list"></ul></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
		
			<input type="submit" name="add_payment" class="btn primary" value="<?=_('Add');?>" />
		</form>
	</div>
</div>
