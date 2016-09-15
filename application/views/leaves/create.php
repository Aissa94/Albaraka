<?php
/**
 * This view allows an employees (or HR admin/Manager) to create a new leave request
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('leaves_create_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php 
$attributes = array('id' => 'frmLeaveForm');
echo form_open('leaves/create', $attributes) ?>

    <label for="type"><?php echo lang('leaves_create_field_type');?></label>
    <select name="type" id="type">
    <?php
    $default_type = $this->config->item('default_leave_type');
    $default_type = $default_type == FALSE ? 1 : $default_type;
    foreach ($types as $types_item):?>
        <option value="<?php echo $types_item['id'] ?>" 
        <?php switch ($types_item['id']) {
            case 0 : echo "hidden"; break; 
            case $default_type : echo "selected"; break;
            case 2 : break;
            default : echo "disabled";
        }?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>
    </select>&nbsp;<span id="lblCredit"><?php if (!is_null($credit)) echo '('.$credit.')'; ?></span><br />
        
    <label for="viz_startdate"><?php echo lang('leaves_create_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php echo set_value('startdate'); ?>" autocomplete="off" />
    <input type="hidden" name="startdate" id="startdate" />
    <select name="startdatetype" id="startdatetype" style="display : none">
        <option value="Morning" selected><?php echo lang('Morning');?></option>
        <option value="Afternoon"><?php echo lang('Afternoon');?></option>
    </select><br />
    
    <label for="viz_enddate"><?php echo lang('leaves_create_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php echo set_value('enddate'); ?>" autocomplete="off" />
    <input type="hidden" name="enddate" id="enddate" />
    <select name="enddatetype" id="enddatetype" style="display : none">
        <option value="Morning"><?php echo lang('Morning');?></option>
        <option value="Afternoon" selected><?php echo lang('Afternoon');?></option>
    </select><br />

    <label for="duration"><?php echo lang('leaves_create_field_duration');?></label>
    <?php if ($this->config->item('disable_edit_leave_duration') == TRUE) { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" readonly />
    <?php } else { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />
    <?php } ?>
    
    <div class="alert hide alert-error" id="lblCreditAlert">
        <?php echo lang('leaves_create_field_duration_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingAlert">
        <?php echo lang('leaves_create_field_overlapping_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingDayOffAlert">
        <?php echo lang('leaves_flash_msg_overlap_dayoff');?>
    </div>
    
    <label for="substitute"><?php echo lang('leaves_create_field_substitute');?></label>
    <select name="substitute" id="substitute">
    <option value="<?php echo null ?>">aucun remplaçant</option>
    <?php
    foreach ($substitute as $substitute_item): ?>
        <option value="<?php echo $substitute_item['id'] ?>"><?php echo $substitute_item['id'].' '.$substitute_item['firstname'].' '.$substitute_item['lastname'] ?></option>
    <?php endforeach ?>
    </select>
<?php /*
<!-- C'est un test !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->
    <input type="hidden" name="substitute" id="substitute" /><br />
    <label for="txtSubstistute"><?php echo lang('users_create_field_manager');?></label>
    <div class="input-append">
        <input type="text" id="txtSubstitute" name="txtSubstitute" required readonly />
        <a id="cmdSelfSubstitute" class="btn btn-primary"><?php echo lang('users_create_button_self');?></a>
        <a id="cmdSelectSubstitute" class="btn btn-primary"><?php echo lang('users_create_button_select');?></a>
    </div><br />
    <i><?php echo lang('users_create_field_manager_description');?></i>
    <br /><br />
 <!--!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->
  */?>
    
    <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
  
    <label for="status"><?php echo lang('leaves_create_field_status');?></label>
    <select name="status" id="status">
        <option value="1" <?php if ($this->config->item('leave_status_requested') == FALSE) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($this->config->item('leave_status_requested') == TRUE) echo 'selected'; ?>><?php echo lang('Requested');?></option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_cancel');?></a>
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
<?php /*
<!--////////////////////////////////////////////////////////////////////////////-->
<div id="frmSelectSubstitute" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectSubstitute').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_create_popup_substitute_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectSubstituteBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_substitute();" class="btn"><?php echo lang('users_create_popup_manager_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectSubstitute').modal('hide');" class="btn"><?php echo lang('users_create_popup_manager_button_cancel');?></a>
    </div>
</div>
<!--////////////////////////////////////////////////////////////////////////////-->
*/?>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />

<?php require_once dirname(BASEPATH) . "/local/triggers/leave_view.php"; ?>

<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $user_id; ?>;
    var leaveId = null;
    var languageCode = '<?php echo $language_code;?>';
    var dateJsFormat = '<?php echo lang('global_date_js_format');?>';
    var dateMomentJsFormat = '<?php echo lang('global_date_momentjs_format');?>';
    
    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";
    
    var overlappingWithDayOff = "<?php echo lang('leaves_flash_msg_overlap_dayoff');?>";
    var listOfDaysOffTitle = "<?php echo lang('leaves_flash_spn_list_days_off');?>";
<?php /*
    //----------------------------------------------------------------------------------
   function select_substitute() {
        var employees = $('#employees').DataTable();
        if ( employees.rows({ selected: true }).any() ) {
            var substitute = employees.rows({selected: true}).data()[0][0];
            var text = employees.rows({selected: true}).data()[0][1] + ' ' + employees.rows({selected: true}).data()[0][2];
            $('#substitute').val(substitute);
            $('#txtSubstitute').val(text);
        }
        $("#frmSelectSubstitute").modal('hide');
    }
    //-----------------------------------------------------------------------------------
*/?>
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
    if (typeof triggerValidateCreateForm == 'function') { 
       if (triggerValidateCreateForm() == false) return false;
    }
    
    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_create_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_create_field_end');?>";
    if ($('#duration').val() == "" || $('#duration').val() == 0) fieldname = "<?php echo lang('leaves_create_field_duration');?>";
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
       
       //Popup select substitute
        /*$("#cmdSelectSubstitute").click(function() {
            $("#frmSelectSubstitute").modal('show');
            $("#frmSelectSubstituteBody").load('<?php echo base_url(); ?>users/employees');
        });
        
        //Self substitute button
        $("#cmdSelfSubstitute").click(function() {
            $("#substitute").val('-1');
            $('#txtSubstitute').val('<?php //echo lang('users_create_field_manager_alt');?>');
        });*/

        
});
<?php }?>
        
        
</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms/leave.edit.js" type="text/javascript"></script>
