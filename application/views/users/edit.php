<?php 
/**
 * This view allows to modify an employee record.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<div class="row-fluid">
<div class="page-title"> 
<h1><?php echo lang('users_edit_title');?><?php echo $users_item['id']; ?></h1>
</div>
<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('users/edit/' . $users_item['id'] .'?source=' . $_GET['source']);
} else {
    echo form_open('users/edit/' . $users_item['id']);
} ?>
    <div class="form-group">
    <input type="hidden" name="id" value="<?php echo $users_item['id']; ?>" required /><br />

    <label class="col-md-3" for="firstname"><?php echo lang('users_edit_field_firstname');?></label>
    <input type="text" name="firstname" value="<?php echo $users_item['firstname']; ?>" required /><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="lastname"><?php echo lang('users_edit_field_lastname');?></label>
    <input type="text" name="lastname" value="<?php echo $users_item['lastname']; ?>" required /><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="login"><?php echo lang('users_edit_field_login');?></label>
    <input type="text" name="login" value="<?php echo $users_item['login']; ?>" required /><br />
	</div>
    <div class="form-group">
    <label class="col-md-3" for="email"><?php echo lang('users_edit_field_email');?></label>
    <input type="email" id="email" name="email" value="<?php echo $users_item['email']; ?>" required /><br />
	</div>
    <div class="form-group">	
    <label class="col-md-3" for="role[]"><?php echo lang('users_edit_field_role');?></label>
    <select class="selectpicker" name="role[]">
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ((((int)$roles_item['id']) & ((int) $users_item['role']))) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>
    </div>
    <input type="hidden" name="manager" id="manager" value="<?php echo $users_item['manager']; ?>" /><br />

    <div class="form-group">
    <label class="col-md-3" for="contract"><?php echo lang('users_edit_field_contract');?></label>
    <select class="selectpicker" name="contract" id="contract" data-live-search="true">
    <?php foreach ($contracts as $contract): ?>
        <option value="<?php echo $contract['id'] ?>" <?php if ($contract['id'] == $users_item['contract']) echo "selected"; ?>><?php echo $contract['name']; ?></option>
    <?php endforeach ?>
        <option value="0" <?php if ($users_item['contract'] == 0 || is_null($users_item['contract'])) echo "selected"; ?>>&nbsp;</option>
    </select>
    </div>
    <div class="form-group">
    <input type="hidden" name="entity" id="entity" value="<?php echo $users_item['organization']; ?>" /><br />
    <label class="col-md-3" for="txtEntity"><?php echo lang('users_edit_field_entity');?></label>
    <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $organization_label; ?>" required readonly />
        <a id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('users_edit_button_select');?></a>
    </div>
    <br />
    </div>
    <div class="form-group">
    <input type="hidden" name="position" id="position" value="<?php echo $users_item['position']; ?>" /><br />
    <label class="col-md-3" for="txtPosition"><?php echo lang('users_create_field_position');?></label>
    <div class="input-append">
        <input type="text" id="txtPosition" name="txtPosition" value="<?php echo $position_label; ?>" required readonly />
        <a id="cmdSelectPosition" class="btn btn-primary"><?php echo lang('users_edit_button_select');?></a>
    </div>    
    <br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="viz_datehired"><?php echo lang('users_edit_field_hired');?></label>
    <input type="text" id="viz_datehired" name="viz_datehired" value="<?php 
$date = new DateTime($users_item['datehired']);
echo $date->format(lang('global_date_format'));
?>" /><br />
    <input type="hidden" name="datehired" id="datehired" /><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="identifier"><?php echo lang('users_edit_field_identifier');?></label>
    <input type="text" name="identifier" value="<?php echo $users_item['identifier']; ?>" /><br />
    </div>
    <div class="form-group">
    <label class="col-md-3" for="language"><?php echo lang('users_edit_field_language');?></label>
    <select class="selectpicker" name="language">
         <?php 
         $languages = $this->polyglot->nativelanguages($this->config->item('languages'));
         foreach ($languages as $code => $language): ?>
        <option value="<?php echo $code; ?>" <?php if ($code == $users_item['language']) echo "selected"; ?>><?php echo $language; ?></option>
        <?php endforeach ?>
    </select>
    </div>
    <div class="form-group">
    <?php 
        if (!is_null($users_item['timezone'])) {
            $tzdef = $users_item['timezone'];
        } else {
            $tzdef = $this->config->item('default_timezone');
            if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
        }
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);?>
    <label class="col-md-3" for="timezone"><?php echo lang('users_edit_field_timezone');?></label>
    <select class="selectpicker" id="timezone" name="timezone" data-live-search="true">
    <?php foreach ($tzlist as $tz) { ?>
        <option value="<?php echo $tz ?>" <?php if ($tz == $tzdef) echo "selected"; ?>><?php echo $tz; ?></option>
    <?php } ?>
    </select>
    </div>
    <div class="form-group">
    <?php if ($this->config->item('ldap_basedn_db')) {?>
    <label class="col-md-3" for="ldap_path"><?php echo lang('users_edit_field_ldap_path');?></label>
    <input type="text" class="input-xxlarge" name="ldap_path" value="<?php echo $users_item['ldap_path']; ?>" />
    <?php }?>
    </div>
    <br />
    <br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('users_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('users_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url();?>users" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('users_edit_button_cancel');?></a>
    <?php } ?>
</form>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_edit_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('users_edit_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('users_edit_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectPosition" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_edit_popup_position_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectPositionBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_position();" class="btn"><?php echo lang('users_edit_popup_position_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="btn"><?php echo lang('users_edit_popup_position_button_cancel');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript">
    
    
    
    //Popup select entity: on click OK, find the entity id for the selected node
    function select_entity() {
        var entity = $('#organization').jstree('get_selected')[0];
        var text = $('#organization').jstree().get_text(entity);
        $('#entity').val(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }
    
    //Popup select postion: on click OK, find the position id for the selected line
    function select_position() {
        var positions = $('#positions').DataTable();
        if ( positions.rows({ selected: true }).any() ) {
            var position = positions.rows({selected: true}).data()[0][0];
            var text = positions.rows({selected: true}).data()[0][1];
            $('#position').val(position);
            $('#txtPosition').val(text);
        }
        $("#frmSelectPosition").modal('hide');
    }

    $(document).ready(function() {
        $("#viz_datehired").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: '<?php echo lang('global_date_js_format');?>',
            altFormat: "yy-mm-dd",
            altField: "#datehired"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        $("#viz_datehired").datepicker( "setDate", "<?php echo $date->format(lang('global_date_format'));?>");
        
        
        
        
        //Popup select position
        $("#cmdSelectPosition").click(function() {
            $("#frmSelectPosition").appendTo("body").modal('show');
            $("#frmSelectPositionBody").load('<?php echo base_url(); ?>positions/select');
        });
        
        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").appendTo("body").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //Load alert forms
        $("#frmSelectEntity").appendTo('body').show();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
    });
</script>
