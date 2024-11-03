<?php
    session_start();
    if (!isset($_SESSION) || empty($_SESSION)) {
        header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");
    function getStartAndEndDateW($week, $year) {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $debut = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $fin = $dto->format('Y-m-d');
        return array($debut, $fin);
    }

    $emp=new DateTime();
    $emp = $emp->format('Y-W');
    $emp=explode('-', $emp);
    list($debut, $fin) = getStartAndEndDateW($emp[1], $emp[0]);



    try {
        $reqi = $conn->prepare("SELECT seance.*, formateur.idForm AS id_Form FROM seance JOIN formateur ON seance.idForm=formateur.idForm");
        $reqi->execute();
        $result = $reqi->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $idForm = $result['id_Form'];
            $req = $conn->prepare("SELECT * FROM seance WHERE idForm = ? AND dateSeance BETWEEN ? AND ? ORDER BY dateSeance, h_debut");
            $req->execute([$idForm, $debut, $fin]);
            $seances = $req->fetchAll(PDO::FETCH_ASSOC);

            foreach ($seances as $seance) {
            }
        } else {
            echo "Aucun formateur trouvé.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
    if($_SERVER["REQUEST_METHOD"]=="POST"){
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
    <title>Emploi du temps</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f0f2f5;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            padding: 20px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 50px;
            animation: pulse 2s infinite;
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

        .titre {
            font-size: 24px;
            font-weight: 600;
        }

        .utilisateur {
            font-size: 18px;
            font-weight: 400;
        }

        .main {
            display: flex;
            min-height: calc(100vh - 90px);
        }

        nav {
            width: 250px;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style-type: none;
        }

        nav ul li {
            margin-bottom: 15px;
        }

        nav ul li a {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background: #f0f2f5;
            transform: translateX(5px);
        }

        nav ul li a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .container {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
            position: relative;
        }

        h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: #6e8efb;
            margin: 10px auto;
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
            background: white;
        }

        th {
            background: #6e8efb;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        tr:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        td:first-child, th:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        td:last-child, th:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        td:nth-child(1)::before {
            content: '\f073';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            color: #6e8efb;
        }

        td:nth-child(2)::before {
            content: '\f017';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            color: #6e8efb;
        }

        td:nth-child(3)::before {
            content: '\f017';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            color: #6e8efb;
        }

        td:nth-child(4)::before {
            content: '\f0c0';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            color: #6e8efb;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        tr {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        tr:nth-child(1) { animation-delay: 0.1s; }
        tr:nth-child(2) { animation-delay: 0.2s; }
        tr:nth-child(3) { animation-delay: 0.3s; }
        @media (max-width: 768px) {
            .main {
                flex-direction: column;
            }

            nav {
                width: 100%;
                margin-bottom: 20px;
            }

            .container {
                padding: 15px;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }
        button[name="retour"] {
            background-color: #6e8efb;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button[name="retour"]:hover {
            background-color: #5a75db;
            transform: translateY(-2px);
        }

        button[name="retour"]:active {
            background-color: #4a63bb;
            transform: translateY(0);
        }

    </style>
</head>
<body>
<header>
        <img class="logo" src="lg.png" alt="">
        <h1 class="titre">Votre emploi du temps</h1>
        <h1 class="utilisateur">
            <?php 
                echo "Bonjour, " . $_SESSION['nom'] . " " . $_SESSION['prenom'];
            ?>
        </h1>
    </header>
    <div class="main">
        <div class="container">
            <h2>Emploi du temps</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Heure de début</th>
                    <th>Heure de fin</th>
                    <th>Groupe</th>
                </tr>
                <?php foreach ($seances as $seance): ?>
                <tr>
                    <td><?php echo $seance['dateSeance']; ?></td>
                    <td><?php echo $seance['h_debut']; ?></td>
                    <td><?php echo $seance['h_fin']; ?></td>
                    <td><?php echo $seance['grp_Seance']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <form method="POST">
                <button type="submit" name="retour">Espace Formateur</button>
            </form>
        </div>
    </div>
</body>
</html>