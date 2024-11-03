<?php
    session_start();
    if (!isset($_SESSION['user']) || empty($_SESSION['user']) || $_SESSION['user']['rôle'] != "formateur") {
        header("location: ../login.php");
            exit();
        }
        include("../dbconnect.php");


    $idSeance = 1; 

    try {
        // Vérifier si l'ID de séance existe
        $req = $conn->prepare("SELECT COUNT(*) FROM seance WHERE idSeance = ?");
        $req->execute([$idSeance]);
        $seanceExists = $req->fetchColumn();

        if (!$seanceExists) {
            // Ajouter la séance si elle n'existe pas
            $req = $conn->prepare("INSERT INTO seance (idSeance, dateSeance, h_debut, h_fin, idForm, grp_Seance) VALUES (?, CURDATE(), '08:00:00', '10:00:00', 1, 'DEV101')");
            $req->execute([$idSeance]);
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la vérification/ajout de la séance : " . $e->getMessage();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $presenceData = json_decode(file_get_contents('php://input'), true);

        if ($presenceData) {
            try {
                $conn->beginTransaction();

                $now = new DateTime();
                $date_enregistrement = $now->format('Y-m-d');
                $heure_enregistrement = $now->format('H:i:s');
                $jour_semaine = $now->format('l');

                foreach ($presenceData as $data) {
                    $idStg = $data['idStg'];
                    $idSeance = $data['idSeance'];
                    $status = $data['status'];
                    $nomPrenom = $data['nom_prenom'];

                    // Vérifier si un enregistrement existe déjà pour ce stagiaire dans cette séance
                    $dernierEnregistrement = $conn->prepare("SELECT MAX(heure_enregistrement) FROM absence WHERE idStg = ? AND idSeance = ?");
                    $dernierEnregistrement->execute([$idStg, $idSeance]);
                    $dernierTemps = $dernierEnregistrement->fetchColumn();

                    if ($dernierTemps) {
                        $dernierTemps = new DateTime($dernierTemps);
                        $diff = $now->diff($dernierTemps);
                        $minutes = $diff->days * 24 * 60 + $diff->h * 60 + $diff->i;

                        if ($minutes < 150) { // 150 minutes = 2h30
                            continue;
                        }
                    }

                    $req = $conn->prepare("INSERT INTO absence (idStg, idSeance, statut, cumul_absences, `Nom et Prénom`, date_enregistrement, heure_enregistrement, jour_semaine) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $cumulAbsences = ($status === 'absent') ? '02:30:00' : '00:00:00';
                    $req->execute([$idStg, $idSeance, $status, $cumulAbsences, $nomPrenom, $date_enregistrement, $heure_enregistrement, $jour_semaine]);
                }

                $conn->commit();
                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                $conn->rollBack();
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Formateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        header {
            background: linear-gradient(to right, #2c3e50, #3498db);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .titre {
            font-size: 2.5em;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: glowText 2s ease-in-out infinite alternate;
        }

        @keyframes glowText {
            from { text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #fff; }
            to { text-shadow: 0 0 10px #ff4da6, 0 0 20px #ff4da6, 0 0 30px #ff4da6; }
        }

        .utilisateur {
            background-color: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 30px;
            color: #fff;
            font-size: 1.2em;
            transition: all 0.3s ease;
        }

        .utilisateur:hover {
            background-color: rgba(255,255,255,0.3);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Layout principal */
        .main {
            flex: 1;
            display: flex;
            min-height: calc(100vh - 120px);
        }

        /* Navigation */
        nav {
            width: 250px;
            background: #2c3e50;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        nav ul {
            list-style: none;
        }

        nav ul li {
            margin-bottom: 10px;
        }

        nav ul li a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }

        nav ul li a:hover, nav ul li a.active {
            background: #34495e;
            border-left: 5px solid #3498db;
            transform: translateX(5px);
        }

        nav ul li a i {
            margin-right: 10px;
        }

        /* Contenu  */
        .container {
            flex: 1;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            margin: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        /* Tableaux */
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
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr {
            background-color: #f8f9fa;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        tr:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        /* Boutons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-success {
            background-color: #2ecc71;
            color: #fff;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-warning {
            background-color: #f39c12;
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-enregistrer {
            background-color: #3498db;
            color: #fff;
            padding: 15px 30px;
            font-size: 1.2em;
            margin-top: 30px;
            display: block;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Animation 3D p */
        .paragraph {
            perspective: 1000px;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .paragraph:hover {
            transform: rotateY(10deg);
        }

        /* Responsive  */
        @media (max-width: 768px) {
            .main {
                flex-direction: column;
            }

            nav {
                width: 100%;
            }

            .container {
                margin: 10px;
            }
        }

        .dd1, .dd2, .dd3 {
            display: none;
        }
        .footer {
            background: #2c3e50;
            color: #ecf0f1;
            text-align: center;
            padding: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            bottom: 0;
            position: absolute;
            width: 100%;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
    
</head>
<body>
    <header>
        <img class="logo" src="lg.png" alt="">
        <h1 class="titre">Bienvenue dans votre espace <?php echo strtoupper($_SESSION['user']['rôle']);?></h1>
        <h1 class="utilisateur">
            <?php 
                if(date("H") >= 1 && date("H") <= 12) {
                    echo "Bonjour ! <span>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . "</span>";
                } else {
                    echo "Bonsoir ! <span>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . "</span>";
                }
            ?>
        </h1>
    </header>
    <div class="main">
        <nav>
            <ul>
                <li><a href="formateur.php" class="active"><span></span>
                <span></span>
                <span></span>
                <span></span><i class="bi bi-house-fill"></i> Accueil</a></li>
                <li><a href="#dev101" class="specific-link"><span></span>
                <span></span>
                <span></span>
                <span></span>Dev   <i class="bi bi-1-circle-fill"></i> <i class="bi bi-0-circle-fill"></i> <i class="bi bi-1-circle-fill"></i></a></li>
                <li><a href="#dev102" class="specific-link">
                <span></span>
                <span></span>
                <span></span>Dev   <i class="bi bi-1-circle-fill"></i> <i class="bi bi-0-circle-fill"></i> <i class="bi bi-2-circle-fill"></i></a></li>
                <li><a href="#dev103" class="specific-link">
                <span></span>
                <span></span>
                <span></span>Dev   <i class="bi bi-1-circle-fill"></i> <i class="bi bi-0-circle-fill"></i> <i class="bi bi-3-circle-fill"></i></a></li>
                <li><a href="absences.php" name="absences">
                <span></span>
                <span></span>
                <span></span><i class="bi bi-exclamation-triangle-fill"></i> Absences</a></li>
                <li><a href="presences.php" name="retard">
                <span></span>
                <span></span>
                <span></span><i class="bi bi-person-fill"></i> Présences</a></li>
                <li><a href="retards.php" name="retard">
                <span></span>
                <span></span>
                <span></span><i class="bi bi-alarm-fill"></i> Retards</a></li>
                <li><a href="emplois.php" name="retard">
                <span></span>
                <span></span>
                <span></span><i class="bi bi-calendar-event-fill"></i> Emplois</a></li>
                <li><a href="../dcx.php" >
                <span></span>
                <span></span>
                <span></span><i class="bi bi-box-arrow-right"></i> Se déconnecter</a></li>
            </ul>
        </nav>


        <div class="container">
            <div class="liste_par_defaut">
                <?php
                    include('../dbconnect.php');
                    try {
                        $dateSeance = date("Y-m-d");
                        $heureJour = date("H:i:s");
                        echo '<div class="paragraph">';
                        echo '<p>Date actuelle : <span>' . $dateSeance . '</span></p>';
                        echo '<p>Heure actuelle : <span>' . $heureJour . '</span></p>';

                        $req_seance = $conn->prepare("SELECT * FROM seance WHERE dateSeance = :dateSeance AND :heureJour BETWEEN h_debut AND h_fin");
                        $req_seance->bindValue(':dateSeance', $dateSeance);
                        $req_seance->bindValue(':heureJour', $heureJour);
                        $req_seance->execute();
                        $seance = $req_seance->fetch(PDO::FETCH_ASSOC);

                        if ($seance) {
                            echo 'Heure de début de la séance : <span>' . $seance['h_debut'] . '</span></p>';
                            echo 'Heure de fin de la séance : <span>' . $seance['h_fin'] . '</span></p>';
                            echo '<p>Séance trouvée : <span>' . htmlspecialchars($seance['idSeance']) . '</span> - Groupe : <span>' . htmlspecialchars($seance['grp_Seance']) . '</span></p>';
                            echo '</div>';

                            // Récupérer les stagiaires du groupe correspondant
                            $req_stagiaires = $conn->prepare("SELECT * FROM stagiaire WHERE groupe = :groupe ORDER BY nom ASC");
                            $req_stagiaires->bindValue(':groupe', $seance['grp_Seance']);
                            $req_stagiaires->execute();
                            $stagiaires = $req_stagiaires->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($stagiaires)) {
                                echo '<div class="' . strtolower($seance["grp_Seance"]) . '">';
                                echo '<h2>Développement Digital</h2>';
                                echo '<p class="title101">Groupe N° : ' . $seance["grp_Seance"] . '</p>';
                                echo '<table>';
                                echo '<tr>';
                                echo '<th>N°</th>';
                                echo '<th>Nom</th>';
                                echo '<th>Prénom</th>';
                                echo '<th>Présence</th>';
                                echo '</tr>';
                                $n = 1;
                                foreach ($stagiaires as $stg) {
                                    $nom_prenom = strtoupper($stg['nom']) . ' ' . strtoupper($stg['prenom']);
                                    echo "<tr>";
                                    echo "<td>" . $n++ . "</td>"; 
                                    echo "<td>" . htmlspecialchars($stg['nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($stg['prenom']) . "</td>";
                                    echo '<td class="presence">';
                                    echo '<input type="hidden" name="presence_data['.$stg['idStg'].'][idStg]" value="' . $stg['idStg'] . '">';
                                    echo '<input type="hidden" name="presence_data['.$stg['idStg'].'][nom_prenom]" value="' . $nom_prenom . '">';
                                    echo '<input type="hidden" name="presence_data['.$stg['idStg'].'][idSeance]" value="' . $seance['idSeance'] . '">'; 
                                    echo '<button type="button" name="present" class="btn present" onclick="updatePresence(this, \'present\')"><i class="bi bi-check-circle-fill"></i> Présent</button>';
                                    echo '<button type="button" name="absent" class="btn absent" onclick="updatePresence(this, \'absent\')"><i class="bi bi-x-circle-fill"></i> Absent</button>';
                                    echo '<button type="button" name="retard" class="btn retard" onclick="updatePresence(this, \'retard\')"><i class="bi bi-clock-fill"></i> Retard</button>';
                                    echo '</td>';
                                    echo "</tr>";
                                }   

                                echo '</table>';
                                echo '</div>';
                            } else {
                                echo "Aucun stagiaire trouvé pour le groupe : " . $seance['grp_Seance'] . "<br>";
                            }
                        } else {
                            echo '<p>Aucune séance en cours pour afficher les présences.</p>';
                        }
                    } catch (PDOException $e) {
                        echo 'Erreur : ' . $e->getMessage();
                    }
                ?>
            </div>

            <button type="button" id="enregistrerBtn" class="btn btn-enregistrer"><i class="bi bi-save"></i> Enregistrer</button>
            <?php
                include("../dbconnect.php");
                try {
                    $req = $conn->prepare("SELECT * FROM stagiaire ORDER BY nom ASC");
                    $req->execute();
                    $tab_stg = $req->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Erreur d'extraction : " . $e->getMessage();
                }
                $output = array('dev101' => '', 'dev102' => '', 'dev103' => '');
                if (!empty($tab_stg)) {
                    $num_stg_dev101 = 0;
                    $num_stg_dev102 = 0;
                    $num_stg_dev103 = 0;
            
                    foreach ($tab_stg as $stg) {
                        $nom_prenom = strtoupper($stg['nom']) . ' ' . strtoupper($stg['prenom']);
                        $content = "<tr>";
                    
                        if ($stg['groupe'] == 'DEV101') {
                            $num_stg_dev101++;
                            $content .= "<td>" . $num_stg_dev101 . "</td>";
                        } elseif ($stg['groupe'] == 'DEV102') {
                            $num_stg_dev102++;
                            $content .= "<td>" . $num_stg_dev102 . "</td>";
                        } elseif ($stg['groupe'] == 'DEV103') {
                            $num_stg_dev103++;
                            $content .= "<td>" . $num_stg_dev103 . "</td>";
                        }
                        $content .= '<form method="POST">';
                        $content .= '<td>' . strtoupper($stg['nom']) . '</td>';
                        $content .= '<td>' . strtoupper($stg['prenom']) . '</td>';
                        $content .= '</form>';
                        $content .= "</tr>";
            
            
                        if ($stg['groupe'] == 'DEV103') {
                            $output['dev103'] .= $content;
                        } elseif ($stg['groupe'] == 'DEV101') {
                            $output['dev101'] .= $content;
                        } elseif ($stg['groupe'] == 'DEV102') {
                            $output['dev102'] .= $content;
                        }
                    }
                }
            ?>
        
            <div class="dev101 dd1" id="dev101">
                <h2>Développement Digital</h2>
                <p class="title101">Groupe N° : DEV101</p>
                <table>
                    <tr>
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                    </tr>
                    <?php echo $output['dev101']; ?>
                </table>
            </div>
        
            <div class="dev102 dd2" id="dev102">
                <h2>Développement Digital</h2>
                <p class="title102">Groupe N° : DEV102</p>
                <table>
                    <tr>
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                    </tr>
                    <?php echo $output['dev102']; ?>
                </table>
            </div>
        
            <div class="dev103 dd3" id="dev103">
                <h2>Développement Digital</h2>
                <p class="title103">Groupe N° : DEV103</p>
                <table>
                    <tr>
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                    </tr>
                    <?php echo $output['dev103']; ?>
                </table>
            </div>
        </div>

    </div>
    <footer class="footer">
        <p>&copy; 2023 Espace Formateurs. Tous droits réservés. <a href="#">Mentions légales</a></p>
    </footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Navigation
    const links = document.querySelectorAll('nav ul li a');
    links.forEach(link => {
        link.addEventListener('click', function() {
            links.forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Gestion des liens spécifiques
    const specificLinks = document.querySelectorAll('nav ul li a.specific-link');
    const containers = document.querySelectorAll('.dd1, .dd2, .dd3');

    specificLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);

            containers.forEach(content => {
                content.style.display = 'none';
            });

            if (targetContent) {
                targetContent.style.display = 'block';
            }

            specificLinks.forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    });

    // Fonction AJAX pour gérer les clics sur les boutons


    let presenceData = {};

    function updatePresence(button, status) {
    const row = button.closest('tr');
    const idStg = row.querySelector('input[name$="[idStg]"]').value;
    const nomPrenom = row.querySelector('input[name$="[nom_prenom]"]').value;
    const idSeance = row.querySelector('input[name$="[idSeance]"]').value;

    presenceData[idStg] = { idStg: idStg, nom_prenom: nomPrenom, idSeance: idSeance, status: status };

    // Mettre à jour l'apparence visuelle du bouton sélectionné
    const buttons = row.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-success', 'btn-danger', 'btn-warning', 'active');
    });

    button.classList.add('active');
    if (status === 'present') {
        button.classList.add('btn-success');
    } else if (status === 'absent') {
        button.classList.add('btn-danger');
    } else if (status === 'retard') {
        button.classList.add('btn-warning');
    }
    }

    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            const status = this.name;
            updatePresence(this, status);
        });
    });

    document.getElementById('enregistrerBtn').addEventListener('click', function() {
        if (confirm('Vous êtes sûr d\'enregistrer ?')) {
            enregistrerPresences();
        }
    });
    });
    function enregistrerPresences() {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', window.location.href, true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    alert('Les présences ont été enregistrées avec succès.');
                    window.location.reload();
                } else {
                    alert('Erreur : ' + response.message);
                }
            } else {
                console.error('Erreur lors de la requête AJAX');
            }
        }
    };
    xhr.send(JSON.stringify(Object.values(presenceData)));
    }
</script>
</body>
</html>