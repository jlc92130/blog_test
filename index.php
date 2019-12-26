
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Mon blog</title>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
       <link href="style.css" rel="stylesheet" />
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   </head>

   <body>
     <h1> Mon super blog</h1>
     <p>derniers billets du blog :</p>
     <!--connexion a la base de donnees -->
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
     ?>
    <!-- afficher 2 articles par page -->
     <section>
    <?php
     $limite = 2;
     $page = (!empty($_GET['page']) ? $_GET['page'] : 1); // numero de page par defaut
     $debut = ($page-1) * $limite; // premier element a recuprer


     $reponse = $bdd->prepare('SELECT  id,titre, contenu, DATE_FORMAT(date_creation, \'%d/%m/%Y à %Hh%imin%ss\') AS date_creation_fr FROM billets ORDER BY date_creation DESC LIMIT :debut, :limite') or die(print_r($bdd->errorInfos()));
     $reponse->bindValue(
       ':debut',
       $debut,
       PDO::PARAM_INT
     );
     $reponse->bindValue(
       ':limite',
       $limite,
       PDO::PARAM_INT
     );
     $reponse->execute();


      while ($donnees = $reponse->fetch())
      {
        ?>

        <div class="news">
          <h3>
            <?php echo htmlspecialchars($donnees['titre']); ?>
            <em>le <?php echo $donnees['date_creation_fr']; ?></em>
          </h3>
          <p>
            <?php
            // on affiche le contenu du billet
            echo nl2br(htmlspecialchars($donnees['contenu']));
            ?>
          </p>
          <img src="copyright.php?image=images/voyage.jpg" />


          <h5>
            <em><a href="commentaires.php?billet=<?php echo $donnees['id']; ?>">commentaire</a></em>
          </h5>
        </div>
          <?php
        }
          $reponse->closeCursor();
          ?>

      </section>
      <p><a href="index.php">Retour page d'accueil</a></p>
      <!--pagination -->
      <?php
        $req = $bdd->query('SELECT COUNT(*) as nb_billets FROM billets');
        $nb_billets = $req->fetch(); // array contenu le chiffre correspondant au nombre d'article
        $req->closeCursor();
        // je veux deux articles par pages
        $nb_pages = ceil($nb_billets['nb_billets']/2);

       ?>
       <p>page :
        <div class="container">
            <ul class="pagination">
       <?php
       for($i=1;$i<=$nb_pages;$i++)
       {
       ?>
            <li><a href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
       <?php
       }
       ?>
          </ul>
      </div>
      </p>

      </body>
      </html>
