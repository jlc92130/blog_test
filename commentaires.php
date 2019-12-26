<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>liste des billets</title>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
       <link href="style.css" rel="stylesheet" />
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   </head>
   <body>
     <h1> Mon super blog!</h1>
       <p><a href="index.php">Retour à la liste des billets </a></p>
     <?php

     try
     {
       $bdd = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'root', '',
       array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
     }
     catch(Exception $e)
     {
       die('Erreur : '.$e->getMessage());
     }
     $req = $bdd->prepare('SELECT id,titre, contenu, DATE_FORMAT(date_creation,\'%d/%m/%Y à %Hh%imin%ss\') AS date_creation_fr FROM billets WHERE id = ?');
     $req->execute(array($_GET['billet']));
     $donnees=$req->fetch();

     if (!empty($donnees)) {
     ?>
     <div class="news">
       <h3>
         <?php echo htmlspecialchars($donnees['titre']); ?>
         <em>le <?php echo $donnees['date_creation_fr']; ?></em>
       </h3>
        <p><?php echo htmlspecialchars($donnees['contenu']); ?></p>
      </div>
      <?php
      }
      else {
        echo "ce billet n'existe pas";
      }

      ?>
      <p> Commentaires</p>
    <?php

    $req->closeCursor();
    //on veut deux articles par page
    $limite = 2;
    $page = (!empty($_GET['page']) ? $_GET['page'] : 1); // numero de page par defaut
    $debut = ($page-1) * $limite; // premier element a recuprer

    $resp = 'SELECT id_billet, auteur, commentaire, DATE_FORMAT(date_commentaire,\'%d/%m/%Y à %Hh%imin%ss\') AS date_com FROM commentaires WHERE id_billet = :id_billet ORDER BY date_commentaire DESC LIMIT :debut,:limite';
    $resp = $bdd-> prepare($resp);
    $resp->bindValue(
      ':id_billet',
    $_GET['billet'],
      PDO::PARAM_INT
    );
    $resp->bindValue(
      ':debut',
      $debut,
      PDO::PARAM_INT
    );
    $resp->bindValue(
      ':limite',
      $limite,
      PDO::PARAM_INT
    );
    $resp->execute();

    // if (!empty($resp)) {
    while($donnees=$resp->fetch())
     {
    ?>
    <p>
    <?php echo htmlspecialchars($donnees['auteur']); ?> le
    <em><?php echo htmlspecialchars($donnees['date_com']); ?></em>
    </p>
    <p>
    <?php
        echo nl2br(htmlspecialchars($donnees['commentaire']));
    ?>
    </p>
    <?php
      }

        $resp->closeCursor();
      ?>
      <!-- we want two comments for every page -->
      <?php
        $req=$bdd->prepare('SELECT COUNT(*) AS nb_commentaires FROM commentaires WHERE id_billet=?');
        $req->execute(array($_GET['billet']));
        $nb_commentaires=$req->fetch();
        $req->closeCursor();
        $nb_pages=ceil($nb_commentaires['nb_commentaires']/2);
       ?>

       <!-- ajout commentaires -->
       <h3>Veuillez entrer votre commentaire</h3>
       <form  action="admin/commentaires_post.php" method="post">
         <label for="auteur" >Pseudo</label>
         <input type="text" name="auteur" id="auteur"><br/>
         <label for="commentaire">Message</label>:
         <input type="text" name="commentaire" id="commentaire"><br/>
         <input type="hidden" name="id_billet" value="<?php echo $_GET['billet']; ?>" />
         <input type="submit" value="Envoyer">
       </form>

      <p>page :
       <div class="container">
           <ul class="pagination">
          <?php
            for($i=1;$i<=$nb_pages;$i++) {
          ?>
              <li><a href="commentaires.php?page=<?php $i ?>&billet=<?= $_GET['billet'] ?>"><?php echo $i ?></a></li>
      <?php
        }
      ?>
        </ul>
     </div>
   </p>




    </body>
</html>
