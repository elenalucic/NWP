<?php
$key = '12345678';
$files = glob('uploads/*.enc');

foreach ($files as $file) {
    $data = file_get_contents($file);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    
    $decryptedFile = 'decrypted/' . basename($file, '.enc');
    file_put_contents($decryptedFile, $decrypted);
    
    echo "<a href='$decryptedFile'>Preuzmi " . basename($decryptedFile) . "</a><br>";
}
?>