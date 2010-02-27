<p>Add a <a href="/tournament/create/">new tournament</a></p>

<h2>Upcoming tournaments</h2>

<?php if($future_tournaments): ?>
	<ul>
		<?php foreach($future_tournaments->result() as $tournament): ?>
			<li><a href="<?=site_url('/tournament/view/'.$tournament->id)?>">
				<?=$tournament->name?></a> on <?=mdate('%F %j%S, %Y', mysql_to_unix($tournament->date))?>
				(Players: <?=$this->tournament_model->countSignedUp($tournament->id)?>)
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>No upcoming tournaments found.</p>
<?php endif; ?>

<h2>Past tournaments</h2>

<?php if($past_tournaments): ?>
	<ul>
		<?php foreach($past_tournaments->result() as $tournament): ?>
			<li><a href="<?=site_url('/tournament/view/'.$tournament->id)?>">
				<?=$tournament->name?></a> on <?=mdate('%F %j%S, %Y', mysql_to_unix($tournament->date))?>
				(Players: <?=$this->tournament_model->countSignedUp($tournament->id)?>)
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>No past tournaments found.</p>
<?php endif; ?>
