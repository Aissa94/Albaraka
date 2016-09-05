<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<html lang="fr">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
        <style>
            table {width:50%;margin:5px;border-collapse:collapse;}
            table, th, td {border: 1px solid black;}
            th, td {padding: 20px;}
            h5 {color:red;}
        </style>
    </head>
    <body>
        <h3>{Title}</h3>
        <p>Hello {Firstname} {Lastname},</p>
        <p>This is the list of accounts that must be activated today (the {Date}) :</p>
<?php
foreach ($Data_Activate as $item)
{  ?>
        <table>
            <tr>
                <td>Employee</td><td><?php echo $item->employee ?></td>
            </tr>
            <tr>
                <td>Congé</td><td>du <?php echo $item->startdate ?> au <?php echo $item->enddate ?></td>
            </tr>
            <tr>
                <td>Action</td><td><?php echo $item->doing ?></td>
            </tr>
        </table>
<?php
} ?>
        <br/>
        <p>And this is the list of accounts that must be desactivated :</p>
<?php
foreach ($Data_Desactivate as $item)
{  ?>
        <table>
            <tr>
                <td>Employee</td><td><?php echo $item->employee ?></td>
            </tr>
            <tr>
                <td>Congé</td><td>du <?php echo $item->startdate ?> au <?php echo $item->enddate ?></td>
            </tr>
            <tr>
                <td>Action</td><td><?php echo $item->doing ?></td>
            </tr>
        </table>
<?php
}  ?>
        <br />
        <hr>
        <h5>*** Ceci est un message généré automatiquement, veuillez ne pas répondre à ce message ***</h5>
    </body>
</html>
