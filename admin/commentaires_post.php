<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
       <title>Ajout commentaire</title>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <link href="style.css" rel="stylesheet" />
   </head>

   <body>
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

     if (!empty($_POST['auteur']) AND !empty($_POST['commentaire']))
     {
     $req = 'INSERT INTO commentaires (id_billet,auteur, commentaire,date_commentaire) VALUES (:id_billet,:auteur,:commentaire,NOW())';
     $req = $bdd->prepare($req);
     $req->bindValue(
       ':id_billet',
        $_POST['id_billet'],
        PDO::PARAM_INT
      );
        $req->bindValue(
          ':auteur',
          $_POST['auteur'],
          PDO::PARAM_STR
        );
        $req->bindValue(
          ':commentaire',
          $_POST['commentaire'],
          PDO::PARAM_STR
        );
      $req->execute();
      $req->closeCursor();
      }
      else {
        echo 'Vous devez remplir tous les champs';
      }

header('Location: ../commentaires.php?billet=$donnees[\'id_billet\']');
?>
</body>
</html>
