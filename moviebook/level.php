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
    $user_id = htmlspecialchars($_GET['u']);

    if(is_profile_exist($user_id)) {
        update_achievements($user_id);

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $user_id
        ));
        $account_data = $sql->fetch();

        $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
        $sql->execute(array(
            $user_id
        ));
        $profile_data = $sql->fetch();

        $ranking_1_sql = $database2->prepare('SELECT * FROM profile ORDER BY level_points DESC LIMIT 0,10');
        $ranking_1_sql->execute();

        if($user_id == $_SESSION['id']) {
            $it_is_my_page = true;

            if($account_data['sponsorship_key'] == NULL) {
              $is_sponsorship = false;
            } else {
              $is_sponsorship = true;
              $sponsorship_key = $account_data['sponsorship_key'];
              $sponsorship_link = $actual_link."/login?t=register&uID=".$_SESSION['id']."&sKey=".$sponsorship_key;
            }

        } else {
            $it_is_my_page = false;
        }
    } else {
        header("location: level?u=" . $_SESSION['id']);
    }
} else {
    header("location: level?u=" . $_SESSION['id']);
}

$current_page_title = "Niveau de " . $account_data['username'];
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title"><?php if($it_is_my_page == true) { echo "Mon niveau"; } else { echo "Son niveau"; } ?></h1>
</center>
<br>
<div class="row">
   <div class="col-md-8">
      <div class="panel panel-default">
         <div class="panel-body">
            <p>Augmente ton niveau pour te la <strong class="text-orange">PÉTER</strong> devant les autres ! Les niveaux et les succès sont aussi des objectifs à atteindre, c'est toujours bien d'en avoir. Pour monter des niveaux, il faut gagner des points. Comment faire ? Il y a de nombreux moyens pour gagner des niveaux. Les voici :</p>
            <div class="row">
               <div class="col-md-6">
                  <li>Gagner des succès <small class="text-orange"><strong>(pts qui varient)</strong></small></li>
                  <li>Recevoir un like <small class="text-orange"><strong>(5pts)</strong></small></li>
                  <li>Créer un sujet <small class="text-orange"><strong>(200pts)</strong></small></li>
               </div>
               <div class="col-md-6">
                  <li>Terminer un concours <small class="text-orange"><strong>(500pts)</strong></small></li>
                  <li>Se faire un nouvel ami <small class="text-orange"><strong>(20pts)</strong></small></li>
                  <li>Recevoir une publication <small class="text-orange"><strong>(100pts)</strong></small></li>
               </div>
            </div>
            <br>
            <legend></legend>
            <h4>Niveau <strong class="text-orange"><?= get_level($account_data['id']) ?></strong></h4>
            <div align="center">
               <div class="progress" style="margin-bottom:0px;">
                  <div class="progress-bar progress-bar-success" style="width: <?= get_width_progress_bar_level($user_id, $profile_data['username_id'], $profile_data['level_points']) ?>%"></div>
               </div>
                <h5><?php if($it_is_my_page == true) { echo "Tu as "; } elseif($profile_data['sexe'] == "Garçon") { echo "Il a "; } else { echo "Elle a "; } ?><strong class="text-orange"><?= number_format($profile_data['level_points'], 0, '', ' ') ?></strong> point<?php if($profile_data['level_points'] > 1) { echo "s"; } ?></h5>
            </div>
            <?php if($it_is_my_page == true) { ?>
            <a href="../account?t=ph" class="pull-right">Voir l'historique de mes points</a>
            <br>
            <?php } ?>
            <legend></legend>
            <br>
            <div class="row">
               <?php for ($i = 0; $i < count($achievements); $i++) {
                  $sql = $database2->prepare('SELECT * FROM achievements WHERE name = ? AND username_id = ? ORDER BY id DESC');
                  $sql->execute(array(
                      $achievements[$i]['name'],
                      $user_id
                  ));
                  $achievements_data = $sql->fetch();

                  if($achievements[$i]['name'] == "number_of_friends") {
                    $sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ?');
                    $sql->execute(array(
                        $user_id,
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "number_of_posts") {
                    $sql = $database2->prepare('SELECT * FROM post_wall WHERE posted_by = ? AND deleted = 0');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "number_of_posts_received") {
                    $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND posted_by != ? AND deleted = 0');
                    $sql->execute(array(
                        $user_id,
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "number_of_replies") {
                    $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE username_id = ? AND deleted = 0');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "contests_ended") {
                    $sql = $database->prepare('SELECT * FROM contest_ended WHERE username_id = ?');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "subjects_created") {
                    $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "comments_subjects") {
                    $sql = $database->prepare('SELECT * FROM subject_comments WHERE username_id = ? AND deleted = 0');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "account_created_since") {
                    $time_ago = strtotime($account_data['join_date']);
                    $current_time = time();
                    $time_difference = $current_time - $time_ago;
                    $seconds = $time_difference;
                    $achievement_progress_count = round($seconds / 2629440);
                  } elseif($achievements[$i]['name'] == "profile_picture_added") {
                    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND avatar != "default_avatar_m.png" AND avatar != "default_avatar_f.png"');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "banner_picture_added") {
                    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND banner IS NOT NULL');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "background_picture_added") {
                    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND background != "DEFAULT"');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "music_background_added") {
                    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND music_background IS NOT NULL');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "msp_account_linked") {
                    $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } elseif($achievements[$i]['name'] == "informations_added") {
                    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND name IS NOT NULL AND description IS NOT NULL AND date_of_birth IS NOT NULL');
                    $sql->execute(array(
                        $user_id
                    ));
                    $achievement_progress_count = $sql->rowCount();
                  } ?>
               <div class="col-md-6">
                  <div class="panel panel-default">
                     <div class="panel-body">
                        <strong><?= $achievements[$i]['francais'] ?></strong>
                        <button class="btn btn-sm pull-right
                           <?php if($achievements[$i]['type'] == "various") {
                              if($achievements_data['state'] == 0) {
                                $achievement_state = 1;
                              } elseif($achievements_data['state'] == 1) {
                                $achievement_state = 2;
                              } elseif($achievements_data['state'] == 2 OR $achievements_data['state'] == 3) {
                                $achievement_state = 3;
                              }

                              $total_needed = $achievements[$i]['state_'.$achievement_state];

                              if($achievement_progress_count < $achievements[$i]['state_'.$achievement_state] OR $achievements_data['state'] == 3 OR $it_is_my_page == false)
                                { echo 'btn-warning disabled';
                              } else {
                              echo 'btn-success receive_award_achievement_button" achievement_name="'.$achievements[$i]['name'].'';
                               }
                              } elseif($achievements[$i]['type'] == "once") {

                               $total_needed = $achievements[$i]['state_1'];

                                if($achievement_progress_count == 1 AND $achievements_data['state'] == 0 AND $it_is_my_page == true) {
                                  echo 'btn-success receive_award_achievement_button" achievement_name="'.$achievements[$i]['name'].'';
                                } else {
                                  echo 'btn-warning disabled';
                                }
                              }
                              $width = round(($achievement_progress_count / $total_needed) * 100,2); ?>
                           ">Réclamer</button><br>
                        <?= $achievement_progress_count ?>/<?php if($achievements[$i]['type'] == "various") { if($achievements_data['state'] == 0) { echo $achievements[$i]['state_1']; } elseif($achievements_data['state'] == 1) { echo $achievements[$i]['state_2']; } elseif($achievements_data['state'] == 2 OR $achievements_data['state'] == 3) { echo $achievements[$i]['state_3']; } } else { echo "1"; } if($achievements[$i]['name'] == "account_created_since") { echo ' <strong class="text-orange"><small>mois</small></strong>'; } ?>
                        <br><br>
                        <div class="progress" style="margin-bottom:0px;">
                           <div class="progress-bar progress-bar-success" style="width:<?= $width ?>%"></div>
                        </div>
                        <center>Niveau <?php echo $achievements_data['state']."/"; if($achievements[$i]['type'] == "once") { echo "1"; } else { echo "3"; } ?></center>
                     </div>
                  </div>
               </div>
               <?php } ?>
            </div>
            <?php if($it_is_my_page == true) { ?>
            <br>
            <h5>Parrainage</h5>
            <legend></legend>
            <p>Invite tes amis à rejoindre le site et gagne <strong class="text-orange">1000 points</strong> pour ton niveau !<br>
               Copie ce lien et envoie le à tes amis. Ils doivent ensuite s'inscrire, c'est tout simple.<br>
               <strong class="text-orange">Attention</strong>, ne change pas le lien si tu veux recevoir tes points !
            </p>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="row">
                     <?php if($is_sponsorship == true) { ?>
                     <div class="col-md-9">
                        <div class="form-group">
                           <input class="form-control input-lg" type="text" id="sponsorship_link_text_box" placeholder="<?= $sponsorship_link ?>" readonly>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <a class="btn btn-lg btn-primary" id="copy_sponsorship_link_button" name="copy_sponsorship_link_button" link="<?= $sponsorship_link ?>">Copier</a>
                        <button class="btn btn-lg btn-danger" id="delete_sponsorship_link_button" name="delete_sponsorship_link_button"><span class="glyphicon glyphicon-trash"></span></button>
                     </div>
                     <?php } else { ?>
                     <center><button class="btn btn-lg btn-primary" id="create_sponsorship_link_button" name="create_sponsorship_link_button">Créer mon lien !</button></center>
                     <?php } ?>
                  </div>
               </div>
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="panel panel-default">
         <div class="panel-body">
            <h5>Classement des niveaux</strong></h5>
            <legend></legend>
            <?php $i = 0;
               while($ranking_1_data = $ranking_1_sql->fetch()) {
               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $sql->execute(array(
                   $ranking_1_data['username_id']
               ));
               $account_data = $sql->fetch();
               $i++; ?>
            <span style="font-size: 200%"><?php if($i == 1) { echo '1<span style="font-size: 70%">er</span>'; } else { echo $i.'<span style="font-size: 70%">ème</span>'; } ?></span>
            <span>(<strong class="text-orange"><?= number_format($ranking_1_data['level_points'], 0, '', ' ') ?></strong> pts)</span>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="media">
                     <div class="media-left">
                        <a href="../moviebook/profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="../img/moviebook/avatars/<?= $ranking_1_data['avatar'] ?>"></a>
                     </div>
                     <div class="media-body">
                        <h5><a href="../moviebook/level?u=<?= $account_data['id'] ?>"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?></h5>
                     </div>
                  </div>
               </div>
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
<?php require '../includes/footer.php'; ?>