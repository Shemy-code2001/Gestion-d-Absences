<?php
try{
    $conn = new PDO("mysql:host=localhost;dbname=dbAbsences","root","");
}catch(PDOException $e){
    echo "Erreur de la connexion a la BD : ".$e->getMessage();
}
?>