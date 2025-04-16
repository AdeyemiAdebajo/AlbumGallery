<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Album</title>
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grocerylist";
$table = "albums";
include("welcome.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = htmlspecialchars($_POST['barcode']);
    $band_name = htmlspecialchars($_POST['band_name']);
    $album_name = htmlspecialchars($_POST['album_name']);
    $album_year = htmlspecialchars($_POST['album_year']);
    $artwork_url = htmlspecialchars($_POST['artwork_url']);
    $created_at = date("Y-m-d H:i:s");
    $updated_at = $created_at;

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO albums (barcode, band_name, album_name, album_year, artwork_url, created_at, updated_at) 
            VALUES ('$barcode', '$band_name', '$album_name', '$album_year', '$artwork_url', '$created_at', '$updated_at')";

    if ($conn->query($sql) === TRUE) {
        echo "<h3>Album Added</h3>";
        echo "<p><strong>Band:</strong>".$band_name;
        echo "<p><strong>Album:</strong>".$album_name;
        echo "<p><strong>Year:</strong>".$album_year;
        
            
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

</body>
</html>
