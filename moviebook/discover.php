<?php
include('../includes/config.php');

if(is_connected() == false) {
   $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accèder à cette page.";
   header('Location: ../login?r='.$_SERVER['REQUEST_URI']);
   exit();
}

if(is_profile_exist($_SESSION['id']) == false) {
   $_SESSION['flash']['danger'] = $danger_sign . 'Tu dois avoir un compte <strong>MovieBook</strong> pour accèder à cette page. Crée en toi un dès maintenant !';
   header('Location: profile?u='.$_SESSION['id']);
   exit();
}

if(!isset($_GET['t']) AND empty($_GET['t'])) {
$type = 1;
} elseif(isset($_GET['t']) AND !empty($_GET['t']) AND is_numeric($_GET['t']) AND strlen($_GET['t']) > 0) {
   $type = htmlspecialchars($_GET['t']);
   if($type != 2 AND $type != 3) {
      $type = 1;
   }
} else {
   $type = 1;
}

if($type == 1) {
   $total_of_profiles_sql = $database2->prepare('SELECT * FROM profile');
   $total_of_profiles_sql->execute();
   $total_of_profiles = $total_of_profiles_sql->rowCount();

   $profiles_per_page = 28;
   $total_of_page = ceil($total_of_profiles / $profiles_per_page);

   if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
     $p = htmlspecialchars($_GET['p']);
     $current_page = $p;
   } else {
     $current_page = 1;
   }

   $page_start = ($current_page - 1) * $profiles_per_page;

   $result_sql = $database2->prepare('SELECT * FROM profile ORDER BY id DESC LIMIT ' . $page_start . ',' . $profiles_per_page);
   $result_sql->execute();
}

$current_page_title = "Découvrir des profils";
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title">Découvrir des profils</h1>
   <p class="lead text-white" style="font-size: 200%;">Rencontre de nouvelles personnes inscrites sur MovieBook.</p>
</center>
<br><br>
<div class="row">
   <div class="col-md-4">
      <center><a href="discover" class="btn btn-primary btn-lg <?php if($type == 1) { echo "disabled"; } ?>">Tous les profils</a></center>
   </div>
   <div class="col-md-4">
      <center><a href="discover?t=2" class="btn btn-primary btn-lg <?php if($type == 2) { echo "disabled"; } ?>">Personnages Boubi MSP</a></center>
   </div>
   <div class="col-md-4">
      <center><a href="discover?t=3" class="btn btn-primary btn-lg <?php if($type == 3) { echo "disabled"; } ?>">Meilleurs profils</a></center>
   </div>
</div>
<br><br>
<?php if($type == 1) { ?>
<h5 class="text-white">Il y a <strong><?= $total_of_profiles ?></strong> profils MovieBook créés. Ils sont rangés du plus récent au moins récent.</h5>
<div class="row">
   <?php while($profile_data = $result_sql->fetch()) {
      $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($profile_data['username_id']));
      $account_data = $sql->fetch(); ?>
   <div class="col-md-3">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="media">
               <div class="media-left media-top">
                  <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
               </div>
               <div class="media-body">
                  <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?php if(strlen($account_data['username']) >= 20) { echo substr($account_data['username'], 0, 17)."..."; } else { echo $account_data['username']; } ?></a> <?= badge_check($account_data['id'], "other") ?>
                  <?php if($_SESSION['id'] != $account_data['id'] AND is_friends($_SESSION['id'], $account_data['id']) == false) {
                     $from_query = $database2->prepare('SELECT * FROM friend_request WHERE from_id = ? AND to_id = ?');
                     $from_query->execute(array($_SESSION['id'],$account_data['id']));
                     if($from_query->rowCount() == 0) { ?>
                  <br>
                  <span id="spawn_text_friend_request_sent_span_<?= $account_data['id'] ?>"></span>
                  <span id="add_friend_button_span_<?= $account_data['id'] ?>">
                  <button class="btn btn-success add_friend_button" page="discover" user_id="<?= $account_data['id'] ?>" name="add_friend_button" id="add_friend_button"><span class="glyphicon glyphicon-plus"></span> Ajouter</button>
                  </span>
                  <?php } else { ?>
                  <br>Demande envoyée.
                  <?php } ?>
                  <?php } elseif($account_data['id'] != $_SESSION['id']) { ?>
                  <br>Tu es ami avec.
                  <?php } else { ?>
                  <br>C'est toi !
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
<div id="p"></div>
<div class="row">
   <div class="col-md-4 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="discover?p=<?= $current_page - 1 ?>#p">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="discover?p=<?= $i ?>#p"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="discover?p=<?= $current_page + 1 ?>#p">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
            </center>
         </div>
      </div>
   </div>
</div>
<?php } elseif($type == 2) { ?>
<div class="row">
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/boubi_msp_characters/roger_sidoo.png" class="img-rounded" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Lorem Ipsum</h5>
               <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</p>
               <a href="profile?u=26" class="btn btn-primary">Voir son profil</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/boubi_msp_characters/claudia_sidoo.png" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Lorem Ipsum</h5>
               <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</p>
               <a href="profile?u=9288" class="btn btn-primary">Voir son profil</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/boubi_msp_characters/soon.jpg" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Lorem Ipsum</h5>
               <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</p>
               <a class="btn btn-primary" disabled>Bientôt</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/boubi_msp_characters/nathalie.png" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Lorem Ipsum</h5>
               <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</p>
               <a href="profile?u=18103" class="btn btn-primary">Voir son profil</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } elseif($type == 3) { ?>
<div class="row">
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/best_profiles/gravity_falls.jpg" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Dipper - Gravity Falls</h5>
               <a href="profile?u=13624" class="btn btn-primary">Voir le profil</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/best_profiles/animal_crossing.jpg" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Animal Crossing</h5>
               <a href="profile?u=11372" class="btn btn-primary">Voir le profil</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/best_profiles/maeva.png" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Maeva Sidoo</h5>
               <a href="profile?u=17438" class="btn btn-primary">Voir le profil</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/best_profiles/harry_potter.jpg" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Harry Potter</h5>
               <a href="profile?u=10525" class="btn btn-primary">Voir le profil</a>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-3">
      <div class="thumbnail">
         <center><img src="../img/moviebook/discover/best_profiles/angel.jpg" draggable="false" style="width:100%; height:100%; display:block;"></center>
         <div class="caption">
            <div align="center">
               <h5>Angel</h5>
               <a href="profile?u=686" class="btn btn-primary">Voir le profil</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php require '../includes/footer.php'; ?>