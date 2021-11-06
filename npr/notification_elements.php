<?php
include('../includes/config.php');

if(is_connected() == false) {
    exit();
}

$account_data_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
$account_data_sql->execute(array(
    $_SESSION['id']
));
$my_account_data = $account_data_sql->fetch();

$notifications_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? ORDER BY id DESC');
$notifications_sql->execute(array(
    $_SESSION['id']
));
$notifications_count = $notifications_sql->rowCount();

if($my_account_data['notification_view'] == 0) {
    $notification_view = 0;

    $notifications_important_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_important_sql->execute(array(
        $_SESSION['id'],
        "important_custom_notification"
    ));
    $notifications_important_count = $notifications_important_sql->rowCount();

    $notifications_rare_order_completed_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_rare_order_completed_sql->execute(array(
        $_SESSION['id'],
        "rare_order_completed"
    ));
    $notifications_rare_order_completed_count = $notifications_rare_order_completed_sql->rowCount();

    $notifications_friend_request_accepted_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_friend_request_accepted_sql->execute(array(
        $_SESSION['id'],
        "friend_request_accepted"
    ));
    $notifications_friend_request_accepted_count = $notifications_friend_request_accepted_sql->rowCount();

    $notifications_new_post_on_my_wall_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_new_post_on_my_wall_sql->execute(array(
        $_SESSION['id'],
        "new_post_on_wall"
    ));
    $notifications_new_post_on_my_wall_count = $notifications_new_post_on_my_wall_sql->rowCount();

    $notifications_reply_post_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_reply_post_sql->execute(array(
        $_SESSION['id'],
        "new_reply_on_post",
        $_SESSION['id'],
        "new_reply_on_post_replied"
    ));
    $notifications_reply_post_count = $notifications_reply_post_sql->rowCount();

    $notifications_my_contest_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_my_contest_sql->execute(array(
        $_SESSION['id'],
        "new_participant_contest",
        $_SESSION['id'],
        "new_contest_comment"
    ));
    $notifications_my_contest_count = $notifications_my_contest_sql->rowCount();

    $notifications_my_subjects_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_my_subjects_sql->execute(array(
        $_SESSION['id'],
        "new_comment_my_subject"
    ));
    $notifications_my_subjects_count = $notifications_my_subjects_sql->rowCount();

    $notifications_new_post_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_new_post_sql->execute(array(
        $_SESSION['id'],
        "new_post_wall_friend",
    ));
    $notifications_new_post_count = $notifications_new_post_sql->rowCount();

    $notifications_contest_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_contest_sql->execute(array(
        $_SESSION['id'],
        "contest_deleted_by_author",
        $_SESSION['id'],
        "contest_deleted_by_admin",
        $_SESSION['id'],
        "contest_time_out",
        $_SESSION['id'],
        "participant_deleted_by_author",
        $_SESSION['id'],
        "new_contest_comment_by_author",
        $_SESSION['id'],
        "new_contest_friend",
        $_SESSION['id'],
        "contest_ended"
    ));
    $notifications_contest_count = $notifications_contest_sql->rowCount();

    $notifications_subject_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? ORDER BY id DESC');
    $notifications_subject_sql->execute(array(
        $_SESSION['id'],
        "new_subject_friend",
        $_SESSION['id'],
        "new_comment_subject"
    ));
    $notifications_subject_count = $notifications_subject_sql->rowCount();
} else {
    $notification_view = 1;
}

if($notifications_count != 0) { ?>
<div class="btn-group" style="float: right;">
   <a href="#!" class="btn btn-default btn-sm <?php if($notification_view == 0) { echo " disabled"; } else { echo "notification_view_button"; } ?> ">Affichage organisé</a>
   <a href="#!" class="btn btn-default btn-sm <?php if($notification_view == 1) { echo " disabled"; } else { echo "notification_view_button"; } ?>">Affichage classique</a>
</div>
<?php if($notification_view == 0) { ?>
<?php if($notifications_important_count > 0) { ?>
<div class="row">
   <div class="col-md-6">
      <h5>Important <strong>(<?= $notifications_important_count ?>)</strong></h5>
      <legend></legend>
   </div>
</div>
<?php while($notifications_data = $notifications_important_sql->fetch()) { ?>
<?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
   $sql->execute(array($notifications_data['from_id']));
   $account_data = $sql->fetch();
   $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
<span id="notification_form_<?= $notifications_data['id'] ?>">
   <div class="panel panel-default">
      <div class="panel-body">
         <div class="row">
            <div class="col-md-7">
               <strong style="color: #e74c3c">IMPORTANT :</strong> <?= $notifications_data['content'] ?>
            </div>
            <div class="col-md-5">
               <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" disabled>Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
            </div>
         </div>
      </div>
   </div>
</span>
<?php } ?>
<br>
<?php } ?>
<?php if($notifications_rare_order_completed_count > 0) { ?>
<span id="notif_group_rare_order_completed">
  <div class="row">
     <div class="col-md-6">
        <h5>Rares <strong>(<?= $notifications_rare_order_completed_count ?>)</strong><?php if($notifications_rare_order_completed_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_rare_order_completed" notif_id="nothing">Tout effacer</button><?php } ?></h5>
        <legend></legend>
     </div>
  </div>
  <?php while($notifications_data = $notifications_rare_order_completed_sql->fetch()) { ?>
  <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
     $sql->execute(array($notifications_data['from_id']));
     $account_data = $sql->fetch();
     $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
  <span id="notification_form_<?= $notifications_data['id'] ?>">
     <div class="panel panel-default">
        <div class="panel-body">
           <div class="row">
              <div class="col-md-7">
                 <strong style="color: #10ac84">Rare :</strong> tu as reçu le rare <strong><?= convert_rare_id($notifications_data['content_id']) ?></strong> sur ton compte MSP.
              </div>
              <div class="col-md-5">
                 <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" disabled>Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
              </div>
           </div>
        </div>
     </div>
  </span>
  <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_friend_request_accepted_count > 0) { ?>
<span id="notif_group_friend_request_accepted">
   <div class="row">
      <div class="col-md-6">
         <h5>Demandes d'amis acceptées <strong>(<?= $notifications_friend_request_accepted_count ?>)</strong><?php if($notifications_friend_request_accepted_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_friend_request_accepted" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_friend_request_accepted_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> a accepté ta demande d'ami.
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" disabled>Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_new_post_on_my_wall_count > 0) { ?>
<span id="notif_group_new_post_on_wall">
   <div class="row">
      <div class="col-md-6">
         <h5>Nouvelles publications sur ton mur <strong>(<?= $notifications_new_post_on_my_wall_count ?>)</strong><?php if($notifications_new_post_on_my_wall_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_new_post_on_my_wall" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_new_post_on_my_wall_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> a publié quelque chose sur ton profil.
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="moviebook/post?id=<?= $notifications_data['content_id']; ?>">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_reply_post_count > 0) { ?>
<span id="notif_group_reply_post">
   <div class="row">
      <div class="col-md-6">
         <h5>Réponses à mes publications <strong>(<?= $notifications_reply_post_count ?>)</strong><?php if($notifications_reply_post_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_reply_post" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_reply_post_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <?php if($notifications_data['type'] == "new_reply_on_post") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> a répondu à ta publication.
                  <?php } elseif($notifications_data['type'] == "new_reply_on_post_replied") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a répondu à une publication que tu as déjà répondu.
                  <?php } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="moviebook/post?id=<?= $notifications_data['content_id'] ?>">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_my_contest_count > 0) { ?>
<span id="notif_group_my_contest">
   <div class="row">
      <div class="col-md-6">
         <h5>Mon concours <strong>(<?= $notifications_my_contest_count ?>)</strong><?php if($notifications_my_contest_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_my_contest" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_my_contest_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <?php if($notifications_data['type'] == "new_participant_contest") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a participé à ton concours.
                  <?php } elseif($notifications_data['type'] == "new_contest_comment") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a posté un commentaire sur ton concours.
                  <?php } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="contest/contest?c=<?= $notifications_data['content_id']; ?><?php if($notifications_data['type'] == "new_contest_comment") { echo "&t=gtb"; } ?>">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_my_subjects_count > 0) { ?>
<span id="notif_group_my_subjects">
   <div class="row">
      <div class="col-md-6">
         <h5>Mes sujets <strong>(<?= $notifications_my_subjects_count ?>)</strong><?php if($notifications_my_subjects_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_my_subjects" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_my_subjects_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a commenté ton sujet.
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="forum/subject?id=<?= $notifications_data['content_id']; ?>&t=gtb">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_new_post_count > 0) { ?>
<span id="notif_group_new_post">
   <div class="row">
      <div class="col-md-6">
         <h5>Nouvelles publications sur son mur <strong>(<?= $notifications_new_post_count ?>)</strong><?php if($notifications_new_post_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_new_post" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_new_post_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                <?php if($notifications_data['type'] == "new_post_wall_friend") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a publié du nouveau contenu sur son profil.
                  <?php } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="moviebook/profile?u=<?= $notifications_data['content_id'] ?>">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_contest_count > 0) { ?>
<span id="notif_group_contest">
   <div class="row">
      <div class="col-md-6">
         <h5>Concours <strong>(<?= $notifications_contest_count ?>)</strong><?php if($notifications_contest_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_contest" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_contest_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <?php if($notifications_data['type'] == "contest_deleted_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a supprimé son concours.
                <?php } elseif($notifications_data['type'] == "contest_deleted_by_admin") { ?>
                  <strong style="color: #e59726">Concours :</strong> Le concours de <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a été supprimé par un administrateur.
                <?php } elseif($notifications_data['type'] == "contest_time_out") { ?>
                    <strong style="color: #e59726">Concours :</strong> Le concours de <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a été supprimé car il existe depuis plus d'1 mois.
                    <?php } elseif($notifications_data['type'] == "participant_deleted_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> t'a supprimé de son concours.
                  <?php } elseif($notifications_data['type'] == "new_contest_comment_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a posté un commentaire sur son concours. Clique sur "Voir" pour le consulter.
                  <?php } elseif($notifications_data['type'] == "new_contest_friend") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a créé un concours. Clique sur "Voir" pour le consulter.
                  <?php } elseif($notifications_data['type'] == "contest_ended") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a terminé son concours. Clique sur "Voir" pour consulter les résultats.
                  <?php } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" <?php if($notifications_data['type'] == "contest_deleted_by_author" OR $notifications_data['type'] == "contest_deleted_by_admin" OR $notifications_data['type'] == "contest_time_out") { echo "disabled"; } else { ?> target="_blank" href="contest/<?php if($notifications_data['type'] == "contest_ended") { echo "endContest"; } else { echo "contest"; }?>?c=<?= $notifications_data['content_id']; ?><?php if($notifications_data['type'] == "new_contest_comment_by_author") { ?>&t=gtb<?php } ?>"<?php } ?>>Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_subject_count > 0) { ?>
<span id="notif_group_subject">
   <div class="row">
      <div class="col-md-6">
         <h5>Sujets <strong>(<?= $notifications_subject_count ?>)</strong><?php if($notifications_subject_count >= 2) { ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_all_subject" notif_id="nothing">Tout effacer</button><?php } ?></h5>
         <legend></legend>
      </div>
   </div>
   <?php while($notifications_data = $notifications_subject_sql->fetch()) { ?>
   <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <?php if($notifications_data['type'] == "new_subject_friend") { ?>
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a créé un nouveau sujet.
                  <?php } elseif($notifications_data['type'] == "new_comment_subject") { ?>
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a commenté un sujet que tu suis.
                  <?php } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <a class="btn btn-primary btn-sm" target="_blank" href="forum/subject?id=<?= $notifications_data['content_id']; ?><?php if($notifications_data['type'] == "new_comment_subject") { echo "&t=gtb"; } ?>">Voir</a> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
</span>
<br>
<?php } ?>
<?php if($notifications_count > 3 ) { ?>
<div align="center">
   <button class="btn btn-danger delete_notification_button" type="delete_all" notif_id="nothing">Supprimer toutes les notifications (<?= $notifications_count ?>)</button>
</div>
<?php } ?>
<?php } else { ?>
<br><br>
<span id="all_notification_form">
   <?php while($notifications_data = $notifications_sql->fetch()) {
      $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
      $sql->execute(array($notifications_data['from_id']));
      $account_data = $sql->fetch();
      $notification_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($notifications_data['notification_date']))); ?>
   <span id="notification_form_<?= $notifications_data['id'] ?>">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="row">
               <div class="col-md-7">
                  <?php if($notifications_data['type'] == "new_post_on_wall") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> a publié quelque chose sur ton profil.
                  <?php } elseif($notifications_data['type'] == "new_reply_on_post") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> a répondu à ta publication.
                  <?php } elseif($notifications_data['type'] == "new_participant_contest") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a participé à ton concours.
                  <?php } elseif($notifications_data['type'] == "new_contest_comment") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a posté un commentaire sur ton concours.
                  <?php } elseif($notifications_data['type'] == "contest_deleted_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a supprimé son concours.
                  <?php } elseif($notifications_data['type'] == "participant_deleted_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> t'a supprimé de son concours.
                  <?php } elseif($notifications_data['type'] == "new_contest_comment_by_author") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a posté un commentaire sur son concours.
                  <?php } elseif($notifications_data['type'] == "new_contest_friend") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a créé un concours.
                  <?php } elseif($notifications_data['type'] == "contest_ended") { ?>
                  <strong style="color: #e59726">Concours :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a terminé son concours. Clique sur "Voir" pour consulter les résultats.
                  <?php } elseif($notifications_data['type'] == "important_custom_notification") { ?>
                  <strong style="color: #e74c3c">IMPORTANT :</strong> <?= $notifications_data['content'] ?>
                  <?php } elseif($notifications_data['type'] == "new_post_wall_friend") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a publié du nouveau contenu sur son profil.
                  <?php } elseif($notifications_data['type'] == "new_reply_on_post_replied") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a répondu à une publication que tu as déjà répondu.
                  <?php } elseif($notifications_data['type'] == "friend_request_accepted") { ?>
                  <strong style="color: #8c49ec">MovieBook :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a accepté ta demande d'ami.
                  <?php } elseif($notifications_data['type'] == "new_comment_my_subject") { ?>
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a commenté ton sujet.
                  <?php } elseif($notifications_data['type'] == "new_subject_friend") { ?>
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a créé un nouveau sujet.
                  <?php } elseif($notifications_data['type'] == "new_comment_subject") { ?>
                  <strong style="color: #2CC7D2">Forum :</strong> <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a commenté un sujet que tu suis.
                <?php } elseif($notifications_data['type'] == "rare_order_completed") { ?>
                  <strong style="color: #10ac84">Rare :</strong> tu as reçu le rare <strong><?= convert_rare_id($notifications_data['content_id']) ?></strong> sur ton compte MSP.
                <?php } elseif($notifications_data['type'] == "contest_deleted_by_admin") { ?>
                  <strong style="color: #e59726">Concours :</strong> Le concours de <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a été supprimé par un administrateur.
                <?php } elseif($notifications_data['type'] == "contest_time_out") { ?>
                  <strong style="color: #e59726">Concours :</strong> Le concours de <a href="moviebook/profile?u=<?= $account_data['id']?>"><?= $account_data['username'] ?></a> a été supprimé car il a dépassé la limite de temps.
                <?php } ?>
                  <?php if(!empty($notifications_data['content_id'])) {
                      if($notifications_data['type'] == "new_post_on_wall" OR $notifications_data['type'] == "new_reply_on_post" OR $notifications_data['type'] == "new_reply_on_post_replied") {
                          $link_see_button = "moviebook/post?id=" . $notifications_data['content_id'];
                      } elseif($notifications_data['type'] == "new_contest_comment" OR $notifications_data['type'] == "new_contest_comment_by_author") {
                          $link_see_button = "contest/contest?c=" . $notifications_data['content_id'] . "&t=gtb";
                      } elseif($notifications_data['type'] == "new_participant_contest" OR $notifications_data['type'] == "participant_deleted_by_author" OR $notifications_data['type'] == "new_contest_friend") {
                          $link_see_button = "contest/contest?c=" . $notifications_data['content_id'];
                      } elseif($notifications_data['type'] == "contest_ended") {
                          $link_see_button = "contest/endContest?c=" . $notifications_data['content_id'];
                      } elseif($notifications_data['type'] == "new_comment_my_subject" OR $notifications_data['type'] == "new_comment_subject") {
                          $link_see_button = "forum/subject?id=" . $notifications_data['content_id'] . "&t=gtb";
                      } elseif($notifications_data['type'] == "new_subject_friend") {
                          $link_see_button = "forum/subject?id=" . $notifications_data['content_id'];
                      } elseif($notifications_data['type'] == "new_post_wall_friend") {
                        $link_see_button = "moviebook/profile?u=".$notifications_data['content_id'];
                      }
                  } else {
                      $link_see_button = NULL;
                  } ?>
               </div>
               <div class="col-md-5">
                  <span class="pull-right"><?= $notification_date ?> <?php if(!empty($link_see_button)) { ?><a class="btn btn-primary btn-sm" target="_blank" href="<?= $link_see_button ?>">Voir</a><?php } else { ?><a class="btn btn-primary btn-sm" disabled>Voir</a><?php } ?> <button class="btn btn-danger btn-sm delete_notification_button" type="delete_one" notif_id="<?= $notifications_data['id'] ?>">Supprimer la notification</button></span>
               </div>
            </div>
         </div>
      </div>
   </span>
   <?php } ?>
   <?php if($notifications_count > 3 ) { ?>
   <div align="center">
      <button class="btn btn-danger delete_notification_button" type="delete_all" notif_id="nothing">Supprimer toutes les notifications (<?= $notifications_count ?>)</button>
   </div>
   <?php } ?>
</span>
<?php } ?>
<?php } else { ?>
<div align="center">
   Tu n'as aucune notification.
</div>
<?php } ?>
<script type="text/javascript">
  $(document).ready(function() {

    var danger_sign = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ';
    var success_sign = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ';
    var warning_sign = '<span class="glyphicon glyphicon-alert" aria-hidden="true"></span> ';

      $(".delete_notification_button").click(function() {
          var button = this;
          var notif_id = $(button).attr('notif_id');
          var type = $(button).attr('type');
          button.disabled = true;

          $.post("action", {
                  action: "delete_notification",
                  notif_id: notif_id,
                  type: type
              },

              function(data, status) {
                  button.disabled = false;
                  if(status == "success") {
                      var JSON_data = $.parseJSON(data);
                      var status_1 = JSON_data.status;
                      if(status_1 == "SUCCESS") {
                          if(type == "delete_one") {
                              document.getElementById("notification_form_" + notif_id).remove();
                          } else if(type == "delete_all") {
                              window.location.replace("account?t=n");
                          } else if(type == "delete_all_rare_order_completed") {
                              document.getElementById("notif_group_rare_order_completed").remove();
                          }else if(type == "delete_all_friend_request_accepted") {
                              document.getElementById("notif_group_friend_request_accepted").remove();
                          } else if(type == "delete_all_new_post_on_my_wall") {
                              document.getElementById("notif_group_new_post_on_wall").remove();
                          } else if(type == "delete_all_reply_post") {
                              document.getElementById("notif_group_reply_post").remove();
                          } else if(type == "delete_all_my_contest") {
                              document.getElementById("notif_group_my_contest").remove();
                          } else if(type == "delete_all_my_subjects") {
                              document.getElementById("notif_group_my_subjects").remove();
                          } else if(type == "delete_all_new_post") {
                              document.getElementById("notif_group_new_post").remove();
                          } else if(type == "delete_all_contest") {
                              document.getElementById("notif_group_contest").remove();
                          } else if(type == "delete_all_subject") {
                              document.getElementById("notif_group_subject").remove();
                          }
                      } else if(status_1 == "TOO_FAST") {
                          $.bootstrapGrowl(warning_sign + "Hé ! Tu vas un petit peu vite là. <strong>Réessaie dans quelques secondes</strong>.", {
                              type: 'warning',
                              width: 'auto',
                              allow_dismiss: false
                          });
                      } else {
                          $.bootstrapGrowl(danger_sign + "Une erreur inconnue s'est produite.", {
                              type: 'danger',
                              width: 'auto',
                              allow_dismiss: false
                          });
                      }
                  } else {
                      $.bootstrapGrowl(danger_sign + "Désolé mais le serveur est <strong>indisponible</strong>.", {
                          type: 'danger',
                          width: 'auto',
                          allow_dismiss: false
                      });
                  }
              });
      });

      $(".notification_view_button").click(function() {
          var button = this;
          button.disabled = true;

          $.post("action", {
                  action: "notification_view_update"
              },

              function(data, status) {
                  button.disabled = false;
                  if(status == "success") {
                      var JSON_data = $.parseJSON(data);
                      var status_1 = JSON_data.status;
                      if(status_1 == "SUCCESS") {
                          window.location.replace("account?t=n");
                      } else if(status_1 == "TOO_FAST") {
                          $.bootstrapGrowl(warning_sign + "Hé ! Tu vas un petit peu vite là. <strong>Réessaie dans quelques secondes</strong>.", {
                              type: 'warning',
                              width: 'auto',
                              allow_dismiss: false
                          });
                      } else {
                          $.bootstrapGrowl(danger_sign + "Une erreur inconnue s'est produite.", {
                              type: 'danger',
                              width: 'auto',
                              allow_dismiss: false
                          });
                      }
                  } else {
                      $.bootstrapGrowl(danger_sign + "Désolé mais le serveur est <strong>indisponible</strong>.", {
                          type: 'danger',
                          width: 'auto',
                          allow_dismiss: false
                      });
                  }
              });
      });
  });
</script>
