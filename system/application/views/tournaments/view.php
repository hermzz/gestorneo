<?php if($tournament): ?>
	<h2><?=$tournament->name?>, <?=mdate('%F %j%S %Y', mysql_to_unix($tournament->date))?></h2>
	
	<p>
		<?=$tournament->notes ? $tournament->notes : "No notes" ?>
	</p>
	
	<h3>Players confirmed</h3>
	<?php if($players_confirmed): ?>
		<ul>
			<?php foreach($players_confirmed as $player): ?>
				<li><a href="/player/view/<?=$player->id?>"><?=$player->username?></a>
					<?php if($tank_auth->is_admin()): ?>
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
					<?php if($tank_auth->is_admin()): ?>
						 - <a href="/tournament/approve_player/<?=$tournament->id;?>/<?=$player->id;?>">Approve</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No players confirmed yet.</p>
	<?php endif; ?>
	
<?php else: ?>
	<p>Tournament not found.</p>
<?php endif; ?>
