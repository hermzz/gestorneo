<?php if($this->tank_auth->is_admin()): ?>
	<ul class="nav nav-tabs">
		<li class="dropdown pull-left" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="/tournament/create/"><?=_('New tournament');?></a></li>
			</ul>
		</li>
	</ul>
<?php endif; ?>

<h2><?=_('Upcoming tournaments');?></h2>

<?php if($future_tournaments): ?>
	<?php $month_year = false; ?>
		<?php foreach($future_tournaments as $tournament): ?>
			<?php
				if(!$month_year)
				{
					echo '<h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
					$month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
				} else {
					$t_month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
					if($t_month_year != $month_year)
					{
						echo '</ul><h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
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
					echo '<h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
					$month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
				} else {
					$t_month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
					if($t_month_year != $month_year)
					{
						echo '</ul><h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
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
