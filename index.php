<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Result</title>
</head>
<body>
<?php


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['scan_barcode'])) {
    $barcode = urlencode(trim($_POST['barcode']));
    $apiKey = "fFcWCXQkYQAlkddXjWzIZXrFSTOFytYtovcNVlSE";

    // First API Call: Search by Barcode
    $searchUrl = "https://api.discogs.com/database/search?q=$barcode&token=$apiKey";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $searchUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "User-Agent: MyDiscogsApp/1.0"
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    

    

    if (!empty($data['results'])) {
        $releaseId = $data['results'][0]['id'];

        // Second API Call: Get Release Details
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

        echo "<h2>Album Details</h2>";
        echo "<p><strong>Band Name:</strong> " . htmlspecialchars($release['artists'][0]['name'] ?? 'Unknown') . "</p>";
        echo "<p><strong>Album Name:</strong> " . htmlspecialchars($release['title'] ?? 'Unknown') . "</p>";
        echo "<p><strong>Album Year:</strong> " . htmlspecialchars($release['year'] ?? 'Unknown') . "</p>";

        if (!empty($release['images'][0]['resource_url'])) {
            echo "<img src='" . htmlspecialchars($release['images'][0]['resource_url']) . "' alt='Album Cover' style='max-width:200px;'>";
        }

    } else {
        echo "<p>No results found for barcode: " . htmlspecialchars($_POST['barcode']) . "</p>";
    }

    echo '<br><a href="firstpage.php">Search Another album</a>';
    
}
?>

    
</body>
</html>