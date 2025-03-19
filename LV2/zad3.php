<?php
$xmlFile = 'LV2.xml';

if (file_exists($xmlFile)) {
    $xml = simplexml_load_file($xmlFile, null, LIBXML_NOERROR | LIBXML_NOWARNING);
    
    if ($xml === false) {
        echo "Greška pri učitavanju XML datoteke!<br>";
        foreach (libxml_get_errors() as $error) {
            echo "Linija {$error->line}: {$error->message}<br>";
        }
        exit;
    }
    
    echo "<h1>Popis osoba</h1>";
    foreach ($xml->record as $person) {
        $id = (string)$person->id;
        $ime = (string)$person->ime;
        $prezime = (string)$person->prezime;
        $email = (string)$person->email;
        $spol = (string)$person->spol;
        $slika = (string)$person->slika;
        $zivotopis = (string)$person->zivotopis;
        
        echo "<div class='profile'>";
        echo "<img src='$slika' alt='Slika $ime $prezime' width='100'>";
        echo "<h2>$ime $prezime</h2>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Spol:</strong> $spol</p>";
        echo "<p><strong>Životopis:</strong> $zivotopis</p>";
        echo "</div><hr>";
    }
} else {
    echo "XML datoteka '$xmlFile' ne postoji!";
}
?>

<style>
.profile {
    margin: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
}
h1 {
    text-align: center;
}
h2 {
    color: #333;
}
img {
    border-radius: 50%;
    margin-bottom: 10px;
}
</style>