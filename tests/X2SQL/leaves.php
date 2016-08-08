<?php
ini_set("display_errors",1);
require_once 'excel_reader2.php';
require_once 'db.php';
$data = new Spreadsheet_Excel_Reader("leaves.xls");
function data_base_connect ()
   {
     $dbh = new PDO("mysql:host=localhost;dbname=lms;charset=utf8", "root", "");

     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

     return ($dbh);
   }
            $db = data_base_connect ();
//some initializations
$html="<table border='1'>";
$i=0;
$status = 3;//Accepted
$cause='NULL';
$starType='Morning';
$endType='Afternoon';
$typeConge=1;//n'importe

  if (isset($data->sheets[$i]['cells']))
  {
    if(count($data->sheets[$i]['cells'])>0) // checking sheet not empty
    {
    
      for($j=2;$j<=count($data->sheets[$i]['cells']);$j++) // loop used to get each row of the sheet
      {
        $html.="<tr>";
        for($k=1;$k<=count($data->sheets[$i]['cells'][$j]);$k++) // This loop is created to get data in a table format.
        {
          $html.="<td>";
          $html.=$data->sheets[$i]['cells'][$j][$k];
          $html.="</td>";
        }
        $id = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][1]);
        $dateDebutCong = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][2]);
        $dateFinCong = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][3]);
        $employee =  mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][5]);
        $duration = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][9]);
                $query = "insert into leaves(id,startdate,enddate,status,employee,cause,startdatetype,enddatetype,duration,type)
                values('".$id."','".$dateDebutCong."','".$dateFinCong."','".$status."','".$employee."','".$cause."','".$starType."','".$endType."','".$duration."','".$typeConge."')";
                $st = $db->prepare($query);
                $st->execute();
        $html.="</tr>";
      }
    }
  }

$html.="</table>";
echo "\t\t\tTable des conges:\n\n";
echo $html;
echo utf8_decode("<br />Les données sont insérées dans la BDD.");
?>
