<?php if($tournament): ?>
	<h2><?=$tournament->name?>, <?=mdate('%F %j%S %Y', mysql_to_unix($tournament->date))?></h2>
	
	<p>
		<?=$tournament->notes ? $tournament->notes : "No notes" ?>
	</p>
	
	<h3>Players</h3>
	<?php if($players): ?>
		<ul>
			<?php foreach($players as $player): ?>
				<li><?=$player->name?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No players signed up.</p>
	<?php endif; ?>
	
<?php else: ?>
	<p>Tournament not found.</p>
<?php endif; ?>
