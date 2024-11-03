<?php
include("../dbconnect.php");
if(isset($_GET['id']) && isset($_GET['date'])){
    extract($_GET);
    try {
        $req1 = $conn->prepare("DELETE  FROM seance WHERE idForm = ? AND dateSeance = ?");
        $req1->execute([$id,$date]);
        header('location:demplois.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>