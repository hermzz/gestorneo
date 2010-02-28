<h2>Players</h2>

<?php if(count($players) > 0): ?>
	<ul>
		<?php foreach($players->result() as $player): ?>
			<li><a href="<?=site_url('/player/view/'.$player->id)?>">
				<?=$player->username?></a> (joined <?=mdate('%F %j%S, %Y', mysql_to_unix($player->created))?>)
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>No players found.</p>
<?php endif; ?>
