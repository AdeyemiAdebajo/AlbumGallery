<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM $table WHERE id = $id";
        if ($conn->query($sql)) {
            header("Location: remove.php");
            exit;
        }else{
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
     }//else{
    //     echo "No ID provided for deletion.";
    // }
    $sql = "SELECT * FROM $table ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p><strong>{$row['album_name']}</strong> by {$row['band_name']} ({$row['album_year']})</p>
            <img src='" . htmlspecialchars($row['artwork_url']) . "' alt='Cover' width='100'><br>
            <form method='post' action='remove.php'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <button type='submit'>Delete</button>
            </form>";
        }
    } else {
        echo "No album found with the given ID.";
    }
    $conn->close();

    ?>

</body>
</html>