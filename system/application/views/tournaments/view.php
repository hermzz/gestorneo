<?php if($tournament): ?>
	<script type="text/javascript">
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
		});
	</script>
	
	<?php
		$is_tournament_admin = $this->tank_auth->is_admin(array('tournament' => $tournament->id));
	?>
	
	<h1>
		<?=sprintf(_('%s <span class="header-small">on %s</span>'), $tournament->name, strftime('%A %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
	</h1>
	
	<div id="content_wrapper">
		<div id="right_column">
			<div id="tournament_signup">
				<?php if(!$this->tournament_model->is_old($tournament)):?>
					<?php if($this->tournament_model->undeadlined($tournament)):?>
						<p><?=sprintf(_('The signup deadline for this tournament is %s'), strftime('%A %e, %B %Y', mysql_to_unix($tournament->signup_deadline)));?></p>
			
						<?php if($this->tournament_model->is_signed_up($tournament->id, $this->tank_auth->get_user_id())): ?>
							<form action="/tournament/cancel_sign_up" method="post">
								<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
								<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
								
								<?=_('You\'re already signed up.');?>
								
								<input type="submit" name="submitCancel" value="<?=_('Cancel');?>" />
							</form>
						<?php elseif($this->tournament_model->can_sign_up($tournament->id, $this->tank_auth->get_user_id())): ?>
							<form action="/tournament/sign_up" method="post">
								<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
								<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
								
								<?=_('You aren\'t signed up yet.');?>
								
								<input type="submit" name="submitSignup" value="<?=_('Signup');?>" />
							</form>
						<?php endif; ?>
					<?php else: ?>
						<p class="message neutral"><?=_('The signup deadline for this tournament has already passed, you\'re too late!');?></p>
					<?php endif; ?>
				<?php else: ?>
					<p class="message neutral"><?=_('This tournament has already passed');?></p>
				<?php endif; ?>
			</div>
		
			<div id="tournament_notes">
				<?=$tournament->notes ? markdown($tournament->notes) : '<p>'._('No notes').'</p>'; ?>
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
	
	<div id="left_column">
		<?php if($is_tournament_admin): ?>
			<p>
				<a href="/tournament/email/<?=$tournament->id;?>"><?=_('Email team');?></a> | 
				<a href="/tournament/edit/<?=$tournament->id;?>"><?=_('Edit tournament');?></a>
			</p>
		<?php endif; ?>
	
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
	
<?php else: ?>
	<p><?=_('Tournament not found');?></p>
<?php endif; ?>
