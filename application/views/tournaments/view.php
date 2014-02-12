<?php if($tournament): ?>
	<script type="text/javascript">
		var last_values = false;
		// var tournament_id = <?= $tournament->id; ?>;

		$(document).ready(function() {
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

			$('.admin_controls .entypo').click(function(e) {
				$(e.target).next().toggle();

				return false;
			});

			$('#include_player_dialog').bind('hide', function() { location.reload(); });
			$('#include_player_dialog .modal-footer a').click(function() { $('#include_player_dialog').modal('hide'); });

			$('input[name="player_autocomplete"]').autocomplete({
				source: function(request, response) {
					$.ajax({
						url: '/ajax/player_autocomplete',
						dataType: 'jsonp',
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
									};
								}));
							} else {
								//console.log('fail');
							}
						}
					});
				},
				open: function(event, ui) {
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
					dataType: 'jsonp',
					data: {
						tid: tournament_id,
						pid: last_values.value
					},
					success: function(data)
					{
						if(data.success)
						{
							$('#include_player_dialog p').html('<?= _('Success!'); ?>').removeClass('label-error').addClass('label label-success');
							$('input[name="player_autocomplete"]').val('');
							last_values = false;
							$('input[name="add_player"]').attr('disabled', false);
						} else {
							$('#include_player_dialog p').html('<?= _('Dang it, something failed.'); ?>').removeClass('label-success').addClass('label label-error');
						}
					}
				});

				return false;
			});


			/** Player Admin **/
			$('input:checkbox').prettyCheckable({
				color: 'red'
			});
			var $pl = $('input:checkbox[name=player_id]');
			var $pl_pc = $('.prettycheckbox a');
			var $pa = $('.player-actions');
			$('button[class*=select-]').click(function(){
				if($(this).hasClass('select-all')) {
					if($pl_pc.length > 0)
						$pl.not(':checked').siblings('a').click();
					else
						$pl.attr('checked', 'checked').last().change();
				}
				else {
					if($pl_pc.length > 0)
						$pl.filter(':checked').siblings('a').click();
					else
						$pl.removeAttr('checked').last().change();
				}
			});

			$pl.change(function(){
				if($pl.filter(":checked").length > 0) {
					$pa.removeClass("disabled");
				}
				else {
					$pa.addClass('disabled');
				}
			});

			$(".player-admin .team-action li a").click(function(e){
				e.preventDefault();
				$(".player-admin input#team-id").val($(this).data('id'));
				selected_players = [];
				$pl.filter(":checked").each(function(){
					selected_players.push($(this).val());
				});
				$(".player-admin input#player-ids").val(selected_players.join(','));
				$(".player-admin form").submit();
			});
		});
	</script>

	<?php
		$is_tournament_admin = $this->tank_auth->is_admin(array('tournament' => $tournament->id));
	?>

	<?php if($is_tournament_admin): ?>
		<ul class="nav nav-tabs">
			<li class="dropdown pull-left" data-dropdown="dropdown">
				<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="/tournament/email/<?= $tournament->id; ?>"><?= _('Email team'); ?></a></li>
					<li><a href="/tournament/edit/<?= $tournament->id; ?>"><?= _('Edit'); ?></a></li>
					<li><a href="#include_player_dialog" id="include_player" data-toggle="modal"><?= _('Include player'); ?></a></li>
				</ul>
			</li>
		</ul>
	<?php endif; ?>

	<h1 class="left">
		<?= sprintf(_('%s <span class="header_small">on %s</span>'), $tournament->name, strftime('%A '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%d' : '%e').', %B %Y', mysql_to_unix($tournament->start_date))); ?>
	</h1>

		<?php if(!$this->tournament_model->is_old($tournament)):?>
			<?php if($this->tournament_model->undeadlined($tournament)):?>
				<?php if($this->tournament_model->is_signed_up($tournament->id, $this->tank_auth->get_user_id())): ?>
					<form action="/tournament/cancel_sign_up" method="post" class="self-action pull-right">
						<input type="hidden" name="tournament_id" value="<?= $tournament->id; ?>" />
						<input type="hidden" name="player_id" value="<?= $this->tank_auth->get_user_id(); ?>" />

						<input type="submit" name="submitCancel" class="btn btn-danger btn-lg" value="<?= _ ('Not going'); ?>" />
					</form>
				<?php elseif($this->tournament_model->can_sign_up($tournament->id, $this->tank_auth->get_user_id())): ?>
					<form action="/tournament/sign_up" method="post" class="pull-right self-action">
						<input type="hidden" name="tournament_id" value="<?= $tournament->id; ?>" />
						<input type="hidden" name="player_id" value="<?= $this->tank_auth->get_user_id(); ?>" />

						<input type="submit" name="submitSignup" class="btn btn-success btn-lg" value="<?= _ ('I want to go!'); ?>" />
					</form>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

	<div class="row cb">
		<div class="col-md-6">


			<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
			<div class="panel panel-default player-admin">
				<div class="panel-body">
					<label><?= _('Team Admin'); ?>:</label>
					<div class="btn-group">
						<button type="button" class="btn btn-default select-all"><?= _('Select All'); ?></button>
						<button type="button" class="btn btn-default select-none"><?= _('Select None'); ?></button>

						<div class="btn-group team-action">
							<button type="button" class="btn btn-primary dropdown-toggle player-actions disabled" data-toggle="dropdown">
								<?= _('Player Action') ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<?php foreach($teams as $team): ?>
								<li><a href="#" data-id="<?= $team->id; ?>"><?= _('Add to'); ?> <?= $team->name; ?></a></li>
								<?php endforeach; ?>
								<li><a href="#" data-id="-1"><?= _('Add to Waiting List'); ?></a></li>
								<li><a href="#" data-id="0"><?= _('Remove from Tournament'); ?></a></li>
							</ul>
					 	</div>
					</div>
				</div>
				<form class="assign-player" action="<?= site_url('tournament/assign_players/' . $tournament->id); ?>" method="post">
					<input type="hidden" name="team_id" id="team-id" />
					<input type="hidden" name="player_ids" id="player-ids" />
				</form>
				</form>
			</div>
			<?php endif; ?>

			<h3><?= _('Players confirmed'); ?></h3>

			<?php if($teams): ?>
				<?php foreach($teams as $team): ?>
					<h4>
						<?= $team->name; ?>
						<?= $team->males + $team->females; ?>:
						<span class="badge badge-info"><?= $team->males; ?>M</span>
						/
						<span class="badge badge-danger"><?= $team->females; ?>F</span>
					</h4>

					<ul class="player_list">
						<?php if($team->players): ?>
							<?php foreach($team->players as $k => $player): ?>
								<li class="<?= $k % 2 ? 'even': 'odd'; ?>">
								<a href="/player/view/<?= $player->id; ?>"><?= $player->username; ?></a>
									<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
										<div class="admin_controls">
											<input type="checkbox" name="player_id" value="<?= $player->id; ?>" />
										</div>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						<?php else: ?>
							<li><?= _('No players assigned to this team yet'); ?></li>
						<?php endif; ?>
					</ul>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if($unassigned['players']): ?>
				<h4><?= _('No longer going'); ?> [<?= $unassigned['males']; ?>M / <?= $unassigned['females']; ?>F]</h4>
				<ul class="player_list">
					<?php foreach($unassigned['players'] as $k => $player): ?>
						<li class="<?= $k % 2 ? 'even': 'odd'; ?>">
						<a href="/player/view/<?= $player->id; ?>"><?= $player->username; ?></a>
							<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
								<div class="admin_controls">
									 <input type="checkbox" name="player_id" value="<?= $player->id; ?>" />
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<h3><?= _('Waiting list'); ?> [<?= $waiting['males']; ?>M / <?= $waiting['females']; ?>F]</h3>
			<?php if($waiting['players']): ?>
				<ul class="player_list">
					<?php foreach($waiting['players'] as $k => $player): ?>
						<li class="<?= $k % 2 ? 'even': 'odd'; ?>">
							<a href="/player/view/<?= $player->id; ?>"><?= $player->username; ?></a>
							<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
								<div class="admin_controls">
									<input type="checkbox" name="player_id" value="<?= $player->id; ?>" />
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p><?= _('No one\'s been left out, yay!'); ?></p>
			<?php endif; ?>
		</div>

		<div class="col-md-6">
			<div id="tournament_notes">
				<?= $tournament->notes ? markdown($tournament->notes) : '<p>'._('No notes').'</p>'; ?>

				<p><?= sprintf(_('The signup deadline for this tournament is %s'), strftime('%A '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%d' : '%e').', %B %Y', mysql_to_unix($tournament->signup_deadline))); ?></p>
			</div>

			<div id="payment_details">
				<?php if($player_owes): ?>
					<p><?= sprintf(_('You still have to pay €%0.2f'), $player_owes); ?></p>
				<?php else: ?>
					<p><?= _('You don\'t owe any money!'); ?>
				<?php endif; ?>
				<p><a href="/tournament/payments/<?= $tournament->id; ?>"><?= _('See payment details'); ?></a></p>
			</div>

			<div id="travel_details">
				<p><a href="/tournament/add_trip_leg/<?= $tournament->id; ?>"><?= _('Add trip leg'); ?></a></p>

				<?php if($trips): ?>
					<?php foreach(array('way', 'return') as $direction): ?>
						<?php if(isset($trips[$direction])): ?>
							<h2><?= $direction == 'way' ? _('Going to') : _('Return'); ?></h2>
							<ul>
								<?php foreach($trips[$direction] as $trip): ?>
									<li>
										<?php switch($trip->trip_type):
											case 'car': ?>
												<?= sprintf(
													_('Car from %s to %s, leaving on %s'),
													$trip->origin,
													$trip->destination,
													strftime('%A '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%d' : '%e').', %B %Y @%H:%M', mysql_to_unix($trip->departure_time)));
												?>
												<?php break; ?>
											<?php default: ?>
												<?= $trip->trip_name; ?>,
												<?= $trip->origin;?> &rarr; <?= $trip->destination; ?>,
												<?= strftime('%a '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%d' : '%e').', %H:%M-', mysql_to_unix($trip->departure_time)); ?><?= strftime('%H:%M', mysql_to_unix($trip->arrival_time)); ?>
												<?php break; ?>
										<?php endswitch; ?><br />

										<?= _('On this trip'); ?>:
										<?php if($trip->passengers): ?>
											<?= implode(', ', array_map(function($p){ return $p->username; }, $trip->passengers)); ?><br />
										<?php endif; ?>

										<form action="#" method="post"  class="trip_signup">
											<input type="hidden" name="tlid" value="<?= $trip->leg_id; ?>" />

											<?php if($trip->player_on_it): ?>
												<input type="submit" name="signoffFromTrip" value="<?= _('Not going'); ?>" />
											<?php else: ?>
												<input type="submit" name="signupToTrip" value="<?= _('Going'); ?>" />
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
  <div class="modal fade" id="include_player_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
				<div class="modal-header">
					<a href="#" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
					<h3><?= _('Include player'); ?></h3>
				</div>
				<div class="modal-body">
					<form action="#" method="post">
						<div class="input-group">
							<input type="text" class="form-control" name="player_autocomplete" />
					<span class="input-group-btn">
								<input type="submit" name="add_player" value="<?= _('Add'); ?>" disabled="disabled" class="btn btn-primary" />
							</span>
						</div>
						<p></p>
					</form>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-default">Close</a>
				</div>
			</div>
		</div>
	</div>

<?php else: ?>
	<p><?= _('Tournament not found'); ?></p>
<?php endif; ?>
