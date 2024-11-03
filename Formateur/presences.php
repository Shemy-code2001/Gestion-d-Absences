<?php
session_start();
if (!isset($_SESSION) || empty($_SESSION)) {
    header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");

$date_aujourdhui = date("Y-m-d");

try {
    $req = $conn->prepare("SELECT a.id, s.idStg, s.nom, s.prenom, s.groupe, a.statut, a.date_enregistrement 
                          FROM absence a 
                          JOIN stagiaire s ON a.idStg = s.idStg 
                          WHERE a.date_enregistrement = :date_aujourdhui AND a.statut = 'present'
                          ORDER BY s.groupe, s.nom, s.prenom");
    $req->execute(['date_aujourdhui' => $date_aujourdhui]);
    $presences = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur d'extraction des données: " . $e->getMessage();
}

// Traitement de la modification du statut
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier_statut'])) {
    $id = $_POST['id'];
    $nouveau_statut = $_POST['nouveau_statut'];
    
    try {
        $req = $conn->prepare("UPDATE absence SET statut = :nouveau_statut WHERE id = :id");
        $req->execute(['nouveau_statut' => $nouveau_statut, 'id' => $id]);
        header("Location: presences.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la modification du statut: " . $e->getMessage();
    }
}
if($_SERVER['REQUEST_METHOD']=='POST'){
    extract($_POST);
    if(isset($retour)){
        header('Location: formateur.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Présences</title>
    <link rel="stylesheet" href="abs.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50c878;
            --background-color: #f0f4f8;
            --text-color: #333;
            --card-bg: #ffffff;
            --hover-color: #e8f0fe;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: fadeInDown 1s ease-out;
        }

        .filtrage {
            background-color: var(--card-bg);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .filtrage:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .retour {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .retour:hover {
            background-color: darken(var(--primary-color), 10%);
            transform: scale(1.05);
        }

        .retour i {
            margin-right: 5px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 15px;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: none;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr {
            background-color: var(--card-bg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        tr:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        td {
            border-top: 1px solid #e0e0e0;
        }

        form {
            display: flex;
            align-items: center;
        }

        select, button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        select {
            margin-right: 10px;
            background-color: white;
        }

        button[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        button[type="submit"]:hover {
            background-color: darken(var(--secondary-color), 10%);
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Animations */
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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* 3D effect for cards */
        .card-3d {
            transition: transform 0.5s;
            transform-style: preserve-3d;
        }

        .card-3d:hover {
            transform: rotateY(5deg) rotateX(5deg);
        }

        /* Icon styles */
        .icon {
            font-size: 1.2rem;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            table, tr, td {
                display: block;
            }

            tr {
                margin-bottom: 1rem;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
            }
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            :root {
                --background-color: #1a1a1a;
                --text-color: #f0f0f0;
                --card-bg: #2c2c2c;
                --hover-color: #3a3a3a;
            }

            .filtrage, tr {
                background-color: var(--card-bg);
            }

            select {
                background-color: var(--card-bg);
                color: var(--text-color);
            }
        }
    
    </style>
</head>
<body>
<div class="container">
        <div class="filtrage card-3d">
            <form method="POST">
                <button class="retour" type="submit" name="retour">
                    <i class="bi bi-arrow-left icon"></i>
                </button>
            </form>
        </div>
        <h1><i class="bi bi-calendar-check icon"></i> Présences enregistrées le <?php echo $date_aujourdhui; ?></h1>
    <div class="stagiaires" id="stagiaires">
    <?php if (!empty($presences)): ?>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Groupe</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
            <?php foreach ($presences as $presence): ?>
                <tr>
                    <td data-label="Nom"><?php echo htmlspecialchars($presence['nom']); ?></td>
                    <td data-label="Prénom"><?php echo htmlspecialchars($presence['prenom']); ?></td>
                    <td data-label="Groupe"><?php echo htmlspecialchars($presence['groupe']); ?></td>
                    <td data-label="Statut"><?php echo htmlspecialchars($presence['statut']); ?></td>
                    <td data-label="Action">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $presence['id']; ?>">
                            <select name="nouveau_statut">
                                <option value="present" <?php echo $presence['statut'] == 'present' ? 'selected' : ''; ?>>Présent</option>
                                <option value="absent" <?php echo $presence['statut'] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                            </select>
                            <button type="submit" name="modifier_statut">Modifier</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucune présence enregistrée pour aujourd'hui.</p>
    <?php endif; ?>
</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function filtrerAbsencesParGroupe(groupe) {
                const cards = document.querySelectorAll('.card');
                cards.forEach(card => {
                    if (groupe === 'tous' || card.getAttribute('data-group') === groupe) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

        });

        
</script>

</body>
</html>
