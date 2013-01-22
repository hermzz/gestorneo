<?php if($this->tank_auth->is_admin()): ?>
  <ul class="nav nav-tabs">
    <li class="dropdown pull-left" data-dropdown="dropdown">
      <a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="/team/create/"><?=_('Create a new team');?></a></li>
      </ul>
    </li>
  </ul>
<?php endif; ?>

<h2><?=_('Teams');?></h2>

<?php if($teams): ?>
	<ul>
		<?php foreach($teams as $team): ?>
			<li><a href="<?=site_url('/team/view/'.$team->id)?>"><?=$team->name?></a></li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No teams found.');?></p>
<?php endif; ?>
