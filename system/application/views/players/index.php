<p>Add a <a href="/player/create/">new player</a></p>

<h2>Players</h2>

<?php if(count($players) > 0): ?>
	<ul>
		<?php foreach($players->result() as $player): ?>
			<li><a href="<?=site_url('/player/view/'.$player->id)?>">
				<?=$player->name?></a> (joined <?=mdate('%F %j%S %Y', mysql_to_unix($player->joined))?>)
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>No players found.</p>
<?php endif; ?>
