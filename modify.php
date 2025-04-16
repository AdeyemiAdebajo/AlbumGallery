<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify page </title>
</head>

<body>
    <?php
     include 'welcome.php';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerylist";
    $table = "albums";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("connection failed" . $conn->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
        $id = $_POST['update_id'];
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $band_name = isset($_POST['band_name']) ? $_POST['band_name'] : '';
        $album_name = isset($_POST['album_name']) ? $_POST['album_name'] : '';
        $album_year = isset($_POST['album_year']) ? (int)$_POST['album_year'] : 0;
        $artwork_url = isset($_POST['artwork_url']) ? $_POST['artwork_url'] : '';
        $updated_at = date("Y-m-d H:i:s");
        $sql = "UPDATE $table SET barcode='$barcode', band_name='$band_name', album_name='$album_name', album_year='$album_year', artwork_url='$artwork_url', updated_at='$updated_at' WHERE id=$id";
        $conn->query($sql);
    }
    $editId = $_POST['edit_id'] ?? null;
    $sql = "SELECT * FROM $table ORDER BY created_at DESC";
    // echo "click <a href='remove.php'>here </a>to Delete an ablum";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($editId == $row['id']) {
                
                echo "
                
                <form method='post' action='modify.php'>
                    <input type='hidden' name='update_id' value='{$row['id']}'>
                    Barcode: <input type='text' name='barcode' value=\"{$row['barcode']}\"><br>
                    Band Name: <input type='text' name='band_name' value=\"{$row['band_name']}\"><br>
                    Album Name: <input type='text' name='album_name' value=\"{$row['album_name']}\"><br>
                    Album Year: <input type='number' name='album_year' value=\"{$row['album_year']}\"><br>
                    Artwork URL: <input type='text' name='artwork_url' value=\"{$row['artwork_url']}\"><br>
                    <button type='submit'>Update Album</button>
                </form>
                ";
            } else {

                echo "<p><strong>{$row['album_name']}</strong> by {$row['band_name']} ({$row['album_year']})</p>
                <img src='" . htmlspecialchars($row['artwork_url']) . "' alt='Cover' width='100'><br>
                <form method='post' action='modify.php'>
                    <input type='hidden' name='edit_id' value='{$row['id']}'>
                    <button type='submit'>Edit</button>
                   
                  
                </form>
            ";
            }
        }
    } else {
        echo "No album found with the given ID.";
    }
    $conn->close();
    ?>
</body>

</html>