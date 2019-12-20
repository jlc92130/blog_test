<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>liste des billets</title>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
     $req = $bdd->prepare('SELECT titre, contenu, DATE_FORMAT(date_creation,\'%d/%m/%Y à %Hh%imin%ss\') AS date_creation_fr FROM billets WHERE id = ?');
     $req->execute(array($_GET['billet']));
     $donnees=$req->fetch();

     if (!empty($donnees)) {
     ?>
     <div class="news">
        <h3>
          <?php echo htmlspecialchars($donnees['titre']); ?>;
          <em>le <?php echo $donnees['date_creation_fr']; ?></em>
        </h3>
        <p><?php htmlspecialchars($donnees['contenu']); ?></p>
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

    $resp = $bdd-> prepare('SELECT id_billet, auteur, commentaire, DATE_FORMAT(date_commentaire,\'%d/%m/%Y à %Hh%imin%ss\') AS date_com FROM commentaires WHERE id_billet = ? ORDER BY date_commentaire DESC LIMIT :debut , :limite');
    $resp -> execute(array($_GET['billet']));
    $resp->bindValue(
      ':debut',
      $debut,
      ':limit',
      $limite,
      PDO::PARAM_INT
      )
      $resp->execute();
    $donnees=$resp->fetch(); // tableau contenant tout les commentaires pour id_billet=billet
    if (!empty($donnees)) {
    while($donnees=$resp->fetch())
     {
    ?>
    <p>
    <?php echo htmlspecialchars($donnees['auteur']); ?> le
    <em><?php echo htmlspecialchars($donnees['date_com']); ?></em>
    </p>
    <?php
        echo nl2br(htmlspecialchars($donnees['commentaire']));
      }
    }
      catch (Exception $e)
      {
        die('Erreur :' . $e->getMessage());
      }
        $resp->closeCursor();
      ?>
      <?php
        $req=$bdd->query('SELECT COUNT(*) AS nb_commentaires FROM commentaires ');
        $nb_commentaires=$req->fetch();
        $req->closeCursor();
        $nb_pages=ceil($nb_commentaires['nb_commentaires']/2);
       ?>

      <p>page :
       <div class="container">
           <ul class="pagination">
      <?php
        for($i=0;$i<=$nb_pages;$i++) {
      ?>
        <li><a href="commentaires.php?page=<?php echo $page ?>"><?php echo $i ?></a></li>
      <?php
        }
      ?>




    </body>
</html>
