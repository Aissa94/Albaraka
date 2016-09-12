<?php 
/**
 * This partial view (embedded into a modal form) allows a user to select an entity of the organization.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="input-append">
<input type="text" placeholder="<?php echo lang('organization_select_field_search_placeholder');?>" id="txtSearch" />
<button id="cmdSearchOrg" class="btn btn-primary"><?php echo lang('organization_select_button_search');?></button>
</div>

<div style="text-align: left;" id="organization"></div>

<link rel="stylesheet" href='<?php echo base_url(); ?>assets/jsTree/themes/default/style.css' type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jsTree/jstree.min.js"></script>

<script type="text/javascript">
    $(function () {
        //Search in the treeview
        $("#cmdSearchOrg").click(function () {
            $("#organization").jstree("search", $("#txtSearch").val());
        });
        
        $('#organization').jstree({
            rules : {
                deletable  : false,
                creatable  : false,
                draggable  : false,
                dragrules  : false,
                renameable : false
              },
            core : {
              multiple : false,
              data : {
                url : function (node) {
                    if (node.id === '#') {
                        var pathArray = window.location.pathname.split( '/' );
                        if (pathArray[pathArray.length - 1] == 'collaborators') return '<?php echo base_url().'organization/minRoot/'.array_shift(array_keys($_GET));  ?>';
                        else return '<?php echo base_url(); ?>organization/root' ;
                    } else return'<?php echo base_url(); ?>organization/children';
                },
                'data' : function (node) {
                  return { 'id' : node.id };
                }
              },
              'check_callback' : true
            },
            plugins: [ "search", "state", "sort" ]
        });
    });
</script>
