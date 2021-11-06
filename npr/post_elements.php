<?php
include('../includes/config.php');

if(is_connected() == false) {
   $is_connected = false;
} else {
   $is_connected = true;
}

if(isset($_GET['u']) AND !empty($_GET['u']) AND is_numeric($_GET['u']) AND strlen($_GET['u']) > 0) {
	$profile_id = htmlspecialchars($_GET['u']);

  if(isset($_GET['vp']) AND !empty($_GET['vp'])) {
    if($_GET['vp'] == 1 OR $_GET['vp'] == 2) {
      $view_post = $_GET['vp'];
    } else {
      $view_post = "1";
    }
  } else {
    $view_post = "1";
  }

  if($is_connected == true) {
   $my_account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
   $my_account_sql->execute(array(
     $_SESSION['id']
   ));
   $my_account_data = $my_account_sql->fetch();
  }

	$account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
  $account_sql->execute(array(
    $profile_id
  ));

    if($view_post == 2) {
      $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND posted_by = ? AND deleted = 0 ORDER BY id DESC');
      $sql->execute(array(
          $profile_id,
          $profile_id
      ));
    } else {
      $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND deleted = 0 ORDER BY id DESC');
      $sql->execute(array(
          $profile_id
      ));
    }
    $total_post_on_wall = $sql->rowCount();

    $post_per_page = 20;
    $total_of_page = ceil($total_post_on_wall / $post_per_page);

    if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
        $p = htmlspecialchars($_GET['p']);
        $current_page = $p;
    } else {
        $current_page = 1;
    }

    if(isset($_GET['typePage']) AND !empty($_GET['typePage'])) {
        $typePage = htmlspecialchars($_GET['typePage']);
    } else {
    	$typePage = "";
    }

    $page_start = ($current_page - 1) * $post_per_page;

    if($view_post == 2) {
      $post_on_wall_sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND posted_by = ? AND deleted = 0 ORDER BY id DESC LIMIT ' . $page_start . ',' . $post_per_page);
      $post_on_wall_sql->execute(array(
          $profile_id,
          $profile_id
      ));
    } else {
      $post_on_wall_sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND deleted = 0 ORDER BY id DESC LIMIT ' . $page_start . ',' . $post_per_page);
      $post_on_wall_sql->execute(array(
          $profile_id
      ));
    }

    if($account_sql->rowCount() == 1) {
	    if($is_connected == true AND $_SESSION['id'] == $profile_id) {
	        $it_is_my_profile = true;
	    } else {
	        $it_is_my_profile = false;
	    }
	} else {
		exit();
	}

} else {
    exit();
} ?>
<div class="panel panel-defaut">
  <div class="panel-body">
    <ul class="nav nav-tabs nav-justified">
      <li class="change_view_post_nav_tabs<?php if($view_post == 2) { echo " active"; } ?>" link="post_elements?u=<?= $profile_id ?>&t=ap" type="only_user"><a href="#vp" data-toggle="tab">Uniquement les publications de l'utilisateur</a></li>
      <li class="change_view_post_nav_tabs<?php if($view_post == 1) { echo ' active'; } ?>" link="post_elements?u=<?= $profile_id ?>&t=ap" type="all"><a href="#vp" data-toggle="tab">Publications de l'utilisateur et des autres</a></li>
    </ul>
  </div>
</div>
<?php $pinned_post_sql = $database2->prepare('SELECT * FROM post_wall WHERE pinned = 1 AND posted_by = ? AND wall_id = ? AND deleted = 0');
$pinned_post_sql->execute(array(
    $profile_id,
    $profile_id
));
if($pinned_post_sql->rowCount() == 1) {
    $pinned_post_wall_data = $pinned_post_sql->fetch();
    $is_there_pinned = true;
} else {
    $is_there_pinned = false;
}

if($is_there_pinned == true) {
 $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
 $sql->execute(array($pinned_post_wall_data['posted_by']));
 $account_data = $sql->fetch();
 $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
 $sql->execute(array($pinned_post_wall_data['posted_by']));
 $profile_data = $sql->fetch();

 $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0');
 $reply_post_wall_sql->execute(array($pinned_post_wall_data['id']));
 $total_reply = $reply_post_wall_sql->rowCount();

 $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0 ORDER BY id LIMIT 1');
 $reply_post_wall_sql->execute(array($pinned_post_wall_data['id']));
 if($reply_post_wall_sql->rowCount() > 0) {
     $is_there_reply = true;
 } else {
     $is_there_reply = false;
 }
 ?>
<div class="panel panel-danger">
   <div class="panel-heading">
      <span class="glyphicon glyphicon-pushpin"></span> Publication épinglée
   </div>
   <div class="panel-body">
      <div class="media">
         <div class="media-left media-top">
            <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
         </div>
         <div class="media-body">
            <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($pinned_post_wall_data['post_date']) ?></small></label><br>
            <?= return_string($pinned_post_wall_data['content']) ?>
         </div>
      </div>
      <?php if($pinned_post_wall_data['is_picture'] == 1 AND isset($pinned_post_wall_data['picture_path'])) { ?>
      <legend></legend>
      <div align="center">
         <div class="post_picture_group"><a href="../img/moviebook/pictures/<?= $pinned_post_wall_data['picture_path'] ?>" data-lightbox="image-pinned" data-title="Image de <?= $account_data['username'] ?>"><img class="post_picture img-post-wall" src="../img/moviebook/pictures/<?= $pinned_post_wall_data['picture_path'] ?>"></a></div>
      </div>
      <?php } ?>
   </div>
   <div class="panel-footer">
      <a class="btn btn-success btn-sm mb_post_wall_reply_button" post_id="<?= $pinned_post_wall_data['id'] ?>"><span class="glyphicon glyphicon-share-alt"></span></a>
      <!-- DROPDOWN MENU -->
      <div class="btn-group">
         <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-option-horizontal"></span></a>
         <ul class="dropdown-menu">
            <?php if($is_connected == true AND $_SESSION['id'] == $pinned_post_wall_data['posted_by'] AND $it_is_my_profile == true) { ?>
            <li><a href="#!" class="mb_pin_post_button" post_id="<?= $pinned_post_wall_data['id'] ?>" post_id="<?= $pinned_post_wall_data['id'] ?>" type="de-pin">Dépingler</a></li>
            <?php } ?>
            <?php if($is_connected == true AND $pinned_post_wall_data['posted_by'] == $_SESSION['id']) { ?>
            <li><a href="#!" class="mb_update_post_wall_notification_settings_button" post_id="<?= $pinned_post_wall_data['id'] ?>" id="update_post_wall_notification_button_<?= $pinned_post_wall_data['id'] ?>"><?php if($pinned_post_wall_data['posted_by'] == $_SESSION['id'] AND $pinned_post_wall_data['notification'] == 1) { echo "Désactiver les notifications"; } else { echo "Activer les notifications"; } ?></a></li>
            <?php } elseif($is_connected == true) {
               $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND username_id = ? AND deleted = 0');
               $sql->execute(array($pinned_post_wall_data['id'], $_SESSION['id']));
               if($sql->rowCount() > 0) {
                $reply_post_wall_data = $sql->fetch(); ?>
            <li><a href="#!" class="mb_update_post_wall_notification_settings_button" post_id="<?= $pinned_post_wall_data['id'] ?>" id="update_post_wall_notification_button_<?= $pinned_post_wall_data['id'] ?>"><?php if($reply_post_wall_data['notification'] == 1) { echo "Désactiver les notifications"; } else { echo "Activer les notifications"; } ?></a></li>
            <?php } ?>
            <?php } ?>
            <li><a href="#!" class="mb_copy_link_button" post_link="<?= $actual_link ?>/moviebook/post?id=<?= $pinned_post_wall_data['id'] ?>">Copier le lien</a></li>
            <?php if($is_connected == true AND $pinned_post_wall_data['posted_by'] != $_SESSION['id']) { ?>
            <li><a href="#!" class="mb_report_post" modal_type="report_post" post_id="<?= $pinned_post_wall_data['id'] ?>" data-toggle="modal" data-target="#report_post" data-report_type="post_wall" data-post_id="<?= $pinned_post_wall_data['id'] ?>">Signaler</a></li>
            <?php } ?>
         </ul>
      </div>
      <!-- DROPDOWN MENU -->
      <?php
         $sql = $database2->prepare('SELECT * FROM dislikes WHERE post_id = ?');
         $sql->execute(array($pinned_post_wall_data['id']));
         $total_dislikes_post = $sql->rowCount();
         $sql = $database2->prepare('SELECT * FROM likes WHERE post_id = ?');
         $sql->execute(array($pinned_post_wall_data['id']));
         $total_likes_post = $sql->rowCount();
         ?>
      <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $pinned_post_wall_data['id'] ?>" type="dislike"><span class="glyphicon glyphicon-thumbs-down"></span> (<span id="number_of_dislikes_span_<?= $pinned_post_wall_data['id'] ?>"><?php if(isset($total_dislikes_post) AND !empty($total_dislikes_post)) { echo $total_dislikes_post; } else { echo "0"; } ?></span>)</a>
      <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $pinned_post_wall_data['id'] ?>" type="like"><span class="glyphicon glyphicon-thumbs-up"></span> (<span id="number_of_likes_span_<?= $pinned_post_wall_data['id'] ?>"><?php if(isset($total_likes_post) AND !empty($total_likes_post)) { echo $total_likes_post; } else { echo "0"; } ?></span>)</a>
   </div>
   <div class="send_reply_post_wall_form_<?= $pinned_post_wall_data['id'] ?>" style="display: none;">
      <div class="panel-body">
         <h5>Répondre</h5>
         <textarea class="form-control" rows="1" placeholder="<?php if($is_connected == true) { echo "Réponse..."; } else { echo "Connecte-toi pour répondre à cette publication"; } ?>" id="post_reply_textarea_<?= $pinned_post_wall_data['id'] ?>" name="post_reply_textarea_<?= $pinned_post_wall_data['id'] ?>" maxlength="1000" style="resize: none;"<?php if($is_connected == false) { echo " disabled"; } ?>></textarea>
         <br>
         <div align="center">
            <?php if($is_connected) { ?>
               <button class="btn btn-success mb_post_wall_send_reply_button" type="all_post" post_id="<?= $pinned_post_wall_data['id'] ?>"><span class="glyphicon glyphicon-ok"></span></button>
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
   <?php if($total_reply > 1) { ?>
   <div class="panel panel-default">
      <div class="panel-body">
         <div align="center">
            <a target="_blank" href="post?id=<?= $pinned_post_wall_data['id'] ?>">Voir les autres réponses (<?= $total_reply - 1 ?>)</a>
         </div>
      </div>
   </div>
   <?php } ?>
   <?php } ?>
   <?php } ?>
</div>
<?php } ?>
<?php while($post_wall_data = $post_on_wall_sql->fetch()) {
   $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
   $sql->execute(array($post_wall_data['posted_by']));
   $account_data = $sql->fetch();
   $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
   $sql->execute(array($post_wall_data['posted_by']));
   $profile_data = $sql->fetch();

   $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0');
   $reply_post_wall_sql->execute(array($post_wall_data['id']));
   $total_reply = $reply_post_wall_sql->rowCount();

   $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0 ORDER BY id LIMIT 1');
   $reply_post_wall_sql->execute(array($post_wall_data['id']));
   if($reply_post_wall_sql->rowCount() > 0) {
       $is_there_reply = true;
   } else {
       $is_there_reply = false;
   }
   ?>
<span id="post_form_<?= $post_wall_data['id'] ?>">
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
         <a class="btn btn-success btn-sm mb_post_wall_reply_button" post_id="<?= $post_wall_data['id'] ?>"><span class="glyphicon glyphicon-share-alt"></span></a>
         <?php if($is_connected == true AND $_SESSION['id'] == $post_wall_data['posted_by'] OR $it_is_my_profile == true) { ?>
         <a class="btn btn-danger btn-sm" data-type="post_wall" data-content_id="<?= $post_wall_data['id'] ?>" modal_type="delete_content" data-toggle="modal" data-target="#delete_content_modal"><span class="glyphicon glyphicon-trash"></span></a>
         <?php } ?>
         <!-- DROPDOWN MENU -->
         <div class="btn-group">
            <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-option-horizontal"></span></a>
            <ul class="dropdown-menu">
               <?php if($is_connected == true AND $_SESSION['id'] == $post_wall_data['posted_by'] AND $it_is_my_profile == true) { ?>
               <li><a href="#!" class="mb_pin_post_button" post_id="<?= $post_wall_data['id'] ?>" post_id="<?= $post_wall_data['id'] ?>" type="pin">Épingler</a></li>
               <?php } ?>
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
         <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $post_wall_data['id'] ?>" type="dislike"><span class="glyphicon glyphicon-thumbs-down"></span> (<span id="number_of_dislikes_span_<?= $post_wall_data['id'] ?>"><?php if(isset($total_dislikes_post) AND !empty($total_dislikes_post)) { echo $total_dislikes_post; } else { echo "0"; } ?></span>)</a>
         <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post<?php if($is_connected == false) { echo " disabled"; } ?>" post_id="<?= $post_wall_data['id'] ?>" type="like"><span class="glyphicon glyphicon-thumbs-up"></span> (<span id="number_of_likes_span_<?= $post_wall_data['id'] ?>"><?php if(isset($total_likes_post) AND !empty($total_likes_post)) { echo $total_likes_post; } else { echo "0"; } ?></span>)</a>
      </div>
      <div class="send_reply_post_wall_form_<?= $post_wall_data['id'] ?>" style="display: none;">
         <div class="panel-body">
            <h5>Répondre</h5>
            <textarea class="form-control" rows="1" placeholder="<?php if($is_connected == true) { echo "Réponse..."; } else { echo "Connecte-toi pour répondre à cette publication"; } ?>" id="post_reply_textarea_<?= $post_wall_data['id'] ?>" name="post_reply_textarea_<?= $post_wall_data['id'] ?>" maxlength="1000" style="resize: none;"<?php if($is_connected == false) { echo " disabled"; } ?>></textarea>
            <br>
            <div align="center">
            <?php if($is_connected) { ?>
               <button class="btn btn-success mb_post_wall_send_reply_button" type="all_post" post_id="<?= $post_wall_data['id'] ?>"><span class="glyphicon glyphicon-ok"></span></button>
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
      <?php if($total_reply > 1) { ?>
      <div class="panel panel-default">
         <div class="panel-body">
            <div align="center">
               <a target="_blank" href="post?id=<?= $post_wall_data['id'] ?>">Voir les autres réponses (<?= $total_reply - 1 ?>)</a>
            </div>
         </div>
      </div>
      <?php } ?>
      <?php } ?>
      <?php } ?>
   </div>
</span>
<?php } ?>
<?php if($total_post_on_wall > 20) { ?>
<div class="row">
   <div class="col-md-7 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="#post_elements_div" class="change_page_post" link="p?u=<?= $profile_id ?>&t=ap&p=<?= $current_page - 1 ?>&vp=<?= $view_post ?>">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="#post_elements_div" class="change_page_post" link="post_elements?u=<?= $profile_id ?>&t=ap&p=<?= $i ?>&vp=<?= $view_post ?>"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="#post_elements_div" class="change_page_post" link="post_elements?u=<?= $profile_id ?>&t=ap&p=<?= $current_page + 1 ?>&vp=<?= $view_post ?>">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
               <br><a href="post?t=ap&u=<?= $profile_id ?>">Voir toutes les publications</a>
            </center>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<script src="../js/post_elements.min.js?v=<?= $javascript_post_elements_version ?>"></script>
