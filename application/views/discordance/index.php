<?php
/**
 * This view lists the contracts created into the application
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<script type="application/javascript">
    $("#menu_discordance_title").addClass('active');
    $("#menu_discordance_leave").addClass('active');
</script>
<div class="row-fluid">
    <div class="page-title">   
<h1><?php echo lang('discordance_index_title');?></h1>
</div>
    <div class="span12">

<?php if($this->session->flashdata('msg')){ ?>
<div class="alert fade in" id="flashbox">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $this->session->flashdata('msg'); ?>
</div>
 
<script type="text/javascript">
//Flash message
$(document).ready(function() {
    $("#flashbox").show();
});
</script>
<?php } ?>
<div class="row-fluid">
<div class="table-responsive">
<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered " id="contracts" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('discordance_index_thead_id');?></th>
            <th><?php echo lang('discordance_index_thead_employee');?></th>
            <th><?php echo lang('discordance_index_thead_lastname');?></th>
            <th><?php echo lang('discordance_index_thead_firstname');?></th>
            <th><?php echo lang('discordance_index_thead_service');?></th>
            <th><?php echo lang('discordance_index_thead_start');?></th>
            <th><?php echo lang('discordance_index_thead_end');?></th>
            <th><?php echo lang('discordance_index_thead_time');?></th>
            <th><?php echo lang('discordance_index_thead_description');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($discordances as $contracts_item): ?>
    <tr>
        <td data-order="<?php echo $contracts_item['id']; ?>">
            <?php echo $contracts_item['titredecng'] ?></td>
        <td><?php echo $contracts_item['employee']; ?></td>
        <td><?php echo $contracts_item['firstname']; ?></td>
        <td><?php echo $contracts_item['lastname']; ?></td>
        <td><?php echo $contracts_item['name']; ?></td>
        <?php 
        $startentdate = $contracts_item['startdate'];
        $endentdate = $contracts_item['enddate'];
        if (strpos(lang('global_date_format'), 'd') < strpos(lang('global_date_format'), 'm')) {
            $pieces = explode("-", $startentdate);
            $startentdate = $pieces[2] . '/' . $pieces[1] . '/' . $pieces[0];
            $pieces = explode("-", $endentdate);
            $endentdate = $pieces[2] . '/' . $pieces[1] . '/' . $pieces[0];
        }
        ?>
        <td><?php echo $startentdate; ?></td>
        <td><?php echo $endentdate; ?></td>
        <td><?php echo intval($contracts_item['duration']); ?></td>
        <td><?php echo $contracts_item['description']; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table></div>
	</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>


<div class="row-fluid"><div class="span12">&nbsp;</div></div>
</div>
<div id="frmDeleteContract" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('discordance_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('discordance_index_popup_delete_description');?></p>
        <p><?php echo lang('discordance_index_popup_delete_confirm');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteContract" class="btn btn-danger"><?php echo lang('discordance_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="btn"><?php echo lang('discordance_index_popup_delete_button_no');?></a>
    </div>
</div>

<div id="frmEntitledDays" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('discordance_index_popup_entitled_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="btn"><?php echo lang('discordance_index_popup_entitled_button_close');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#contracts').dataTable({
        stateSave: true,
        language: {
            decimal:            "<?php echo lang('datatable_sInfoThousands');?>",
            processing:       "<?php echo lang('datatable_sProcessing');?>",
            search:              "<?php echo lang('datatable_sSearch');?>",
            lengthMenu:     "<?php echo lang('datatable_sLengthMenu');?>",
            info:                   "<?php echo lang('datatable_sInfo');?>",
            infoEmpty:          "<?php echo lang('datatable_sInfoEmpty');?>",
            infoFiltered:       "<?php echo lang('datatable_sInfoFiltered');?>",
            infoPostFix:        "<?php echo lang('datatable_sInfoPostFix');?>",
            loadingRecords: "<?php echo lang('datatable_sLoadingRecords');?>",
            zeroRecords:    "<?php echo lang('datatable_sZeroRecords');?>",
            emptyTable:     "<?php echo lang('datatable_sEmptyTable');?>",
            paginate: {
                first:          "<?php echo lang('datatable_sFirst');?>",
                previous:   "<?php echo lang('datatable_sPrevious');?>",
                next:           "<?php echo lang('datatable_sNext');?>",
                last:           "<?php echo lang('datatable_sLast');?>"
            },
            aria: {
                sortAscending:  "<?php echo lang('datatable_sSortAscending');?>",
                sortDescending: "<?php echo lang('datatable_sSortDescending');?>"
            }
        }
    });
    $("#frmChangePwd").appendTo('body').show();
    $("#frmEntitledDays").appendTo('body').show();
	
    //On showing the confirmation pop-up, add the contract id at the end of the delete url action
    $('#frmDeleteContract').on('show', function() {
        var link = "<?php echo base_url();?>discordance/delete/" + $(this).data('id');
        $("#lnkDeleteContract").attr('href', link);
    });

    //Display a modal pop-up so as to confirm if a contract has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#contracts tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteContract').data('id', id).modal('show');
    });
    
    $('#frmEntitledDays').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
