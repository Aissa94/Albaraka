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
        <p>{Firstname} {Lastname} veut soumettre une demande d'absence de type "{Type}" à son manager. Voici les <!--a href="{BaseUrl}leaves/requests/{LeaveId}">détails</a-->détails :</p>
        <table>
            <tr>
                <td>Du &nbsp;</td><td>{StartDate}</td>
            </tr>
            <tr>
                <td>Au &nbsp;</td><td>{EndDate}</td>
            </tr>
            <tr>
                <td>Type &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Durée &nbsp;</td><td>{Duration}</td>
            </tr>
            <tr>
                <td>Crédit &nbsp;</td><td>{Balance}</td>
            </tr>
            <tr>
                <td>Cause &nbsp;</td><td>{Reason}</td>
            </tr>
            <tr>
                <td>Manager &nbsp;</td><td>{FirstnameManager} {LastnameManager}</td>
            </tr>
        </table>
        <br />
        <p>
            <a href="{BaseUrl}requests/accept/{LeaveId}">Permettre</a>&nbsp;
            <a href="{BaseUrl}requests/reject/{LeaveId}">Refuser</a>
        </p>
        <p>Si vous permettez, cette demande sera envoyée au manager.</p>
        <h5>*** Ceci est un message généré automatiquement, veuillez ne pas répondre à ce message ***</h5>
    </body>
</html>
