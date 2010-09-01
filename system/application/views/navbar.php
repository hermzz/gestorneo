<ul>
    <li><a href="/"><?=_('home');?></a></li>
    
	<?php if($this->tank_auth->is_logged_in()): ?>
	    <li><a href="/tournament/"><?=_('tournaments');?></a></li>
	    <li><a href="/player/"><?=_('players');?></a></li>
    <?php endif; ?>
</ul>
