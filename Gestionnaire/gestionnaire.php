<?php
    session_start();
    # Vérification de l'utilisateur
    if (!isset($_SESSION['user']) || empty($_SESSION['user']) || $_SESSION['user']['rôle'] != "gestionnaire") {
        header("location: ../login.php");
        exit();
    }
    include("../dbconnect.php");

    // Enregistrement de la notification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveNotification') {
        $notification = [
            'nomStagiaire' => $_POST['nomStagiaire'],
            'dateAbsence' => $_POST['dateAbsence'],
            'nomFormateur' => $_POST['nomFormateur'],
            'cmnt' => $_POST['cmnt']
        ];
    
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
        
        $_SESSION['notifications'][] = $notification;
        echo 'success';
        exit;
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
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
            padding: 0;
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

        h2{
            font-size: 2.5em;
            width: fit-content;
            margin-top: 20px;
            margin-bottom: 20px;
            margin-left: 20px;
            padding: 10px;
            color: #34495e;
            border-bottom: 10px solid #2c3e50;
            border-left: 2px solid #2c3e50;
            border-bottom-left-radius: 5px;
        }

        .content{
            border-radius: 5px;
            background-color: white;
            min-height: calc(100vh - 250px);
            width: calc(100vw - 305px);
        }

        /* Contenu */
        .absences{
            background-color: rgba(52, 73, 94, 0.7);
            margin: 5px 10px;
            width: 500px;
            padding: 20px 100px 20px 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: white;
            display: flex;
            flex-direction: column;
            row-gap: 20px;
        }
        .absences li{
            margin-left: 10px;
        }

        .notifications{
            background-color: #305a33;
            width: 500px;
            padding: 20px 100px 20px 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: white;
            display: flex;
            flex-direction: column;
            row-gap: 20px;
        }
        .notifications li{
            margin-left: 10px;
        }

        .attendance-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }



        /* Contenu  */
        .container {
            flex: 1;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            margin: 20px;
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .NOTIF{
            margin: 30px 0px 20px 0px;
            display: flex;
            align-items: flex-start;
            justify-content: space-evenly;
            
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

    <!--***************************************************************************************-->

    <div class="main">
        <nav>
            <ul>
                <li><a href="gestionnaire.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg>
                    Accueil</a></li>

                <li><a href="dev.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-globe2" viewBox="0 0 16 16"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855q-.215.403-.395.872c.705.157 1.472.257 2.282.287zM4.249 3.539q.214-.577.481-1.078a7 7 0 0 1 .597-.933A7 7 0 0 0 3.051 3.05q.544.277 1.198.49zM3.509 7.5c.036-1.07.188-2.087.436-3.008a9 9 0 0 1-1.565-.667A6.96 6.96 0 0 0 1.018 7.5zm1.4-2.741a12.3 12.3 0 0 0-.4 2.741H7.5V5.091c-.91-.03-1.783-.145-2.591-.332M8.5 5.09V7.5h2.99a12.3 12.3 0 0 0-.399-2.741c-.808.187-1.681.301-2.591.332zM4.51 8.5c.035.987.176 1.914.399 2.741A13.6 13.6 0 0 1 7.5 10.91V8.5zm3.99 0v2.409c.91.03 1.783.145 2.591.332.223-.827.364-1.754.4-2.741zm-3.282 3.696q.18.469.395.872c.552 1.035 1.218 1.65 1.887 1.855V11.91c-.81.03-1.577.13-2.282.287zm.11 2.276a7 7 0 0 1-.598-.933 9 9 0 0 1-.481-1.079 8.4 8.4 0 0 0-1.198.49 7 7 0 0 0 2.276 1.522zm-1.383-2.964A13.4 13.4 0 0 1 3.508 8.5h-2.49a6.96 6.96 0 0 0 1.362 3.675c.47-.258.995-.482 1.565-.667m6.728 2.964a7 7 0 0 0 2.275-1.521 8.4 8.4 0 0 0-1.197-.49 9 9 0 0 1-.481 1.078 7 7 0 0 1-.597.933M8.5 11.909v3.014c.67-.204 1.335-.82 1.887-1.855q.216-.403.395-.872A12.6 12.6 0 0 0 8.5 11.91zm3.555-.401c.57.185 1.095.409 1.565.667A6.96 6.96 0 0 0 14.982 8.5h-2.49a13.4 13.4 0 0 1-.437 3.008M14.982 7.5a6.96 6.96 0 0 0-1.362-3.675c-.47.258-.995.482-1.565.667.248.92.4 1.938.437 3.008zM11.27 2.461q.266.502.482 1.078a8.4 8.4 0 0 0 1.196-.49 7 7 0 0 0-2.275-1.52c.218.283.418.597.597.932m-.488 1.343a8 8 0 0 0-.395-.872C9.835 1.897 9.17 1.282 8.5 1.077V4.09c.81-.03 1.577-.13 2.282-.287z"/></svg>
                    Developpement Digital</a></li>

                <li><a href="id.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-server" viewBox="0 0 16 16"><path d="M1.333 2.667C1.333 1.194 4.318 0 8 0s6.667 1.194 6.667 2.667V4c0 1.473-2.985 2.667-6.667 2.667S1.333 5.473 1.333 4z"/><path d="M1.333 6.334v3C1.333 10.805 4.318 12 8 12s6.667-1.194 6.667-2.667V6.334a6.5 6.5 0 0 1-1.458.79C11.81 7.684 9.967 8 8 8s-3.809-.317-5.208-.876a6.5 6.5 0 0 1-1.458-.79z"/><path d="M14.667 11.668a6.5 6.5 0 0 1-1.458.789c-1.4.56-3.242.876-5.21.876-1.966 0-3.809-.316-5.208-.876a6.5 6.5 0 0 1-1.458-.79v1.666C1.333 14.806 4.318 16 8 16s6.667-1.194 6.667-2.667z"/></svg>
                    Infrastructure Digital</a></li>

                <li><a href="info.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-easel2-fill" viewBox="0 0 16 16"><path d="M8.447.276a.5.5 0 0 0-.894 0L7.19 1H2.5A1.5 1.5 0 0 0 1 2.5V10h14V2.5A1.5 1.5 0 0 0 13.5 1H8.809z"/><path fill-rule="evenodd" d="M.5 11a.5.5 0 0 0 0 1h2.86l-.845 3.379a.5.5 0 0 0 .97.242L3.89 14h8.22l.405 1.621a.5.5 0 0 0 .97-.242L12.64 12h2.86a.5.5 0 0 0 0-1zm3.64 2 .25-1h7.22l.25 1z"/></svg>
                    Infographie</a></li>

                <li><a href="stats.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-file-spreadsheet-fill" viewBox="0 0 16 16"><path d="M12 0H4a2 2 0 0 0-2 2v4h12V2a2 2 0 0 0-2-2m2 7h-4v2h4zm0 3h-4v2h4zm0 3h-4v3h2a2 2 0 0 0 2-2zm-5 3v-3H6v3zm-4 0v-3H2v1a2 2 0 0 0 2 2zm-3-4h3v-2H2zm0-3h3V7H2zm4 0V7h3v2zm0 1h3v2H6z"/></svg>  
                    Statistiques</a></li>

                <li><a href="../dcx.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg>
                    Se Deconnecter</a></li>
            </ul>
        </nav>
        <div class="attendance-form">
            <div class="content" >
                <h2>ACCUEIL</h2>
                <div class="NOTIF">
                    <div>
                        <?php 
                            try{
                                $stmt = $conn->prepare("SELECT * FROM groupe");
                                $stmt->execute();
                                $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                            echo "<h3>Notification d'absences : </h3>";
                            echo"<ul class='absences'>";
                            foreach ($groups as $group) {
                                try{
                                    $stmt = $conn->prepare("SELECT DISTINCT a.idStg FROM absence a LEFT JOIN stagiaire s ON a.idStg = s.idStg WHERE groupe = ? AND statut = 'absent'");
                                    $stmt->execute([$group['ref_grp']]);
                                    $abs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                if(count($abs) == 1) {
                                    echo "<li> Il y'a une seul absence en $group[ref_grp]</li>";
                                }
                                elseif(count($abs) > 1) {
                                    echo "<li> Il y'a ". count($abs) ." absences en $group[ref_grp]</li>";
                                }
                            }
                            echo "</ul>";
                        ?>
                    </div>
                    <div >
                    <?php
                        //  affichage de notification
                        if (isset($_SESSION['notifications']) && !empty($_SESSION['notifications'])) {
                            echo "<h3>Nouvelles notifications de retard</h3>";
                            
                            foreach ($_SESSION['notifications'] as $notification) {
                                echo "<div class='notification' style='display: flex;'>";
                                echo "<ul class='notifications'>";
                                echo "<li>Formateur : " . htmlspecialchars($notification['nomFormateur']) . "</li>";
                                echo "<li>Stagiaire : " . htmlspecialchars($notification['nomStagiaire']) . "</li>";
                                echo "<li>Heure de retard : " . htmlspecialchars($notification['dateAbsence']) . "</li>";
                                echo "<li>Commentaire : " . htmlspecialchars($notification['cmnt']) . "</li>";
                                echo "</ul>";
                                echo "</div>";
                            }
                        }
                    ?>
                    </div>
                </div>
               
                
            </div>
              
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2023 Espace Formateurs. Tous droits réservés. <a href="#">Mentions légales</a></p>
    </footer>
</body>
</html>
