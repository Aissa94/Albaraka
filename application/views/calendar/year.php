<?php
/**
 * This view displays a yearly calendar of the leave taken by a user (can be displayed by HR or manager)
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */
 
$isCurrentYear = (int)date('Y') === (int)$year;
$currentMonth = (int)date('m');
$currentDay = (int)date('d');
?>

<h2><?php echo lang('calendar_year_title');?>&nbsp;<span class="muted">(<?php echo $employee_name;?>)</span>&nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span4">
        <div class="span3 btn conge"><?php echo lang('Planned');?></div>
        <div class="span3 btn btn-success conge"><?php echo lang('Accepted');?></div>
        <div class="span3 btn btn-warning conge"><?php echo lang('Requested');?></div>
        <div class="span3 btn btn-danger red conge"><?php echo lang('Rejected');?></div>
    </div>
    <div class="span4">
        <a href="<?php echo base_url();?>calendar/year/export/<?php echo $employee_id;?>/<?php echo ($year);?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('calendar_year_button_export');?></a>
    </div>
    <div class="span4">
        <div class="pull-right">
            <a href="<?php echo base_url();?>calendar/year/<?php echo $employee_id;?>/<?php echo ($year - 1);?>" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></a>
            <?php echo $year;?>
            <a href="<?php echo base_url();?>calendar/year/<?php echo $employee_id;?>/<?php echo ($year + 1);?>" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></a>
        </div>
    </div>
</div>

<div class="row-fluid">

</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <?php for ($ii = 1; $ii <=31; $ii++) {
                    echo '<td'.($ii === $currentDay ?' class="currentday-bg"':'').'>' . $ii . '</td>';
                }?>
        </tr>
    </thead>
  <tbody>
  <?php 
  
  $monthNumber = 0;  
  foreach ($months as $month_name => $month) { 
    $monthNumber++;
    $isCurrentMonth = $currentMonth === $monthNumber;

  ?>
    <tr>
      <td rowspan="2"<?php echo $isCurrentMonth ?' class="currentday-bg"':'';?>><?php echo $month_name; ?></td>
        <?php //Iterate so as to display all mornings
        $pad_day = 1;
        foreach ($month->days as $dayNumber => $day) {
            $isCurrentDay = $isCurrentYear && $isCurrentMonth && $currentDay === $dayNumber;
            $class = '';
            if($isCurrentDay){
                $class .= ' currentday-border';
            }
            
            if (strstr($day->display, ';')) {//Two statuses in the cell
                $periods = explode(";", $day->display);
                $statuses = explode(";", $day->status);
                $types = explode(";", $day->type);
                $substitutes = explode(";", $day->substitute);
                $causes = explode(";", $day->cause);
                if (($periods[0] == 1) || ($periods[0] == 2) || ($periods[0] == 4) || ($periods[0] == 5)) {
                    $display = $periods[0];
                    $status = $statuses[0];
                    $type = $types[0];
                    $substitute = $substitutes[0];
                    $cause = $causes[0];
                } else {
                    $display = $periods[1];
                    $status = $statuses[1];
                    $type = $types[1];
                    $substitute = $substitutes[1];
                    $cause = $causes[1];   
                }
            } else {
                $display = $day->display;
                $status = $day->status;
                $type = $day->type;
                $substitute = $day->substitute;
                $cause = $day->cause; 
            }
            //0 - Working day  _
            //1 - All day           []
            //2 - Morning        |\
            //3 - Afternoon      /|
            //4 - All Day Off       []
            //5 - Morning Day Off   |\
            //6 - Afternoon Day Off /|
            //9 - Error in start/end types
            if ($display == 9) echo '<td'.($class?' class="'.$class.'"':'').'><img src="'.  base_url() .'assets/images/date_error.png"></td>';
            if ($display == 0) echo '<td'.($class?' class="'.$class.'"':'').'>&nbsp;</td>';
            if ($display == 3 || $display == 6) echo '<td'.($class?' '.$class:'').'>&nbsp;</td>';
            if ($display == 4 || $display == 5) echo '<td title="' . $type .'" class="dayoff'.($class?' '.$class:'').'">&nbsp;</td>';
            if ($display == 1 || $display == 2) {
                switch ($status)
                {
                  case 1: echo ('<td title="' . $type . $substitute . $cause .'" class="allplanned'.($class?' '.$class:'').'">&nbsp;</td>'); break;  // Planned
                  case 2: echo ('<td title="' . $type . $substitute . $cause .'" class="allrequested'.($class?' '.$class:'').'">&nbsp;</td>'); break;  // Requested
                  case 3: echo ('<td title="' . $type . $substitute . $cause .'" class="allaccepted'.($class?' '.$class:'').'">&nbsp;</td>'); break;  // Accepted
                  case 4: echo ('<td title="' . $type . $substitute . $cause .'" class="allrejected'.($class?' '.$class:'').'">&nbsp;</td>'); break;  // Rejected
                }
            }
        $pad_day++;
        } ?>
      <?php //Fill 
      if ($pad_day <= 31) echo '<td colspan="' . (32 - $pad_day) . '" rowspan="2" style="background-color:#00FFFF;">&nbsp;</td>';
        ?>
    </tr>
    <tr>
    </tr>
  <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <?php for ($ii = 1; $ii <=31; $ii++) {
                    echo '<td>' . $ii . '</td>';
                }?>
        </tr>
  </tbody>
</table>
        
    </div>
</div>
