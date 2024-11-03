<?php
session_start();
if (!isset($_SESSION) || empty($_SESSION)) {
  header("location: ../login.php");
  exit();
}
include("../dbconnect.php");

try {
    $groupe = isset($_GET['groupe']) ? $_GET['groupe'] : 'all';
    $sql = "SELECT s.idStg, a.id, s.nom AS Nom, s.prenom AS Prenom, a.cumul_absences AS CumulAbsences, s.groupe AS Groupe, a.date_enregistrement AS DateAbsence
        FROM absence a
        JOIN stagiaire s ON a.idStg = s.idStg
        WHERE a.statut = 'absent' AND s.groupe IN ('DEV101', 'DEV102', 'DEV103')";
    
    if ($groupe != 'all') {
        $sql .= " AND s.groupe = :groupe";
    }

    $req = $conn->prepare($sql);
    
    if ($groupe != 'all') {
        $req->bindParam(':groupe', $groupe);
    }
    
    $req->execute();
    $absences = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur d'extraction des données: " . $e->getMessage();
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    extract($_POST);
    if(isset($retour)){
        header("Location: formateur.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des absences</title>
    <link rel="stylesheet" href="abs.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
  margin-left: 15%;
  height: 200px;
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
    </div>
    <h1>Liste des absences</h1>
    <div class="stagiaires" id="stagiaires">
    <?php
        if (!empty($absences)) {
            foreach ($absences as $absence) {
                $idStg = $absence['idStg'];
                $nomPrenom = $absence['Nom'] . ' ' . $absence['Prenom'];
                $cumulAbsences = $absence['CumulAbsences'];
                $groupe = $absence['Groupe'];
                $dateAbsence = $absence['DateAbsence'];
    ?>
                <div class="card" data-id="<?php echo $idStg; ?>" data-group="<?php echo $groupe; ?>" data-date-absence="<?php echo $dateAbsence; ?>">
                    <button type="button" class="btn-supprimer" data-id="<?php echo $idStg; ?>"><i class="bi bi-x-circle-fill"></i></button>
                    <img src="stg.jpg" alt="Image stagiaire">
                    <div class="card-content">
                        <h3><?php echo $nomPrenom; ?></h3>
                        <p>Statut: absent</p>
                        <p>Cumul Absences: <?php echo $cumulAbsences; ?></p>
                        <p>Groupe: <?php echo $groupe; ?></p>
                        <a href="modifier_présence.php?idex=<?php echo $idStg; ?>" class="btn-modifier">Modifier</a>
                    </div>
                </div>
               
    <?php
            }
        } else {
            echo '<p>Aucun stagiaire absent trouvé.</p>';
        }
    ?>
</div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    function filtrerAbsencesParGroupe(groupe) {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            const cardGroup = card.getAttribute('data-group');
            const cardId = card.getAttribute('data-id');
            const cardDateAbsence = card.getAttribute('data-date-absence');
            const isDeleted = isCardDeleted(cardId, cardDateAbsence);

            if (!isDeleted && (groupe === 'all' || cardGroup === groupe)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('#groupeForm button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const groupe = this.id;
            filtrerAbsencesParGroupe(groupe);
        });
    });

    function isCardDeleted(id, dateAbsence) {
        const deletedItems = JSON.parse(localStorage.getItem('deletedItems') || '[]');
        return deletedItems.some(item => item.id === id && item.dateAbsence === dateAbsence);
    }

    function saveDeletedId(id, dateAbsence) {
        const deletedItems = JSON.parse(localStorage.getItem('deletedItems') || '[]');
        deletedItems.push({ id, dateAbsence });
        localStorage.setItem('deletedItems', JSON.stringify(deletedItems));
    }

    function supprimerCard(event) {
        const button = event.currentTarget;
        const card = button.closest('.card');
        if (card) {
            const id = card.getAttribute('data-id');
            const dateAbsence = card.getAttribute('data-date-absence');
            card.style.display = 'none';
            saveDeletedId(id, dateAbsence);
            console.log(`Élément avec ID ${id} caché.`);
        }
    }

    function verifierChangementDateAbsence(idStg, nouvelleDate) {
        const card = document.querySelector(`.card[data-id="${idStg}"]`);
        if (card) {
            const dateAbsence = card.getAttribute('data-date-absence');
            if (dateAbsence !== nouvelleDate) {
                card.style.display = '';
                card.setAttribute('data-date-absence', nouvelleDate);
            }
        }
    }

    function cacherElementsSupprimes() {
        const deletedItems = JSON.parse(localStorage.getItem('deletedItems') || '[]');
        deletedItems.forEach(item => {
            const element = document.querySelector(`.card[data-id="${item.id}"]`);
            if (element) {
                const currentDateAbsence = element.getAttribute('data-date-absence');
                if (currentDateAbsence === item.dateAbsence) {
                    element.style.display = 'none';
                }
            }
        });
    }

    document.querySelectorAll('.btn-supprimer').forEach(button => {
        button.addEventListener('click', supprimerCard);
    });

    cacherElementsSupprimes();
    filtrerAbsencesParGroupe('all'); // Appliquer le filtre initial
});
</script>

</body>
</html>
