<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h2>Search or Add an Album</h2>

<form method="POST" action="index.php">
    <label for="barcode">Enter Barcode (UPC):</label>
    <input type="text" name="barcode" placeholder="123456789012"  required />
    <button type="submit" name="scan_barcode">Search by Barcode</button>
</form>

<!-- Manual Search -->
<form method="POST" action="index.php">
    <h3>OR Search Manually:</h3>
    
    <input type='hidden' name='barcode' value='" . htmlspecialchars($barcodeInput) . "'>
            Band Name: <input type='text' name='name_quarry' required><br><br>
            Album Name: <input type='text' name='album_quarry' required><br><br>
            Album Year: <input type='number' name='year_quarry'><br><br>
           <input type='hidden' name='artwork_url'><br><br>
          
    <button type="submit" name="manual_search">Search Album</button>
</form>

</body>
</html>