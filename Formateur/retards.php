<?php
session_start();
if (!isset($_SESSION) || empty($_SESSION)) {
    header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");

// Gestion de la suppression
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

// Gestion du retour
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['retour'])){
        header("Location: formateur.php");
        exit();
    }
}

// Récupération des retards
try {
    $groupe = isset($_GET['groupe']) ? $_GET['groupe'] : 'all';
    $sql = "SELECT s.idStg, a.id, s.nom AS Nom, s.prenom AS Prenom, a.cumul_absences AS CumulAbsences, s.groupe AS Groupe
            FROM absence a
            JOIN stagiaire s ON a.idStg = s.idStg
            WHERE a.statut = 'retard' AND s.groupe IN ('DEV101', 'DEV102', 'DEV103')";
    
    if ($groupe != 'all') {
        $sql .= " AND s.groupe = :groupe";
    }

    $req = $conn->prepare($sql);
    
    if ($groupe != 'all') {
        $req->bindParam(':groupe', $groupe);
    }
    
    $req->execute();
    $retards = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur d'extraction des données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des retards</title>
    <link rel="stylesheet" href="abs.css">
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

        .filtrage {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 20px var(--shadow-color);
        margin-bottom: 2rem;
        transform: perspective(1000px) rotateX(0deg);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
        }

        .filtrage:hover {
        transform: perspective(1000px) rotateX(5deg);
        box-shadow: 0 15px 30px var(--shadow-color);
        }

        #groupeForm {
        display: flex;
        justify-content: center;
        gap: 1rem;
        }

        .filtrage button {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        background-image: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        box-shadow: 0 4px 6px var(--shadow-color);
        }

        .filtrage button:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 8px var(--shadow-color);
        }

        .stagiaires {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        padding: 1rem;
        }

        .card {
        background-color: var(--card-bg);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px var(--shadow-color);
        transform: perspective(1000px) rotateY(0deg);
        }

        .card:hover {
        transform: perspective(1000px) rotateY(5deg) translateY(-10px);
        box-shadow: 0 15px 30px var(--shadow-color);
        }

        .card img {
        width: 70%;
        height: 200px;
        margin-left: 15%;
        object-fit: cover;
        transition: transform 0.5s ease;
        }

        .card:hover img {
        transform: scale(1.1);
        }

        .card-content {
        padding: 1.5rem;
        }

        .card h3 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
        }

        .card p {
        font-size: 1rem;
        color: var(--text-color);
        margin-bottom: 0.5rem;
        }

        .btn-supprimer {
        background-color: #e74c3c;
        color: #ffffff;
        border: none;
        padding: 0.5rem;
        border-radius: 50%;
        cursor: pointer;
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        transition: all 0.3s ease;
        opacity: 0;
        }

        .card:hover .btn-supprimer {
        opacity: 1;
        }

        .btn-supprimer:hover {
        transform: rotate(90deg);
        background-color: #c0392b;
        }

        .btn-modifier {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-image: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: white;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        }

        .btn-modifier:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px var(--shadow-color);
        }

        .btn-modifier>a {
            text-decoration: none;
            color: white;
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

        @media (max-width: 768px) {
        .filtrage {
            padding: 1rem;
        }

        #groupeForm {
            flex-direction: column;
        }

        .stagiaires {
            grid-template-columns: 1fr;
        }
        }
        :root {
        --primary-color: #4a90e2;
        --secondary-color: #50c878;
        --background-color: #f4f7f9;
        --form-bg: #ffffff;
        --text-color: #333333;
        --input-bg: #f0f4f8;
        --shadow-color: rgba(0, 0, 0, 0.1);
        }

        #notif {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px var(--shadow-color);
        position: relative;
        overflow: hidden;
        }

        #notif:before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(45deg);
        transition: all 0.3s ease;
        }

        #notif:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px var(--shadow-color);
        }

        #notif:hover:before {
        left: 100%;
        }

        #notificationForm {
        background-color: var(--form-bg);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px var(--shadow-color);
        transform: perspective(1000px) rotateX(0deg);
        transition: all 0.5s ease;
        opacity: 0;
        max-width: 500px;
        margin: 30px auto;
        }

        #notificationForm.visible {
        opacity: 1;
        transform: perspective(1000px) rotateX(0deg);
        }

        #notificationForm legend {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        text-align: center;
        }

        #notificationForm label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        color: var(--text-color);
        }

        #notificationForm input[type="text"],
        #notificationForm input[type="date"],
        #notificationForm textarea {
        width: 95%;
        padding: 12px 15px;
        border: 2px solid var(--input-bg);
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: var(--input-bg);
        }

        #notificationForm input[type="text"]:focus,
        #notificationForm input[type="date"]:focus,
        #notificationForm textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        outline: none;
        }

        #env {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        padding: 12px 25px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px var(--shadow-color);
        display: block;
        width: 100%;
        margin-top: 20px;
        }

        #env:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px var(--shadow-color);
        }

        @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
        animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="filtrage">
        <h6>Choisir votre groupe :</h6>
        <form method="POST">
            <button class="retour" type="submit" name="retour"><i class="bi bi-box-arrow-in-left"></i></button>
        </form>
        <form id="groupeForm">
            <button id="all" class="btn btn-primary">Tous</button>
            <button id="DEV101" class="btn btn-primary dev101">Dev101</button>
            <button id="DEV102" class="btn btn-primary dev102">Dev102</button>
            <button id="DEV103" class="btn btn-primary dev103">Dev103</button>
        </form>
        <?php
        if(isset($_GET['msgSupp'])) {
            echo "<p>" . htmlspecialchars($_GET['msgSupp']) . "</p>";
        }
        ?>
    </div>
    <h1>Liste des retards</h1>
    <div class="stagiaires" id="stagiaires">
        <?php
        if (!empty($retards)) {
            foreach ($retards as $retard) {
                $nomPrenom = $retard['Nom'] . ' ' . $retard['Prenom'];
                $cumulAbsences = $retard['CumulAbsences'];
                $groupe = $retard['Groupe'];
        ?>
                <div class="card">
                    <button type="submit" class="btn-supprimer">
                        <a href="supprimer.php?idex=<?php echo $retard['idStg']; ?>"><i class="bi bi-x-circle-fill"></i></a>
                    </button>
                    <img src="stg.jpg" alt="Image stagiaire">
                    <h3><?php echo $nomPrenom; ?></h3>
                    <p>Statut: en retard</p>
                    <p>Cumul Absences: <?php echo $cumulAbsences; ?></p>
                    <button type="submit" class="btn-modifier">
                        <a href="modifier_présence.php?idex=<?php echo $retard['idStg']; ?>">Modifier</a>
                    </button>  
                </div>
        <?php
            }
        } else {
            echo '<p>Aucun stagiaire en retard trouvé.</p>';
        }
        
        ?>
    </div>
    <button type="button" id="notif">Notification</button>

    <fieldset id="notificationForm" class="hidden">
        <legend>Formulaire De Notification</legend>
        <form id="notificationFormInner">
            <label for="nomStagiaire">Nom du Stagiaire :</label>
            <input type="text" id="nomStagiaire" name="nomStagiaire" required><br><br>
            
            <label for="dateAbsence">Date de l'Absence :</label>
            <input type="time" id="dateAbsence" name="dateAbsence" required><br><br>
            
            <label for="nomFormateur">Nom du Formateur :</label>
            <input type="text" id="nomFormateur" name="nomFormateur" required><br><br>
            
            <label for="cmnt">Commentaires :</label>
            <textarea id="cmnt" name="cmnt" rows="4" placeholder="Entrez vos commentaires ici"></textarea><br><br>
            
            <button type="button" id="env">Envoyer la Notification</button>
        </form>
    </fieldset>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn-primary');
            buttons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();  
                    const groupe = this.id;
                    window.location.href = `retards.php?groupe=${groupe}`;
                });
            });
        });

        //afficher la formulaire notification :
            document.addEventListener('DOMContentLoaded', function() {
                const notifButton = document.getElementById('notif');
                const notificationForm = document.getElementById('notificationForm');
                const envButton = document.getElementById('env');

                notifButton.addEventListener('click', function() {
                    if (notificationForm.classList.contains('visible')) {
                        notificationForm.classList.remove('visible');
                        setTimeout(() => {
                            notificationForm.style.display = 'none';
                        }, 500);
                    } else {
                        notificationForm.style.display = 'block';
                        setTimeout(() => {
                            notificationForm.classList.add('visible');
                        }, 50);
                    }
                });

                envButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const inputs = notificationForm.querySelectorAll('input, textarea');
                    let isValid = true;
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            isValid = false;
                            input.classList.add('shake');
                            setTimeout(() => input.classList.remove('shake'), 500);
                        }
                    });

                    if (isValid) {
                        // Envoyer le formulaire
                        notificationForm.classList.remove('visible');
                        setTimeout(() => {
                            notificationForm.style.display = 'none';
                            // Réinitialiser le formulaire ici
                        }, 500);
                    }
                });
            });
        // Envoi de la notification via AJAX
        $('#env').click(function() {
            var nomStagiaire = $('#nomStagiaire').val();
            var dateAbsence = $('#dateAbsence').val();
            var nomFormateur = $('#nomFormateur').val();
            var cmnt = $('#cmnt').val();

            if (!nomStagiaire || !dateAbsence || !nomFormateur || !cmnt) {
                alert("Veuillez remplir tous les champs du formulaire.");
                return;
            }

            $.ajax({
                url: '../PROJECT/Gestionnaire/gestionnaire.php',
                type: 'POST',
                data: {
                    action: 'saveNotification',
                    nomStagiaire: nomStagiaire,
                    dateAbsence: dateAbsence,
                    nomFormateur: nomFormateur,
                    cmnt: cmnt
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Notification envoyée avec succès.');
                        $('#nomStagiaire').val('');
                        $('#dateAbsence').val('');
                        $('#nomFormateur').val('');
                        $('#cmnt').val('');
                        $('#notificationForm').hide();
                    } else {
                        alert('Erreur lors de l\'envoi de la notification. Veuillez réessayer.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de l\'envoi de la notification:', error);
                    alert('Une erreur est survenue lors de l\'envoi de la notification. Veuillez réessayer.');
                }
            });
        });
    </script>
</body>
</html>
