<div class="tabbable">
	<ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab"><?=_('Upcoming tournaments');?></a></li>
    <li><a href="#tab2" data-toggle="tab"><?=_('Past tournaments');?></a></li>
    <li><a href="#tab3" data-toggle="tab"><?=_('All tournaments');?></a></li>
<?php if($this->tank_auth->is_admin()): ?>
		<li class="dropdown pull-left" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="/tournament/create/"><?=_('New tournament');?></a></li>
			</ul>
		</li>
<?php endif; ?>
	</ul>

  <div class="tab-content" id="tournament-tables">
	  <div class="tab-pane active" id="tab1">
			<h2><?=_('Upcoming tournaments');?></h2>

			<?php if($future_tournaments): ?>
				<?php $this->load->view('tournaments/table', array('tournaments'=>$future_tournaments)); ?>
			<?php else: ?>
				<p><?=_('No upcoming tournaments found.');?></p>
			<?php endif; ?>
	  </div>

	  <div class="tab-pane" id="tab2">
			<h2><?=_('Past tournaments');?></h2>

			<?php if($past_tournaments): ?>
				<?php $this->load->view('tournaments/table', array('tournaments'=>$past_tournaments)); ?>
			<?php else: ?>
				<p><?=_('No past tournaments found.');?></p>
			<?php endif; ?>
	  </div>

	  <div class="tab-pane" id="tab3">
			<h2><?=_('All tournaments');?></h2>
			<?php if($past_tournaments or $future_tournaments): ?>
				<?php $this->load->view('tournaments/table', array('one_table'=>true, 'tournaments'=>array_merge($future_tournaments, $past_tournaments))); ?>
			<?php else: ?>
				<p><?=_('No tournaments found.');?></p>
			<?php endif; ?>

	  </div>
	</div>
</div>



