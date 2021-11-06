<?php
include('includes/config.php');
$current_page_title = "Accueil";
require 'includes/headerParallax.php';

$news = $database->query('SELECT * FROM news ORDER BY id DESC LIMIT 3');
$news->execute();

// if($_SESSION['id'] == 1) {
//     $sql_1 = $database2->prepare('SELECT * FROM profile');
//     $sql_1->execute();
//     while($profile_data = $sql_1->fetch()) {
//         $sql = $database2->prepare('SELECT * FROM achievements WHERE state != 0 AND name = "number_of_posts_received" AND username_id = ?');
//         $sql->execute(array(
//             $profile_data['username_id']
//         ));
//         if($sql->rowCount() == 1) {
//             $achievements_data = $sql->fetch();

//             if($achievements_data['state'] == 1) {
//                 $points = -5000;
//             } elseif($achievements_data['state'] == 2) {
//                 $points = -20000;
//             } elseif($achievements_data['state'] == 3) {
//                 $points = -50000;
//             }
            
//             $sql = $database2->prepare('UPDATE achievements SET state = 0 WHERE username_id = ? AND name = "number_of_posts_received"');
//             $sql->execute(array(
//                 $profile_data['username_id']
//             ));
            
//             $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?');
//             $sql->execute(array(
//                 $profile_data['level_points'] + $points,
//                 $profile_data['username_id']
//             ));
            
//             add_points_history($profile_data['username_id'], "1", $profile_data['username_id'], "custom", 'Succès "Publication reçues" enlevé', $points, NULL);

//             $sql = $database2->prepare('INSERT INTO notifications(to_id,from_id,content,type,content_id) VALUES (?,?,?,"important_custom_notification",NULL)');
//             $sql->execute(array(
//                 $profile_data['username_id'],
//                 "1",
//                 'Suite à une erreur du site, la notification "Publications reçues" a eu un petit problème. C\'est pour cela que ce succès a été réinitialisé, tu as donc perdu tous les points que tu as gagné avec ce dernier. Pas d\'inquiètude, tu peux récupérer les points que tu as gagné en les réclamant.'
//             ));
//         }
//     }
// }

?>
<div class="parallax-window" data-parallax="scroll" data-image-src="<?php if($theme == 1) { echo "https://i.imgur.com/vGQd6OZ.jpg"; } elseif($theme == 2) { echo "https://i.imgur.com/XlktqTO.jpg"; } ?>">
   <br><br>
   <div align="center">
      <img draggable="false" src="<?php if($theme == 1) { echo "https://i.imgur.com/z2FHs6y.png"; } elseif($theme == 2) { echo "https://i.imgur.com/ZAO3rHL.png"; } ?>"><br><br>
      <p class="lead text-white" style="font-size: 200%;">Profite des nombreux concours et du réseau social spécial MovieStarPlanet.</p>
      <br>
      <?php if(isset($_SESSION['id']) AND !empty($_SESSION['id'])) { ?>
      <a href="moviebook/home" class="btn btn-primary btn-lg">Accéder à MovieBook</a>
      <?php } else { ?>
      <a href="login?t=register" class="btn btn-primary btn-lg">Commencer dès maintenant !</a>
      <?php } ?>
      <br><br>
   </div>
</div>
<div class="container">
   <center>
      <h1 class="text-orange" style="margin: 0px;">Les dernières news</h1>
   </center>
   <div class="row">
      <?php while($n = $news->fetch()) {
         $date_news = ucfirst(strftime('%d/%m/%y ', strtotime($n['date']) + 60*60));
         ?>
      <div class="col-sm-6 col-md-4">
         <div class="thumbnail">
            <img src="<?= $n['img_url'] ?>" class="img-circle transition img-news" width="240" height="200">
            <div class="caption">
               <center>
                  <h4 class="text-orange"><?= $n['title'] ?></h4>
                  <p><?= reduce_string($n['text'], 30, 30) ?></p>
                  <small>Posté par <?= $n['news_by'] ?>, le <?= $date_news ?></small><br>
                  <a href="news?id=<?= $n['id'] ?>" class="btn btn-primary">Lire la suite</a>
               </center>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<div class="parallax-window" data-parallax="scroll" data-image-src="<?php if($theme == 1) { echo "https://i.imgur.com/5zcPzff.jpg"; } elseif($theme == 2) { echo "https://i.imgur.com/oNaDiqe.jpg"; } ?>">
   <br>
   <div align="center">
      <img src="https://i.imgur.com/r5c3nNw.png"><br><br><br>
      <p class="lead text-white" style="font-size: 200%;">Découvre maintenant le réseau social spécial<br>
         MovieStarPlanet, MovieBook.
      </p>
      <br>
      <a href="moviebook/profile" class="btn btn-info btn-lg">En savoir plus</a><br><br>
   </div>
</div>
<div id="contact">
   <?php require 'includes/footerParallax.php'; ?>
</div>
<?php require 'includes/footer.php'; ?>
<!-- <script type="text/javascript">
   document.onkeydown = checkKey;
    function checkKey(e) {
        e = e || window.event;
        if(e.keyCode == '37') {
           window.location.replace("eventHalloween");
        }
    }
</script> -->