<div class="row-fluid">
    <div class="page-title">
        <h1>Leave Management System</h1>
        <p>Welcome in Leave Management System.</p>
    </div>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="contracts" width="100%">
            <thead id="table-titles">
                <tr>    
                    <th>Employees</th>
                    <?php if ($is_manager == TRUE) { ?><th>Managers</th><?php } ?>
                    <?php if ($is_hr == TRUE) { ?><th>HR responsible</th><?php } ?>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>Request a <a href="<?php echo base_url();?>leaves/create">new leave</a></td>
                <?php if ($is_manager == TRUE) { ?><td>Validate <a href="<?php echo base_url();?>requests">leave requests</a> submitted to you</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Create, edit, filter or remove <a href="<?php echo base_url();?>users">users</a></td><?php } ?>
            </tr>
            <tr>
                <td>See the <a href="<?php echo base_url();?>leaves">list of requests</a> you have submitted</td>
                <?php if ($is_manager == TRUE) { ?><td>List <a href="<?php echo base_url();?>requests/collaborators">your subordinates</a> to control the presence</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Attache responsible and employees <a href="<?php echo base_url();?>organization">to entities</td><?php } ?>
            </tr>
            <tr>
                <td>See your <a href="<?php echo base_url();?>leaves/counters">leave balance</a></td>
                <?php if ($is_manager == TRUE) { ?><td>Choose <a href="<?php echo base_url();?>requests/delegations">delegations</a> to allow another person to receive requests in your place</a></td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Suivre <a href="<?php echo base_url();?>hr/employees">leaves balance</a> (leave title, contracts, reports...)</td><?php } ?>
            </tr>
            <tr>
                <td>Visualize your <a href="<?php echo base_url();?>calendar/individual">calendars (individual and annual)</td>
                <?php if ($is_manager == TRUE) { ?><td>Visualize the <a href="<?php echo base_url();?>calendar/collaborators"> calendar of your subordinates</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Visualize <a href="<?php echo base_url();?>calendar/organization">global calendar of the organisation</a></td><?php } ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>



    <li><a href="#"></a></li>
    <li><a href="#">.</a></li>
    <li>View <a href="#"></a> </a></li>
