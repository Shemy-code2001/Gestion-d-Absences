<?php
    session_start();
    # Vérification de l'utilisateur
    if (!isset($_SESSION['user']) || empty($_SESSION['user']) || $_SESSION['user']['rôle'] != "directeur") {
        header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");

    // Obtenir la date actuelle
    $dateActuelle = new DateTime();
    // Calculer le nombre de jours jusqu'au prochain lundi (1 signifie lundi, 2 signifie mardi, etc.)
    $joursJusquAuProchainLundi = (8 - $dateActuelle->format('N')) % 7;
    // Modifier la date actuelle pour obtenir le prochain lundi
    $dateActuelle->modify('+' . $joursJusquAuProchainLundi . ' day');
    // Initialiser un tableau pour stocker les dates du lundi au samedi
    $datesProchaines = [];
    // Boucle pour obtenir les dates du lundi au samedi
    for ($i = 0; $i <= 5; $i++) {
        // Formater la date au format souhaité (Y-m-d)
        $datesProchaines[] = $dateActuelle->format('Y-m-d');
        // Passer au jour suivant
        $dateActuelle->modify('+1 day');
    }

    // Extracter les IDs, les noms et les prenoms des formateurs     
        try{
            $req = $conn->prepare("SELECT u.nom,u.prenom,f.idForm FROM formateur f INNER JOIN utilisateur u USING(matricule)");
            $req->execute();
            $formateur = $req->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo $e->getMessage();
        }

    // HTML and JavaScript section
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet Interface</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            height: 100vh;
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

        .container {
            display: flex;
            min-height: calc(100vh - 120px);
        }
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

        .main-content {
            background-color: #ecf0f1;
            flex: 1;
            padding: 20px;
        }

        .main-content #formateur{
            width: 200px;
            padding: 10px;
        }

                
        .timesheet {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0px 50px 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
        } 
        .timesheet  select {
            width: 90px;
            padding: 5px;
            border-radius: 5px;
        }

        .main-content > span{
            font-weight: bold;
            font-size: 150%;
            background: none;
            color: #333;
        }
        .utilisateur > span{
            background: none;
        }
        span{
            font-weight: bold;
            background-color: #415f7e;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .calendar {
            display: flex;
            justify-content: space-between;
        }

        
        button{
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .day {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 10px;
            width: 18%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .trt{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mod{
            background-color: #3498db;
            padding: 10px 12.5px;
            cursor: pointer; 
            color: #fff;
            border-radius: 5px;
            border: none;
        }
        .mod a{
            text-decoration: none;
            color: white;
        }

        .sup{
            background-color: #9b2121;
            padding: 10px 12.5px;
            cursor: pointer; 
            color: #fff;
            border-radius: 5px;
            border: none;
        }
        .sup a {
            text-decoration: none;
            color: white;
        }

        .date {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .time-block {
            background-color: #bdc3c7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 125px;
        }

        .footer {
            background: #2c3e50;
            color: #ecf0f1;
            text-align: center;
            padding: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
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


    <div class="container">
        <nav>
            <ul>
                <li><a href="directeur.php" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check-fill" viewBox="0 0 16 16"><path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2m-5.146-5.146-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708"/></svg>
                    Affectation D'Emplois</a></li>

                <li><a href="dstats.php" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-clipboard-data-fill" viewBox="0 0 16 16"><path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zM10 8a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1"/></svg>
                    Statistiques</a></li>

                <li><a href="demplois.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-calendar-week-fill" viewBox="0 0 16 16"><path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2M9.5 7h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m3 0h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M2 10.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/></svg>
                    Emplois</a></li>

                <li><a href="../dcx.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg>
                    Se Deconnecter</a></li>
            </ul>
        </nav>
        <main class="main-content">
                <?php
                $weekdays=['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
                foreach($formateur as $frm){
                    try{
                        $req = $conn->prepare("SELECT * FROM seance WHERE idForm = ? AND dateSeance BETWEEN ? AND ? ");
                        $req->execute([$frm['idForm'],$datesProchaines[0],$datesProchaines[5]]);
                        $seance = $req->fetchAll(PDO::FETCH_ASSOC);
                    } catch(PDOException $e){
                        echo "Error: " . $e->getMessage();
                    }
                    if(!empty($seance)){
                        echo"<div style ='display: flex; align-items: center; justify-content: space-between'>";
                        echo"<div><p><h4>Formateur:</h4><h2>". $frm['nom'] . " ". $frm['prenom']."</h2></p></div>";
                        echo"<div style='display:flex; align-items: center'><p><h3>Date d'application :".$datesProchaines[0]."</h3></p></div>";
                        echo"</div>";
                        echo"<section class='timesheet'>";
                        for ($i = 0; $i < 6; $i++) {
                            try{
                                $req = $conn->prepare("SELECT * FROM seance WHERE idForm = ? AND dateSeance = ? ");
                                $req->execute([$frm['idForm'],$datesProchaines[$i]]);
                                $sec = $req->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e){
                                echo "Error: " . $e->getMessage();
                            }
                            if(!empty($sec)){
                                echo '<div class="day">';
                                echo '<div class="date"><h3>'.$weekdays[$i].'</h3></div>';
                                echo '<div class="time-block"><span>08:30 - 11:00</span><br>';
                                    foreach($sec as $s){
                                        if($s['h_debut']=='08:30:00'){echo "<h3>".$s['grp_Seance']."</h3>"; break;}
                                        
                                    }
                                echo '</div>';
                                echo '<div class="time-block"><span>11:00 - 13:30</span><br>';
                                    foreach($sec as $s){
                                        if($s['h_debut']=='11:00:00'){echo "<h3>".$s['grp_Seance']."</h3>"; break;}
                                        
                                    }                       
                                echo '</div>';
                                echo '<div class="time-block"><span>13:30 - 16:00</span><br>';
                                    foreach($sec as $s){
                                        if($s['h_debut']=='13:30:00'){echo "<h3>".$s['grp_Seance']."</h3>"; break;}
                                        
                                    }                                
                                echo '</div>';
                                echo '<div class="time-block"><span>16:00 - 18:30</span><br>';
                                    foreach($sec as $s){
                                        if($s['h_debut']=='16:00:00'){echo "<h3>".$s['grp_Seance']."</h3>"; break;}
                                        
                                    }                                
                                echo '</div>';
                                echo "<div class='trt'>";
                                echo "<button type='button' class='mod'><a href='dmodifier.php?id=".$frm['idForm']."&date=".$datesProchaines[$i]."'>Modifier</a></button>";
                                echo "<button type='button' class='sup'>Supprimer</a></button>";
                                echo "</div>";
                                echo '</div>';
                            }else{
                                echo '<div class="day">';
                                echo'<input type="hidden" name="dateS" value='.$datesProchaines[$i].'>';
                                echo '<div class="date"><h3>'.$weekdays[$i].'</h3></div>';
                                echo '<div class="time-block"><span>08:30 - 11:00</span><br>';

                                echo '</div>';
                                echo '<div class="time-block"><span>11:00 - 13:30</span><br>';
                                echo '</div>';
                                echo '<div class="time-block"><span>13:30 - 16:00</span><br>';
                                echo '</div>';
                                echo '<div class="time-block"><span>16:00 - 18:30</span><br>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } 
                        echo"</section>";
                    }
                }
                ?>
                       
        </main>
    </div>
    <footer class="footer">
        <p>&copy; 2023 Espace Formateurs. Tous droits réservés. <a href="#">Mentions légales</a></p>
    </footer>
</body>
</html>
