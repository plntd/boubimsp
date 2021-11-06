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

if(isset($_GET['t']) AND !empty($_GET['t']) AND is_numeric($_GET['t']) AND strlen($_GET['t']) > 0) {
    $current_selected = htmlspecialchars($_GET['t']);
}

if(isset($_GET['s']) AND !empty($_GET['s']) AND is_numeric($_GET['s']) AND strlen($_GET['s']) > 0) {
    $selected = htmlspecialchars($_GET['s']);
}

if(isset($_GET['q']) AND !empty($_GET['q'])) {
    $query_lentgh = strlen($_GET['q']);
    if($query_lentgh >= 4 AND $query_lentgh <= 30) {
        $query = htmlspecialchars($_GET['q']);
        $query_exist = true;
        if(isset($_GET['t']) AND !empty($_GET['t']) AND is_numeric($_GET['t']) AND strlen($_GET['t']) > 0) {
            $type = htmlspecialchars($_GET['t']);
            if($type == "1") {
                $result_sql = $database->prepare("SELECT * FROM account WHERE username LIKE ? ORDER BY id");
                $result_sql->execute(array(
                    '%' . $query . '%'
                ));
                $total_result = $result_sql->rowCount();
                if($total_result == 0) {
                    $_SESSION['flash']['danger'] = 'Aucun résultat pour "<strong>' . $query . '</strong>".';
                    header("Location: search?s=" . $current_selected);
                    exit();
                }
            } elseif($type == "2") {
                $result_sql = $database2->prepare("SELECT * FROM msp_account WHERE msp_username LIKE ? ORDER BY id");
                $result_sql->execute(array(
                    '%' . $query . '%'
                ));
                $total_result = $result_sql->rowCount();
                if($total_result == 0) {
                    $_SESSION['flash']['danger'] = "Le compte MSP <strong>" . $query . "</strong> n\'est associé avec aucun profil <strong>MovieBook</strong>.";
                    header("Location: search?s=" . $current_selected);
                    exit();
                }
            } else {
                header("Location: search");
                exit();
            }
        } else {
            header("Location: search");
            exit();
        }
    } else {
        $query_exist = false;
    }
} else {
    $query_exist = false;
}

$current_page_title = "Rechercher";
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title">Rechercher</h1>
</center>
<br>
<?php if($query_exist == false) { ?>
<div class="panel panel-default">
   <div class="panel-body">
      <div class="row">
         <div class="col-md-10">
            <div class="form-group">
               <input class="form-control input-lg" type="text" placeholder="Je recherche... (minimum 4 caractères)" maxlength="30" id="mb_search_text_box" autofocus>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <select class="form-control input-lg" id="search_type_select">
                  <option id="1">Utilisateur</option>
                  <option <?php if(isset($selected) AND !empty($selected) AND $selected == "2") { echo "selected"; } ?> id="2">Pseudo MSP</option>
               </select>
            </div>
         </div>
      </div>
   </div>
</div>
<div align="center">
   <a class="btn btn-primary btn-lg" id="mb_search_button">C'est parti !</a>
</div>
<?php } elseif($total_result) { ?>
<a href="search?s=<?= $current_selected ?>" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left"></span> Faire une autre recherche</a>
<?php if($type == "1") { ?>
<h5 class="text-white"><?php if($total_result == 1) { echo "Un profil MovieBook trouvé pour <strong>".$query."</strong> :"; } else { echo "<strong>".$total_result."</strong> profils MovieBook trouvés pour <strong>".$query."</strong> :"; } ?></h5>
<?php } elseif($type == 2) { ?>
<h5 class="text-white"><?php if($total_result == 1) { echo "Le compte MSP <strong>".$query."</strong> est associé avec ce profil MovieBook :"; } else { echo "Le compte MSP <strong>".$query."</strong> est associé avec <strong>".$total_result."</strong> profils MovieBook :"; } ?></h5>
<?php } ?>
<div class="row">
   <?php while($account_data = $result_sql->fetch()) {
      if($type == "1") {
         $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
         $sql->execute(array($account_data['id']));
         $account_data = $sql->fetch();

         if(is_profile_exist($account_data['id'])) {
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array($account_data['id']));
            $profile_data = $sql->fetch();
         }
      } elseif($type == "2") {
         $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
         $sql->execute(array($account_data['username_id']));
         $account_data = $sql->fetch();

         if(is_profile_exist($account_data['id'])) {
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array($account_data['id']));
            $profile_data = $sql->fetch();
         }
      }
      ?>
   <div class="col-md-3">
      <div class="panel panel-default">
         <div class="panel-body">
            <?php if(is_profile_exist($account_data['id'])) { ?>
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
                  <button class="btn btn-success add_friend_button" user_id="<?= $account_data['id'] ?>" page="search" name="add_friend_button" id="add_friend_button"><span class="glyphicon glyphicon-plus"></span> Ajouter</button>
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
            <?php } else { ?>
            <div class="media">
               <div class="media-left media-top">
                  <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="../img/moviebook/avatars/default_avatar_m.png"></a>
               </div>
               <div class="media-body">
                  <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a>
               </div>
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
<?php } ?>
<?php require '../includes/footer.php'; ?>