<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    
    if (in_array($file['type'], $allowedTypes) && $file['error'] === UPLOAD_ERR_OK) {
        $key = '12345678';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        
        $inputFile = $file['tmp_name'];
        $outputFile = 'uploads/' . uniqid() . '.enc';
        
        $data = file_get_contents($inputFile);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        
        if ($encrypted !== false) {
            file_put_contents($outputFile, $iv . $encrypted);
            echo "Datoteka uspješno kriptirana!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload dokumenta</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
        <input type="submit" value="Učitaj">
    </form>
</body>
</html>