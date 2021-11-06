<?php
include('../includes/config.php');

if(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id'])) {
    $subject_id = htmlspecialchars($_GET['id']);
    $sql = $database->prepare('SELECT * FROM subject WHERE id = ? AND deleted = 0');
    $sql->execute(array(
        $subject_id
    ));
    if($sql->rowCount() == 1) {
        $subject_data = $sql->fetch();

        $web_description = $subject_data['description'];

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $subject_data['username_id']
        ));
        $owner_account_data = $sql->fetch();

        $current_page_title = "Sujet de " . $owner_account_data['username'];

        if(is_profile_exist($subject_data['username_id']) == true) {
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array(
                $subject_data['username_id']
            ));
            $owner_profile_data = $sql->fetch();

            $profile_picture = $owner_profile_data['avatar'];
        } else {
            $profile_picture = "default_avatar_m.png";
        }

        if(is_connected() == true AND $subject_data['username_id'] == $_SESSION['id']) {
            $is_my_subject = true;
        } else {
            $is_my_subject = false;
        }

        if(is_connected() == true) {
            $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND username_id = ? AND deleted = 0');
            $sql->execute(array(
                $subject_data['id'],
                $_SESSION['id']
            ));
            if($sql->rowCount() > 0) {
                $comments_data = $sql->fetch();
                $notification_new_comment = $comments_data['notification_new_comment'];

                $user_commented = true;
            } else {
                $user_commented = false;
            }
        }

        $total_of_comments_sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND deleted = 0');
        $total_of_comments_sql->execute(array(
            $subject_id
        ));
        $total_of_comments = $total_of_comments_sql->rowCount();

        $comments_per_page = 15;
        $total_of_page = ceil($total_of_comments / $comments_per_page);

        if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
            $p = htmlspecialchars($_GET['p']);
            $current_page = $p;
        } else {
            $current_page = 1;
        }

        $page_start = ($current_page - 1) * $comments_per_page;

        $subject_comments_sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND deleted = 0 ORDER BY id LIMIT ' . $page_start . ',' . $comments_per_page);
        $subject_comments_sql->execute(array(
            $subject_id
        ));

    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "L\'ID du sujet est <strong>invalide</strong>. Il a peut-être été supprimé.";
        header("location: home");
        exit();
    }
} else {
    header("location: home");
    exit();
}

$subject_pinned_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 1 AND deleted = 0 ORDER BY last_comment_date DESC');
$subject_pinned_sql->execute();

$subject_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 0 AND deleted = 0 ORDER BY last_comment_date DESC LIMIT 17');
$subject_sql->execute();

require '../includes/header.php';

?>
<br><br>
<a href="home" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left"></span></a>
<br><br>
<input type="hidden" id="subject_id_hidden" name="subject_id_hidden" value="<?= $subject_data['id'] ?>">
<div class="row">
   <div class="col-md-8">
      <div class="panel panel-default">
         <div class="panel-body">
            <?php if($is_my_subject == true) { ?>
            <a class="btn btn-warning pull-right" data-toggle="modal" data-target="#settings_my_subject_modal"><span class="glyphicon glyphicon-cog"></span></a>
            <?php } elseif(is_connected() == true AND $user_commented == true) { ?>
            <button class="btn btn-<?php if($notification_new_comment == 1) { echo "success"; } else { echo "default"; } ?> pull-right" id="forum_update_notification_new_comments_subject_button" name="forum_update_notification_new_comments_subject_button"><span class="glyphicon glyphicon-bell"></span></button>
            <?php } ?>
            <h5><?php if($subject_data['pinned'] == 1) { echo '<span class="glyphicon glyphicon-pushpin"></span> '; } ?><?= $subject_data['title'] ?></h5>
            <legend></legend>
            <div class="media">
               <div class="media-left">
                  <a href="../moviebook/profile?u=<?= $owner_account_data['id'] ?>">
                  <img class="media-object" height="64" width="64" src="../img/moviebook/avatars/<?= $profile_picture ?>">
                  </a>
               </div>
               <div class="media-body">
                  <a href="../moviebook/profile?u=<?= $owner_account_data['id'] ?>" class="media-heading"><?= $owner_account_data['username'] ?></a> <?= badge_check($owner_account_data['id'], "other") ?> <label><small><?= time_ago($subject_data['creation_date']) ?></small></label><br>
                  <?= return_string($subject_data['description']) ?>
               </div>
            </div>
         </div>
      </div>
      <div class="panel panel-default">
         <div class="panel-body">
            <?php if(is_connected() == true) { ?>
            <textarea class="form-control" rows="3" id="forum_comment_subject_textarea" name="forum_comment_subject_textarea" maxlength="5000" style="resize: none;" placeholder="Ecris ton commentaire..."></textarea>
            <br>
            <button class="btn btn-success" id="forum_send_comment_subject_button" name="forum_send_comment_subject_button">Envoyer le commentaire</button>
            <a class="btn btn-default pull-right" data-toggle="modal" data-target="#smileys_modal"><span class="glyphicon glyphicon-plus"></span> Smiley</a><br><br>
            <?php } else { ?>
            <textarea class="form-control" rows="3" disabled style="resize: none;" placeholder="Ecris ton commentaire..."></textarea>
            <br>
            <button class="btn btn-success" disabled>Envoyer le commentaire</button>
            <a class="btn btn-default pull-right" disabled><span class="glyphicon glyphicon-plus"></span> Smiley</a><br><br>
            <div align="center">
               <label>Tu dois être connecté pour poster un commentaire. <a href="../login?r=<?= $_SERVER['REQUEST_URI'] ?>">Clique ici</a> pour te connecter.</label>
            </div>
            <?php } ?>
            <br>
            <?php if($total_of_comments > 0) {
               $i = 0;
               while($subject_comments_data = $subject_comments_sql->fetch()) {
                 $i++;
                $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                $sql->execute(array($subject_comments_data['username_id']));
                $account_data = $sql->fetch();

                if(is_profile_exist($subject_comments_data['username_id']) == true) {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
                $sql->execute(array($subject_comments_data['username_id']));
                $profile_data = $sql->fetch();

                $profile_picture = $profile_data['avatar'];
                } else {
                $profile_picture = "default_avatar_m.png";
                }
                if($i == $total_of_comments - 1) {
                 echo '<div id="gtb"></div>';
               } ?>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="media">
                     <div class="media-left">
                        <a href="../moviebook/profile?u=<?= $account_data['id'] ?>">
                        <img class="media-object" height="64" width="64" src="../img/moviebook/avatars/<?= $profile_picture ?>">
                        </a>
                     </div>
                     <div class="media-body">
                        <a href="../moviebook/profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($subject_comments_data['comment_date']) ?></small></label><?php if(is_connected() == true AND $_SESSION['id'] == $subject_comments_data['username_id']) { ?>
                        <a class="btn btn-danger btn-sm pull-right" modal_type="forum_delete_comment" data-comment_id="<?= $subject_comments_data['id'] ?>" data-toggle="modal" data-target="#delete_comment_modal"><span class="glyphicon glyphicon-trash"></span></a>
                        <?php } ?><br>
                        <?= return_string($subject_comments_data['content']) ?>
                     </div>
                  </div>
               </div>
            </div>
            <?php } ?>
            <?php if($total_of_comments > 15) { ?>
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="subject?id=<?= $subject_data['id'] ?>&p=<?= $current_page - 1 ?>">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="subject?id=<?= $subject_data['id'] ?>&p=<?= $i ?>"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="subject?id=<?= $subject_data['id'] ?>&p=<?= $current_page + 1 ?>">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
            </center>
            <?php } ?>
            <?php } else { ?>
            <div align="center">
               <h4>Il n'y a aucun commentaire. Sois le premier à en poster un !</h4>
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="panel panel-default">
         <div class="panel-body">
            <h5>Les derniers sujets</h5>
            <legend></legend>
            <div class="list-group">
              <?php while($other_subject_data_pinned = $subject_pinned_sql->fetch()) { ?>
              <a href="subject?id=<?= $other_subject_data_pinned['id'] ?>" class="list-group-item<?php if($other_subject_data_pinned['id'] == $subject_id) { echo " active"; } ?>">
              <span class="glyphicon glyphicon-pushpin"></span> <?= $other_subject_data_pinned['title'] ?>
              </a>
              <?php } ?>
               <?php while($other_subject_data = $subject_sql->fetch()) { ?>
               <a href="subject?id=<?= $other_subject_data['id'] ?>" class="list-group-item<?php if($other_subject_data['id'] == $subject_id) { echo " active"; } ?>">
               <?= $other_subject_data['title'] ?>
               </a>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</div>
<?php if($is_my_subject == true) { ?>
<!-- Modal -->
<div class="modal fade" id="settings_my_subject_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Modifier mon sujet</h4>
            </div>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label class="control-label" for="subject_name_text_box">Titre du sujet</label>
                     <input type="text" class="form-control" id="subject_name_text_box" placeholder="Ecris le titre de ton sujet..." name="subject_name_text_box" maxlength="60" value="<?= $subject_data['title'] ?>">
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="description_text_area">Description</label>
                     <textarea class="form-control" rows="5" id="description_text_area" name="description_text_area" maxlength="5000" placeholder="Ecris ta description..." style="resize: none;"><?= $subject_data['description'] ?></textarea>
                  </div>
                  <label>
                  <input type="checkbox" name="notification_new_comment_checkbox" id="notification_new_comment_checkbox" <?php if($subject_data['notification_new_comment'] == 1) { echo "checked"; } ?>> Recevoir une notification à chaque nouveau commentaire</a>
                  </label>
                  <?php if($owner_account_data['subject_pin_token'] > 0 AND $subject_data['pinned'] == 0) { ?>
                    <div align="center">
                      <a class="btn btn-warning" data-toggle="modal" data-target="#pin_my_subject_modal" data-dismiss="modal"><span class="glyphicon glyphicon-pushpin"></span> Épingler mon sujet</a>
                    </div>
                  <?php } elseif($subject_data['pinned'] == 0) { ?>
                    <div align="center">
                      <span data-toggle="tooltip" title="Tu ne disposes d'aucun jeton pour épingler ton sujet."><a class="btn btn-warning" disabled><span class="glyphicon glyphicon-pushpin"></span> Épingler mon sujet</a></span>
                    </div>
                    <?php } elseif($subject_data['pinned'] == 1) {
                    $pin_date = strtotime($subject_data['pin_date']);
                    $future = strtotime("+1 week", $pin_date);
                    $timeleft = $future - time();
                    $daysleft = round((($timeleft/24)/60)/60); ?>
                    <div align="center">
                      <strong>Ton sujet est encore épinglé pour <?= $daysleft ?> jours.</strong>
                    </div>
                  <?php } ?>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" id="update_settings_my_subject_button" name="update_settings_my_subject_button"><span class="glyphicon glyphicon-ok"></span> Sauvegarder</button>
               <a class="btn btn-danger" data-toggle="modal" data-target="#delete_my_subject_modal" data-dismiss="modal"><span class="glyphicon glyphicon-trash"></span> Supprimer mon sujet</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="delete_my_subject_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Supprimer mon sujet</h4>
            </div>
            <p>Es-tu sûr(e) de supprimer ton sujet ? Tous les commentaires seront aussi supprimées.<br>
               Cette action est <strong>irréversible</strong>.
            </p>
            <div align="center">
               <button class="btn btn-success" id="forum_delete_my_subject_button" name="forum_delete_my_subject_button"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#settings_my_subject_modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if(is_connected() == true) { ?>
<!-- Modal -->
<?= smileys_modal("forum_new_comment_subject") ?>
<!-- Modal -->
<div class="modal fade" id="delete_comment_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Supprimer ce commentaire</h4>
            </center>
            <p>Es-tu sûr(e) de supprimer ce commentaire ?<br>
               Cette action est <strong>irréversible</strong>.
            </p>
            <input type="hidden" name="delete_subject_comment_id_hidden" id="delete_subject_comment_id_hidden">
            <div align="center">
               <button class="btn btn-success" id="forum_delete_comment_button" name="forum_delete_comment_button"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($is_my_subject AND $owner_account_data['subject_pin_token'] > 0 AND $subject_data['pinned'] == 0) { ?>
<!-- Modal -->
<div class="modal fade" id="pin_my_subject_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Épingler mon sujet</h4>
            </center>
            <p>Tu as actuellement <strong><?= $owner_account_data['subject_pin_token'] ?></strong> jetons pour épingler les sujets.<br>
              Si tu l'épingles, ton sujet sera en tout premier quoi qu'il arrive !<br>
              Il sera épinglé pendant une semaine.<br><br>
              Es-tu sûr(e) d'utiliser un jeton ?</p>
            <input type="hidden" name="delete_subject_comment_id_hidden" id="delete_subject_comment_id_hidden">
            <div align="center">
               <button class="btn btn-success" id="pin_my_subject_button" name="pin_my_subject_button"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#settings_my_subject_modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php require '../includes/footer.php'; ?>
<script type="text/javascript">
  <?php if(isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "gtb") { ?>
  $('html, body').animate({
  scrollTop: $("#gtb").offset().top
  }, 1000);
  <?php } ?>
</script>