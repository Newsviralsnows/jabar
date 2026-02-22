<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['zip_file'])) {
    $zip = new ZipArchive;
    $file = $_FILES['zip_file']['tmp_name'];
    $originalName = pathinfo($_FILES['zip_file']['name'], PATHINFO_FILENAME); 
    $extractTo = $originalName; // Nama folder = nama file ZIP tanpa ekstensi

    if ($zip->open($file) === TRUE) {
        // bikin folder kalau belum ada
        if (!is_dir($extractTo)) {
            mkdir($extractTo, 0777, true);
        }
        $zip->extractTo($extractTo);
        $zip->close();
        echo "<p><strong>Berhasil diekstrak ke folder: $extractTo</strong></p>";

        // tampilkan isi folder
        $files = scandir($extractTo);
        echo "<ul>";
        foreach ($files as $f) {
            if ($f != '.' && $f != '..') {
                echo "<li>$f</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "Gagal membuka file ZIP.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <h3>Upload Gambar (Hanya .JPG):</h3>
    <input type="file" name="zip_file" accept=".zip" required>
    <br><br>
    <button type="submit">Upload</button>
</form>