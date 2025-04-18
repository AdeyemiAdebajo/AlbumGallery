<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Result</title>
</head>
<body>

<?php include 'welcome.php'; ?>



<?php

$band = "";
$title = "";
$year = "";
$barcodeInput = $_POST['barcode'] ?? "";
$name_quarry = $_POST['name_quarry'] ?? "";
$album_quarry = $_POST['album_quarry'] ?? "";
$year_quarry = $_POST['year_quarry'] ?? "";



    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $searchQuery = '';
        $apiKey = "fFcWCXQkYQAlkddXjWzIZXrFSTOFytYtovcNVlSE";
    
        if (isset($_POST['scan_barcode']) && !empty($barcodeInput)) {
            $searchQuery = urlencode(trim($barcodeInput));
        } elseif (isset($_POST['manual_search']) && !empty($name_quarry )&& !empty($album_quarry)&& !empty($year_quarry)) {
            $searchQueryRaw = trim($name_quarry) . " " . trim($album_quarry) . " " . trim($year_quarry);
            $searchQuery = urlencode($searchQueryRaw);
            
        }
    
        if (!empty($searchQuery)) {
            $searchUrl = "https://api.discogs.com/database/search?q=$searchQuery&token=$apiKey";
    
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $searchUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["User-Agent: MyDiscogsApp/1.0"]);
            $response = curl_exec($curl);
            curl_close($curl);
    
            $data = json_decode($response, true);
    
            if (!empty($data['results'])) {
                $releaseId = $data['results'][0]['id'];
    
                $releaseUrl = "https://api.discogs.com/releases/$releaseId";
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $releaseUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    "Authorization: Discogs token=$apiKey",
                    "User-Agent: MyDiscogsApp/1.0"
                ]);
                $releaseResponse = curl_exec($curl);
                curl_close($curl);
                $release = json_decode($releaseResponse, true);
    
                $band = $release['artists'][0]['name'] ?? '';
                $title = $release['title'] ?? '';
                $year = $release['year'] ?? '';
                $image = $release['images'][0]['resource_url'] ?? '';
    
                echo "<h3>Album Found</h3>";
                echo "<p><strong>Band:</strong> " . htmlspecialchars($band) . "</p>";
                echo "<p><strong>Album:</strong> " . htmlspecialchars($title) . "</p>";
                echo "<p><strong>Year:</strong> " . htmlspecialchars($year) . "</p>";
                if ($image) {
                    echo "<img src='" . htmlspecialchars($image) . "' alt='Album Cover' style='max-width:200px;'><br>";
                }
    
                echo "
                <form action='add.php' method='POST'>
                    <input type='hidden' name='barcode' value='" . htmlspecialchars($barcodeInput) . "'>
                    <input type='hidden' name='band_name' value='" . htmlspecialchars($band) . "'>
                    <input type='hidden' name='album_name' value='" . htmlspecialchars($title) . "'>
                    <input type='hidden' name='album_year' value='" . htmlspecialchars($year) . "'>
                    <input type='hidden' name='artwork_url' value='" . htmlspecialchars($image) . "'>
                    <input type='submit' value='Add to Album List'>
                </form>";
            } else {
                echo "<h3>No album found for that search.</h3>";
                echo "
                <form action='add.php' method='POST'>
                    <input type='hidden' name='barcode' value='" . htmlspecialchars($barcodeInput) . "'>
                    Band Name: <input type='text' name='band_name' required><br><br>
                    Album Name: <input type='text' name='album_name' required><br><br>
                    Album Year: <input type='number' name='album_year'><br><br>
                    <input type='submit' value='Add Album Manually'>
                </form>";
            }
        }
    }
?>

</body>
</html>
