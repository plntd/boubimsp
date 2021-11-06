<?php
include('../includes/config.php');

if(is_connected() == false) {
   $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accèder à cette page.";
   header('Location: ../login?r='.$_SERVER['REQUEST_URI']);
   exit();
}

$total_of_items_sql = $database2->prepare('SELECT * FROM users_online');
$total_of_items_sql->execute();
$total_of_items = $total_of_items_sql->rowCount();

$items_per_page = 28;
$total_of_page = ceil($total_of_items / $items_per_page);

if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
  $p = htmlspecialchars($_GET['p']);
  $current_page = $p;
} else {
  $current_page = 1;
}

$page_start = ($current_page - 1) * $items_per_page;

$users_online_sql_2 = $database2->prepare('SELECT * FROM users_online WHERE deleted = 1 ORDER BY last_connection DESC LIMIT ' . $page_start . ',' . $items_per_page);
$users_online_sql_2->execute();

$current_page_title = "Utilisateurs en ligne (".$users_online_count.")";
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title">Utilisateurs en ligne</h1>
</center>
<br>
<h5 class="text-white">Il y a <strong><?= $users_online_count ?> utilisateur<?php if($users_online_count > 1) { echo "s"; } ?></strong> actif<?php if($users_online_count > 1) { echo "s"; } ?> en ce moment même.</h5>
<div class="row">
   <?php while($users_online_data = $users_online_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($users_online_data['username_id']));
      $account_data = $sql->fetch();

      $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
      $sql->execute(array($account_data['id']));
      $profile_data = $sql->fetch(); ?>
   <div class="col-md-3">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="media">
               <div class="media-left media-top">
                  <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?php echo "../img/moviebook/avatars/"; if(is_profile_exist($account_data['id'])) { echo $profile_data['avatar']; } else { echo "default_avatar_m.png"; } ?>"></a>
               </div>
               <div class="media-body">
                  <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?php if(strlen($account_data['username']) >= 20) { echo substr($account_data['username'], 0, 17)."..."; } else { echo $account_data['username']; } ?></a>
                  <?php if(is_profile_exist($account_data['id'])) {
                  if($_SESSION['id'] != $account_data['id'] AND is_friends($_SESSION['id'], $account_data['id']) == false) {
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
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
<br>
<h5 class="text-white">Historique de connexions :</h5>
<div class="row">
   <?php while($users_online_data = $users_online_sql_2->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($users_online_data['username_id']));
      $account_data = $sql->fetch();

      $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
      $sql->execute(array($account_data['id']));
      $profile_data = $sql->fetch(); ?>
   <div class="col-md-3">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="media">
               <div class="media-left media-top">
                  <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?php echo "../img/moviebook/avatars/"; if(is_profile_exist($account_data['id'])) { echo $profile_data['avatar']; } else { echo "default_avatar_m.png"; } ?>"></a>
               </div>
               <div class="media-body">
                  <span class="time-ago"><?= time_ago($users_online_data['last_connection']) ?></span>
                  <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?php if(strlen($account_data['username']) >= 20) { echo substr($account_data['username'], 0, 17)."..."; } else { echo $account_data['username']; } ?></a>
                  <?php if(is_profile_exist($account_data['id'])) {
                  if($_SESSION['id'] != $account_data['id'] AND is_friends($_SESSION['id'], $account_data['id']) == false) {
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
                  <li><a href="usersOnline?p=<?= $current_page - 1 ?>#p">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="usersOnline?p=<?= $i ?>#p"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="usersOnline?p=<?= $current_page + 1 ?>#p">&raquo;</a></li>
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
<?php require '../includes/footer.php'; ?>