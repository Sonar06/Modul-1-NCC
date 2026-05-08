<?php
require_once __DIR__ . "/../config/db.php";

$apiKey = "0ee8b934d92f2fe16433eed1a37b1186";

$categories = [
    'general',
    'business', 
    'technology', 
    'sports', 
    'world'
];

foreach ($categories as $cat){
    $url = "https://gnews.io/api/v4/top-headlines?category=$cat&country=id&lang=id&max=10&expand=content&apikey=$apiKey";
    $response = file_get_contents($url);

    if ($response === FALSE) {
        echo ("Error: Gagal mengambil data dari GNews API untuk kategori $cat.");
        continue;    
    }

    $data = json_decode($response, true);

    if (!isset($data["articles"])) {
        echo ("Error: API tidak mengembalikan data artikel.");
        continue;
    }

    foreach ($data["articles"] as $a) {
        $title = $conn->real_escape_string($a["title"]);
        $desc = $conn->real_escape_string($a["description"]);
        $content = $conn->real_escape_string($a["content"]);
        $image = $a["image"];
        $url = $a["url"];
        $source = $a["source"]["name"];
        $published = date("Y-m-d H:i:s", strtotime($a["publishedAt"]));
        $category_val = $conn->real_escape_string($cat);
        
        $sql = "
        INSERT INTO articles (title, description, content, image, url, source_name, published_at, category)
        VALUES ('$title', '$desc', '$content', '$image', '$url', '$source', '$published', '$category_val')
        ON DUPLICATE KEY UPDATE
            description = '$desc',
            content = '$content',
            image = '$image',
            url = '$url',
            source_name = '$source',
            published_at = '$published',
            category = '$category_val'
        ";

        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }

    echo "Selesai memproses kategori: <b>$cat</b> (10 berita).<br>";
    
    sleep(1);
}

echo "Sync berita selesai.";