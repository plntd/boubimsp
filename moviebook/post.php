<?php
include('../includes/config.php');

if(is_connected() == false) {
   $is_connected = false;
} else {
   $is_connected = true;
}

if($is_connected == true AND is_profile_exist($_SESSION['id']) == false) {
    $_SESSION['flash']['danger'] = $danger_sign . 'Tu dois avoir un compte <strong>MovieBook</strong> pour accèder à cette page. Crée en toi un dès maintenant !';
    header('Location: profile?u='.$_SESSION['id']);
    exit();
}

if(isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "ap" AND isset($_GET['u']) AND !empty($_GET['u']) AND is_numeric($_GET['u']) AND strlen($_GET['u']) > 0) {
    $profile_id = htmlspecialchars($_GET['u']);
    if(is_profile_exist($profile_id) == true) {
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $profile_id
        ));
        $wall_data = $sql->fetch();
        $profile_id = $wall_data['id'];
        $current_page_title = "Toutes les publications du mur de " . $wall_data['username'];

        if($is_connected == true) {
         $my_account_sql = $database->prepare('SELECT admin_access FROM account WHERE id = ?');
         $my_account_sql->execute(array(
               $_SESSION['id']
         ));
         $my_account_data = $my_account_sql->fetch();
        }

        $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND deleted = 0 ORDER BY id DESC');
        $sql->execute(array(
            $profile_id
        ));
        $total_post_on_wall = $sql->rowCount();

        $post_per_page = 20;
        $total_of_page = ceil($total_post_on_wall / $post_per_page);

        if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
            $p = htmlspecialchars($_GET['p']);
            $current_page = $p;
        } else {
            $current_page = 1;
        }

        $type = "all_post";
    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "Cet ID de profil est <strong>incorrect</strong>.";
        header("Location: home");
        exit();
    }
} elseif(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id']) AND strlen($_GET['id']) > 0) {
    $id = htmlspecialchars($_GET['id']);
    $type = "one_post";
    $current_page = 1;

    if($is_connected == true) {
      $my_account_sql = $database->prepare('SELECT admin_access FROM account WHERE id = ?');
      $my_account_sql->execute(array(
            $_SESSION['id']
      ));
      $my_account_data = $my_account_sql->fetch();
     }

    $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
    $sql->execute(array(
        $id
    ));
    if($sql->rowCount() == 1) {
        $post_wall_data = $sql->fetch();
        $profile_id = $post_wall_data['wall_id'];

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $post_wall_data['posted_by']
        ));
        $account_data = $sql->fetch();

        $current_page_title = "Publication de " . $account_data['username'];
        $web_description = $post_wall_data['content'];

        $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
        $sql->execute(array(
            $post_wall_data['posted_by']
        ));
        $profile_data = $sql->fetch();

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $post_wall_data['wall_id']
        ));
        $wall_data = $sql->fetch();

        $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0 ORDER BY id');
        $reply_post_wall_sql->execute(array(
            $post_wall_data['id']
        ));
        if($reply_post_wall_sql->rowCount() > 0) {
            $is_there_reply = true;
        } else {
            $is_there_reply = false;
        }
    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "Désolé, cet ID de publication est <strong>incorrect</strong>. Peut-être que la publication a été supprimée.";
        header("Location: home");
        exit();
    }
} elseif($_GET['id'] == "random") {
    $sql = $database2->prepare('SELECT * FROM post_wall WHERE deleted = 0 ORDER BY rand() LIMIT 1');
    $sql->execute();
    $rand = $sql->fetch();
    $id = $rand['id'];
    header("Location: post?id=" . $id);
    exit();
} else {
    header("Location: home");
    exit();
}

if(isset($type) AND $type == "all_post" OR $type == "one_post") {
    $profile_sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
    $profile_sql->execute(array(
        $profile_id
    ));
    $profile_data_1 = $profile_sql->fetch();

    $primary_color = $profile_data_1['primary_color'];
    $background_fixed = $profile_data_1['background_fixed'];
    $background = $profile_data_1['background'];
    $background_scretch = $profile_data_1['background_scretch'];
    $private_profile = $profile_data_1['private'];
    $view_post = $profile_data_1['view_post'];

    if($private_profile == 1 AND is_connected == true AND $profile_data_1['username_id'] != $_SESSION['id']) {
      $_SESSION['flash']['danger'] = $danger_sign."Désolé, mais ce profil est en privé. Il est sans doute en train de se refaire une beauté...";
      header("Location: home");
      exit();
    }
}

require '../includes/header.php';

?>
<style type="text/css">
   body {
   overflow-x: hidden;
   }
   <?php if($primary_color != "DEFAULT") { ?>
   .panel-primary > .panel-heading {
   background-color: <?= $primary_color ?>;
   }
   .navbar-inverse {
   background-color: <?= $primary_color ?>;
   }
   .navbar-inverse .navbar-nav > li > a {
   color: <?= adjustBrightness($primary_color, 100) ?>;
   }
   .navbar-inverse .navbar-nav > .open > a,
   .navbar-inverse .navbar-nav > .open > a:hover,
   .navbar-inverse .navbar-nav > .open > a:focus {
   background-color: <?= adjustBrightness($primary_color, -30) ?>;
   }
   a {
   color: <?= $primary_color ?>;
   }
   a:hover,
   a:focus {
   color: <?= $primary_color ?>;
   }
   [contenteditable].form-control:focus,[type=email].form-control:focus,[type=password].form-control:focus,[type=tel].form-control:focus,[type=text].form-control:focus,input.form-control:focus,input[type=email]:focus,input[type=number]:focus,input[type=password]:focus,input[type=text]:focus,textarea.form-control:focus,textarea:focus {
      -webkit-box-shadow:inset 0 -2px 0 <?= $primary_color ?>;box-shadow:inset 0 -2px 0 <?= $primary_color ?>
    }
   .checkbox input[type=checkbox]:focus:after,.checkbox-inline input[type=checkbox]:focus:after,input[type=checkbox]:focus:after {
      border-color:<?= $primary_color ?>
    }
   .checkbox input[type=checkbox]:checked:after,.checkbox-inline input[type=checkbox]:checked:after,input[type=checkbox]:checked:after {
      background-color:<?= $primary_color ?>;border-color:<?= $primary_color ?>
    }
    .pagination>li>a,.pagination>li>span{position:relative;float:left;padding:6px 16px;line-height:1.846;text-decoration:none;color:<?= $primary_color ?>;background-color:#fff;border:1px solid #ddd;margin-left:-1px}
    .pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover{z-index:3;color:#fff;background-color:<?= $primary_color ?>;border-color:<?= $primary_color ?>;cursor:default}
    .pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover{z-index:2;color:<?= $primary_color ?>;background-color:#eee;border-color:#ddd}

    .radio input[type=radio]:before,.radio-inline input[type=radio]:before,input[type=radio]:before{position:absolute;left:0;top:-3px;background-color:<?= $primary_color ?>;-webkit-transform:scale(0);-ms-transform:scale(0);-o-transform:scale(0);transform:scale(0)}
    .radio input[type=radio]:checked:after,.radio-inline input[type=radio]:checked:after,input[type=radio]:checked:after{border-color:<?= $primary_color ?>}

    .nav-tabs>li>a:focus:hover,.nav-tabs>li>a:hover{background-color:transparent;-webkit-box-shadow:inset 0 -2px 0 <?= $primary_color ?>;box-shadow:inset 0 -2px 0 <?= $primary_color ?>;color:<?= $primary_color ?>}
    .nav-tabs>li.active>a,.nav-tabs>li.active>a:focus{border:none;-webkit-box-shadow:inset 0 -2px 0 <?= $primary_color ?>;box-shadow:inset 0 -2px 0 <?= $primary_color ?>;color:<?= $primary_color ?>}
    .nav-tabs>li.active>a:focus:hover,.nav-tabs>li.active>a:hover{border:none;color:<?= $primary_color ?>}
   <?php } else {
      $primary_color = "#56966d";
      } ?>
   <?php if($background != "DEFAULT") { ?>
   body {
   background-image: none;
   background: url(../img/moviebook/backgrounds/<?= $background ?>) center <?php if($background_fixed) { echo "fixed"; } ?>;
   <?php if($background_scretch == 1) { ?>
   -webkit-background-size: cover;
   -moz-background-size: cover;
   -o-background-size: cover;
   background-size: cover;
   <?php } ?>
   }
   <?php } ?>
</style>
<br>
<input type="hidden" name="profile_id_hidden" id="profile_id_hidden" value="<?= $profile_id ?>">
<?php if($type == "one_post") { ?>
<span id="post_form_<?= $post_wall_data['id'] ?>">
   <?php if($account_data['id'] != $wall_data['id']) { ?>
   <h5 class="text-white">Publication de <a class="text-white" href="profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> sur le profil de <a class="text-white" href="profile?u=<?= $wall_data['id'] ?>"><?= $wall_data['username']; ?></a> :</h5>
   <?php } else { ?>
   <h5 class="text-white">Publication de <?= $account_data['username'] ?> sur son profil :</h5>
   <?php } ?>
   <div class="panel panel-default">
      <div class="panel-body">
         <div class="media">
            <div class="media-left media-top">
               <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
            </div>
            <div class="media-body">
               <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($post_wall_data['post_date']) ?></small></label><br>
               <?= return_string($post_wall_data['content']) ?>
            </div>
         </div>
         <?php if($post_wall_data['is_picture'] == 1 AND isset($post_wall_data['picture_path'])) { ?>
         <legend></legend>
         <div align="center">
            <div class="post_picture_group"><a href="../img/moviebook/pictures/<?= $post_wall_data['picture_path'] ?>" data-lightbox="image-pinned" data-title="Image de <?= $account_data['username'] ?>"><img class="post_picture img-post-wall" src="../img/moviebook/pictures/<?= $post_wall_data['picture_path'] ?>"></a></div>
         </div>
         <?php } ?>
      </div>
      <div class="panel-footer">
         <a class="btn btn-success mb_post_wall_reply_button" post_id="<?= $post_wall_data['id'] ?>"><span class="glyphicon glyphicon-share-alt"></span></a>
         <?php if($is_connected == true AND $_SESSION['id'] == $post_wall_data['posted_by'] OR $is_connected == true AND $_SESSION['id'] == $wall_data['id']) { ?>
         <a class="btn btn-danger" data-type="post_wall" data-content_id="<?= $post_wall_data['id'] ?>" modal_type="delete_content" data-toggle="modal" data-target="#delete_content_modal"><span class="glyphicon glyphicon-trash"></span></a>
         <?php } ?>
         <!-- DROPDOWN MENU -->
         <div class="btn-group">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-option-horizontal"></span></a>
            <ul class="dropdown-menu">
               <?php if($is_connected == true AND $post_wall_data['posted_by'] == $_SESSION['id']) { ?>
               <li><a href="#!" class="mb_update_post_wall_notification_settings_button" post_id="<?= $post_wall_data['id'] ?>" id="update_post_wall_notification_button_<?= $post_wall_data['id'] ?>"><?php if($post_wall_data['posted_by'] == $_SESSION['id'] AND $post_wall_data['notification'] == 1) { echo "Désactiver les notifications"; } else { echo "Activer les notifications"; } ?></a></li>
               <?php } elseif($is_connected == true) {
                  $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND username_id = ? AND deleted = 0');
                  $sql->execute(array($post_wall_data['id'], $_SESSION['id']));
                  if($sql->rowCount() > 0) {
                   $reply_post_wall_data = $sql->fetch(); ?>
               <li><a href="#!" class="mb_update_post_wall_notification_settings_button" post_id="<?= $post_wall_data['id'] ?>" id="update_post_wall_notification_button_<?= $post_wall_data['id'] ?>"><?php if($reply_post_wall_data['notification'] == 1) { echo "Désactiver les notifications"; } else { echo "Activer les notifications"; } ?></a></li>
               <?php } ?>
               <?php } ?>
               <li><a href="#!" class="mb_copy_link_button" post_link="<?= $actual_link ?>/moviebook/post?id=<?= $post_wall_data['id'] ?>">Copier le lien</a></li>
               <?php if($is_connected == true AND $post_wall_data['posted_by'] != $_SESSION['id']) { ?>
               <li><a href="#!" class="mb_report_post" modal_type="report_post" post_id="<?= $post_wall_data['id'] ?>" data-toggle="modal" data-target="#report_post" data-report_type="post_wall" data-post_id="<?= $post_wall_data['id'] ?>">Signaler</a></li>
               <?php } ?>
               <?php if($is_connected == true AND $my_account_data['admin_access'] == 1) { ?>
                <li><a href="#!" post_id="<?= $post_wall_data['id'] ?>" class="delete_post_id_button">Supprimer la publication</a></li>
               <?php } ?>
            </ul>
         </div>
         <!-- DROPDOWN MENU -->
         <?php
            $sql = $database2->prepare('SELECT * FROM dislikes WHERE post_id = ?');
            $sql->execute(array($post_wall_data['id']));
            $total_dislikes_post = $sql->rowCount();
            $sql = $database2->prepare('SELECT * FROM likes WHERE post_id = ?');
            $sql->execute(array($post_wall_data['id']));
            $total_likes_post = $sql->rowCount();
            ?>
         <a class="btn btn-default pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $post_wall_data['id'] ?>" type="dislike"><span class="glyphicon glyphicon-thumbs-down"></span> (<span id="number_of_dislikes_span_<?= $post_wall_data['id'] ?>"><?php if(isset($total_dislikes_post) AND !empty($total_dislikes_post)) { echo $total_dislikes_post; } else { echo "0"; } ?></span>)</a>
         <a class="btn btn-default pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $post_wall_data['id'] ?>" type="like"><span class="glyphicon glyphicon-thumbs-up"></span> (<span id="number_of_likes_span_<?= $post_wall_data['id'] ?>"><?php if(isset($total_likes_post) AND !empty($total_likes_post)) { echo $total_likes_post; } else { echo "0"; } ?></span>)</a>
      </div>
      <div class="send_reply_post_wall_form_<?= $post_wall_data['id'] ?>" style="display: none;">
         <div class="panel-body">
            <h5>Répondre</h5>
            <textarea class="form-control" rows="1" placeholder="<?php if($is_connected == true) { echo "Réponse..."; } else { echo "Connecte-toi pour répondre à cette publication"; } ?>" id="post_reply_textarea_<?= $post_wall_data['id'] ?>" name="post_reply_textarea_<?= $post_wall_data['id'] ?>" maxlength="1000" style="resize: none;"<?php if($is_connected == false) { echo " disabled"; } ?>></textarea>
            <br>
            <div align="center">
            <?php if($is_connected) { ?>
               <button class="btn btn-success mb_post_wall_send_reply_button" type="one_post" post_id="<?= $post_wall_data['id'] ?>"><span class="glyphicon glyphicon-ok"></span></button>
            <?php } else { ?>
               <a class="btn btn-success mb_post_wall_send_reply_button disabled"><span class="glyphicon glyphicon-ok"></span></a>
            <?php } ?>
            </div>
         </div>
      </div>
      <?php if($is_there_reply == true) {
         while($reply_post_wall_data = $reply_post_wall_sql->fetch()) {
             $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
             $sql->execute(array($reply_post_wall_data['username_id']));
             $account_data = $sql->fetch();
             $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
             $sql->execute(array($reply_post_wall_data['username_id']));
             $profile_data = $sql->fetch();
            ?>
      <div class="panel-body">
         <div class="media">
            <div class="media-left media-top">
               <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
            </div>
            <div class="media-body">
               <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($reply_post_wall_data['post_date']) ?></small></label><br>
               <?= return_string($reply_post_wall_data['content']) ?>
            </div>
         </div>
      </div>
      <div class="panel-footer">
         <?php if($is_connected == true AND $_SESSION['id'] == $reply_post_wall_data['username_id']) { ?>
         <a class="btn btn-danger btn-sm" data-type="reply_post_wall" data-content_id="<?= $reply_post_wall_data['id'] ?>" modal_type="delete_content" data-toggle="modal" data-target="#delete_content_modal"><span class="glyphicon glyphicon-trash"></span></a>
         <?php } elseif($is_connected == true) { ?>
         <div class="btn-group">
            <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-option-horizontal"></span></a>
            <ul class="dropdown-menu">
               <li><a href="#!" class="mb_report_post" modal_type="report_post" post_id="<?= $reply_post_wall_data['id'] ?>" data-toggle="modal" data-target="#report_post" data-report_type="reply_post_wall" data-post_id="<?= $reply_post_wall_data['id'] ?>">Signaler</a></li>
            </ul>
         </div>
         <?php } ?>
      </div>
      <?php } ?>
      <?php } ?>
   </div>
</span>
<?php } elseif($type == "all_post") { ?>
<div class="panel panel-default">
   <div class="panel-heading">
      <center>
         <h3><?php if($is_connected == true AND $_SESSION['id'] == $wall_data['id']) { echo "Exprime toi !"; } else { echo "Poste quelque chose sur son mur !"; } ?></h3>
      </center>
   </div>
   <div class="panel-body">
      <textarea class="form-control" rows="3" placeholder="<?php if($is_connected == true) { echo "J'ai mangé des pâtes ce midi"; } else { echo "Connecte-toi pour écrire une publication"; } ?>" id="post_textarea" name="post_textarea" maxlength="5000" style="resize: none;"<?php if($is_connected == false) { echo " disabled"; } ?>></textarea>
   </div>
   <div class="panel-footer">
      <form id="mb_post_wall_form">
         <input type="hidden" name="wall_id" id="wall_id" value="<?= $profile_id ?>">
         <input type="hidden" name="type" id="type" value="picture_post">
         <input type="hidden" name="post_id_hidden" id="post_id_hidden">
         <input type="file" name="picture_file" id="picture_file" style="display: none;">
         <label for="picture_file" class="btn btn-warning<?php if($is_connected == false) { echo " disabled"; } ?>" id="picture_button" name="picture_button"><span class="glyphicon glyphicon-picture"></span></label>
         <a class="btn btn-success<?php if($is_connected == false) { echo " disabled"; } ?>" id="post_wall_button"><span class="glyphicon glyphicon-send"></span></a>
         <a class="btn btn-default pull-right<?php if($is_connected == false) { echo " disabled"; } ?>" id="smiley_button" data-toggle="modal" data-target="#smileys_modal"><span class="glyphicon glyphicon-plus"></span> Smiley</a>
         <div id="progress_bar_upload_picture" class="progress progress-striped active" style="display: none;">
            <div class="progress-bar" style="width: 0%"></div>
         </div>
      </form>
   </div>
</div>
<br>
<?php if($total_post_on_wall > 0) { ?>
<div class="row">
   <div class="col-md-11 col-centered">
      <h5 class="text-white">Voici les <strong><?= $total_post_on_wall ?></strong> publications du mur de <a class="text-white" href="profile?u=<?= $wall_data['id'] ?>"><?= $wall_data['username'] ?></a>.</h5>
      <div id="post_elements_div"></div>
      <div id="loading">
         <div class="panel panel-default">
            <div class="panel-body">
               <div align="center">
                  <img draggable="false" src="../img/loading.gif">
                  <h4>Chargement, patiente...</h4>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } else { ?>
   <div align="center">
      <h4 class="text-white">Il n'y a aucune publication sur le mur de <strong><?= $wall_data['username'] ?></strong>. Écris lui quelque chose !</h4>
   </div>
   <?php } ?>
</div>
<?php } ?>
<!-- Modal -->
<?= smileys_modal("mb_new_post") ?>
<!-- Modal -->
<div class="modal fade" id="report_post" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Signaler cette publication</h4>
            </center>
            <div class="panel panel-default">
               <div class="panel-body">
                  <p>Es-tu sûr(e) de signaler ce contenu ?</p>
                  <input type="hidden" name="report_post_id" id="report_post_id" value="">
                  <input type="hidden" name="report_type" id="report_type" value="">
                  <div class="form-group">
                     <textarea class="form-control" rows="3" id="report_info_textarea" name="report_info_textarea" maxlength="1000" style="resize: none;" placeholder="Informations supplémentaires (facultatif)"></textarea>
                  </div>
                  <div align="center">
                     <a class="btn btn-success" id="confirm_report_post_button" name="confirm_report_post_button"><span class="glyphicon glyphicon-ok"></span></a>
                     <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
                  </div>
               </div>
            </div>
            <span class="glyphicon glyphicon-info-sign"></span> <small>Si cette publication est en non-respect des <a target="_blank" href="../rules">règles de MovieBook</a>, l'utilisateur aura un avertissement ou sera banni définitivement. Rappel : il est <strong>strictement interdit</strong> de faire des faux signalements, et d'en abuser.</small>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="delete_content_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Supprimer ce contenu</h4>
            </center>
            <p>Es-tu sûr(e) de supprimer ce contenu ?<br>
               Cette action est <strong>irréversible</strong>.
            </p>
            <input type="hidden" name="delete_content_id_hidden" id="delete_content_id_hidden" value="">
            <input type="hidden" name="delete_type_hidden" id="delete_type_hidden" value="">
            <div align="center">
               <button class="btn btn-success" id="mb_delete_content_button" name="mb_delete_content_button" location_type="one_post"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php require '../includes/footer.php'; ?>
<script src="../js/post_elements.min.js?v=<?= $javascript_post_elements_version ?>"></script>
<script type="text/javascript">
  $("#post_elements_div").load("../npr/post_elements?u=<?= $profile_id ?>&p=<?= $current_page ?>&vp=<?= $view_post ?>", function() {
    $('#loading').hide();
  });
</script>