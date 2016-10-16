<?php 
/**
 * This view contains the menu of the application
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('menu', $language);?>

<?php 
$boolean_to_send = $this->db->query('SELECT boolean_to_send FROM imal_sender');
$boolean_to_send = $boolean_to_send->result()[0]->boolean_to_send;
if($boolean_to_send){
    require_once('/application/controllers/leaves.php');
    $leave_object = new Leaves();
    $leave_object->sendMailToImal();
    $boolean_to_send = $this->db->query('UPDATE imal_sender SET boolean_to_send = 0');
}
?>

<?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
<div id="frmChangeMyPwd" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h3><?php echo lang('menu_password_popup_title');?></h3>
    </div>
    <div class="modal-body" id="frmChangeMyPwdBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" onclick="document.getElementById('send').click()"><?php echo lang('menu_password_popup_button_modify');?></button>
        <button class="btn" data-dismiss="modal"><?php echo lang('menu_password_popup_button_cancel');?></button>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        //Popup change password
        $("#cmdChangePassword").click(function() {
            $("#frmChangeMyPwd").appendTo("body").modal('show');
            $("#frmChangeMyPwdBody").load('<?php echo base_url();?>users/reset/<?php echo $user_id; ?>');
        });
        //Popup change password
        $("#cmdChangePass").click(function() {
            $("#frmChangeMyPwd").appendTo("body").modal('show');
            $("#frmChangeMyPwdBody").load('<?php echo base_url();?>users/reset/<?php echo $user_id; ?>');
        });
    });

</script>
<?php } ?>

<?php if ($this->config->item('enable_mobile') != FALSE) { ?>
<div id="frmGenerateQRCode" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h3>QR Code</h3>
    </div>
    <div class="modal-body" id="frmGenerateQRCodeBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal"><?php echo lang('OK');?></button>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        //Popup generate a QR Code for mobile access
        $("#cmdGenerateQRCode").click(function() {
            $("#frmGenerateQRCode").appendTo("body").modal('show');
            $("#frmGenerateQRCodeBody").load('<?php echo base_url();?>admin/qrcode');
        });
        
    });
</script>
<?php } ?>

<style>
     .dev-page{visibility: hidden;}            
</style>
<div class="dev-page">

             <!-- page header -->    
            <div class="dev-page-header">
                
                <div class="dph-logo">
                    <a href="<?php echo base_url();?>home">Albaraka</a>
                    <a class="dev-page-sidebar-collapse">
                        <div class="dev-page-sidebar-collapse-icon">
                            <span class="line-one"></span>
                            <span class="line-two"></span>
                            <span class="line-three"></span>
                        </div>
                    </a>
                </div>

                <ul class="dph-buttons pull-right">                    
                    <li class="dph-button-stuck">
                        <a href="<?php echo base_url();?>users/myprofile" title="<?php echo lang('menu_banner_tip_myprofile');?>">
                           <i class="icon-user"></i>
                        </a>
                    </li>
                    <?php if ($this->config->item('enable_mobile') != FALSE) { ?>                   
                    <li class="dph-button-stuck">
                        <a href="#"  id="cmdGenerateQRCode" title="QR Code">
                            <i class="fa fa-mobile"></i>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if ($this->config->item('ldap_enabled') === FALSE && $this->config->item('saml_enabled') === FALSE) { ?>
                    <li class="dph-button-stuck">
                        <a href="#" id="cmdChangePassword" title="<?php echo lang('menu_banner_tip_reset');?>">
                            <i class="icon-lock"></i>
                        </a>
                    </li>
                    <?php }
                    $urlLogout = 'session/logout';
                    if ($this->config->item('saml_enabled') === TRUE){
                        $urlLogout = 'api/slo';
                    } ?>
                    <li class="dph-button-stuck">
                        <a  href="<?php echo base_url() . $urlLogout;?>" title="<?php echo lang('menu_banner_logout');?>">
                           <i class="icon-off"></i>
                        </a>
                    </li>
                </ul>                                                
                
            </div>
            <!-- ./page header -->
            
            <!-- page container -->
            <div class="dev-page-container">

                <!-- page sidebar -->
                <div class="dev-page-sidebar">
                    <div class="profile profile-transparent">
                        <div class="profile-image">
                            <img src="<?php echo base_url();?>assets/images/user.png" class="mCS_img_loaded">
                            <div class="profile-badges">
                                <a href="#" id="cmdChangePass" title="<?php echo lang('menu_banner_tip_reset');?>" class="profile-badges-left"><i class="fa fa fa-key"></i> </a>
                                <a href="<?php echo base_url();?>users/myprofile" title="<?php echo lang('menu_banner_tip_myprofile');?>" class="profile-badges-right"><i class="fa fa-info"></i> </a>
                            </div>
                            <div class="profile-status online"></div>
                        </div>
                        <div class="profile-info">
                            <h4><?php echo $fullname;?></h4>
                        </div>                        
                    </div>
                    <ul class="dev-page-navigation">
                        <li class="title">Navigation</li>
                        <?php if ($is_hr == TRUE) { ?>
                        <li id="menu_admin_title">
                            <a href="#"><i class="fa fa-user"></i> <span><?php echo lang('menu_admin_title');?></span></a>
                            <ul>                              
                                <li id="menu_admin_list_users"><a href="<?php echo base_url();?>users"><i class="fa fa-users"></i> <?php echo lang('menu_admin_list_users');?></a></li>                                
                                <li id="menu_admin_add_user"><a href="<?php echo base_url();?>users/create"><i class="fa fa-user-plus"></i> <?php echo lang('menu_admin_add_user');?></a></li>
                                <li id="menu_hr_list_leaves_type"><a href="<?php echo base_url();?>leavetypes"><?php echo lang('menu_hr_list_leaves_type');?></a>
                                <li id="menu_admin_settings_divider">
                                    <a href="#"><?php echo lang('menu_admin_settings_divider');?></a>
                                    <ul>
                                        <li id="menu_admin_settings"><a href="<?php echo base_url();?>admin/settings"><?php echo lang('menu_admin_settings');?></a></li>
                                        <li id="menu_admin_diagnostic"><a href="<?php echo base_url();?>admin/diagnostic"><?php echo lang('menu_admin_diagnostic');?></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li> 
                        <?php } ?>

                <?php if ($is_hr == TRUE) { ?>
                <li id="menu_hr_title">
                  <a href="#"><i class="fa fa-file-text-o"></i> <span><?php echo lang('menu_hr_title');?>&nbsp;</span>
                      <?php if ($requests_count > 0) { ?>
                      <span class="badge badge-warning"><?php echo $requests_count;?></span>
                      <?php } ?>
                  </a>
                  <ul>
                    <li id="menu_hr_employees_divider">
                        <a href="#"><?php echo lang('menu_hr_employees_divider');?></a>
                        <ul>
                            <li id="menu_hr_list_employees"><a href="<?php echo base_url();?>hr/employees"><i class="fa fa-list"></i> <?php echo lang('menu_hr_list_employees');?></a></li>
                            <li id="menu_hr_list_organization"><a href="<?php echo base_url();?>organization"><i class="fa fa-sitemap"></i> <?php echo lang('menu_hr_list_organization');?></a></li>
                            <li id="menu_hr_request_leave"><a href="<?php echo base_url();?>hr/requests">
                                <?php echo lang('menu_hr_request_leave');?>
                                <?php if ($requested_leaves_count > 0) { ?>
                                <span class="badge badge-info"><?php echo $requested_leaves_count;?></span>
                                <?php } ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li id="menu_hr_contracts_divider">
                        <a href="#"><?php echo lang('menu_hr_contracts_divider');?></a>
                        <ul>
                            <li id="menu_hr_list_contracts"><a href="<?php echo base_url();?>contracts"><?php echo lang('menu_hr_list_contracts');?></a></li>
                            <li id="menu_hr_list_positions"><a href="<?php echo base_url();?>positions"><?php echo lang('menu_hr_list_positions');?></a></li>
                        </ul>
                    </li>
                   <li id="menu_hr_report_leaves"><a href="<?php echo base_url();?>reports/leaves"><?php echo lang('menu_hr_reports_divider');?></a></li>
                    
                </ul>
            </li>
              <?php } ?>

             <?php if ($is_manager == TRUE) { ?>
                <li id="menu_validation_title">
                  <a href="#"><i class="fa fa-check-square-o"></i> <span><?php echo lang('menu_validation_title');?>&nbsp;
                      <?php if ($requests_count > 0) { ?>
                      <span class="badge badge-warning"><?php echo $requests_count;?></span>
                      <?php } ?>
                      &nbsp;</span>
                  </a>
                  <ul>
                    <li id="menu_validation_leaves"><a href="<?php echo base_url();?>requests"><?php echo lang('menu_validation_leaves');?>
                                <?php if ($requested_leaves_count > 0) { ?>
                                <span class="badge badge-info"><?php echo $requested_leaves_count;?></span>
                                <?php } ?></a>
                    </li>
                    <li id="menu_validation_divider">
                    <a href="#"><?php echo lang('menu_validation_title');?></a>
                        <ul>
                            <li id="menu_validation_collaborators"><a href="<?php echo base_url();?>requests/collaborators"><i class="fa fa-level-down"></i> <?php echo lang('menu_validation_collaborators');?></a></li>
                            <li id="menu_hr_report_leave_balance"><a href="<?php echo base_url();?>requests/balance"><?php echo lang('menu_hr_report_leave_balance');?></a></li>
                            <li id="menu_validation_delegations"><a href="<?php echo base_url();?>requests/delegations"><i class="fa fa-level-up"></i> <?php echo lang('menu_validation_delegations');?></a></li>
                        </ul>
                    </li>
                </ul>
                </li>
              <?php } ?>

                <li id="menu_requests_title">
                    <a href="#"><i class="fa fa-mail-reply-all"></i> <span><?php echo lang('menu_requests_title');?></span></a>
                    <ul>
                        <li id="menu_leaves_create_request"><a href="<?php echo base_url();?>leaves/create"><i class="fa fa-pencil-square-o"></i> <?php echo lang('menu_leaves_create_request');?></a></li>
                        <li id="menu_leaves_list_requests"><a href="<?php echo base_url();?>leaves"><i class="glyphicon glyphicon-list-alt"></i> <?php echo lang('menu_leaves_list_requests');?></a></li>
                        <li id="menu_leaves_counters"><a href="<?php echo base_url();?>leaves/counters"><?php echo lang('menu_leaves_counters');?></a></li>
                        <?php if ($this->config->item('disable_overtime') == FALSE) { ?>
                        <li id="menu_requests_overtime">
                            <a href="#"><?php echo lang('menu_requests_overtime');?></a>
                            <ul>
                                <li id="menu_requests_list_extras"><a href="<?php echo base_url();?>extra"><?php echo lang('menu_requests_list_extras');?></a></li>
                                <li id="menu_requests_request_extra"><a href="<?php echo base_url();?>extra/create"><?php echo lang('menu_requests_request_extra');?></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <li id="menu_calendar_title">
                    <a href="#"><i class="fa fa-calendar"></i> <span><?php echo lang('menu_calendar_title');?></span></a>
                    <ul>
                      <li id="menu_calendar_individual"><a href="<?php echo base_url();?>calendar/individual"><?php echo lang('menu_calendar_individual');?></a></li>
                      <li id="menu_calendar_year"><a href="<?php echo base_url();?>calendar/year"><?php echo lang('menu_calendar_year');?></a></li>
                      <!--li><a href="<?php echo base_url();?>calendar/workmates"><?php //echo lang('menu_calendar_workmates');?></a></li-->
                      <?php if ($is_manager == TRUE) { ?>
                      <li id="menu_calendar_collaborators"><a href="<?php echo base_url();?>calendar/collaborators"><?php echo lang('menu_calendar_collaborators');?></a></li>
                      <?php } ?>
                      <!--li><a href="<?php echo base_url();?>calendar/department"><?php //echo lang('menu_calendar_department');?></a></li-->
                      <?php if ($is_hr == TRUE) { ?>
                      <li id="menu_calendar_organization"><a href="<?php echo base_url();?>calendar/organization"><?php echo lang('menu_calendar_organization');?></a></li>
                      <li id="menu_calendar_tabular"><a href="<?php echo base_url();?>calendar/tabular"><?php echo lang('menu_calendar_tabular');?></a></li>
                      <?php } ?>
                    </ul>
                </li>
              <?php if ($is_hr == TRUE) { ?>
                  <li id="menu_discordance_title">
                            <a href="#"><i class="fa fa-code-fork"></i> <span><?php echo lang('menu_discordance_title');?></span></a>
                            <ul>
                                <li id="menu_discordance_leave"><a href="<?php echo base_url();?>discordance"><?php echo lang('menu_discordance_leave');?></a></li>
                                <li id="menu_discordance_pay"><a href="<?php echo base_url();?>discordance/pay"><?php echo lang('menu_discordance_pay');?></a></li>
                            </ul>
                        </li>
              <?php } ?>
              </ul>            
    </div><!-- /.navbar -->


    <div class="dev-page-content">                    
                    <!-- page content container -->
                    <div class="container">