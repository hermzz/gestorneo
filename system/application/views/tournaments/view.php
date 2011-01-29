<?php if($tournament): ?>
	<?php if($this->tournament_model->is_old($tournament)):?>
		<p class="message neutral"><?=_('This tournament has already passed');?></p>
	<?php endif;?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('form.approve_player select').change(function(e) {
				$(e.target).parent().submit();
			});
		});
	</script>
	
	<?php
		$is_tournament_admin = $this->tank_auth->is_admin(array('tournament' => $tournament->id));
	?>
	
	<h1>
		<?=sprintf(_('%s <span class="header-small">on %s</span>'), $tournament->name, strftime('%A %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
	</h1>
	
	<div id="tournament_content">
		<?php if($is_tournament_admin): ?>
			<p>
				<a href="/tournament/email/<?=$tournament->id;?>">Email team</a> | 
				<a href="/tournament/edit/<?=$tournament->id;?>">Edit tournament</a>
			</p>
		<?php endif; ?>
		
		<?php if(!$this->tournament_model->is_old($tournament)):?>
			<?php if($this->tournament_model->undeadlined($tournament)):?>
				<p><?=sprintf(_('The signup deadline for this tournament is %s'), strftime('%A %e, %B %Y', mysql_to_unix($tournament->signup_deadline)));?></p>
			
				<?php if($this->tournament_model->is_signed_up($tournament->id, $this->tank_auth->get_user_id())): ?>
					<form action="/tournament/cancel_sign_up" method="post">
						<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
						<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
						<input type="submit" name="submitCancel" value="<?=_('Cancel');?>" />
					</form>
				<?php elseif($this->tournament_model->can_sign_up($tournament->id, $this->tank_auth->get_user_id())): ?>
					<form action="/tournament/sign_up" method="post">
						<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
						<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
						<input type="submit" name="submitSignup" value="<?=_('Signup');?>" />
					</form>
				<?php endif; ?>
			<?php else: ?>
				<p><?=_('The signup deadline for this tournament has already passed, you\'re too late!');?></p>
			<?php endif; ?>
		<?php endif; ?>
	
		<h3><?=_('Players confirmed');?></h3>
		
		<?php if($teams): ?>
			<?php foreach($teams as $team): ?>
				<h4><?=$team->name;?> (<?=$team->males;?>M / <?=$team->females;?>F)</h4>
				
				<ul>
					<?php if($team->players): ?>
						<?php foreach($team->players as $player): ?>
							<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
								<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
									 - <a href="/tournament/drop_player/<?=$tournament->id;?>/<?=$player->id;?>"><?=_('Drop');?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					<?php else: ?>
						<li><?=_('No players assigned to this team yet');?></li>
					<?php endif; ?>
				</ul>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php if($players_unassigned): ?>
			<h4><?=_('Unassigned players');?></h4>
			<ul>
				<?php foreach($players_unassigned as $player): ?>
					<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
						<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
							
							 <form class="approve_player" action="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>" method="post">
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
	
		<h3><?=_('Waiting list');?></h3>
		<?php if($players_waiting): ?>
			<ul>
				<?php foreach($players_waiting as $player): ?>
					<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
						<?php if($is_tournament_admin && !$this->tournament_model->is_old($tournament)): ?>
							 <form class="approve_player" action="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>" method="post">
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
	
	<div id="tournament_notes">
		<?=$tournament->notes ? markdown($tournament->notes) : '<p>'._('No notes').'</p>'; ?>
	</div>
	
<?php else: ?>
	<p><?=_('Tournament not found');?></p>
<?php endif; ?>
