<?php
/**
 * This view displays the leave requests of the collaborators of the connected user (if any).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<script type="application/javascript">
    $("#menu_calendar_title").addClass('active');
    $("#menu_calendar_collaborators").addClass('active');
</script>
<div class="page-title">   
    <h1><?php echo lang('calendar_collaborators_title');?></h1>
    <p><?php echo lang('calendar_collaborators_description');?></p>
</div>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="span12">
<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('calendar_organization_field_select_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $organization_name;?>" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('calendar_organization_button_select_entity');?></button>
        </div>
    </div>
    <div class="span3">
        <label for="chkIncludeChildren">
            <input type="checkbox" value="" id="chkIncludeChildren" name="chkIncludeChildren"> <?php echo lang('calendar_organization_check_include_subdept');?>
        </label>
    </div>
</div>

<div class="row-fluid" data-toggle="buttons" style="padding-bottom:20px;">
    <div class="span3 btn conge"><?php echo lang('Planned');?></div>
    <div class="span3 btn btn-success conge"><?php echo lang('Accepted');?></div>
    <div class="span3 btn btn-warning conge"><?php echo lang('Requested');?></div>
    <div class="span3 btn btn-danger red conge"><?php echo lang('Rejected');?></div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_organization_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('calendar_organization_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('calendar_organization_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id='calendar'></div>

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

<link href="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.min.js"></script>
<?php if ($language_code != 'en') {?>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lang/<?php echo $language_code;?>.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    var entity = "<?php echo $organization_id; ?>"; //Id of responsible's entity
    var entityName = "<?php echo $organization_name; ?>";
    var includeChildren = true;
    var text; //Label of the selected entity
    
    
    //Refresh the calendar if data is available
    function refresh_calendar() {
        $('#calendar').fullCalendar('removeEventSources');
        //$('#calendar').fullCalendar('removeEvents');
        if (entity != -1) {
            var source = '<?php echo base_url();?>leaves/organization/' + entity;
            if ($('#chkIncludeChildren').prop('checked') == true) {
                source += '?children=true';
            } else {
                source += '?children=false';
            }
            $('#calendar').fullCalendar('addEventSource', source);
        }
        source = '<?php echo base_url();?>contracts/calendar/alldayoffs?entity=' + entity;
        if ($('#chkIncludeChildren').prop('checked') == true) {
            source += '&children=true';
        } else {
            source += '&children=false';
        }
            $('#calendar').fullCalendar('removeEventSource', source);
    }
    
    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        entityName = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(entityName);
        refresh_calendar();
        $.cookie('cal_entity', entity);
        $.cookie('cal_entityName', entityName);
        $.cookie('cal_includeChildren', includeChildren);
        $("#frmSelectEntity").modal('hide');
    }

$(document).ready(function() {
    
    //Global Ajax error handling mainly used for session expiration
    $( document ).ajaxError(function(event, jqXHR, settings, errorThrown) {
        $('#frmModalAjaxWait').modal('hide');
        if (jqXHR.status == 401) {
            bootbox.alert("<?php echo lang('global_ajax_timeout');?>", function() {
                //After the login page, we'll be redirected to the current page 
               location.reload();
            });
        } else { //Oups
            bootbox.alert("<?php echo lang('global_ajax_error');?>");
        }
        });

        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").appendTo("body").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select', "<?php echo $organization_id; ?>");
        });

        //On click the check box "include sub-department", refresh the content if a department was selected
        $('#chkIncludeChildren').click(function() {
            $.cookie('cal_includeChildren', $('#chkIncludeChildren').prop('checked'));
            refresh_calendar();
      });
    
        //Load alert forms
        $("#frmSelectEntity").appendTo('body').show();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });

    //Create a calendar and fill it with AJAX events
    $('#calendar').fullCalendar({
        timeFormat: ' ', /*Trick to remove the start time of the event*/
        header: {
            left: "prev,today,next",
            center: "title",
            right: ""
        },
        events: '<?php echo base_url();?>leaves/collaborators',
        loading: function(isLoading) {
            if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                $('#frmModalAjaxWait').appendTo("body").modal('show');
            } else {
                $('#frmModalAjaxWait').modal('hide');
            }    
        },
        eventRender: function(event, element, view) {
            if(event.imageurl){
                $(element).find('span:first').prepend('<img src="' + event.imageurl + '" />');
            }
        },
        eventAfterRender: function(event, element, view) {
            //Add tooltip to the element
            if(event.substitute != undefined || event.cause != undefined) $(element).attr('title', event.type+event.substitute+event.cause);
            else $(element).attr('title', event.title+'\n'+event.type);
            
            if (event.enddatetype == "Morning" || event.startdatetype == "Afternoon") {
                var nb_days = event.end.diff(event.start, "days");
                var duration = 0.5;
                var halfday_length = 0;
                var length = 0;
                var width = parseInt(jQuery(element).css('width'));
                if (nb_days > 0) {
                    if (event.enddatetype == "Afternoon") {
                        duration = nb_days + 0.5;
                    } else {
                        duration = nb_days;
                    }
                    nb_days++;
                    halfday_length = Math.round((width / nb_days) / 2);
                    if (event.startdatetype == "Afternoon" && event.enddatetype == "Morning") {
                        length = width - (halfday_length * 2);
                    } else {
                        length = width - halfday_length;
                    }
                } else {
                    halfday_length = Math.round(width / 2);   //Average width of a day divided by 2
                    length = halfday_length;
                }
            }
            $(element).css('width', length + "px");
            
            //Starting afternoon : shift the position of event to the right
            if (event.startdatetype == "Afternoon") {
                $(element).css('margin-left', halfday_length + "px");
            }
        },
        windowResize: function(view) {
            $('#calendar').fullCalendar( 'rerenderEvents' );
        },
        eventAfterAllRender: function(view) {
            var links = document.querySelectorAll(".fc-event-container a");
            var tabRed = [], tabGreen = [], tabOrange = [], tabGrey = [];
            var affect = "block";
            for (var i = 0, c = links.length ; i < c ; i++) {
                switch (links[i].style.backgroundColor)
                {
                    case "rgb(255, 0, 0)":
                        tabRed.push(i);
                        break;
                    case "rgb(153, 153, 153)":
                        tabGrey.push(i);
                        break;
                    case "rgb(248, 148, 6)":
                        tabOrange.push(i);
                        break;
                    case "rgb(70, 136, 71)":
                        tabGreen.push(i);
                        break;
                }
            }  
            $('.conge').on('click', function() {
                switch ($(this).text())
                {
                    case "<?php echo lang('Rejected');?>":
                        if (tabRed.length !=0 && links[tabRed[0]].style.display == "none") affect = "block";
                        else affect = "none";
                        for (var i=0, c = tabRed.length ; i < c ; i++) links[tabRed[i]].style.display = affect;
                        break;
                    case "<?php echo lang('Planned');?>": 
                        if (tabGrey.length !=0 && links[tabGrey[0]].style.display == "none") affect = "block";
                        else affect = "none";
                        for (var i=0, c = tabGrey.length ; i < c ; i++) links[tabGrey[i]].style.display = affect;
                        break;
                    case "<?php echo lang('Requested');?>": 
                        if (tabOrange.length !=0 && links[tabOrange[0]].style.display == "none") affect = "block";
                        else affect = "none";
                        for (var i=0, c = tabOrange.length ; i < c ; i++) links[tabOrange[i]].style.display = affect;
                        break;
                    case "<?php echo lang('Accepted');?>":
                        if (tabGreen.length !=0 && links[tabGreen[0]].style.display == "none") affect = "block";
                        else affect = "none";
                        for (var i=0, c = tabGreen.length ; i < c ; i++) links[tabGreen[i]].style.display = affect;
                        break;
                }
            });
        }
    });
    refresh_calendar();     
});
</script>