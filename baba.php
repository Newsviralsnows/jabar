<?php

$root = __DIR__;

function fixPermissions($dir) {
    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;

        $path = $dir . '/' . $item;

        if (is_dir($path)) {
            chmod($path, 0755); // folder
            fixPermissions($path);
        } else {
            chmod($path, 0644); // file
        }
    }
}

fixPermissions($root);

echo "Permissions updated.";

?>