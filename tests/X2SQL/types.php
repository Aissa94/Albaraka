<?php
ini_set("display_errors",1);
require_once 'excel_reader2.php';
require_once 'db.php';
$data = new Spreadsheet_Excel_Reader("types.xls");
function data_base_connect ()
   {
     $dbh = new PDO("mysql:host=localhost;dbname=lms;charset=utf8", "root", "");

     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

     return ($dbh);
   }
            $db = data_base_connect ();

$html="<table border='1'>";
$i=0;
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
        $id =  mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][1]);
        $name = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][2]);
        //$id = $db->lastInsertId('Code');
                $query = "insert into types(id,name)
                values('".$id."','".$name."')";
                $st = $db->prepare($query);
                $st->execute();
        $html.="</tr>";
      }
    }
  }

$html.="</table>";
echo "\t\t\tTypes des conges:\n\n";
echo $html;
echo utf8_decode("<br />Les données sont inserees dans la BDD.");
?>
