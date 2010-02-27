<?php if($player): ?>
	<h2><?=$player->name?></h2>
	
	<h3>Tournaments</h3>
	
	<?php if($tournaments): ?>
		<ul>
			<?php foreach($tournaments->result() as $tournament): ?>
				<li><?=$tournament->name?> - <?=date('F Y', $tournament->u_date)?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No tournaments for this player.</p>
	<?php endif; ?>
<?php else: ?>
	<p>Player not found.</p>
<?php endif; ?>
