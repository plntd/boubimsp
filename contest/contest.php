<?php
include('../includes/config.php');

if(is_connected() == false) {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accèder à cette page.";
    header('Location: ../login?r='.$_SERVER['REQUEST_URI']);
    exit();
}

if(isset($_GET['c']) AND !empty($_GET['c']) AND strlen($_GET['c']) > 0) {
    $contest_id = htmlspecialchars($_GET['c']);

    $contest_sql = $database->prepare('SELECT * FROM contest WHERE contest_id = ? AND deleted = 0');
    $contest_sql->execute(array(
        $contest_id
    ));

    if($contest_sql->rowCount() == 1) {
        $contest_data = $contest_sql->fetch();

        if(is_profile_exist($contest_data['username_id'])) {
            $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
            $sql->execute(array(
                $contest_data['username_id']
            ));
            if($sql->rowCount() == 1) {
                $msp_account_data = $sql->fetch();
                if($msp_account_data['is_confirmed'] == 1) {
                    $contest_owner_msp_username = $msp_account_data['msp_username'] . ' <img data-toggle="tooltip" title="Ce compte MSP lui appartient" src="../img/moviebook/certified_badge.png" width="15" height="15">';
                } else {
                    $contest_owner_msp_username = $msp_account_data['msp_username'];
                }
                $is_msp_username = true;
            } else {
                $is_msp_username = false;
            }
        } else {
            $is_msp_username = false;
        }

        if($contest_data['username_id'] == $_SESSION['id']) {
            $is_my_contest = true;
            $sql = $database->prepare('SELECT * FROM contest WHERE contest_id = ? AND username_id = ? AND deleted = 0 AND creation_date < DATE_SUB(NOW(), INTERVAL 1 DAY)');
            $sql->execute(array(
                $contest_id,
                $_SESSION['id']
            ));
            $can_finish_contest = $sql->rowCount();

        } else {
            $sql = $database->prepare('SELECT * FROM contest_participants WHERE username_id = ? AND contest_id = ?');
            $sql->execute(array(
                $_SESSION['id'],
                $contest_data['id']
            ));
            $me_participant_data = $sql->fetch();
            $is_in_contest = $sql->rowCount();
            $is_my_contest = false;

            if($contest_data['allow_only_msp_account_linked'] == 1) {
                $allow_only_msp_account_linked = true;
            } else {
                $allow_only_msp_account_linked = false;
            }
        }

        $participants_contest_sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ? ORDER BY id');
        $participants_contest_sql->execute(array(
            $contest_data['id']
        ));
        $nop = $participants_contest_sql->rowCount();

        $account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $account_sql->execute(array(
            $contest_data['username_id']
        ));
        $owner_account_data = $account_sql->fetch();

        $prices_sql = $database->prepare('SELECT * FROM contest_prices WHERE contest_id = ? ORDER BY id');
        $prices_sql->execute(array(
            $contest_data['id']
        ));

        $total_of_comments_sql = $database->prepare('SELECT * FROM contest_comments WHERE contest_id = ?');
        $total_of_comments_sql->execute(array(
            $contest_data['id']
        ));
        $total_of_comments = $total_of_comments_sql->rowCount();

        $comments_per_page = 6;
        $total_of_page = ceil($total_of_comments / $comments_per_page);

        if(isset($_GET['cp']) AND !empty($_GET['cp']) AND is_numeric($_GET['cp']) AND strlen($_GET['cp']) > 0 AND $_GET['cp'] <= $total_of_page) {
            $cp = htmlspecialchars($_GET['cp']);
            $current_page = $cp;
        } else {
            $current_page = 1;
        }

        $page_start = ($current_page - 1) * $comments_per_page;

        $contest_comments_sql = $database->prepare('SELECT * FROM contest_comments WHERE contest_id = ? ORDER BY id DESC LIMIT ' . $page_start . ',' . $comments_per_page);
        $contest_comments_sql->execute(array(
            $contest_data['id']
        ));

    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "L\'ID du concours est <strong>invalide</strong>.";
        header("Location: home");
        exit();
    }
} else {
    header("Location: home");
    exit();
}

$current_page_title = "Concours de " . $owner_account_data['username'];
require '../includes/header.php';

$token = uniqid(rand(), true);
$_SESSION['token'] = $token;
$_SESSION['token_time'] = time();

if(is_profile_exist($owner_account_data['id'])) {
    $username_str = '<a href="../moviebook/profile?u=' . $owner_account_data['id'] . '" target="_blank">' . $owner_account_data['username'] . '</a>';
} else {
    $username_str = $owner_account_data['username'];
}

if(isset($_POST['contest_update_info_button']) AND !empty($_POST['contest_update_info_button'])) {
    if(isset($_POST['title_contest_text_box']) AND !empty($_POST['title_contest_text_box']) AND isset($_POST['description_text_area']) AND !empty($_POST['description_text_area'])) {
        $title = htmlspecialchars($_POST['title_contest_text_box']);
        $description = htmlspecialchars($_POST['description_text_area']);
        $title = trim($title);
        $description = trim($description);
        if(mb_strlen($title, 'UTF-8') <= 30 OR mb_strlen($title, 'UTF-8') >= 5) {
            if(mb_strlen($description, 'UTF-8') <= 1000) {
                if(isset($_POST['max_participant_combo_box']) AND !empty($_POST['max_participant_combo_box'])) {
                    if(isset($_POST['contest_type_radio']) AND !empty($_POST['contest_type_radio'])) {

                        if(isset($_POST['notification_new_participant_checkbox']) AND !empty($_POST['notification_new_participant_checkbox'])) {
                            $notification_new_participant = 1;
                        } else {
                            $notification_new_participant = 0;
                        }

                        if(isset($_POST['participate_button_list_checkbox']) AND !empty($_POST['participate_button_list_checkbox'])) {
                            $participate_button_list = 1;
                        } else {
                            $participate_button_list = 0;
                        }

                        if(isset($_POST['allow_only_msp_account_linked_checkbox']) AND !empty($_POST['allow_only_msp_account_linked_checkbox'])) {
                            $allow_only_msp_account_linked = 1;
                        } else {
                            $allow_only_msp_account_linked = 0;
                        }

                        if(isset($_POST['comments_contest_checkbox']) AND !empty($_POST['comments_contest_checkbox'])) {
                            $comments_area = 1;
                            if(isset($_POST['notification_new_comment_checkbox']) AND !empty($_POST['notification_new_comment_checkbox'])) {
                                $notification_comments = 1;
                            } else {
                                $notification_comments = 0;
                            }
                            if(isset($_POST['only_participants_allow_to_comment_checkbox']) AND !empty($_POST['only_participants_allow_to_comment_checkbox'])) {
                                $only_participants_allow_to_comment = 1;
                            } else {
                                $only_participants_allow_to_comment = 0;
                            }
                        } else {
                            $comments_area = 0;
                            $notification_comments = 0;
                            $only_participants_allow_to_comment = 0;
                        }

                        $type = htmlspecialchars($_POST['contest_type_radio']);
                        if($type == "public") {
                            $type = 1;
                        } else {
                            $type = 2;
                        }

                        if(isset($_POST['allow_to_participate_checkbox']) AND !empty($_POST['allow_to_participate_checkbox'])) {
                            $allow_to_participate = 1;
                        } else {
                            $allow_to_participate = 0;
                        }

                        $max_participants = htmlspecialchars($_POST['max_participant_combo_box']);
                        $category_contest = htmlspecialchars($_POST['category_contest_combo_box']);

                        if($max_participants != "5" AND $max_participants != "10" AND $max_participants != "30" AND $max_participants != "50" AND $max_participants != "100" AND $max_participants != "300" AND $max_participants != "500") {
                            $max_participants = "unlimited";
                        }

                        if($category_contest != "Concours VIP" AND $category_contest != "Concours cadeaux" AND $category_contest != "Concours rares" AND $category_contest != "Concours salutations") {
                            $category_contest = "Autre";
                        }

                        if($category_contest == "Concours VIP") {
                            $category_contest = "1";
                        } elseif($category_contest == "Concours cadeaux") {
                            $category_contest = "2";
                        } elseif($category_contest == "Concours rares") {
                            $category_contest = "3";
                        } elseif($category_contest == "Concours salutations") {
                            $category_contest = "4";
                        } else {
                            $category_contest = "5";
                        }

                        $sql = $database->prepare('UPDATE contest SET title = ?, description = ?, type = ?, category = ?, max_participants = ?, comments_area = ?, notification_new_participant = ?, notification_comments = ?, only_participants_allow_to_comment = ?, participate_button_list = ?, allow_only_msp_account_linked = ?, allow_to_participate = ? WHERE username_id = ? AND contest_id = ? AND deleted = 0')->execute(array(
                            $title,
                            $description,
                            $type,
                            $category_contest,
                            $max_participants,
                            $comments_area,
                            $notification_new_participant,
                            $notification_comments,
                            $only_participants_allow_to_comment,
                            $participate_button_list,
                            $allow_only_msp_account_linked,
                            $allow_to_participate,
                            $_SESSION['id'],
                            $contest_data['contest_id']
                        ));
                        $_SESSION['flash']['success'] = $success_sign . "Ton concours a été mis-à-jour.";
                        header("Refresh:0");
                        exit();
                    }
                }
            } else {
                $_SESSION['flash']['danger'] = $danger_sign . "Ta description ne peut pas dépasser <strong>1000 caractères</strong>.";
            }
        } else {
            $_SESSION['flash']['danger'] = $danger_sign . "Ton titre ne peut pas dépasser <strong>30 caractères</strong> et dois avoir au <strong>minimim 5 caractères</strong>.";
        }
    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "Tu dois entrer une un titre et une description pour ton coucours.";
    }
}
?>
<br>
<center>
   <a href="home?t=sc" class="btn btn-primary pull-left"><span class="glyphicon glyphicon-arrow-left"></span></a>
   <h1 class="page-title">Page du concours</h1>
</center>
<input type="hidden" id="contest_id_hidden" name="contest_id_hidden" value="<?= $contest_data['id'] ?>">
<input type="hidden" name="token" id="token" value="<?= $token ?>">
<br>
<div class="panel panel-default">
   <div class="panel-body">
      <h4><?= $contest_data['title'] ?></h4>
      <div class="row">
         <div class="col-md-6">
            <legend></legend>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <?= return_string($contest_data['description']) ?>
         </div>
         <div class="col-md-6">
            <?php if($prices_sql->rowCount() > 0) { ?>
            <strong>Récompenses :</strong><br>
            <?php $i = 0;
               while($prices_data = $prices_sql->fetch()) {
                $i++;
                echo $i.". ".$prices_data['name']."<br>";
               } ?>
            <br>
            <?php } ?>
            <strong>Créateur :</strong> <?= $username_str ?> <?= badge_check($contest_data['username_id'], "other") ?><br>
            <strong>Nombre de places :</strong> <?php echo $nop."/"; if($contest_data['max_participants'] == "unlimited") { echo "illimité"; } else { echo $contest_data['max_participants']; } ?><br>
            <?php $creation_date = ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($contest_data['creation_date']))); ?>
            <strong>Date de création :</strong> <?= $creation_date ?> <small><i>(<?= time_ago($contest_data['creation_date']) ?>)</i></small>
            <?php if($is_msp_username == true) { ?>
            <br><strong>Pseudo MSP :</strong> <?= $contest_owner_msp_username ?>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default">
   <div class="panel-body">
      <?php if($is_my_contest == true) { ?>
      <a class="btn btn-success" data-toggle="modal" data-target="#update_contest_modal"><span class="glyphicon glyphicon-pencil"></span> Modifier mon concours</a>
      <a class="btn btn-info" data-toggle="modal" data-target="<?php if($can_finish_contest == 1) { echo "#finish_contest_modal"; } else { echo "#can_not_finish_contest_modal"; } ?>"><span class="glyphicon glyphicon-star"></span> Terminer mon concours</a>
      <a class="btn btn-danger" data-toggle="modal" data-target="#delete_contest_modal"><span class="glyphicon glyphicon-trash"></span> Supprimer mon concours</a>
      <?php if($nop > 1) { ?>
      <a class="btn btn-warning" data-toggle="modal" data-target="#select_random_modal"><span class="glyphicon glyphicon-fire"></span> Tirer au sort</a>
      <?php } ?>
      <?php } else { ?>
      <?php if($is_in_contest == 0) { ?>
      <?php if($contest_data['max_participants'] == "unlimited" OR $nop < $contest_data['max_participants']) {
         if($contest_data['allow_to_participate'] == 1) { ?>
      <button class="btn btn-success participate_contest_button" id="participate_contest_button" name="participate_contest_button" type="join_contest" location="contest_page" contest_id="<?= $contest_data['id'] ?>"><span class="glyphicon glyphicon-plus"></span> Participer</button>
      <?php } else { ?>
      <a class="btn btn-warning" disabled>Participation impossible, le créateur a désactivé les participations</a>
      <?php } ?>
      <?php } else { ?>
      <a class="btn btn-warning" disabled>Participation impossible, le concours est complet</a>
      <?php } ?>
      <?php } else { ?>
      <button class="btn btn-danger participate_contest_button" id="participate_contest_button" name="participate_contest_button" type="leave_contest" location="contest_page" contest_id="<?= $contest_data['id'] ?>"><span class="glyphicon glyphicon-remove"></span> Se désinscrire</button>
      <a class="btn btn-warning" data-toggle="modal" data-target="#participant_settings_modal"><span class="glyphicon glyphicon-cog"></span> Mes paramètres</a>
      <?php } ?>
      <a class="btn btn-warning pull-right" data-toggle="modal" modal_type="contest" data-target="#report_contest_modal" data-contest_id="<?= $contest_data['id'] ?>"><span class="glyphicon glyphicon-flag"></span> Signaler</a>
      <?php } ?>
      <?php if($contest_data['comments_area'] == 1) { ?>
      <a id="go_to_comments_contest_button" class="btn btn-primary"><span class="glyphicon glyphicon-comment"></span> Commentaires</a>
      <?php } ?>
      <?php if($_SESSION['id'] == 1) { ?>
      <div class="btn-group">
         <a class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Actions <span class="caret"></span></a>
         <ul class="dropdown-menu">
            <li><a href="#!" id="admin_delete_contest_button" name="admin_delete_contest_button">Supprimer le concours</a></li>
            <li><a href="#!" class="notification_contest_button" contest_id="<?= $contest_data['id'] ?>" type="contest_need_password">Notification concours qui demande un mot de passe</a></li>
            <li><a href="#!" class="notification_contest_button" contest_id="<?= $contest_data['id'] ?>" type="contest_price_do_not_have">Notification récompense que l'auteur n'a pas</a></li>
         </ul>
      </div>
      <?php } ?>
   </div>
</div>
<div class="panel panel-default">
   <div class="panel-body">
      <h4>Liste des participants</h4>
      <legend></legend>
      <?php if($nop != 0) { ?>
      Il y a <strong><?= $nop ?></strong> participant<?php if($nop > 1) { echo "s"; } ?> au total. <?php if($contest_data['max_participants'] != "unlimited") { $sub = $contest_data['max_participants'] - $nop; if($sub > 0) { echo "Il reste encore <strong>".$sub."</strong> place(s) de libre."; } } ?>
      <table class="table table-bordered table-hover" id="participants_table">
         <thead>
            <tr>
               <th>
                  <center>#</center>
               </th>
               <th>
                  <center>Pseudo</center>
               </th>
               <th>
                  <center>Pseudo MSP</center>
               </th>
               <th>
                  <center>Date de participation</center>
               </th>
               <?php if($is_my_contest == true) { ?>
               <th>
                  <center></center>
               </th>
               <?php } ?>
            </tr>
         </thead>
         <tbody>
            <?php $i = 0; ?>
            <?php while($participant_data = $participants_contest_sql->fetch()) {
               $i++;
               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $sql->execute(array($participant_data['username_id']));
               $account_data = $sql->fetch();

               if(is_profile_exist($account_data['id'])) {
                 $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
                 $sql->execute(array($participant_data['username_id']));
                 if($sql->rowCount() == 1) {
                  $msp_account_data = $sql->fetch();
                  $msp_username_str = $msp_account_data['msp_username'];
                 } else {
                  $msp_username_str = "aucun";
                 }
                 $username_str = '<a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . $account_data['username'] . '</a>';
               } else {
                 $msp_username_str = "aucun";
                 $username_str = $account_data['username'];
               }
               $join_date = strftime('%d/%m/%y à %H:%M', strtotime($participant_data['join_date']));
               if($participant_data['username_id'] == $_SESSION['id']) {
                    $class = "success";
                  } else {
                    $class = "active";
               } ?>
            <tr class="<?= $class ?>">
               <td class="id_cell">
                  <center><?= $i; ?></center>
               </td>
               <td>
                  <center><?= $username_str ?></center>
               </td>
               <td>
                  <center><?= $msp_username_str ?></center>
               </td>
               <td>
                  <center><?= $join_date ?></center>
               </td>
               <?php if($is_my_contest == true) { ?>
               <td>
                  <center><a class="btn btn-danger btn-xs" data-participant_id="<?= $participant_data['id'] ?>" modal_type="delete_participant" data-toggle="modal" data-target="#delete_participant_modal"><span class="glyphicon glyphicon-remove"></span> Supprimer</a></center>
               </td>
               <?php } ?>
            </tr>
            <?php } ?>
         </tbody>
      </table>
      <?php } else { ?>
      <div align="center">
         <h4>Oups ! On dirait qu'il y a aucune personne qui participe à ce concours.</h4>
      </div>
      <?php } ?>
   </div>
</div>
<?php if($contest_data['comments_area'] == 1) { ?>
<div class="row">
<div class="col-md-10 col-centered">
   <div class="panel panel-default">
      <div class="panel-heading">
         <center>
            <h3 class="text-orange">Commente ce concours</h3>
         </center>
      </div>
      <div id="gtb"></div>
      <div class="panel-body">
         <?php if($is_my_contest == true OR $contest_data['only_participants_allow_to_comment'] == 1 AND $is_in_contest == 1 OR $contest_data['only_participants_allow_to_comment'] == 0) { ?>
         <textarea class="form-control" rows="3" id="comment_textarea" name="comment_textarea" maxlength="1000" style="resize: none;"></textarea>
         <br>
         <button class="btn btn-success" id="post_comment_contest_button" name="post_comment_contest_button" contest_id="<?= $contest_data['id'] ?>">Poster le commentaire</button>
         <a class="btn btn-default pull-right" data-toggle="modal" data-target="#smileys_modal"><span class="glyphicon glyphicon-plus"></span> Smiley</a><br>
         <?php } else { ?>
         <textarea class="form-control" rows="3" id="comment_textarea" name="comment_textarea" maxlength="1000" style="resize: none;" disabled></textarea>
         <br>
         <a class="btn btn-success" disabled>Seuls les participants peuvent commenter</a>
         <a class="btn btn-default pull-right disabled"><span class="glyphicon glyphicon-plus"></span> Smiley</a><br>
         <?php } ?>
         <br>
         <?php
            if($contest_comments_sql->rowCount() >= 1) {
            while($comment_data = $contest_comments_sql->fetch()) {
            $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
            $sql->execute(array(
                $comment_data['username_id']
            ));
            $account_data = $sql->fetch();

            if(is_profile_exist($comment_data['username_id']) == true) {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
                $sql->execute(array($comment_data['username_id']));
                $profile_data = $sql->fetch();

                $profile_picture = $profile_data['avatar'];
            } else {
                $profile_picture = "default_avatar_m.png";
            }
            if($contest_data['username_id'] == $account_data['id']) { ?>
         <div class="panel panel-info">
            <div class="panel-heading">
               Créateur du concours
            </div>
            <?php } else { ?>
            <div class="panel panel-default">
               <?php } ?>
               <div class="panel-body">
                  <div class="media">
                     <div class="media-left">
                        <a href="../moviebook/profile?u=<?= $account_data['id'] ?>">
                        <img class="media-object" height="64" width="64" src="../img/moviebook/avatars/<?= $profile_picture ?>">
                        </a>
                     </div>
                     <div class="media-body">
                        <a href="../moviebook/profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($comment_data['post_date']) ?></small></label><?php if(is_connected() == true AND $_SESSION['id'] == $comment_data['username_id']) { ?>
                        <a class="btn btn-danger btn-sm pull-right" modal_type="contest_delete_comment_modal" data-comment_id="<?= $comment_data['id'] ?>" data-toggle="modal" data-target="#delete_comment_modal"><span class="glyphicon glyphicon-trash"></span></a>
                        <?php } ?><br>
                        <?= return_string($comment_data['content']) ?>
                     </div>
                  </div>
               </div>
            </div>
            <?php } ?>
            <?php if($total_of_comments > 6) { ?>
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="contest?c=<?= $contest_data['contest_id'] ?>&cp=<?= $current_page - 1 ?>&t=gtb">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="contest?c=<?= $contest_data['contest_id'] ?>&cp=<?= $i ?>&t=gtb"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="contest?c=<?= $contest_data['contest_id'] ?>&cp=<?= $current_page + 1 ?>&t=gtb">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
            </center>
            <?php } ?>
         </div>
      </div>
      <?php } else { ?>
      <center>
         <br>
         <h4>Il n'y a aucun commentaire. Sois le premier à en poster un !</h4>
      </center>
      <?php } ?>
   </div>
</div>
<?php } ?>
<?php if($is_my_contest == false AND $is_in_contest == 1) { ?>
<!-- Modal -->
<div class="modal fade" id="participant_settings_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Mes paramètres</h4>
            </div>
            <div class="panel panel-default">
               <div class="panel-body">
                  <label>
                  <input type="checkbox" name="notification_comment_author_checkbox" id="notification_comment_author_checkbox" <?php if($me_participant_data['notification_comment'] == 1) { echo "checked"; } ?>> Reçevoir une notification à chaque nouveau commentaire du créateur</a>
                  </label>
                  <label>
                  <input type="checkbox" name="notification_delete_contest_checkbox" id="notification_delete_contest_checkbox" <?php if($me_participant_data['notification_delete_contest'] == 1) { echo "checked"; } ?>> Reçevoir une notification lorsque le concours sera supprimé</a>
                  </label>
                  <label>
                  <input type="checkbox" name="notification_end_contest_checkbox" id="notification_end_contest_checkbox" <?php if($me_participant_data['notification_end_contest'] == 1) { echo "checked"; } ?>> Reçevoir une notification lorsque le concours sera terminé</a>
                  </label>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" id="participant_settings_contest_button" name="participant_settings_contest_button"><span class="glyphicon glyphicon-ok"></span></button> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($is_my_contest == true) { ?>
<!-- Modal -->
<div class="modal fade" id="update_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Modifier mon concours</h4>
            </div>
            <form method="POST">
               <div class="panel panel-default">
                  <div class="panel-body">
                     <div class="form-group">
                        <label class="control-label" for="title_contest_text_box">Titre du concours <strong>(5 caractères minimum)</strong></label>
                        <input type="text" class="form-control" placeholder="Ex : Gagne des snakes" id="title_contest_text_box" name="title_contest_text_box" maxlength="30" value="<?= $contest_data['title'] ?>">
                     </div>
                     <div class="form-group">
                        <label class="control-label" for="description_text_area">Description et règles</label>
                        <textarea class="form-control" rows="5" id="description_text_area" name="description_text_area" maxlength="1000" placeholder="Ex : Salut, je crée un concours pour vous remercier des 500 abonnés ! Les règles sont simples : tu dois faire un artbook sur le thème horreur et m'envoyer ton pseudo MSP par Skype. Je désignerai les meilleurs artbooks !&#x0a;Mon Skype : xxxxxxx&#x0a;Ma chaîne Youtube : xxxxxxx" style="resize: none;"><?= $contest_data['description'] ?></textarea>
                     </div>
                     <legend></legend>
                     <div class="row">
                        <div class="col-md-6">
                           <center>
                              <label>Type de concours</label>
                           </center>
                           <label><input type="radio" name="contest_type_radio" id="public_radio_button" value="public" <?php if($contest_data['type'] == 1) { echo "checked"; } ?>>Publique</label><br>
                           <label><input type="radio" name="contest_type_radio" id="unlisted_radio_button" value="unlisted" <?php if($contest_data['type'] == 2) { echo "checked"; } ?>>Non listé</label>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <center>
                                 <label class="control-label" for="color_select">Nombre max. de participants</label>
                              </center>
                              <select class="form-control" id="max_participant_combo_box" name="max_participant_combo_box">
                                 <option id="1" <?php if($contest_data['max_participants'] == 5) { echo "selected"; } ?>>5</option>
                                 <option id="2" <?php if($contest_data['max_participants'] == 10) { echo "selected"; } ?>>10</option>
                                 <option id="3" <?php if($contest_data['max_participants'] == 30) { echo "selected"; } ?>>30</option>
                                 <option id="4" <?php if($contest_data['max_participants'] == 50) { echo "selected"; } ?>>50</option>
                                 <option id="5" <?php if($contest_data['max_participants'] == 100) { echo "selected"; } ?>>100</option>
                                 <option id="6" <?php if($contest_data['max_participants'] == 300) { echo "selected"; } ?>>300</option>
                                 <option id="7" <?php if($contest_data['max_participants'] == 500) { echo "selected"; } ?>>500</option>
                                 <option id="8" <?php if($contest_data['max_participants'] == "unlimited") { echo "selected"; } ?>>Illimité</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <center>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <center>
                                    <label class="control-label" for="category_contest_combo_box">Type de concours</label>
                                 </center>
                                 <select class="form-control" id="category_contest_combo_box" name="category_contest_combo_box">
                                    <option id="1" <?php if($contest_data['category'] == 1) { echo "selected"; } ?>>Concours VIP</option>
                                    <option id="2" <?php if($contest_data['category'] == 2) { echo "selected"; } ?>>Concours cadeaux</option>
                                    <option id="3" <?php if($contest_data['category'] == 3) { echo "selected"; } ?>>Concours rares</option>
                                    <option id="4" <?php if($contest_data['category'] == 4) { echo "selected"; } ?>>Concours salutations</option>
                                    <option id="5" <?php if($contest_data['category'] == 5) { echo "selected"; } ?>>Autre</option>
                                 </select>
                              </div>
                           </div>
                        </center>
                     </div>
                     <label><input type="checkbox" name="comments_contest_checkbox" id="comments_contest_checkbox" <?php if($contest_data['comments_area'] == 1) { echo "checked"; } ?>>Activer l'espace commentaire</a></label>
                     <label><input type="checkbox" name="only_participants_allow_to_comment_checkbox" id="only_participants_allow_to_comment_checkbox" <?php if($contest_data['comments_area'] == 1) { if($contest_data['only_participants_allow_to_comment'] == 1) { echo "checked"; } } else { echo "disabled"; } ?>>Autoriser seulement les participants à commenter</a></label>
                     <label><input type="checkbox" name="participate_button_list_checkbox" id="participate_button_list_checkbox" <?php if($contest_data['participate_button_list'] == 1) { echo "checked"; } ?>>Mettre le bouton "Participer" dans la liste des concours</a></label>
                     <label><input type="checkbox" name="allow_only_msp_account_linked_checkbox" id="allow_only_msp_account_linked_checkbox" <?php if($contest_data['allow_only_msp_account_linked'] == 1) { echo "checked"; } ?>>Autoriser seulement ceux qui ont un compte MSP associé à participer</a></label>
                     <legend></legend>
                     <label><input type="checkbox" name="notification_new_participant_checkbox" id="notification_new_participant_checkbox" <?php if($contest_data['notification_new_participant'] == 1) { echo "checked"; } ?>>Reçevoir une notification à chaque nouveau participant</a></label>
                     <label><input type="checkbox" name="notification_new_comment_checkbox" id="notification_new_comment_checkbox" <?php if($contest_data['comments_area'] == 1) { if($contest_data['notification_comments'] == 1) { echo "checked"; } } else { echo "disabled"; } ?>>Reçevoir une notification à chaque nouveau commentaire</a></label>
                     <label><input type="checkbox" name="allow_to_participate_checkbox" id="allow_to_participate_checkbox" <?php if($contest_data['allow_to_participate'] == 1) { echo "checked"; } ?>>Autoriser les participations</a></label>
                  </div>
               </div>
               <div align="center">
                  <input type="submit" class="btn btn-success" id="contest_update_info_button" name="contest_update_info_button" value="Sauvegarder"> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="delete_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Supprimer mon concours</h4>
            </div>
            <p><strong>Cette action est irréversible</strong>. Tous les participants seront supprimés.<br>
               Veux-tu vraiment supprimer ton concours ?
            </p>
            <div align="center">
               <button class="btn btn-success" id="delete_contest_button" name="delete_contest_button" contest_id="<?= $contest_data['id'] ?>"><span class="glyphicon glyphicon-ok"></span> Oui</button> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="delete_participant_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Supprimer un participant</h4>
            </div>
            <input type="hidden" name="participant_id_hidden" id="participant_id_hidden">
            <p>Veux-tu vraiment supprimer ce participant de la liste ? Même si tu le supprimes, il pourra quand même participer plus tard s'il le veut. Il sera également averti.</p>
            <div align="center">
               <button class="btn btn-success" id="remove_participant_button" name="remove_participant_button"><span class="glyphicon glyphicon-ok"></span> Oui</button> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="select_random_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Sélectionner un participant au hasard</h4>
            </div>
            <div align="center">
               <p>Tu as un concours au tirage au sort ? Clique sur "Générer un numéro" pour générer un numéro au hasard. Le numéro qui te sera affiché correspondra au numéro qui est inscrit dans le tableau de tes participants. Pratique, non ?</p>
            </div>
            <div align="center">
               <h4><strong id="random_number_strong"></strong></h4>
               <a class="btn btn-success" id="select_random_number_button" name="select_random_number_button">Générer un numéro</a> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php if($can_finish_contest == 1) { ?>
<!-- Modal -->
<div class="modal fade" id="finish_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Terminer mon concours</h4>
            </div>
            <div class="panel panel-default">
               <div class="panel-body">
                  <p><strong>Cette action est irréversible</strong>. Ton concours sera supprimé et tous les participants seront avertis que tu as terminé ton concours.</p>
                  <p>Ecris un texte pour la finalisation de ton concours ! Annonce les vainqueurs, etc.</p>
                  <legend></legend>
                  <textarea class="form-control" rows="5" id="final_text_contest_textarea" name="final_text_contest_textarea" maxlength="1000" style="resize: none;" placeholder="Ex : Salut donc voilà mon concours est terminé ! Les gagnants sont :&#x0a;1er : xxxxxxx&#x0a;2è : xxxxxxx&#x0a;3è : xxxxxxx&#x0a;Merci à tous d'avoir participé !"></textarea><br>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" id="finish_contest_button" name="finish_contest_button" contest_id="<?= $contest_data['id'] ?>"><span class="glyphicon glyphicon-ok"></span> Oui</button> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } else { ?>
<div class="modal fade" id="can_not_finish_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Terminer mon concours</h4>
            </div>
            <p>Désolé, mais tu ne peux pas terminer ton concours maintenant.<br>
               Ton concours <strong>doit dater de plus d'un jour</strong> pour que tu puisses le terminer.
            </p>
            <div align="center"><a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php } else { ?>
<!-- Modal -->
<div class="modal fade" id="report_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Signaler ce concours</h4>
            </center>
            <div class="panel panel-default">
               <div class="panel-body">
                  <p>Es-tu sûr(e) de signaler ce concours ?</p>
                  <div class="form-group">
                     <textarea class="form-control" rows="3" id="report_info_textarea" name="report_info_textarea" maxlength="1000" style="resize: none;" placeholder="Informations supplémentaires (facultatif)"></textarea>
                  </div>
                  <div align="center">
                     <button class="btn btn-success" id="confirm_contest_report" name="confirm_contest_report"><span class="glyphicon glyphicon-ok"></span></button>
                     <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
                  </div>
               </div>
            </div>
            <small><span class="glyphicon glyphicon-info-sign"></span> Rappel : il est <strong>strictement interdit</strong> de faire des faux signalements, et d'en abuser.</small>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($contest_data['comments_area'] == 1) { ?>
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
            <input type="hidden" name="delete_contest_comment_id_hidden" id="delete_contest_comment_id_hidden">
            <div align="center">
               <button class="btn btn-success" id="delete_contest_comment" name="delete_contest_comment"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<?= smileys_modal("contest_new_comment") ?>
<?php } ?>
<?php if($is_my_contest == false AND $allow_only_msp_account_linked == true) { ?>
<!-- Modal -->
<div class="modal fade" id="allow_only_msp_account_linked_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Participation impossible</h4>
               <p>Oups ! Il semblerait que le créateur de ce concours autorise seulement ceux qui ont un compte MSP associé à leur profil MovieBook à participer...</p>
            </center>
            <div align="center">
               <a class="btn btn-success" href="../moviebook/profile?u=<?= $_SESSION['id'] ?>&t=lma">Associer un compte MSP</a>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php require '../includes/footer.php'; ?>
<script>
<?php if($contest_data['comments_area'] == 1) { ?>
    <?php if(isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "gtb") { ?>
  $('html, body').animate({
  scrollTop: $("#gtb").offset().top
  }, 1000);
  <?php } ?>
$("#go_to_comments_contest_button").click(function() {
$('html, body').animate({
scrollTop: $("#gtb").offset().top
}, 1000);
});
<?php } ?>
</script>