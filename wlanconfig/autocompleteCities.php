<?php
$servername = "localhost";
$username = "root";
$password = "glancr";
$dbname = "glancr";

$conn = new mysqli($servername, $username, $password, $dbname);

$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");

$parts = explode(" ", str_replace($search, $replace, $_GET['term']));

$sql = 'SELECT id,name,country FROM owm_cities WHERE MATCH (name) AGAINST ("+' . implode(' +', $parts) . '*" in boolean mode) ORDER BY name';
//echo $sql;
$result = $conn->query($sql);

$a_json = array();
$a_json_row = array();


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	$a_json_row['id'] = $row['id'];
    	$a_json_row['value'] = $row['name'];
  		$a_json_row['label'] = $row['name'] . ' <img style="position: absolute; right: 20px;padding-top: 6px;" src="../config/img/00_cctld/' . strtolower($row['country']) . '.png" alt="' . $row['country'] . '">';
  		array_push($a_json, $a_json_row);
    //    echo $row["id"]. "\t" . $row["name"]. "\t" . $row["country"]. "\n";
    }
} 
$conn->close();
echo json_encode($a_json);
?>