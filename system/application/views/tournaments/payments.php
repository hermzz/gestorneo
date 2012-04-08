<?php if($this->tank_auth->is_admin()): ?>
	<ul class="nav nav-tabs">
		<li class="dropdown pull-left" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="#" id="new_payment" data-controls-modal="new_payment_dialog" data-backdrop="static"><?=_('Add new payment');?></a></li>
			</ul>
		</li>
	</ul>
<?php endif; ?>

<h2><?=_('Tournament payments')?></h2>

<script type="text/javascript">
	var PAID_TXT = "<?=_('Paid');?>";
	var NOT_PAID_TXT = "<?=_('Not paid');?>";
	
	$(document).ready(function (){
		$('#new_payment_dialog').modal();
		
		$('#new_payment_dialog').bind('show', function(e) {
			$('#concept').attr('value', '');
			$('#amount').attr('value', '');
		
			$('input[value="all_team"]').attr('checked', 'checked');
		
			$('#payment_player_list').html('');
		
			$('input[name="tpid"]').attr('value', '');
		});
		
		$('input[name="player_autocomplete"]').autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '/ajax/player_autocomplete',
					dataType: "jsonp",
					data: {
						term: request.term,
						tournament_id: <?=$tournament->id;?>,
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
				add_player_to_modal(ui.item.value, ui.item.label);
			},
			close: function() {	
				$('input[name="player_autocomplete"]').val('');
			}
		});
		
		$('#new_payment_dialog form').submit(function(f) {
			$.ajax({
				url: '/ajax/add_payments',
				dataType: "jsonp",
				type: 'POST',
				data: $(f.target).serialize(),
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
				url: '/ajax/set_paid',
				dataType: "jsonp",
				type: 'POST',
				data: {
					tpid: $(e.target).attr('tpid'),
					plid: $(e.target).attr('plid'),
					paid: parseInt($(e.target).attr('paid')) ? 0 : 1
				},
				success: function(data) 
				{
					if(data.success)
					{
						if($(e.target).attr('paid') == 1)
						{
							$(e.target).html(NOT_PAID_TXT);
							$(e.target).attr('paid', '0');
						} else {
							$(e.target).html(PAID_TXT);
							$(e.target).attr('paid', '1');
						}
					}
				}
			});
			
			return false;
		});
		
		$('#payment_table th a.edit_payment').click(function(e) {
			$.ajax({
				url: '/ajax/get_payment_data',
				dataType: "jsonp",
				type: 'POST',
				data: {
					tpid: $(e.target).parent().attr('tpid'),
				},
				success: function(response) 
				{
					if(response.success)
					{
						$('#new_payment_dialog').modal().show();
						
						$('#concept').attr('value', response.data.concept);
						$('#amount').attr('value', response.data.amount);
						
						$('input[value="individuals"]').attr('checked', 'checked');
						
						$.each(response.data.players, function(k, v) {
							add_player_to_modal(v.plid, v.username);
						});
						
						$('input[name="tpid"]').attr('value', response.data.tpid);
					}
				}
			});
			
			return false;
		});
		
		$('#payment_table th a.delete_payment').click(function(e) {
			if(confirm("<?=_("Are you sure you want to delete this payment?");?>"))
			{
				$.ajax({
					url: '/ajax/delete_payment',
					dataType: "jsonp",
					type: 'POST',
					data: {
						tpid: $(e.target).parent().attr('tpid'),
					},
					success: function(response) 
					{
						if(response.success)
						{
							location.reload();
						}
					}
				});
			}
			
			return false;
		});
	});
	
	function add_player_to_modal(player_id, player_name)
	{
		$('#payment_player_list').append(
			'<li>' + player_name + ' [<a href="#">x</a>]' + 
			'<input type="hidden" name="pids[]" value="' + player_id + '" /'+'>' + 
			'</li>'
		);
		
		as = $('#payment_player_list a');
		$(as[as.length-1]).click(function(e) {
			$(e.target).parent().remove();
			return false;
		});
	}
</script>

<?php if($payments): ?>
	<table id="payment_table" class="table table-striped">
		<thead>
			<tr>
				<th class="span3"><?=_('Players');?></th>
				<?php foreach($payments as $payment): ?>
					<th class="span3"><?=$payment->concept;?> - €<?=$payment->amount;?>
						<?php if($this->tank_auth->is_admin(array('tournament' => $tournament->id))): ?>
							<a href="#" class="edit_payment" tpid="<?=$payment->tpid;?>"><img src="/static/images/Boolean/Papermart/Text Edit.png" /></a>
							<a href="#" class="delete_payment" tpid="<?=$payment->tpid;?>"><img src="/static/images/Boolean/Signage/Close Square.png" /></a>
						<?php endif; ?>
					</th>
				<?php endforeach; ?>
				<th class="span3"><?=_('Owes');?></th>
			</th>
		</thead>
		<tbody>
			<?php foreach($players as $player): ?>
				<tr class="<?=$player->amount_owed > 0 ? 'bad' : 'good' ;?>">
					<td class="span3"><?=$player->username;?></td>
					<?php foreach($payments as $payment): ?>
						<td class="span3">
							<?php
								foreach($payment->players as $p)
									if($p->plid == $player->id)
										if($this->tank_auth->is_admin())
										{
											echo '<a href="#" paid="'.$p->paid.'" class="payment_links" tpid="'.$payment->tpid.'" plid="'.$p->plid.'">' 
												. ($p->paid ? _('Paid') : _('Not paid')) . '</a>';
										} else {
											echo $p->paid ? _('Paid') : _('Not paid');
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
			<input type="hidden" name="tpid" value="" />
		
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
		
			<input type="submit" name="add_payment" class="btn btn-primary" value="<?=_('Add');?>" />
		</form>
	</div>
</div>
