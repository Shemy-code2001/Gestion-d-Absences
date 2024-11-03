<?php
session_start();
if (!isset($_SESSION) || empty($_SESSION)) {
    header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");

if(isset($_GET['idex'])) {
    $idStg = $_GET['idex'];
    
    try {
        $req = $conn->prepare("SELECT s.idStg, s.nom, s.prenom, a.cumul_absences, a.statut, s.groupe 
                              FROM stagiaire s 
                              JOIN absence a ON s.idStg = a.idStg 
                              WHERE s.idStg = :idStg");
        $req->bindParam(':idStg', $idStg, PDO::PARAM_INT);
        $req->execute();
        $stagiaire = $req->fetch(PDO::FETCH_ASSOC);
        // Traiter le formulaire si soumis
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nouveau_statut = $_POST['statut'];
            $update_req = $conn->prepare("UPDATE absence SET statut = :statut WHERE idStg = :idStg");
            $update_req->bindParam(':statut', $nouveau_statut, PDO::PARAM_STR);
            $update_req->bindParam(':idStg', $idStg, PDO::PARAM_INT);
            $update_req->execute();

            header("Location: retards.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Aucun ID d'élève n'a été fourni.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la présence</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
          --primary-color: #3498db;
          --secondary-color: #2ecc71;
          --background-color: #f4f7f9;
          --card-bg: #ffffff;
          --text-color: #2c3e50;
          --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
          font-family: 'Poppins', sans-serif;
          background-color: var(--background-color);
          color: var(--text-color);
          margin: 0;
          padding: 0;
          transition: all 0.3s ease;
        }

        .container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 2rem;
        }

        h1 {
          font-size: 3rem;
          font-weight: 700;
          text-align: center;
          margin-bottom: 2rem;
          color: var(--primary-color);
          text-transform: uppercase;
          letter-spacing: 2px;
          animation: fadeInDown 1s ease-out;
        }

        .card {
          max-width: 400px;
          margin: 0 auto;
          background: var(--card-bg);
          border-radius: 10px;
          padding: 20px;
          box-shadow: 0 10px 20px var(--shadow-color);
          transition: all 0.3s ease;
          transform: perspective(1000px) rotateY(0deg);
        }

        .card:hover {
          transform: perspective(1000px) rotateY(5deg) translateY(-10px);
          box-shadow: 0 15px 30px var(--shadow-color);
        }

        .card img {
          width: 100%;
          border-radius: 10px;
          margin-bottom: 10px;
          transition: transform 0.5s ease;
        }

        .card:hover img {
          transform: scale(1.1);
        }

        .card h3 {
          font-size: 1.5em;
          margin-bottom: 10px;
          text-align: center;
          color: var(--primary-color);
        }

        .card p {
          margin-bottom: 10px;
        }

        form {
          margin-top: 20px;
        }

        form label {
          display: block;
          font-weight: bold;
          margin-bottom: 5px;
        }

        form select {
          width: 100%;
          padding: 8px;
          font-size: 1em;
          border: 1px solid #ddd;
          border-radius: 4px;
          margin-bottom: 10px;
        }

        form button {
          background-color: var(--primary-color);
          color: #fff;
          border: none;
          padding: 10px 20px;
          cursor: pointer;
          border-radius: 4px;
          font-size: 1em;
          transition: all 0.3s ease;
        }

        form button:hover {
          transform: translateY(-3px);
          box-shadow: 0 4px 6px var(--shadow-color);
        }

        a {
          display: block;
          margin-top: 20px;
          text-align: center;
          color: var(--primary-color);
          text-decoration: none;
          transition: all 0.3s ease;
        }

        a:hover {
          text-decoration: underline;
          color: var(--secondary-color);
        }

        @keyframes fadeInDown {
          from {
            opacity: 0;
            transform: translateY(-20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier la présence</h1>
        <?php if(isset($stagiaire)): ?>
        <div class="card">
            <img src="stg.jpg" alt="Image stagiaire">
            <h3><?php echo $stagiaire['nom'] . ' ' . $stagiaire['prenom']; ?></h3>
            <p>Groupe: <?php echo $stagiaire['groupe']; ?></p>
            <p>Cumul Absences: <?php echo $stagiaire['cumul_absences']; ?></p>
            <p>Statut actuel: <?php echo $stagiaire['statut']; ?></p>
            
            <form method="POST">
                <label for="statut">Nouveau statut:</label>
                <select name="statut" id="statut">
                    <option value="present" <?php echo ($stagiaire['statut'] == 'present') ? 'selected' : ''; ?>>Présent</option>
                    <option value="absent" <?php echo ($stagiaire['statut'] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                </select>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>
        <?php endif; ?>

        <a href="absences.php">Retour à la liste des absences</a>
    </div>
</body>
</html>
