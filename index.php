<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$searchInput = htmlspecialchars($_POST['searchInput']);


$apiKey="UeXaLeDjAJXFnsKZeWvcczcWUNByxXKbyVUjVAzD";

$curl= curl_init();
curl_setopt($curl, CURLOPT_URL,"https://api.discogs.com/database/search?q=" . urlencode($searchInput ) );
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // Set as POST request
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Authorization: Discogs token=" . $apiKey,
    "User-Agent: MyDiscogsApp/1.0",
    "Content-Type: application/json", // Required for POST
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);
}
?>
