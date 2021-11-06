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

if(isset($_GET['u']) AND !empty($_GET['u']) AND is_numeric($_GET['u']) AND strlen($_GET['u']) > 0) {
    $id = htmlspecialchars($_GET['u']);
    if(is_profile_exist($id)) {
        $sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC');
        $sql->execute(array(
            $id,
            $id
        ));
        $total_of_friends = $sql->rowCount();

        if($total_of_friends > 52) {
            $friends_per_page = 52;
            $total_of_page = ceil($total_of_friends / $friends_per_page);

            if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
                $p = htmlspecialchars($_GET['p']);
                $current_page = $p;
            } else {
                $current_page = 1;
            }

            $page_start = ($current_page - 1) * $friends_per_page;

            $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC LIMIT ' . $page_start . ',' . $friends_per_page);
            $friends_sql->execute(array(
                $id,
                $id
            ));
        } else {
            $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC');
            $friends_sql->execute(array(
                $id,
                $id
            ));
        }

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $id
        ));
        $account_data = $sql->fetch();

        if($id == $_SESSION['id']) {
            $it_is_my_page = true;
        } else {
            $it_is_my_page = false;
        }
    } else {
        header('Location: friends?u=' . $_SESSION['id']);
    }
} else {
    header("Location: friends?u=" . $_SESSION['id']);
}

$current_page_title = "Liste d'amis de " . $account_data['username'];
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title"><?php if($it_is_my_page == true) { echo "Mes amis"; } else { echo "Sa liste d'amis"; } ?></h1>
</center>
<br>
<?php if($total_of_friends != 0) { ?>
<h5 class="text-white"><?php if($it_is_my_page == true) { echo "Tu as <strong><span id='number_of_friend_span'>".$total_of_friends."</span></strong> ami(s)."; } else { echo "<strong>".$account_data['username']."</strong> a <strong>".$total_of_friends."</strong> ami(s)."; } ?></h5>
<div class="row">
   <?php while($friends_data = $friends_sql->fetch()) {
      if($friends_data['user_one'] == $id) {
        $user_friend = "user_two";
      } else {
        $user_friend = "user_one";
      }

      $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($friends_data[$user_friend]));
      $account_data = $sql->fetch();

      $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
      $sql->execute(array($friends_data[$user_friend]));
      $profile_data = $sql->fetch(); ?>
   <span id="friend_form_<?= $friends_data['id'] ?>">
      <div class="col-md-3">
         <div class="panel panel-default">
            <div class="panel-body">
               <div class="media">
                  <div class="media-left media-top">
                     <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
                  </div>
                  <div class="media-body">
                     <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?php if(strlen($account_data['username']) >= 20) { echo substr($account_data['username'], 0, 17)."..."; } else { echo $account_data['username']; } ?></a> <?= badge_check($account_data['id'], "other") ?>
                     <?php if($it_is_my_page == true) { ?>
                     <br>
                     <button class="btn btn-danger delete_friend_button" page="friends" user_id="<?= $friends_data[$user_friend] ?>" name="delete_friend_button" id="delete_friend_button"><span class="glyphicon glyphicon-remove"></span> Supprimer</button>
                     <?php } elseif($_SESSION['id'] != $account_data['id'] AND is_friends($_SESSION['id'], $account_data['id']) == false) {
                        $from_query = $database2->prepare('SELECT * FROM friend_request WHERE from_id = ? AND to_id = ?');
                        $from_query->execute(array($_SESSION['id'],$account_data['id']));
                        if($from_query->rowCount() == 0) { ?>
                     <br>
                     <span id="spawn_text_friend_request_sent_span_<?= $account_data['id'] ?>"></span>
                     <span id="add_friend_button_span_<?= $account_data['id'] ?>">
                     <button class="btn btn-success add_friend_button" page="friends" user_id="<?= $account_data['id'] ?>" name="add_friend_button" id="add_friend_button"><span class="glyphicon glyphicon-plus"></span> Ajouter</button>
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
   </span>
   <?php } ?>
</div>
<?php if($total_of_friends > 52) { ?>
<div class="row">
   <div class="col-md-4 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="friends?u=<?= $id ?>&p=<?= $current_page - 1 ?>">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="friends?u=<?= $id ?>&p=<?= $i ?>"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="friends?u=<?= $id ?>&p=<?= $current_page + 1 ?>">&raquo;</a></li>
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
<?php } ?>
<?php } else { ?>
<div align="center">
   <h5 class="text-white"><?php if($it_is_my_page == true) { echo "Tu n'as aucun ami. :'("; } else { echo "<strong>".$account_data['username']."</strong> n'a aucun ami."; } ?></h5>
</div>
<?php } ?>
<?php require '../includes/footer.php'; ?>