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
        <h3>Activation / Désactivation des comptes</h3>
        <p>Bonjour {Lastname} {Firstname},</p>
        <p>Voici les comptes à activer pour aujourd'hui (le <?php echo date('d-m-Y'); ?>) :</p>
<?php
foreach ($Data_Activate as $item)
{  ?>
        <table>
            <tr>
                <td>Employee</td><td><?php echo $item->employee ?></td>
            </tr>
            <!--tr>
                <td>Congé</td><td>du <?php //echo date('d-m-Y', strtotime($item->startdate)) ?> au <?php //echo date('d-m-Y', strtotime($item->enddate)) ?></td>
            </tr-->
            <tr>
                <td>Action</td><td><?php echo $item->doing ?></td>
            </tr>
        </table>
<?php
} ?>
        <br/>
        <p>Et voici les comptes à désactiver :</p>
<?php
foreach ($Data_Desactivate as $item)
{  ?>
        <table>
            <tr>
                <td>Employee</td><td><?php echo $item->employee ?></td>
            </tr>
            <!--tr>
                <td>Congé</td><td>du <?php //echo date('d-m-Y', strtotime($item->startdate)) ?> au <?php //echo date('d-m-Y', strtotime($item->enddate)) ?></td>
            </tr-->
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
