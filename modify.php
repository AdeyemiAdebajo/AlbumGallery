<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify page </title>
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerylist";
    $table = "albums";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("connection failed" . $conn->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
    $band_name = isset($_POST['band_name']) ? $_POST['band_name'] : '';
    $album_name = isset($_POST['album_name']) ? $_POST['album_name'] : '';
    $album_year = isset($_POST['album_year']) ? (int)$_POST['album_year'] : 0;
    $artwork_url = isset($_POST['artwork_url']) ? $_POST['artwork_url'] : '';
    $updated_at = date("Y-m-d H:i:s");
    $sql="UPDATE $table SET barcode='$barcode', band_name='$band_name', album_name='$album_name', album_year='$album_year', artwork_url='$artwork_url', updated_at='$updated_at' WHERE id=$id";
    $conn->query($sql);}
    $editId = $_POST['edit_id'] ?? null;
    $sql = "SELECT * FROM $table ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($editId == $row['id']) {
                // ✅ Edit form for the selected album
                echo "<form method='post' action='modify.php'>";
                echo "<input type='hidden' name='update_id' value='" . $row['id'] . "'>";
                echo "Barcode: <input type='text' name='barcode' value='" . htmlspecialchars($row['barcode']) . "'><br>";
                echo "Band Name: <input type='text' name='band_name' value='" . htmlspecialchars($row['band_name']) . "'><br>";
                echo "Album Name: <input type='text' name='album_name' value='" . htmlspecialchars($row['album_name']) . "'><br>";
                echo "Album Year: <input type='number' name='album_year' value='" . htmlspecialchars($row['album_year']) . "'><br>";
                echo "Artwork URL: <input type='text' name='artwork_url' value='" . htmlspecialchars($row['artwork_url']) . "'><br>";
                echo "<button type='submit'>Update Album</button>";
                echo "</form>";
            } else {
                // ✅ Display read-only with edit button
                echo "<p><strong>{$row['album_name']}</strong> by {$row['band_name']} ({$row['album_year']})</p>";
                echo "<img src='" . htmlspecialchars($row['artwork_url']) . "' alt='Cover' width='100'><br>";
                echo "<form method='post' action='modify.php'>";
                echo "<input type='hidden' name='edit_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Edit</button>";
                echo "</form>";
        }
    }
    } else {
        echo "No album found with the given ID.";
    }
    ?>
</body>
</html>