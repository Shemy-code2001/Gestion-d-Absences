<?php
include('dbconnect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    $err = [];

    if (!isset($login) || empty($login)) {
        $err["login"] = "Veuillez saisir le nom d'utilisateur!";
    } 
    if (!isset($password) || empty($password)) {
        $err["password"] = "Veuillez saisir le mot de passe";
    }

    if (empty($err)) {
        $login = htmlspecialchars($login);
        $password = htmlspecialchars($password);

        try {
            $req = $conn->prepare("SELECT * FROM utilisateur WHERE login = ? AND mot_de_passe = ?");
            $req->execute([$login, $password]);
            $user = $req->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (isset($remember_me) && $remember_me === 'on') {
                    setcookie("login", $login, time() + 3600 * 24 * 12 * 4);
                    setcookie("mot_de_passe", $password, time() + 3600 * 24 * 12 * 4);
                } else {
                    setcookie("login", "", time() - 1);
                    setcookie("mot_de_passe", "", time() - 1);
                }

                session_start();
                $_SESSION['user'] = $user;
                $_SESSION['login'] = $login;
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['rôle'] = $user['rôle'];

                switch (strtolower($user['rôle'])) {
                    case 'formateur':
                        header("Location: ../PROJECT/Formateur/formateur.php");
                        exit;
                    case 'directeur':
                        header("Location: ../PROJECT/Directeur/directeur.php");
                        exit;
                    case 'gestionnaire':
                        header("Location: ../PROJECT/Gestionnaire/gestionnaire.php");
                        exit;
                    // default:
                    //     header("Location: accueil.php");
                    //     exit;
                }
            } else {
                $err["connexion"] = "Login ou mot de passe erroné";
            }
        } catch (PDOException $e) {
            echo "Erreur d'authentification : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="ripple"></div>
  </div>
  <button onclick="castSpell()">Cast a Spell</button>
  <div class="magic-text">Abracadabra!</div>
  <div id="particles-js"></div>
  <form id="loginForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <h2>Page de connexion</h2>
    <?php if (isset($err["connexion"])) { echo '<div class="error">' . $err["connexion"] . '</div>'; } ?>
    <label for="login">Nom d'utilisateur</label>
    <?php if (isset($err["login"])) { echo '<div class="error">' . $err["login"] . '</div>'; } ?>
    <input type="text" name="login" placeholder="Entrez votre nom d'utilisateur" value="<?php if(isset($_COOKIE["login"])) echo $_COOKIE["login"]; ?>">
    <label for="password">Mot de passe</label>
    <?php if (isset($err["password"])) { echo '<div class="error">' . $err["password"] . '</div>'; } ?>
    <input type="password" name="password" placeholder="Mot de passe" value="<?php if(isset($_COOKIE["mot_de_passe"])) echo $_COOKIE["mot_de_passe"]; ?>">
    <label for="remember_me">Vous êtes enregistré, ce souvenir?</label>
    <div class="checkbox-wrapper-55">
      <label class="rocker rocker-small"> 
        <input type="checkbox" name="remember_me" <?php if(isset($_COOKIE["login"])) echo "checked"; ?>>
        <span class="switch-left">Yes</span>
        <span class="switch-right">No</span>
      </label>
    </div>
    <button type="submit" id="submitButton">Connexion</button>
  </form>

  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script>
    particlesJS("particles-js", {
      "particles": {
        "number": {
          "value": 80,
          "density": {
            "enable": true,
            "value_area": 800
          }
        },
        "color": {
          "value": "#ffffff"
        },
        "shape": {
          "type": "circle",
          "stroke": {
            "width": 0,
            "color": "#000000"
          },
          "polygon": {
            "nb_sides": 5
          }
        },
        "opacity": {
          "value": 0.5,
          "random": true,
          "anim": {
            "enable": false,
            "speed": 1,
            "opacity_min": 0.1,
            "sync": false
          }
        },
        "size": {
          "value": 3,
          "random": true,
          "anim": {
            "enable": false,
            "speed": 40,
            "size_min": 0.1,
            "sync": false
          }
        },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#ffffff",
          "opacity": 0.4,
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 6,
          "direction": "none",
          "random": false,
          "straight": false,
          "out_mode": "out",
          "bounce": false,
          "attract": {
            "enable": false,
            "rotateX": 600,
            "rotateY": 1200
          }
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": {
            "enable": true,
            "mode": "repulse"
          },
          "onclick": {
            "enable": true,
            "mode": "push"
          },
          "resize": true
        },
        "modes": {
          "grab": {
            "distance": 400,
            "line_linked": {
              "opacity": 1
            }
          },
          "bubble": {
            "distance": 400,
            "size": 40,
            "duration": 2,
            "opacity": 8,
            "speed": 3
          },
          "repulse": {
            "distance": 200,
            "duration": 0.4
          },
          "push": {
            "particles_nb": 4
          },
          "remove": {
            "particles_nb": 2
          }
        }
      },
      "retina_detect": true
    });
  </script>
</body>
</html>
