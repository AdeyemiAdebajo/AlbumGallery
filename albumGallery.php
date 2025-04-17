    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Collection</title>
    <style>
        .albumGallery 
        {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .albumItem 
        {
            position: relative;
            cursor: pointer;
        }
        
        .albumCover 
        {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        
        .albumInfo 
        {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            display: none;
        }
        
        .albumItem:hover .albumInfo 
        {
            display: block;
        }
        
        .sortOptions 
        {
            padding: 20px;
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    
    <div class="sortOptions">
        <form method="post">
            Sort by: 
            <select name="sortField">
                <option value="band_name">Band Name</option>
                <option value="album_name">Album Name</option>
                <option value="album_year">Year</option>
            </select>
            
            Order: 
            <select name="sortOrder">
                <option value="ASC">A-Z (Ascending)</option>
                <option value="DESC">Z-A (Descending)</option>
            </select>
            
            <button type="submit" name="sortButton">Sort</button>
        </form>
    </div>

    <div class="albumGallery">
        <?php
        //So this is done by Udit and creating connection and options for delete, modify, and add has been done by Adeyemi
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "grocerylist";
        $table = "albums";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }
        
       
        $sortField = "band_name";
        $sortOrder = "ASC";
        
       
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sortButton'])) 
        {
            $sortField = $_POST['sortField'];
            $sortOrder = $_POST['sortOrder'];
        }
        
        
        $sql = "SELECT * FROM albums ORDER BY $sortField $sortOrder";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                echo "<div class='albumItem'>" . "\n";
                echo "<img src='" . htmlspecialchars($row['artwork_url']) . "' class='albumCover'>" . "\n";
                echo "<div class='albumInfo'>" . "\n";
                echo "<strong>" . htmlspecialchars($row['band_name']) . "</strong><br>" . "\n";
                echo " " . htmlspecialchars($row['album_name']) . "<br>" . "\n";
                echo " " . htmlspecialchars($row['album_year']) . "\n";
                echo "</div>" . "\n";
                echo "</div>" . "\n";
            }
        } 
        else 
        {
            echo "<p>No albums found in the collection.</p>";
        }
        
        $conn->close();
        ?>
    </div>
</body>
</html>