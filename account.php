<?php
include('includes/config.php');

if(is_connected() == false) {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accéder à cette page.";
    header('Location: login');
    exit();
}

$current_page_title = "Mon compte";
require 'includes/header.php';

if(is_profile_exist($_SESSION['id']) == true) {
    $friend_request_sql = $database2->prepare('SELECT * FROM friend_request WHERE to_id = ? ORDER BY id');
    $friend_request_sql->execute(array(
        $_SESSION['id']
    ));
    $friend_request_count = $friend_request_sql->rowCount();

    $sql = $database2->prepare('SELECT * FROM points_history WHERE username_id = ? ORDER BY id DESC');
    $sql->execute(array(
        $_SESSION['id']
    ));
    $points_history_count = $sql->rowCount();

    $addition_level_points = 0;
    while($points_history_data = $sql->fetch()) {
      $addition_level_points = $points_history_data['points'] + $addition_level_points;
    }

    $items_per_page = 20;
    $total_of_page = ceil($points_history_count / $items_per_page);

    if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
        $p = htmlspecialchars($_GET['p']);
        $current_page = $p;
    } else {
        $current_page = 1;
    }

    $page_start = ($current_page - 1) * $items_per_page;

    $points_history_sql = $database2->prepare('SELECT * FROM points_history WHERE username_id = ? ORDER BY id DESC LIMIT ' . $page_start . ',' . $items_per_page);
    $points_history_sql->execute(array($_SESSION['id']));
}

$code_vip_sql = $database->prepare('SELECT * FROM code_vip WHERE purchased_by = ? ORDER BY id DESC');
$code_vip_sql->execute(array(
    $_SESSION['id']
));

$notifications_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? ORDER BY id DESC');
$notifications_sql->execute(array(
    $_SESSION['id']
));
$notifications_count = $notifications_sql->rowCount();

$contest_sql = $database->prepare('SELECT * FROM contest WHERE username_id = ? AND deleted = 0 ORDER BY id');
$contest_sql->execute(array(
    $_SESSION['id']
));
$contest_count = $contest_sql->rowCount();

$contest_ended_sql = $database->prepare('SELECT * FROM contest_ended WHERE username_id = ? ORDER BY id');
$contest_ended_sql->execute(array(
    $_SESSION['id']
));
$contest_ended_count = $contest_ended_sql->rowCount();

$account_data_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
$account_data_sql->execute(array(
    $_SESSION['id']
));
$my_account_data = $account_data_sql->fetch();

if($my_account_data['admin_access'] == 1 OR $my_account_data['admin_access'] == 2) {
    $reports_moviebook_sql = $database2->prepare('SELECT * FROM report ORDER BY id DESC');
    $reports_moviebook_sql->execute();
    $nmr = $reports_moviebook_sql->rowCount();

    $reports_contest_sql = $database->prepare('SELECT * FROM contest_report ORDER BY id DESC');
    $reports_contest_sql->execute();
    $ncr = $reports_contest_sql->rowCount();
}

if(isset($_GET['t']) AND !empty($_GET['t'])) {
    if($_GET['t'] == "n") {
        $force_notification_tab = true;
        $force_points_history_tab = false;
    } elseif($_GET['t'] == "ph") {
        $force_notification_tab = false;
        $force_points_history_tab = true;
    } else {
      $force_notification_tab = false;
      $force_points_history_tab = false;
    }
} else {
  $force_notification_tab = false;
  $force_points_history_tab = false;
}

if(is_profile_exist($_SESSION['id']) == true) {
    $is_profile_exist = true;
} else {
    $is_profile_exist = false;
}

?>
<br>
<center>
   <h1 class="page-title">Mon compte</h1>
   <p class="lead text-white" style="font-size: 200%;">Garde le contrôle de ton compte grâce au panel.</p>
</center>
<div class="panel panel-default">
<div class="panel-body">
<ul class="nav nav-tabs">
   <?php if($is_profile_exist == true) { ?>
   <li<?php if($force_notification_tab == false AND $force_points_history_tab == false) { echo ' class="active"'; } ?>><a href="#friends_request" data-toggle="tab" aria-expanded="true">Demandes d'amis <span class="badge"><?= $friend_request_count ?></span></a></li>
   <?php } ?>
   <li<?php if($is_profile_exist == false AND $force_notification_tab == false) { echo ' class="active"'; } ?>><a href="#contest" data-toggle="tab" aria-expanded="true">Concours</a></li>
   <li<?php if($force_notification_tab == true) { echo ' class="active"'; } ?>><a id="notifications_tab" href="#notifications" data-toggle="tab" aria-expanded="true">Notifications <span class="badge"><?= $notifications_count ?></span></a></li>
   <?php if($is_profile_exist == true) { ?>
   <li<?php if($force_points_history_tab == true) { echo ' class="active"'; } ?>><a id="points_history_tab" href="#points_history" data-toggle="tab" aria-expanded="true">Historique de points</a></li>
   <?php } ?>
   <li><a href="#my_vip_codes" data-toggle="tab" aria-expanded="true">Mes codes VIP</a></li>
   <li><a href="#my_data" data-toggle="tab" aria-expanded="true">Mes données</a></li>
   <?php if($my_account_data['admin_access'] == 1 OR $my_account_data['admin_access'] == 2) { ?>
   <li><a href="#reports" data-toggle="tab" aria-expanded="true">Signalements</a></li>
   <?php } ?>
   <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">Changer <span class="caret"></span></a>
      <ul class="dropdown-menu">
         <li><a href="#change_username" data-toggle="tab" aria-expanded="false">Mon pseudo</a></li>
         <li><a href="#password" data-toggle="tab" aria-expanded="true">Mon mot de passe</a></li>
         <li><a href="#change_website_theme" data-toggle="tab" aria-expanded="false">Le thème du site</a></li>
      </ul>
   </li>
</ul>
<div id="myTabContent" class="tab-content">
   <?php if($is_profile_exist == true) { ?>
   <div class="tab-pane fade<?php if($force_notification_tab == false AND $force_points_history_tab == false) { echo " active in"; } ?>" id="friends_request">
      <div align="center">
         <h4>Tes demandes d'amis</h4>
      </div>
      <?php if($friend_request_count != 0) { ?>
      <div class="row">
         <?php while($friend_request_data = $friend_request_sql->fetch()) {
            $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
            $sql->execute(array($friend_request_data['from_id']));
            $account_data = $sql->fetch();
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array($friend_request_data['from_id']));
            $profile_data = $sql->fetch(); ?>
         <span id="request_form_<?= $friend_request_data['id'] ?>">
            <div class="col-md-3">
               <div class="panel panel-default">
                  <div class="panel-body">
                     <div class="panel-body">
                        <div class="media">
                           <div class="media-left media-top">
                              <a href="moviebook/profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
                           </div>
                           <div class="media-body">
                              <a href="moviebook/profile?u=<?= $account_data['id'] ?>" class="media-heading"><?php if(strlen($account_data['username']) >= 15) { echo substr($account_data['username'], 0, 15)."..."; } else { echo $account_data['username']; } ?></a><br>
                              <button class="btn btn-primary choice_friend_button" user_id="<?= $account_data['id'] ?>" type="accept" form_request="true" name="choice_friend_button" id="choice_friend_button"><span class="glyphicon glyphicon-ok"></span></button> <button class="btn btn-danger choice_friend_button" user_id="<?= $account_data['id'] ?>" type="decline" form_request="true" name="choice_friend_button" id="choice_friend_button"><span class="glyphicon glyphicon-remove"></span></button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </span>
         <?php } ?>
      </div>
      <a class="pull-right" href="moviebook/friends?id=<?= $_SESSION['id'] ?>"><span class="glyphicon glyphicon-arrow-right"></span> Voir tous mes amis</a>
      <?php } else { ?>
      <div align="center">
         Tu n'as aucune demande d'amis. Tristesse !
      </div>
      <?php } ?>
   </div>
   <?php } ?>
   <div class="tab-pane fade" id="password">
      <div align="center">
         <h4>Changer mon mot de passe</h4>
      </div>
      <div class="form-group">
         <label class="control-label" for="old_text_box">Ton ancien mot de passe</label>
         <input type="password" class="form-control" id="old_text_box" name="old_text_box" maxlength="30" required>
      </div>
      <div class="form-group">
         <label class="control-label" for="password_text_box">Nouveau mot de passe <strong>(minimim 5 caractères)</strong></label>
         <input type="password" class="form-control" id="password_text_box" name="password_text_box" maxlength="30" required>
      </div>
      <div class="form-group">
         <label class="control-label" for="confirm_text_box">Confirmation</label>
         <input type="password" class="form-control" id="confirm_text_box" name="confirm_text_box" maxlength="30" required>
      </div>
      <div align="center">
         <button class="btn btn-success" name="update_password_button" id="update_password_button"><span class="glyphicon glyphicon-ok"></span></button>
      </div>
   </div>
   <div class="tab-pane fade" id="change_username">
      <div align="center">
         <h4>Changer mon pseudo</h4>
      </div>
      <?php $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND last_username_update_at < DATE_SUB(NOW(), INTERVAL 1 MONTH) OR id = ? AND last_username_update_at IS NULL;');
         $sql->execute(array(
             $_SESSION['id'],
             $_SESSION['id']
         ));
         if($sql->rowCount() == 1) {
           $can_update_username = true;
         } else {
           $can_update_username = false;

           $last_username_update_time = strtotime($my_account_data['last_username_update_at']);
           $future = strtotime("+1 month", $last_username_update_time);
           $timeleft = $future - time();
           $daysleft = round((($timeleft/24)/60)/60);

           $last_username_update_date = ucfirst(strftime('%d/%m/%y, à %H:%M', strtotime($my_account_data['last_username_update_at'])));
         } ?>
      <div class="form-group">
         <label class="control-label" for="change_username_text_box">Ton nouveau pseudo <strong>(minimum 4 caractères)</strong></label>
         <input type="text" class="form-control" id="change_username_text_box" name="change_username_text_box" maxlength="15" required <?php if($can_update_username == false) { echo "disabled"; } ?>>
      </div>
      <span class="glyphicon glyphicon-info-sign"></span> <small><?php if($can_update_username == true) { echo "Tu peux changer ton pseudo 1 fois par mois."; } else { echo "Tu as changé ton pseudo le ".$last_username_update_date."."; } ?></small>
      <div align="center">
         <button class="btn btn-success" <?php if($can_update_username == false) { echo "disabled"; } else { echo 'data-toggle="modal" data-target="#update_username_confirmation_modal"'; } ?>><?php if($can_update_username == false) { echo "Tu pourras changer ton pseudo dans <strong>".$daysleft."</strong> jours"; } else { echo '<span class="glyphicon glyphicon-ok"></span>'; } ?></button>
      </div>
   </div>
   <div class="tab-pane fade" id="change_website_theme">
      <div align="center">
         <h4>Changer le thème du site</h4>
      </div>
      <div class="row">
        <div class="col-md-6">
          <center>
            <img src="https://i.imgur.com/z2FHs6y.png" style="display:block;"><br><br>
      <?php if($my_account_data['theme'] != 1) { ?>
      <button class="btn btn-primary btn-lg btn-block update_website_theme_button" type="basic">Sélectionner</button>
      <?php } else { ?>
      <button class="btn btn-primary btn-lg btn-block" disabled>Actuel</button>
      <?php } ?>
    </center>
    </div>
    <div class="col-md-6">
      <center>
        <img src="https://i.imgur.com/ZAO3rHL.png" style="width: 44%; height: 44%; display:block;"><br><br>
      <?php if($my_account_data['theme'] != 2) { ?>
      <button class="btn btn-primary btn-lg btn-block update_website_theme_button" type="halloween">Sélectionner</button>
      <?php } else { ?>
      <button class="btn btn-primary btn-lg btn-block" disabled>Actuel</button>
      <?php } ?>
    </center>
    </div>
  </div>
   </div>
   <div class="tab-pane fade<?php if($force_notification_tab == true) { echo " active in"; } ?>" id="notifications">
      <div align="center">
         <h4>Tes notifications</h4>
      </div>
      <div id="notification_elements_div"></div>
      <div id="loading" style="display:none;">
         <div align="center">
            <img draggable="false" src="img/loading.gif">
            <h4>Chargement, patiente...</h4>
         </div>
      </div>
   </div>
   <?php if($is_profile_exist == true) { ?>
   <div class="tab-pane fade<?php if($force_points_history_tab == true) { echo ' active in'; } ?>" id="points_history">
      <div align="center">
         <h4>Historique de points <small>(<?= number_format($addition_level_points, 0, '', ' ') ?> pts)</small></h4>
      </div>
      <?php if($points_history_count > 0) { ?>
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>
                  <center>De</center>
               </th>
               <th>
                  <center>À</center>
               </th>
               <th>
                  <center>Type</center>
               </th>
               <th>
                  <center>Points</center>
               </th>
               <th>
                  <center>Date</center>
               </th>
            </tr>
         </thead>
         <tbody>
            <?php while($points_history_data = $points_history_sql->fetch()) {

               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                $sql->execute(array(
                   $points_history_data['from_id']
                ));
                $from_account_data = $sql->fetch();

                $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                $sql->execute(array(
                   $points_history_data['to_id']
                ));
                $to_account_data = $sql->fetch();

                if($points_history_data['type'] == "account_created_sponsorship") {
                 $type = "Compte créé <strong class='text-orange'><small>(parrainage)</small></strong>";
                } elseif($points_history_data['type'] == "contest_finished") {
                 $type = "Concours fini";
                } elseif($points_history_data['type'] == "subject_created") {
                 $type = "Sujet créé";
                } elseif($points_history_data['type'] == "received_post_wall") {
                 $type = "Publication reçue";
                } elseif($points_history_data['type'] == "new_friend") {
                 $type = "Nouvel ami";
                } elseif($points_history_data['type'] == "new_like") {
                 $type = "Like reçu";
                } elseif($points_history_data['type'] == "number_of_friends") {
                 $type = "Nombre d'amis <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "number_of_posts") {
                 $type = "Publications <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "number_of_posts_received") {
                 $type = "Publications reçues <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "number_of_replies") {
                 $type = "Réponses publications <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "contests_ended") {
                 $type = "Concours terminés <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "subjects_created") {
                 $type = "Sujets créés <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "comments_subjects") {
                 $type = "Commentaires sujets <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "account_created_since") {
                 $type = "Compte créé depuis <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "profile_picture_added") {
                 $type = "Photo de profil ajoutée <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "banner_picture_added") {
                 $type = "Bannière ajoutée <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "background_picture_added") {
                 $type = "Photo de fond ajoutée <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "music_background_added") {
                 $type = "Musique de fond ajoutée <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "msp_account_linked") {
                 $type = "Compte MSP associé <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "informations_added") {
                 $type = "Informations ajoutées <strong class='text-orange'><small>(succès)</small></strong>";
                } elseif($points_history_data['type'] == "custom") {
                 $type = $points_history_data['content'];
                } elseif($points_history_data['type'] == "subject_deleted") {
                 $type = "Sujet supprimé";
                } elseif($points_history_data['type'] == "post_deleted") {
                 $type = "Publication suprimée";
               } elseif($points_history_data['type'] == "rare_completed") {
                 $type = "Commande de rare complétée";
               } ?>
            <tr class="active">
               <td>
                  <center><a href="moviebook/profile?u=<?= $from_account_data['id'] ?>"><?= $from_account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><a href="moviebook/profile?u=<?= $to_account_data['id'] ?>"><?= $to_account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><?= $type ?></center>
               </td>
               <td>
                  <center><?php if($points_history_data['points'] < 0) { echo '<span style="color: #e74c3c">'; } else { echo '<span style="color: #27ae60">+'; } echo number_format($points_history_data['points'], 0, '', ' '); echo '</span>'; ?></center>
               </td>
               <td>
                  <center><?php echo ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($points_history_data['date']))); ?></center>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
      <?php if($points_history_count > 20) { ?>
      <center>
         <ul class="pagination" style="margin: 0px;">
            <?php if($current_page == 1) { ?>
            <li class="disabled"><a>&laquo;</a></li>
            <?php } else { ?>
            <li><a href="account?t=ph&p=<?= $current_page - 1 ?>">&laquo;</a></li>
            <?php } ?>
            <?php $min = max($current_page - 2, 1);
               $max = min($current_page + 2, $total_of_page);
               for($i = $min;$i <= $max;$i++) {
                  if($i == $current_page) { ?>
            <li class="active"><a><?= $i ?></a></li>
            <?php } else { ?>
            <li><a href="account?t=ph&p=<?= $i ?>"><?= $i ?></a></li>
            <?php }
               } ?>
            <?php if($current_page != $total_of_page) { ?>
            <li><a href="account?t=ph&p=<?= $current_page + 1 ?>">&raquo;</a></li>
            <?php } else { ?>
            <li class="disabled"><a>&raquo;</a></li>
            <?php } ?>
            <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
         </ul>
      </center>
      <?php } ?>
      <?php } else { ?>
      <div align="center">
         Tu n'as accumulé aucun point.
      </div>
      <?php } ?>
   </div>
   <?php } ?>
   <div class="tab-pane fade" id="my_vip_codes">
      <div align="center">
         <h4>Mes codes VIP</h4>
         <?php if($code_vip_sql->rowCount() > 0) { ?>
           <table class="table table-bordered table-hover" id="code_vip_table">
              <thead>
                 <tr>
                    <th>
                       <center>Temps</center>
                    </th>
                    <th>
                       <center>Code</center>
                    </th>
                    <th>
                       <center>Date de l'achat</center>
                    </th>
                    <th>
                       <center></center>
                    </th>
                 </tr>
              </thead>
              <tbody>
                 <?php while($code_vip_data = $code_vip_sql->fetch()) { ?>
                 <tr class="active">
                    <td>
                       <center><?= $code_vip_data['time'] ?></center>
                    </td>
                    <td>
                       <center><?= $code_vip_data['code'] ?></center>
                    </td>
                    <td>
                       <center><?php echo ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($code_vip_data['purchased_date']))); ?></center>
                    </td>
                    <td>
                       <center><a href="<?= $code_vip_data['gift_link'] ?>"><span class="glyphicon glyphicon-download-alt"></span></a></center>
                    </td>
                 </tr>
                 <?php } ?>
              </tbody>
           </table>
           <div align="center">
              <h5>Les codes VIP n'ont pas de date d'expiration et peuvent être utilisés sur n'importe quel serveur d'MSP.</h5>
              <br>
              <a class="btn btn-warning" id="download_table_code_button" name="download_table_code_button"><span class="glyphicon glyphicon-download-alt"></span> Télécharger tableau excel</a> <a type="button" class="btn btn-primary" data-toggle="modal" data-target="#vip_help">Comment entrer un code VIP ?</a>
           </div>
         <?php } else { ?>
           <center>Tu n'as aucun code VIP. Tu peux t'en proccurer <a href="purchase">ici</a> !</center>
         <?php } ?>
      </div>
   </div >
   <div class="tab-pane fade" id="my_data">
      <div align="center">
         <h4>Mes données</h4>
      </div>
      <?php $join_data = ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($my_account_data['join_date']))); ?>
      <strong>Ton pseudo :</strong> <?= $my_account_data['username'] ?><br>
      <strong>Ton adresse email :</strong> <?= $my_account_data['email_address'] ?><br>
      <strong>Date d'inscription :</strong> <?= $join_data ?><br>
      <strong>Tes points de fidélité :</strong> <?= $my_account_data['points'] ?><br>
      <strong>Tes jetons d'épinglage de sujet :</strong> <?= $my_account_data['subject_pin_token'] ?><br>
      <strong>Tes jetons de badge personnalisable :</strong> <?= $my_account_data['badge_token'] ?>
      <?php if($my_account_data['admin_access'] == 1) { ?>
      <br><br>
      <div class="row">
         <div class="col-md-5">
            <div class="panel panel-body">
               <div class="panel-body">
                  <div class="form-group">
                     <input type="number" class="form-control" id="user_id_confirm_msp_account_text_box" placeholder="Pseudo du compte">
                  </div>
                  <button class="btn btn-primary btn-block" id="confirm_msp_account_button" name="confirm_msp_account_button">Confirmer le compte MSP</button>
               </div>
            </div>
            <div class="panel panel-body">
               <div class="panel-body">
                  <div class="form-group">
                     <input type="text" class="form-control" id="user_id_send_notification_text_box" placeholder="Pseudo du compte">
                  </div>
                  <textarea class="form-control" rows="2" id="content_notification_textarea" name="content_notification_textarea" maxlength="1000" style="resize: none;" placeholder="Contenu de la notification"></textarea><br>
                  <label><input type="checkbox" name="send_to_all_notification_checkbox" id="send_to_all_notification_checkbox"> Envoyer à tous les profils MovieBook</a></label>
                  <button class="btn btn-primary btn-block" id="admin_send_notification_button" name="admin_send_notification_button">Envoyer notification</button>
               </div>
            </div>
         </div>
         <div class="col-md-5">
            <div class="panel panel-body">
               <div class="panel-body">
                  <div class="form-group">
                     <input type="number" class="form-control" id="user_id_update_points_text_box" placeholder="Pseudo du compte">
                     <input type="number" class="form-control" id="points_update_points_text_box" placeholder="Point">
                     <input type="text" class="form-control" id="reason_update_points_text_box" placeholder="Raison">
                  </div>
                  <button class="btn btn-primary btn-block" id="update_points_button" name="update_points_button">Mettre à jour les points</button>
               </div>
            </div>
            <div class="panel panel-body">
               <div class="panel-body">
                  <div class="form-group">
                     <input type="number" class="form-control" id="user_id_ban_account_text_box" placeholder="Pseudo du compte">
                     <input type="number" class="form-control" id="number_of_weeks_ban_account_text_box" placeholder="Temps de bannissement (en semaine)">
                     <input type="text" class="form-control" id="reason_ban_account_text_box" placeholder="Raison">
                  </div>
                  <label><input type="radio" name="type_ban" value="ban" checked>Bannir</label><br>
                  <label><input type="radio" name="type_ban" value="deban">Débannir</label>
                  <button class="btn btn-primary btn-block" id="ban_account_button" name="ban_admin_button">Bannir le compte</button>
               </div>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
   <div class="tab-pane fade<?php if($is_profile_exist == false AND $force_notification_tab == false) { echo " active in"; } ?>" id="contest">
      <div align="center">
         <h4>Mes concours</h4>
      </div>
      <div class="row">
         <div class="col-md-6">
            <h5>Mes concours en cours <strong>(<?= $contest_count ?>)</strong></h5>
            <legend></legend>
            <?php if($contest_count != 0) { ?>
            <?php while($contest_data = $contest_sql->fetch()) { ?>
            <div class="panel panel-default">
               <div class="panel-body">
                  <strong>Titre :</strong> <?= $contest_data['title'] ?><a href="contest/contest?c=<?= $contest_data['contest_id'] ?>" class="btn btn-primary btn-sm pull-right">Accéder</a>
               </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div align="center">
               Tu n'as aucun concours en cours.
            </div>
            <?php } ?>
         </div>
         <div class="col-md-6">
            <h5>Mes concours terminés <strong>(<?= $contest_ended_count ?>)</strong></h5>
            <legend></legend>
            <?php if($contest_ended_count != 0) { ?>
            <?php while($contest_ended_data = $contest_ended_sql->fetch()) { ?>
            <div class="panel panel-default">
               <div class="panel-body">
                  <strong>Titre :</strong> <?= $contest_ended_data['title'] ?><a href="contest/endContest?c=<?= $contest_ended_data['contest_id'] ?>" class="btn btn-primary btn-sm pull-right">Accéder</a>
               </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div align="center">
               Tu n'as aucun concours terminé.
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
   <?php if($my_account_data['admin_access'] == 1 OR $my_account_data['admin_access'] == 2) { ?>
   <div class="tab-pane fade" id="reports">
      <div align="center">
         <h4>Signalements</h4>
      </div>
      <div class="row">
         <div class="col-md-6">
            <h5>Signalements de MovieBook <strong>(<?= $nmr ?>)</strong></h5>
            <legend></legend>
         </div>
      </div>
      <?php if($nmr > 0) { ?>
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>
                  <center>ID</center>
               </th>
               <th>
                  <center>Signalé par</center>
               </th>
               <th>
                  <center>Date du signalement</center>
               </th>
               <th>
                  <center>Type de signalement</center>
               </th>
               <th>
                  <center>Auteur du post</center>
               </th>
               <th>
                  <center>Contenu du post</center>
               </th>
               <th>
                  <center>Information sur le signalement</center>
               </th>
               <th>
                  <center></center>
               </th>
            </tr>
         </thead>
         <tbody>
            <?php while($reports_moviebook_data = $reports_moviebook_sql->fetch()) {
               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $sql->execute(array(
                  $reports_moviebook_data['reported_by']
               ));
               $account_data = $sql->fetch();

               if($reports_moviebook_data['type'] == "reply_post_wall") {

                  $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE id = ?');
                  $sql->execute(array(
                     $reports_moviebook_data['content_id']
                  ));
                  if($sql->rowCount() == 1) {
                    $reply_post_wall_data = $sql->fetch();

                    $post_id_reply_post = $reply_post_wall_data['post_id'];
                    $content = $reply_post_wall_data['content'];
                    $deleted = $reply_post_wall_data['deleted'];
                    $content_id = $reply_post_wall_data['id'];

                    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                     $sql->execute(array(
                        $reply_post_wall_data['username_id']
                     ));
                     $author_account_data = $sql->fetch();
                 }

               } elseif($reports_moviebook_data['type'] == "post_wall") {

                  $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ?');
                  $sql->execute(array(
                     $reports_moviebook_data['content_id']
                  ));
                  if($sql->rowCount() == 1) {
                     $post_wall_data = $sql->fetch();
                     $content = $post_wall_data['content'];
                     $deleted = $post_wall_data['deleted'];
                     $content_id = $post_wall_data['id'];
                  }

                  $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                  $sql->execute(array(
                     $post_wall_data['posted_by']
                  ));
                  $author_account_data = $sql->fetch();

               } ?>
            <tr class="active">
               <td>
                  <center><?= $content_id ?></center>
               </td>
               <td>
                  <center><a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><?php echo ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($reports_moviebook_data['date']))); ?></center>
               </td>
               <td>
                  <center><?php if($reports_moviebook_data['type'] == "post_wall") { echo "Publication mur"; } elseif($reports_moviebook_data['type'] == "reply_post_wall") { echo "Réponse à une publication"; } ?></center>
               </td>
               <td>
                  <center><a href="moviebook/profile?u=<?= $author_account_data['id'] ?>"><?= $author_account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><?php echo $content; if($deleted == 1) { echo ' <strong><i style="color: #e74c3c"><small>(contenu supprimé)</small></i></strong>'; } ?></center>
               </td>
               <td>
                  <center><?php if(!empty($reports_moviebook_data['info'])) { echo $reports_moviebook_data['info']; } else { echo "<i>Aucune</i>"; } ?></center>
               </td>
               <td>
                  <center><a class="btn btn-primary" target="_blank" <?php if($deleted == 0) { ?> href="moviebook/post?id=<?php if($reports_moviebook_data['type'] == "post_wall") { echo $reports_moviebook_data['content_id']; } elseif($reports_moviebook_data['type'] == "reply_post_wall") { echo $post_id_reply_post; } ?>" <?php } else { echo "disabled"; } ?>>Voir</a></center>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
      <?php } else { ?>
      <div align="center">
         Il n'y a aucun signalement pour MovieBook.
      </div>
      <?php } ?>
      <div class="row">
         <div class="col-md-6">
            <h5>Signalements des concours <strong>(<?= $ncr ?>)</strong></h5>
            <legend></legend>
         </div>
      </div>
      <?php if($ncr > 0) { ?>
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>
                  <center>ID</center>
               </th>
               <th>
                  <center>Signalé par</center>
               </th>
               <th>
                  <center>Date du signalement</center>
               </th>
               <th>
                  <center>Titre du concours</center>
               </th>
               <th>
                  <center>Auteur du concours</center>
               </th>
               <th>
                  <center>Information sur le signalement</center>
               </th>
               <th>
                  <center></center>
               </th>
            </tr>
         </thead>
         <tbody>
            <?php while($reports_contest_data = $reports_contest_sql->fetch()) {
               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $sql->execute(array(
                  $reports_contest_data['username_id']
               ));
               $account_data = $sql->fetch();

               $sql = $database->prepare('SELECT * FROM contest WHERE id = ?');
               $sql->execute(array(
                  $reports_contest_data['contest_id']
               ));
               if($sql->rowCount() == 1) {
                  $contest_data = $sql->fetch();

                  $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                  $sql->execute(array(
                     $contest_data['username_id']
                  ));
                  $contest_account_data = $sql->fetch();
               } ?>
            <tr class="active">
               <td>
                  <center><?= $contest_data['id'] ?></center>
               </td>
               <td>
                  <center><a href="moviebook/profile?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><?php echo ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($reports_contest_data['date']))); ?></center>
               </td>
               <td>
                  <center><?= $contest_data['title']; ?><?php if($contest_data['deleted'] == 1) { ?> <strong><i style="color: #e74c3c"><small>(concours supprimé)</small></i></strong><?php } ?></center>
               </td>
               <td>
                  <center><a href="moviebook/profile?u=<?= $contest_account_data['id'] ?>"><?= $contest_account_data['username'] ?></a><?php if($contest_data['deleted'] == 1) { ?> <strong><i style="color: #e74c3c"><small>(concours supprimé)</small></i></strong><?php } ?></center>
               </td>
               <td>
                  <center><?php if(!empty($reports_contest_data['info'])) { echo $reports_contest_data['info']; } else { echo "<i>Aucune</i>"; } ?></center>
               </td>
               <td>
                  <center><a class="btn btn-primary btn-sm" target="_blank" <?php if($contest_data['deleted'] == 0) { ?> href="contest/contest?c=<?= $contest_data['contest_id']; ?>" <?php } else { echo "disabled"; } ?>>Voir</a></center>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
      <?php } else { ?>
      <div align="center">
         Il n'y a aucun signalement pour les concours.
      </div>
      <?php } ?>
   </div>
   <?php } ?>
</div>
<?php if($can_update_username == true) { ?>
<!-- Modal -->
<div class="modal fade" id="update_username_confirmation_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Changer de pseudo</h4>
            </center>
            <p>Es-tu sûr(e) de vouloir changer ton pseudo par celui-ci ?<br>
               Tu devras attendre <strong>1 mois</strong> pour pouvoir le re-changer.
            </p>
            <div align="center">
               <button class="btn btn-success" id="update_username_button" name="update_username_button"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($code_vip_sql->rowCount() > 0) { ?>
<!-- Modal -->
<div class="modal fade" id="vip_help" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4>Comment entrer un code VIP ?</h4>
            </center>
            <p><strong>1.</strong> Connecte-toi à ton compte <strong>MovieStarPlanet</strong><br>
               <strong>2.</strong> Clique sur le bouton <strong>"Options"</strong> en haut à droite de ton écran<br>
               <strong>3.</strong> Clique sur le bouton <strong>"Utiliser la Carte Cadeau"</strong><br>
               <strong>4.</strong> Tape le code Star VIP et clique sur <strong>"OK"</strong><br>
            </p>
            <div align="center">
               <a type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php require 'includes/footer.php'; ?>
<script src="js/jquery.table2excel.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $("#download_table_code_button").click(function() {
    $("#code_vip_table").table2excel( {
        name: "Tableau excel codes VIP",
        filename: "Tableau excel codes VIP",
        fileext: ".xls",
        exclude_img: true,
        exclude_links: true,
        exclude_inputs: true
    });
  });
<?php if(isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "n") { ?>
  $('#notification_elements_div').html('');
      $('#loading').show();
      $("#notification_elements_div").load("npr/notification_elements", function() {
          $('#loading').hide();
      });
<?php } ?>
});
</script>