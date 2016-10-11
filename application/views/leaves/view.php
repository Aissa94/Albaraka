<?php 
/**
 * This view allows users to view a leave request in read-only mode
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="page-title">   
<h1><?php echo lang('leaves_view_title');?><?php echo $leave['id']; if ($name != "") {?>&nbsp;<span class="muted">(<?php echo $name; ?>)</span><?php } ?></h1>
</div>
    <div class="span12">
    <div class="form-group">
    <label class="col-md-3" for="startdate"><?php echo lang('leaves_view_field_start');?></label>
    <input type="text" name="startdate" value="<?php $date = new DateTime($leave['startdate']); echo $date->format(lang('global_date_format'));?>" readonly />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="enddate"><?php echo lang('leaves_view_field_end');?></label>
    <input type="text" name="enddate"  value="<?php $date = new DateTime($leave['enddate']); echo $date->format(lang('global_date_format'));?>" readonly />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="duration"><?php echo lang('leaves_view_field_duration');?></label>
    <input type="text" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="type"><?php echo lang('leaves_view_field_type');?></label>
    <select class="selectpicker" name="type" readonly>
        <option selected><?php echo $leave['type_name']; ?></option>
    </select><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="substitute"><?php echo lang('leaves_view_field_substitute');?></label>
    <select class="selectpicker" name="substitute" readonly>
        <option selected><?php echo $leave['substitute'].' '.$leave['substitute_firstname'].' '.$leave['substitute_lastname']; ?></option>
    </select><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="cause"><?php echo lang('leaves_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    </div>
    <div class="form-group">
    <label class="col-md-3" for="status"><?php echo lang('leaves_view_field_status');?></label>
    <select class="selectpicker" name="status" readonly>
        <option selected><?php echo lang($leave['status_name']); ?></option>
    </select><br />
    </div>
    <?php if (($leave['status'] == 1) || ($is_hr)) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_edit');?></a>
    &nbsp;
    <?php } ?>

    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>leaves" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>
    <?php } ?>


   <!--a href="<?php echo base_url() . $source; ?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php //echo lang('leaves_view_button_back_list');?></a-->
   
    </div>
</div>
