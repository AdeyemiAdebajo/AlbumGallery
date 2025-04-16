<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'username';
$db_pass = 'password';
$db_name = 'album_database';

// Discogs API configuration
$api_key = "UeXaLeDjAJXFnsKZeWvcczcWUNByxXKbyVUjVAzD";

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create albums table if not exists
$sql = "CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(50),
    band_name VARCHAR(255) NOT NULL,
    album_name VARCHAR(255) NOT NULL,
    album_year INT,
    artwork_url VARCHAR(512),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['scan_barcode'])) {
        // Barcode scan submitted
        $barcode = $_POST['barcode'];
        
        // Search Discogs by barcode
        $url = "https://api.discogs.com/database/search?barcode=" . urlencode($barcode);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Discogs token=" . $api_key,
            "User-Agent: MyDiscogsApp/1.0"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (!empty($data['results'])) {
            $releaseId = $data['results'][0]['id'];
            
            // Get release details
            $url = "https://api.discogs.com/releases/" . $releaseId;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Discogs token=" . $api_key,
                "User-Agent: MyDiscogsApp/1.0"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $release = json_decode($response, true);
            
            // Prepare data for database
            $bandName = $conn->real_escape_string($release['artists'][0]['name']);
            $albumName = $conn->real_escape_string($release['title']);
            $albumYear = $release['year'];
            $artwork = $release['images'][0]['uri'] ?? '';
            
            // Insert into database
            $sql = "INSERT INTO albums (barcode, band_name, album_name, album_year, artwork_url)
                    VALUES ('$barcode', '$bandName', '$albumName', '$albumYear', '$artwork')";
            $conn->query($sql);
            
            $success = "Album added successfully!";
        } else {
            $error = "No album found with that barcode.";
        }
    } elseif (isset($_POST['manual_entry'])) {
        // Manual form submitted
        $bandName = $conn->real_escape_string($_POST['band_name']);
        $albumName = $conn->real_escape_string($_POST['album_name']);
        $albumYear = $_POST['album_year'];
        $artwork = $conn->real_escape_string($_POST['artwork_url']);
        
        if (!empty($bandName) && !empty($albumName)) {
            $sql = "INSERT INTO albums (band_name, album_name, album_year, artwork_url)
                    VALUES ('$bandName', '$albumName', '$albumYear', '$artwork')";
            $conn->query($sql);
            $success = "Album added successfully!";
        } else {
            $error = "Band name and album name are required.";
        }
    } elseif (isset($_POST['delete'])) {
        // Delete album
        $id = $_POST['id'];
        $sql = "DELETE FROM albums WHERE id = $id";
        $conn->query($sql);
        $success = "Album deleted successfully!";
    }
}

// Fetch all albums from database
$albums = $conn->query("SELECT * FROM albums ORDER BY band_name, album_year");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Database Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        .album-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
        .album-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .album-art { max-width: 100%; height: auto; }
        .error { color: red; }
        .success { color: green; }
        .tab { overflow: hidden; border: 1px solid #ccc; background-color: #f1f1f1; }
        .tab button { background-color: inherit; float: left; border: none; outline: none; cursor: pointer; padding: 14px 16px; transition: 0.3s; }
        .tab button:hover { background-color: #ddd; }
        .tab button.active { background-color: #ccc; }
        .tabcontent { display: none; padding: 6px 12px; border: 1px solid #ccc; border-top: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Album Database Manager</h1>
        
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>
        
        <div class="tab">
            <button class="tablinks active" onclick="openTab(event, 'scan')">Scan Barcode</button>
            <button class="tablinks" onclick="openTab(event, 'manual')">Manual Entry</button>
        </div>
        
        <div id="scan" class="tabcontent" style="display: block;">
            <h2>Scan Album Barcode</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="barcode">Barcode (UPC)</label>
                    <input type="text" id="barcode" name="barcode" required placeholder="Scan or enter barcode">
                </div>
                <button type="submit" name="scan_barcode">Search and Add Album</button>
            </form>
        </div>
        
        <div id="manual" class="tabcontent">
            <h2>Manual Album Entry</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="band_name">Band Name</label>
                    <input type="text" id="band_name" name="band_name" required>
                </div>
                <div class="form-group">
                    <label for="album_name">Album Name</label>
                    <input type="text" id="album_name" name="album_name" required>
                </div>
                <div class="form-group">
                    <label for="album_year">Year</label>
                    <input type="number" id="album_year" name="album_year">
                </div>
                <div class="form-group">
                    <label for="artwork_url">Artwork URL (optional)</label>
                    <input type="text" id="artwork_url" name="artwork_url">
                </div>
                <button type="submit" name="manual_entry">Add Album</button>
            </form>
        </div>
        
        <h2>Your Album Collection</h2>
        <div class="album-grid">
            <?php while ($album = $albums->fetch_assoc()): ?>
                <div class="album-card">
                    <?php if (!empty($album['artwork_url'])): ?>
                        <img src="<?= $album['artwork_url'] ?>" alt="<?= $album['album_name'] ?>" class="album-art">
                    <?php endif; ?>
                    <h3><?= $album['band_name'] ?></h3>
                    <p><?= $album['album_name'] ?> (<?= $album['album_year'] ?>)</p>
                    <?php if (!empty($album['barcode'])): ?>
                        <p>Barcode: <?= $album['barcode'] ?></p>
                    <?php endif; ?>
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="id" value="<?= $album['id'] ?>">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        
        // Focus barcode field automatically
        document.getElementById('barcode').focus();
    </script>
</body>
</html>

<?php $conn->close(); ?>