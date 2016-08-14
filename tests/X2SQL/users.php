<?php
ini_set("display_errors",1);
require_once 'excel_reader2.php';
require_once 'db.php';
$data = new Spreadsheet_Excel_Reader("users.xls");
function data_base_connect ()
   {
     $dbh = new PDO("mysql:host=localhost;dbname=lms;charset=utf8", "root", "");

     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

     return ($dbh);
   }
            $db = data_base_connect ();
//some initializations
//$html="<table border='1'>";
$i=0;
$id=1;
$password='$2a$08$KxK2isBmJtpWW4vEGLJU3.S0U6x80ptjI.ce6ISDqx5bplTZprWUS';
$email='dnghouila@gmail.dz';
$role=2;
$manager=0;
$country=213;
$contract=1;
$language='fr';
$active=1;
$timezone='Africa/Algiers';
  if (isset($data->sheets[$i]['cells']))
  {
    if(count($data->sheets[$i]['cells'])>0) // checking sheet not empty
    {
      for($j=2;$j<=1663;$j++) // loop used to get each row of the sheet
      {
        /*$html.="<tr>";
        for($k=1;$k<=count($data->sheets[$i]['cells'][$j]);$k++) // This loop is created to get data in a table format.
        {
          $html.="<td>";
          $html.=$data->sheets[$i]['cells'][$j][$k];
          $html.="</td>";
        }*/
        $identifier = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][1]);
        $firstname = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][2]);
        $lastname = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][3]);
        $organization = mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][4]);
        $position =  mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][5]);
        $datehired =  mysqli_real_escape_string($connection,$data->sheets[$i]['cells'][$j][6]);      
                $query = "insert into users(id,firstname,lastname,login,email,password,role,manager,country,organization,contract,position,datehired,identifier,language,active,timezone)
                values('".$id."','".$firstname."','".$lastname."','".$firstname."','".$email."','".$password."','".$role."','".$manager."','".$country."','".$organization."','".$contract."','".$position."','".$datehired."','".$identifier."','".$language."','".$active."','".$timezone."')";
                $st = $db->prepare($query);
                $st->execute();
        //$html.="</tr>";
        $id++;
      }
    }
  }

/*$html.="</table>";
echo "\t\t\tTable des utilisateurs:\n\n";
echo $html;*/
echo utf8_decode("<br />Les données sont insérées dans la BDD.");
?>
