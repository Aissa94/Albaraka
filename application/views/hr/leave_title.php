<?php
/**
 * This view lists the list leave requests created by an employee (from HR menu).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<style type="text/css"> 
table  
    { border-collapse:collapse;
     direction:rtl; 
    text-align:center; }
#tab td{ width:8.5% ; } 

td { text-align:center;  }
page { 
    font-size:12;
      }
#tab2 td{text-align:left;}
input {
    background-color: transparent;
    border: 0px solid transparent;
    width: 40px;
    color: black;
}
</style>

<page backtop="20mm" backleft="10mm" backright="10mm" backbottom="10mm">
<table><tr><td style="width:100%;border: 0px solid white;text-align:right;"><img src="<?php echo base_url();?>assets/images/logo_title.png" /></td></tr></table><br>
    <b><u>Direction des Ressources Humaines et de la</u></b><br>
    <b><u>Formation ,</u></b><br>
    <b><u>Service du Personnel</u></b><br><br>
    Réf: <input type="text">/<?php echo date('Y');?>/DRHF<br><br>
    Num:  <?php echo $conge;?>
    <table align=center>
         <tr>
            <td style="width:100%;font-size:18;" ><br><br><b><i>TITRE DE CONGE</i></b>   </td>
        </tr> 
        <tr>
            <td style="width:1%;text-align:right;" ><br><b><u>Alger le,</u></b> <?php echo date('d/m/Y');?></td>
        </tr> 
    </table> <br><br><br>
    
    
    <b>Nom :</b> <?php echo strtoupper($lastname);?><br><br><br><br>
    <b>Prénom : </b>  <?php echo strtoupper($firstname);?><br><br><br><br>  
    <b>Fonction : </b><?php echo $description;?><br><br><br><br>
    <b>Structure :</b>   <?php echo $name;   ?><br><br><br>
    <b>Durée du congé :</b> <?php echo intval($duration);?> Jour<?php if ($duration > 1) echo 's'; ?><br><br><br>
    <b>Date Départ :</b> <?php echo date('d/m/Y',strtotime($startdate));?><br><br><br>
    <b>Date Retour :</b> <?php echo date('d/m/Y',strtotime($enddate."+ 1 day")); ?><br><br><br>
    <b>Nature de congé :</b> <?php echo $type;?><br><br>
   <br><br><br><br><br>
   <div style="text-align:right;"><b>Le Directeur des Ressources<br><br> Humaines et de la Formation </b></div>


</page>

<?php 

$content = ob_get_clean() ;  
 require_once('assets/html2pdf/html2pdf.class.php') ; 
 $pdf = new HTML2PDF('P','A4','fr', true,  'UTF-8' ,  array(5, 5, 5, 8) );
 $pdf->writeHTML($content, isset($_GET['vuehtml']));  
 $pdf->Output($lastname.'_'.$firstname.'_Num_'.$conge.'.pdf') ;  
 ?>
