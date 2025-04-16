<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="index.php"method="POST">
    <div class="form-group">
        <label for="barcode">Barcode (UPC)</label>
        <input type="text" id="barcode" name="barcode" required placeholder="Scan or enter barcode">
    </div>
    <button type="submit" name="scan_barcode">Search and Add Album</button>
</form>
</body>
</html>