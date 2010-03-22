<?php if($tournament): ?>
	<h2><?=$tournament->name?>, <?=mdate('%F %j%S %Y', mysql_to_unix($tournament->date))?></h2>
	
	<?php if($this->tournament_model->is_signed_up($tournament->id, $this->tank_auth->get_user_id())): ?>
		<form action="/tournament/cancel_sign_up" method="post">
			<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
			<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
			<input type="submit" name="submitCancel" value="Cancel" />
		</form>
	<?php elseif($this->tournament_model->can_sign_up($tournament->id, $this->tank_auth->get_user_id())): ?>
		<form action="/tournament/sign_up" method="post">
			<input type="hidden" name="tournament_id" value="<?=$tournament->id;?>" />
			<input type="hidden" name="player_id" value="<?=$this->tank_auth->get_user_id();?>" />
			<input type="submit" name="submitSignup" value="Signup" />
		</form>
	<?php endif; ?>
	
	<p>
		<?=$tournament->notes ? $tournament->notes : "No notes" ?>
	</p>
	
	<h3>Players confirmed</h3>
	<?php if($players_confirmed): ?>
		<ul>
			<?php foreach($players_confirmed as $player): ?>
				<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
					<?php if($this->tank_auth->is_admin()): ?>
						 - <a href="/tournament/drop_player/<?=$tournament->id;?>/<?=$player->id;?>">Drop</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No players confirmed yet.</p>
	<?php endif; ?>
	
	<h3>Waiting list</h3>
	<?php if($players_waiting): ?>
		<ul>
			<?php foreach($players_waiting as $player): ?>
				<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
					<?php if($this->tank_auth->is_admin()): ?>
						 - <a href="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>">Approve</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No one's been left out, yay!</p>
	<?php endif; ?>
	
<?php else: ?>
	<p>Tournament not found.</p>
<?php endif; ?>
