<?php
session_start();
if (!isset($_SESSION) || empty($_SESSION)) {
    header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");
if (isset($_GET["idex"])) {
    $idex = $_GET["idex"];
    try {
        $req = $conn->prepare("DELETE FROM absence WHERE id = ?");
        $result = $req->execute([$idex]);
        if($result) {
            header("Location: retards.php?msgSupp=Le stagiaire en retard a bien été supprimé");
        } else {
            header("Location: retards.php?msgSupp=Échec de la suppression");
        }
        exit;
    } catch (PDOException $e) {
        echo "Erreur de suppression: " . $e->getMessage();
    }
}
?>
