<?php
/**
 * This view displays the leave requests of the collaborators of the connected user (if any).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('calendar_collaborators_title');?> &nbsp;<?php echo $help;?></h2>

<p><?php echo lang('calendar_collaborators_description');?></p>

<div class="row-fluid" data-toggle="buttons">
    <div class="span3 btn conge"><?php echo lang('Planned');?></div>
    <div class="span3 btn btn-success conge"><?php echo lang('Accepted');?></div>
    <div class="span3 btn btn-warning conge"><?php echo lang('Requested');?></div>
    <div class="span3 btn btn-danger red conge"><?php echo lang('Rejected');?></div>
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
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
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
    
    //Create a calendar and fill it with AJAX events
    $('#calendar').fullCalendar({
        timeFormat: ' ', /*Trick to remove the start time of the event*/
        header: {
            left: "prev,next today",
            center: "title",
            right: ""
        },
        events: '<?php echo base_url();?>leaves/collaborators',
        loading: function(isLoading) {
            if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                $('#frmModalAjaxWait').modal('show');
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
});
</script>