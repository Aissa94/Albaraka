<?php 
/**
 * This view allows an employees (or HR admin) to modify a leave request
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<div class="page-title">   
<h1><?php echo lang('leaves_edit_title');?><?php echo $leave['id']; ?> <span class="muted">(<?php echo $name ?>)</span></h1>
</div>
<div class="row-fluid" style="padding-top:20px;">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php 
$attributes = array('id' => 'frmLeaveForm');
if (isset($_GET['source'])) {
    echo form_open('leaves/edit/' . $id . '?source=' . $_GET['source'], $attributes);
} else {
    echo form_open('leaves/edit/' . $id, $attributes);
} ?>
    <div class="form-group">
    <label class="col-md-3" for="type"><?php echo lang('leaves_edit_field_type');?></label>
    <select name="type" id="type" class="selectpicker" data-live-search="true">
    <?php foreach ($types as $types_item): ?>
         <option value="<?php echo $types_item['id'] ?>" 
        <?php switch ($types_item['id']) {
            case 0 : echo 'style = " display:none"'; break; 
            case $leave['type'] : echo "selected"; break;
            case 1 : break;
            case 2 : break;
            default : echo "disabled";
        }?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>    
   </select>&nbsp;<span id="lblCredit"><?php if (!is_null($credit)&& $leave['type'] =="1") echo '('.$credit.')'; ?></span><br />
      </div>
     <div class="form-group">   
    <label class="col-md-3" for="viz_startdate"><?php echo lang('leaves_edit_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php $date = new DateTime($leave['startdate']); echo $date->format(lang('global_date_format'));?>" autocomplete="off" />
    <input type="hidden" name="startdate" id="startdate" value="<?php echo $leave['startdate'];?>" />
    <select name="startdatetype" id="startdatetype" style="display : none">
        <option value="Morning" <?php if ($leave['startdatetype'] == "Morning") {echo "selected";}?>><?php echo lang('Morning');?></option>
        <option value="Afternoon" <?php if ($leave['startdatetype'] == "Afternoon") {echo "selected";}?>><?php echo lang('Afternoon');?></option>
    </select><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="viz_enddate"><?php echo lang('leaves_edit_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php $date = new DateTime($leave['enddate']); echo $date->format(lang('global_date_format'));?>" autocomplete="off" />
    <input type="hidden" name="enddate" id="enddate" value="<?php echo $leave['enddate'];?>" />
    <select name="enddatetype" id="enddatetype" style="display : none">
        <option value="Morning" <?php if ($leave['enddatetype'] == "Morning") {echo "selected";}?>><?php echo lang('Morning');?></option>
        <option value="Afternoon" <?php if ($leave['enddatetype'] == "Afternoon") {echo "selected";}?>><?php echo lang('Afternoon');?></option>
    </select><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="duration"><?php echo lang('leaves_edit_field_duration');?></label>
    <?php if ($this->config->item('disable_edit_leave_duration') == TRUE) { ?>
    <input type="text" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" readonly />
    <?php } else { ?>
    <input type="text" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    <?php } ?>
    
    <div class="alert hide alert-error" id="lblCreditAlert">
        <?php echo lang('leaves_edit_field_duration_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingAlert">
        <?php echo lang('leaves_create_field_overlapping_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingDayOffAlert">
        <?php echo lang('leaves_flash_msg_overlap_dayoff');?>
    </div>
    </div>
    <div class="form-group">
    <label class="col-md-3" for="substitute"><?php echo lang('leaves_edit_field_substitute');?></label>
    <select name="substitute" id="substitute" class="selectpicker" data-live-search="true">
        <option value="<?php echo NULL ?>">aucun remplaçant</option>
    <?php foreach ($substitute as $substitute_item): ?>
        <option value="<?php echo $substitute_item['id'] ?>" <?php if ($substitute_item['id'] == $leave['substitute']) echo "selected" ?> ><?php echo $substitute_item['id'].' '.$substitute_item['firstname'].' '.$substitute_item['lastname'] ?></option>
        <?php endforeach ?>
    </select>
</div>
 <div class="form-group">
    <label class="col-md-3" for="cause"><?php echo lang('leaves_edit_field_cause');?></label>
    <textarea name="cause" id="causeleave"><?php echo $leave['cause']; ?></textarea>
     </div>
    <div class="form-group">
    <label class="col-md-3" for="status"><?php echo lang('leaves_edit_field_status');?></label>
    <select name="status" id="status" class="selectpicker">
        <option value="1" <?php if ($leave['status'] == 1) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if (($leave['status'] == 2) || $this->config->item('leave_status_requested')) echo 'selected'; ?>><?php echo lang('Requested');?></option>
        <?php /*if (($is_hr) && ($leave['type'] == 2)) {?>
        <option value="3" <?php if ($leave['status'] == 3) echo 'selected'; ?>><?php echo lang('Accepted');?></option>
        <option value="4" <?php if ($leave['status'] == 4) echo 'selected'; ?>><?php echo lang('Rejected');?></option>        
        <?php } */?>
    </select><br />
</div>
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_cancel');?></a>
    <?php } ?>
    
</form>

    </div>
    <div class="span4">
        <div class="row-fluid">
            <div class="span12">
                <span id="spnDayType"></span>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <span id="spnDaysOffList"></span>
            </div>
        </div>
    </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<?php require_once dirname(BASEPATH) . "/local/triggers/leave_view.php"; ?>

<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $leave['employee']; ?>;
    var leaveId = <?php echo $leave['id']; ?>;
    var languageCode = '<?php echo $language_code;?>';
    var dateJsFormat = '<?php echo lang('global_date_js_format');?>';
    var dateMomentJsFormat = '<?php echo lang('global_date_momentjs_format');?>';
    
    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";
    
    var overlappingWithDayOff = "<?php echo lang('leaves_flash_msg_overlap_dayoff');?>";
    var listOfDaysOffTitle = "<?php echo lang('leaves_flash_spn_list_days_off');?>";
    
function validate_form() {
    if ($('#lblCreditAlert').css('display') == "block") {
        bootbox.alert($('#lblCreditAlert').html());
        return false;
    }
    if ($('#lblOverlappingAlert').css('display') == "block") {
        bootbox.alert($('#lblOverlappingAlert').html());
        return false;
    }
    if ($('#lblOverlappingDayOffAlert').css('display') == "block") {
        bootbox.alert($('#lblOverlappingDayOffAlert').html());
        return false;
    }

    <?php
    require_once dirname(BASEPATH) . "/application/models/leaves_model.php";
    $leaves_mod = new Leaves_model();
    $name_id = null;
    foreach ($types as $types_item){
        if ($types_item['id'] == 1) 
        {
            $name_id = $types_item['name'];
            break;
        }
    }?>
    var value_type = document.getElementById('type').value;
    var value_status = document.getElementById('status').value;
    if (( value_status == 2) && ( value_type  == 2) && "<?php echo ($leaves_mod->getLeavesTypeBalanceForEmployee($user_id, $name_id) > 0) ?>") { 
        bootbox.alert('Vous devez épuiser le crédit de congé annuel pour pouvoir effectuer une demande de ce type');
        return false;
    }
    switch (value_type)
    {
        case '5':
        if ($('#duration').val() > 3) {bootbox.alert('La durée maximale pour une demande de type "'+$("#type option:selected").text()+'" est : 3 jours');return false;}
        break;
        case '6' :
        if ($('#duration').val() > 3) {bootbox.alert('La durée maximale pour une demande de type "'+$("#type option:selected").text()+'" est : 3 jours');return false;}
        break;
        case '10' :
        if ($('#duration').val() > 3) {bootbox.alert('La durée maximale pour une demande de type "'+$("#type option:selected").text()+'" est : 3 jours');return false;}
        break;
        case '13' :
        if ($('#duration').val() > 3) {bootbox.alert('La durée maximale pour une demande de type "'+$("#type option:selected").text()+'" est : 3 jours');return false;}
        break;
    }
    var fieldname = "";
    
    //Call custom trigger defined into local/triggers/leave.js
    if (typeof triggerValidateEditForm == 'function') { 
       if (triggerValidateEditForm() == false) return false;
    }
    
    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_edit_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_edit_field_end');?>";
    if ($('#duration').val() == "" || $('#duration').val() == 0) fieldname = "<?php echo lang('leaves_edit_field_duration');?>";
    if (fieldname == "") {
        return true;
    } else {
        bootbox.alert(<?php echo lang('leaves_validate_mandatory_js_msg');?>);
        return false;
    }
}

<?php if ($this->config->item('csrf_protection') == TRUE) {?>
$(function () {
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name();?>: "<?php echo $this->security->get_csrf_hash();?>",
        }
    });
});
<?php }?>

//On opening, refresh leave request information
$(function () {
    refreshLeaveInfo();
});

</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms/leave.edit.js" type="text/javascript"></script>
