<?php
/**
 * This view displays the leave requests of the connected user.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<script type="application/javascript">
    $("#menu_calendar_title").addClass('active');
    $("#menu_calendar_individual").addClass('active');
</script>
<div class="row-fluid" style="padding-bottom:20px;">
    <div class="page-title">   
        <h1><?php echo lang('calendar_individual_title');?></h1>
        <p><?php echo lang('calendar_individual_description');?></p>
    </div>
    <div class="span12">


<div class="row-fluid">
    <div class="span6">
        <button id="cmdPrevious" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></button>
        <button id="cmdToday" class="btn btn-primary"><?php echo lang('today');?></button>
        <button id="cmdNext" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></button>
    </div>
    <div class="span6">
        <div class="pull-right">
            <button id="cmdDisplayDayOff" class="btn btn-primary"><i class="icon-calendar icon-white"></i>&nbsp;<?php echo lang('calendar_individual_day_offs');?></button>
        </div>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span3 btn conge"><?php echo lang('Planned');?></div>
    <div class="span3 btn btn-success conge"><?php echo lang('Accepted');?></div>
    <div class="span3 btn btn-warning conge"><?php echo lang('Requested');?></div>
    <div class="span3 btn btn-danger red conge"><?php echo lang('Rejected');?></div>    
    <div class="span4">
        <?php if ($this->config->item('ics_enabled') == FALSE) {?>
        &nbsp;
        <?php } else {?>
        <span class="pull-right"><a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a></span>
        <?php }?>        
    </div>
</div>

<div id='calendar'></div>

    </div>
</div>

<div id="frmEvent" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEvent').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_individual_popup_event_title');?></h3>
    </div>
    <div class="modal-body">
        <a href="#" id="lnkDownloadCalEvnt"><?php echo lang('calendar_individual_popup_event_link_ical');?></a> <?php echo lang('calendar_individual_popup_event_link_ical_description');?>
        
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEvent').modal('hide');" class="btn"><?php echo lang('calendar_individual_popup_event_button_close');?></a>
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

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/individual/' . $user_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/individual/' . $user_id;?>">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.min.js"></script>
<?php if ($language_code != 'en') {?>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lang/<?php echo $language_code;?>.js"></script>
<?php }?>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>
<script type="text/javascript">
var toggleDayoffs = false;
    
$(function () {
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
    
    $("#frmEvent").appendTo('body').show();

    //Load FullCalendar widget
    $('#calendar').fullCalendar({
        timeFormat: ' ', /*Trick to remove the start time of the event*/
        header: {
            left: "",
            center: "title",
            right: ""
        },
        /*defaultView: 'agendaWeek',*/
        events: '<?php echo base_url();?>leaves/individual',
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.color != '#000000') { //This color must probably change to #555555
                var link = "<?php echo base_url();?>ics/ical/" + calEvent.id;
                $("#lnkDownloadCalEvnt").attr('href', link);
                $('#frmEvent').appendTo("body").modal('show');
            }
        },
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
            if(event.substitute != undefined || event.cause != undefined) $(element).attr('title', event.substitute+event.cause);
            else $(element).attr('title', event.title);
            
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
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmEvent').on('hidden', function() {
        $(this).removeData('modal');
    });
    
    //Toggle day offs display
    $('#cmdDisplayDayOff').on('click', function() {
        toggleDayoffs = !toggleDayoffs;
        if (toggleDayoffs) {
            $('#calendar').fullCalendar('addEventSource', '<?php echo base_url();?>contracts/calendar/userdayoffs');
        } else {
            $('#calendar').fullCalendar('removeEventSources', '<?php echo base_url();?>contracts/calendar/userdayoffs');
        }
    });
    
    //Manage Prev/Next buttons
    $('#cmdNext').click(function() {
        $('#calendar').fullCalendar('next');
    });
    $('#cmdPrevious').click(function() {
        $('#calendar').fullCalendar('prev');
    });
    
    //On click on today, if the current month is the same than the displayed month, we refetch the events
    $('#cmdToday').click(function() {
        var displayedDate = new Date($('#calendar').fullCalendar('getDate'));
        var currentDate = new Date();
        if (displayedDate.getMonth() == currentDate.getMonth()) {
            $('#calendar').fullCalendar('refetchEvents');
        } else {
            $('#calendar').fullCalendar('today');
        }
    });
    
    //Copy/Paste ICS Feed
    var client = new ZeroClipboard($("#cmdCopy"));
    $('#lnkICS').click(function () {
        $("#frmLinkICS").appendTo("body").modal('show');
    });
    client.on( "aftercopy", function( event ) {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });
});
</script>
