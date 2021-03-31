<?php

if(isset($_GET['q'])) {

    $shortcut = htmlspecialchars($_GET['q']);

    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
    $requete = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $requete->execute(array($shortcut));

    while($result = $requete->fetch()) {

        if($result['x'] !=1) {
            header('location: ../?error=true&message=Adresse url inconnue');
            exit();
        }
    }

    //Redirection
    $requete = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $requete->execute(array($shortcut));

    while($result = $requete->fetch()){
        header('location: '.$result['url']);
        exit();
    }
}

if (isset($_POST['url'])) {

    $url = $_POST['url'];

//Verification de l'url
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        header('location: ../?error=true&message=Adresse non valide');
        exit();
    }

    $shortcut = crypt($url, rand());

    try
    {
        // On se connecte à MySQL
        $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }

    $requete = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $requete->execute(array($url));

    while ($result = $requete->fetch()) {

        if ($result['x'] != 0) {
            header('location: ../?error=true&message=Adresse déjà raccourcie');
            exit();
        }
    }

    //Envoi
    $requete = $bdd->prepare('INSERT INTO links(`url`,shortcut) VALUES(?, ?)');
    $requete->execute(array($url, $shortcut));
    header('location: ../?short=' . $shortcut);
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Make your url shorter">
    <title>Url Shortener</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="icon" type="image/png" href="./assets/favico.png" />
</head>
<body>

    <section class="hello">
        <div class="container">
            <header>
                <img class="logo" src="./assets/logo.png" alt="logo bitly">
            </header>
            <h1>Raccourcicez votre URL</h1>
            <h2>Simplifiez vous la vie</h2>

            <form action="" method="post">
                <input type="url" name="url" placeholder="Copie ton url ici">
                <input type="submit" value="raccourcir">
            </form>
            <?php if (isset($_GET['error']) && isset($_GET['message'])) {?>
                <div class="center">
                    <div class="result">
                            <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                    </div>
                </div>
            <?php } else if(isset($_GET['short'])) {
                ?>
                <div class="center">
                    <div class="result">
                            <b>URL RACCOURCIE : </b>
                            http://localhost/?q=<?php echo htmlspecialchars($_GET['short']);?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="brands">
        <div class="container">
            <h3>Ils nous font confiance</h3>
            <img class="picture" src="./assets/1.png" alt="icone marque">
            <img class="picture" src="./assets/2.png" alt="icone marque">
            <img class="picture" src="./assets/3.png" alt="icone marque">
            <img class="picture" src="./assets/4.png" alt="icone marque">
        </div>
    </section>

    <footer>
        <img class="logo" src="./assets/logo.png" alt="logo footer"><br>
        <span>Project for php training</span>
    </footer>

</body>
</html>