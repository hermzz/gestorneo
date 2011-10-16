<?php if($tournament): ?>
	<script type="text/javascript">
		var last_values = false;
		var tournament_id = <?=$tournament->id;?>;
		
		$(document).ready(function() {
			$('form.approve_player select').change(function(e) {
				$(e.target).parent().submit();
			});
			
			$('#tn_toggle').click(function() {
				$('#travel_details').hide();
				$('#tournament_notes').show();
				
				$('#tn_toggle').attr('class', 'active');
				$('#td_toggle').attr('class', '');
				
				return false;
			});
			
			$('#td_toggle').click(function() {
				$('#tournament_notes').hide();
				$('#travel_details').show();
				
				$('#td_toggle').attr('class', 'active');
				$('#tn_toggle').attr('class', '');
				
				return false;
			});
			
			$('#include_player_dialog').dialog({
				autoOpen: false,
				modal: true,
				close: function() { location.reload(); }
			});
			
			$('#include_player').click(function() {
				$('#include_player_dialog').dialog('open');
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
				open: function(event, ui) 
				{
					last_values = false; 
					$('input[name="add_player"]').attr('disabled', 'disabled');
				
				},
				select: function(event, ui) { 
					last_values = ui.item; 
					$('input[name="add_player"]').attr('disabled', false);
				},
				close: function(event, ui) {
					$('input[name="player_autocomplete"]').val(last_values.label);
				}
			});
			
			
			$('#include_player_dialog form').submit(function() {
				$.ajax({
					url: '/ajax/invite_player_to_tournament',
					dataType: "jsonp",
					data: {
						tid: tournament_id,
						pid: last_values.value
					},
					success: function(data) 
					{
						if(data.success)
						{
							$('#include_player_dialog p').html('Success!');
							$('input[name="player_autocomplete"]').val('');
							last_values = false;
							$('input[name="add_player"]').attr('disabled', false);
						} else {
							$('#include_player_dialog p').html('Dang it, something failed.');
						}
					}
				});
			
				return false;
			});
		});
	</script>
	
	<?php
		$is_tournament_admin = $this->tank_auth->is_admin(array('tournament' => $tournament->id));
	?>
	
	<?php if($is_tournament_admin): ?>
		<ul class="tabs">
		<li class="dropdown pull-right" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin</a>
			<ul class="dropdown-menu">
				<li><a href="/tournament/email/<?=$tournament->id;?>"><?=_('Email team');?></a></li>
				<li><a href="/tournament/edit/<?=$tournament->id;?>"><?=_('Edit');?></a></li>
				<li><a href="/tournament/payments/<?=$tournament->id;?>"><?=_('Payments');?></a></li>
				<li><a href="#" id="include_player"><?=_('Include player');?></a></li>
			</ul>
		</li>
	</ul>
	<?php endif; ?>
	
	<h1>
		<?=sprintf(_('%s <span class="header-small">on %s</span>'), $tournament->name, strftime('%A %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
	</h1>
	
	<div class="row">
		<div class="span8">
			<h3><?=_('Players confirmed');?></h3>
		
			<?php if($teams): ?>
				<?php foreach($teams as $team): ?>
					<h4><?=$team->name;?> (<?=$team->males;?>M / <?=$team->females;?>F)</h4>
				
					<ul class="player_list">
						<?php if($team->players): ?>
							<?php foreach($team->players as $k => $player): ?>
								<li class="<?=$k % 2 ? 'even': 'odd';?>">
								<a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
									<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
										<a class="admin_controls" href="/tournament/drop_player/<?=$tournament->id;?>/<?=$player->id;?>"><?=_('Drop');?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						<?php else: ?>
							<li><?=_('No players assigned to this team yet');?></li>
						<?php endif; ?>
					</ul>
				<?php endforeach; ?>
			<?php endif; ?>
		
			<?php if($unassigned['players']): ?>
				<h4><?=_('Unassigned players');?> (<?=$unassigned['males'];?>M / <?=$unassigned['females'];?>F)</h4>
				<ul class="player_list">
					<?php foreach($unassigned['players'] as $k => $player): ?>
						<li class="<?=$k % 2 ? 'even': 'odd';?>">
						<a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
							<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
								 <form class="approve_player admin_controls" action="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>" method="post">
								 	<select name="team_id">
								 		<option value="0"><?=_('no team');?></value>
										 <?php foreach($teams as $team): ?>
										 	<option value="<?=$team->id;?>"><?=$team->name;?></option>
										 <?php endforeach; ?>
									</select>
									or <a href="/tournament/drop_player/<?=$tournament->id;?>/<?=$player->id;?>"><?=_('Drop');?></a>
								 </form>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
	
			<h3><?=_('Waiting list');?> (<?=$waiting['males'];?>M / <?=$waiting['females'];?>F)</h3>
			<?php if($waiting['players']): ?>
				<ul class="player_list">
					<?php foreach($waiting['players'] as $k => $player): ?>
						<li class="<?=$k % 2 ? 'even': 'odd';?>">
							<a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
							<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
								 <form class="approve_player admin_controls" action="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>" method="post">
								 	<select name="team_id">
								 		<option value="invalid"><?=_('Assign to');?></value>
										 <?php foreach($teams as $team): ?>
										 	<option value="<?=$team->id;?>"><?=$team->name;?></option>
										 <?php endforeach; ?>
										 <option value="0"><?=_('no team');?></option>
									</select>
								 </form>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p><?=_('No one\'s been left out, yay!');?></p>
			<?php endif; ?>
		</div>
		
		<div class="span8">
			<div id="tournament_notes">
				<?=$tournament->notes ? markdown($tournament->notes) : '<p>'._('No notes').'</p>'; ?>
			</div>
	
			<div id="tournament_signup">
				<?php if(!$this->tournament_model->is_old($tournament)):?>
					<?php if($this->tournament_model->undeadlined($tournament)):?>
						<p><?=sprintf(_('The signup deadline for this tournament is %s'), strftime('%A %e, %B %Y', mysql_to_unix($tournament->signup_deadline)));?></p>
		
						<?php if($this->tournament_model->is_signed_up($tournament->id, $this->tank_auth->get_user_id())): ?>
							<form action="/tournament/cancel_sign_up" method="post">
								<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
								<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
							
								<p><?=_('You\'re already signed up.');?></p>
							
								<input type="submit" name="submitCancel" class="btn danger" value="<?=_('Cancel');?>" />
							</form>
						<?php elseif($this->tournament_model->can_sign_up($tournament->id, $this->tank_auth->get_user_id())): ?>
							<form action="/tournament/sign_up" method="post">
								<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
								<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
							
								<p><?=_('You aren\'t signed up yet.');?></p>
							
								<input type="submit" name="submitSignup" class="btn success" value="<?=_('Signup');?>" />
							</form>
						<?php endif; ?>
					<?php else: ?>
						<p class="alert-message warning"><?=_('The signup deadline for this tournament has already passed, you\'re too late!');?></p>
					<?php endif; ?>
				<?php else: ?>
					<p class="alert-message warning"><?=_('This tournament has already passed');?></p>
				<?php endif; ?>
			</div>
		
			<div id="travel_details">
				<p><a href="/tournament/add_trip_leg/<?=$tournament->id;?>"><?=_('Add trip leg');?></a></p>

				<?php if($trips): ?>
					<?php foreach(array('way', 'return') as $direction): ?>
						<?php if(isset($trips[$direction])): ?>
							<h2><?=$direction == 'way' ? _('Going to') : _('Return');?></h2>
							<ul>
								<?php foreach($trips[$direction] as $trip): ?>
									<li>
										<?php switch($trip->trip_type):
											case 'car': ?> 
												<?=sprintf(
												_('Car from %s to %s, leaving on %s'), 
												$trip->origin, 
												$trip->destination, 
												strftime('%A %e, %B %Y @%R', mysql_to_unix($trip->departure_time)));
												?>
												<?php break; ?>
											<?php default: ?>
												<?=$trip->trip_name;?>,
												<?=$trip->origin;?> &rarr; <?=$trip->destination;?>, 
												<?=strftime('%a %e, %R-', mysql_to_unix($trip->departure_time));?><?=strftime('%R', mysql_to_unix($trip->arrival_time));?>
												<?php break; ?>
										<?php endswitch; ?><br />

										<?=_('On this trip');?>:
										<?php if($trip->passengers): ?>
											<?=implode(', ', array_map(function($p){ return $p->username; }, $trip->passengers));?><br />
										<?php endif; ?>

										<form action="#" method="post"  class="trip_signup">
											<input type="hidden" name="tlid" value="<?=$trip->leg_id;?>" />

											<?php if($trip->player_on_it): ?>
												<input type="submit" name="signoffFromTrip" value="Not going"
											<?php else: ?>
												<input type="submit" name="signupToTrip" value="Going" />
											<?php endif; ?>
										</form>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
			
			
	
		
	
	<div id="include_player_dialog">
		<form action="#" method="post">
			<input type="text" name="player_autocomplete" />
			
			<input type="submit" name="add_player" value="<?=_('Add');?>" disabled="disabled" />
			
			<p></p>
		</form>
	</div>
	
<?php else: ?>
	<p><?=_('Tournament not found');?></p>
<?php endif; ?>
