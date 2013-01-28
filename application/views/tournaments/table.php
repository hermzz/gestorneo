        <?php $year = false;
              $headers = array(
                array('value'=>_('Name')),
                _('Start date'),
                _('Days to sign up'),
                _('Status'),
                _('Players'),
                );
        ?>
          <?php foreach($tournaments as $tournament): ?>
            <?php
                $t_year = strftime('%Y', mysql_to_unix($tournament->start_date));
                $newtable = '';
                if($t_year != $year)
                {
                  if($year !== false) {
                    $newtable .= "</tbody></table>";
                  }
                  $newtable .= '<table class="table table-striped table-bordered">';

                  if((isset($one_table) && !$one_table) || !isset($one_table)) {
                    $newtable .= '<caption>'.strftime('%Y', mysql_to_unix($tournament->start_date)).'</caption>';
                  }
                  $newtable .= '<thead><tr>';
                  for($i=0;$i<count($headers); $i++) {
                    $extra = ' class="col'.($i+1).'"';
                    if(is_array($value = $headers[$i])) {
                      $extra = '';
                      $value['class'] = element('class', $value, '') . ' col'.($i+1);
                      foreach($value as $k=>$v) {
                        $extra .= " $k=\"$v\"";
                      }
                      $value = $value['value'];
                    }

                    $newtable .= "<th$extra>$value<i></i></th>\n";
                  }
                  $newtable .= '</tr></thead>';
                  $newtable .= '<tbody>';
                  $year = $t_year;
                }
                // Only for the first time through
                if(strpos($newtable, '/table') == false || (isset($one_table) && !$one_table) || !isset($one_table)) {
                  print $newtable;
                }
            ?>
            <tr>
              <td>
                <a href="<?= site_url('/tournament/view/'.$tournament->id) ?>"><?= htmlspecialchars($tournament->name) ?></a>
              </td>
              <td title="<?= strftime('%d/%m/%Y', mysql_to_unix($tournament->start_date)) ?>"  data-date="<?= strftime('%d/%m/%Y', mysql_to_unix($tournament->start_date)) ?>">
                  <?= strftime((strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%#d' : '%e').' %b %Y (%a)', mysql_to_unix($tournament->start_date)) ?>
              </td>
              <td title="<?= strftime('%A %B '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%#d' : '%e'), mysql_to_unix($tournament->signup_deadline)) ?>">
                <?php
                $s = $tournament->days_to_signup;
                $w = _('days');
                $c = '';
                if($s < 0) {
                  $w = _('Closed');
                }
                elseif($s == 0) {
                  $w = _('Last day!');
                  $c = 'label label-important';
                }
                else {
                  if($s < 7) {
                    if($s == 1) {
                      $w = _('day');
                    }
                    $c = 'label label-warning';
                  }
                  $w = "$s $w";
                }
                if($tournament->player_signed_up) {
                  $c = '';
                }

                print '<span class="'.$c.'" data-days="'.$s.'">'.$w.'</span>';

              ?>
              </td>
              <td>
                <?= ($tournament->player_signed_up) ? ($tournament->passed ? _('Played') : _('Signed up')) : '-' ?>
              </td>
              <td>
              <?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
                [<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
                <?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F]
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody></table>