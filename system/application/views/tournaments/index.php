<?php if($this->tank_auth->is_admin()): ?>
	<p><?=_('Add a <a href="/tournament/create/">new tournament</a>');?></p>
<?php endif; ?>

<h2><?=_('Upcoming tournaments');?></h2>

<?php if($future_tournaments): ?>
	<?php $month_year = false; ?>
		<?php foreach($future_tournaments as $tournament): ?>
			<?php
				if(!$month_year)
				{
					echo '<h3>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h3><ul>';
					$month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
				} else {
					$t_month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
					if($t_month_year != $month_year)
					{
						echo '</ul><h3>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h3><ul>';
						$month_year = $t_month_year;
					}
				}
			?>
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), site_url('/tournament/view/'.$tournament->id), $tournament->name, strftime('%A %e', mysql_to_unix($tournament->start_date)));?>
				(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
					[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
					<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No upcoming tournaments found.');?></p>
<?php endif; ?>

<h2><?=_('Past tournaments');?></h2>

<?php if($past_tournaments): ?>
	<?php $month_year = false; ?>
		<?php foreach($past_tournaments as $tournament): ?>
			<?php
				if(!$month_year)
				{
					echo '<h3>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h3><ul>';
					$month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
				} else {
					$t_month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
					if($t_month_year != $month_year)
					{
						echo '</ul><h3>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h3><ul>';
						$month_year = $t_month_year;
					}
				}
			?>
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), site_url('/tournament/view/'.$tournament->id), $tournament->name, strftime('%A %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
				(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
					[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
					<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No past tournaments found.');?></p>
<?php endif; ?>
