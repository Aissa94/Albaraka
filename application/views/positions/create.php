<?php 
/**
 * This view allows an HR admin to create a new position (occupied by an employee).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>
<script type="application/javascript">
    $("#menu_hr_title").addClass('active');
    $("#menu_hr_contracts_divider").addClass('active');
    $("#menu_hr_list_positions").addClass('active');
</script>

<div class="row-fluid">
<div class="page-title"> 
<h1><?php echo lang('positions_create_title');?></h1>
</div>
<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('positions/create', $attributes); ?>
    <div class="form-group">
    <label class="col-md-3" for="name"><?php echo lang('positions_create_field_name');?></label>
    <input class="col-md-4" type="text" name="name" id="name" autofocus required /><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="description"><?php echo lang('positions_create_field_description');?></label>
    <textarea class="col-md-4" type="input" name="description" id="description" style="min-height:100px;"></textarea>
    </div>
    <br /><br />
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('positions_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('positions_create_button_cancel');?></a>
</form>
</div>