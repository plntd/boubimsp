<?php
include('../includes/config.php');

if(is_connected() == false) {
   $is_connected = false;
} else {
   $is_connected = true;
}

if(isset($_GET['u']) AND !empty($_GET['u']) AND $_GET['u'] == "random") {
    $sql = $database2->prepare('SELECT * FROM profile WHERE banner IS NOT NULL AND avatar != "default_avatar_m.png" OR banner IS NOT NULL AND avatar != "default_avatar_f.png" AND private = 0 ORDER BY rand() LIMIT 1');
    $sql->execute();
    $rand = $sql->fetch();
    $profile_id = $rand['username_id'];
    header("Location: profile?u=" . $profile_id);
    exit();
} elseif(isset($_GET['u']) AND !empty($_GET['u']) AND is_numeric($_GET['u']) AND strlen($_GET['u']) > 0) {
    $profile_id = htmlspecialchars($_GET['u']);

    $account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $account_sql->execute(array(
        $profile_id
    ));

    if($account_sql->rowCount() == 1) {
    $account_data = $account_sql->fetch();

    $email_address_profile = $account_data['email_address'];
    $join_date_profile = $account_data['join_date'];
    $username_profile = $account_data['username'];
    $profile_id = $account_data['id'];
    $badge_token = $account_data['badge_token'];

    $profile_sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
    $profile_sql->execute(array(
        $profile_id
    ));
    $profile_data = $profile_sql->fetch();

    $web_description = $profile_data['description'];

    $sexe_profile = $profile_data['sexe'];
    $youtube_link_profile = $profile_data['music_background'];
    $primary_color = $profile_data['primary_color'];
    $no_border_avatar = $profile_data['no_border_avatar'];
    $no_border_banner = $profile_data['no_border_banner'];
    $banner_scretch = $profile_data['banner_scretch'];
    $background_fixed = $profile_data['background_fixed'];
    $background = $profile_data['background'];
    $background_scretch = $profile_data['background_scretch'];
    $view_post = $profile_data['view_post'];
    $private_profile = $profile_data['private'];

    if($is_connected == true AND $_SESSION['id'] == $account_data['id']) {
        $it_is_my_profile = true;
        $current_page_title = "Mon profil";
    } else {
        $it_is_my_profile = false;
        $current_page_title = "Profil de " . $account_data['username'];
    }

    if($it_is_my_profile == false) {
      $users_online_sql = $database2->prepare('SELECT * FROM users_online WHERE username_id = ?');
      $users_online_sql->execute(array($account_data['id']));
      if($users_online_sql->rowCount() == 1) {
        $users_online_data = $users_online_sql->fetch();
        if($users_online_data['deleted'] == 1) {
          $last_connection = time_ago($users_online_data['last_connection']);
        } else {
          $last_connection = "en ligne";
        }
      } else {
        $last_connection = "inconnu";
      }
    }

    if($private_profile == 1 AND $it_is_my_profile == false) {
      $_SESSION['flash']['danger'] = $danger_sign."Désolé, mais ce profil est en privé. Il est sans doute en train de se refaire une beauté...";
      header("Location: home");
      exit();
    }

    if(isset($profile_data['banner_color']) AND !empty($profile_data['banner_color'])) {
        $banner_color = $profile_data['banner_color'];
    } else {
        //Si le profil est inexistant.
        $banner_color = "#56966d";
    }

    $msp_account_sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ? ORDER BY id DESC');
    $msp_account_sql->execute(array(
        $profile_id
    ));
    $msp_account_data = $msp_account_sql->fetch();

    if(isset($msp_account_data) AND !empty($msp_account_data)) {
        $is_there_msp_account = true;
    } else {
        $is_there_msp_account = false;
    }

    if(empty($profile_data['name']) AND empty($profile_data['date_of_birth']) AND empty($profile_data['description'])) {
        $is_there_info = false;
    } else {
        $is_there_info = true;
    }

    $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND deleted = 0');
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

    $sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ?');
    $sql->execute(array(
        $profile_id,
        $profile_id
    ));
    $total_friends_count = $sql->rowCount();

    $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC LIMIT 0,9');
    $friends_sql->execute(array(
        $profile_id,
        $profile_id
    ));

    if(isset($profile_data['music_background']) AND !empty($profile_data['music_background'])) {
        $youtube_link = $profile_data['music_background'];
        $parts = parse_url($profile_data['music_background']);
        parse_str($parts['query'], $query);
        $music_v = $query['v'];
        $music_background = true;
        if($profile_data['music_link_button_visible'] == 1) {
            $music_link_button_visible = true;
        } else {
            $music_link_button_visible = false;
        }
    } else {
        $music_background = false;
    }

  } else {
     $_SESSION['flash']['danger'] = $danger_sign . "Désolé, ce profil est introuvable.";
     header("Location: home");
     exit();
  }

} else {
   header("Location: home");
   exit();
}

require '../includes/header.php';

if($it_is_my_profile == true) {

  $badges_pictures_sql = $database2->prepare('SELECT * FROM badge_pictures WHERE user_id = ?');
  $badges_pictures_sql->execute(array(
      $_SESSION['id']
  ));

  if(isset($_POST['mb_custom_save_submit'])) {

      if(isset($_POST['no_border_avatar_checkbox']) AND !empty($_POST['no_border_avatar_checkbox'])) {
          $no_border_avatar = 1;
      } else {
          $no_border_avatar = 0;
      }

      if(isset($_POST['no_border_banner_checkbox']) AND !empty($_POST['no_border_banner_checkbox'])) {
          $no_border_banner = 1;
      } else {
          $no_border_banner = 0;
      }

      if(isset($_POST['banner_scretch_checkbox']) AND !empty($_POST['banner_scretch_checkbox'])) {
          $banner_scretch = 1;
      } else {
          $banner_scretch = 0;
      }

      if(isset($_POST['background_fixed_checkbox']) AND !empty($_POST['background_fixed_checkbox'])) {
          $background_fixed = 1;
      } else {
          $background_fixed = 0;
      }

      if(isset($_POST['background_scretch_checkbox']) AND !empty($_POST['background_scretch_checkbox'])) {
          $background_scretch = 1;
      } else {
          $background_scretch = 0;
      }

      if(isset($_POST['no_avatar_checkbox']) AND !empty($_POST['no_avatar_checkbox'])) {
        if($profile_data['avatar'] != "default_avatar_m.png" AND $profile_data['avatar'] != "default_avatar_f.png") {
          if(is_file("../img/moviebook/avatars/" . $profile_data['avatar'])) {
              unlink("../img/moviebook/avatars/" . $profile_data['avatar']);
          }
          if($profile_data['sexe'] == "Garçon") {
              $avatar_link = "default_avatar_m.png";
          } else {
              $avatar_link = "default_avatar_f.png";
          }
          $sql = $database2->prepare('UPDATE profile SET avatar = ? WHERE username_id = ?')->execute(array(
              $avatar_link,
              $_SESSION['id']
          ));
        }
      }

      if(isset($_POST['no_banner_checkbox']) AND !empty($_POST['no_banner_checkbox'])) {
          if(is_file("../img/moviebook/banners/" . $profile_data['banner'])) {
              unlink("../img/moviebook/banners/" . $profile_data['banner']);
          }
          $sql = $database2->prepare('UPDATE profile SET banner = NULL WHERE username_id = ?')->execute(array(
              $_SESSION['id']
          ));
      }

      if(isset($_POST['no_background_checkbox']) AND !empty($_POST['no_background_checkbox'])) {
          if(is_file("../img/moviebook/backgrounds/" . $profile_data['background'])) {
              unlink("../img/moviebook/backgrounds/" . $profile_data['background']);
          }
          $sql = $database2->prepare('UPDATE profile SET background = ? WHERE username_id = ?')->execute(array(
              "DEFAULT",
              $_SESSION['id']
          ));
      }

      if(isset($_POST['color_1']) AND !empty($_POST['color_1'])) {
          $banner_color_1 = htmlspecialchars($_POST['color_1']);
          if(strlen($banner_color_1) == 7 AND preg_match('/^#[a-f0-9]{6}$/i', $banner_color_1)) {
              $banner_color = $banner_color_1;
          } else {
              $banner_color = $profile_data['banner_color'];
          }
      }

      if(isset($_POST['color_2']) AND !empty($_POST['color_2'])) {
          $primary_color = htmlspecialchars($_POST['color_2']);
          if(strlen($primary_color) == 7 AND preg_match('/^#[a-f0-9]{6}$/i', $primary_color)) {
              if($primary_color == "#56966d") {
                  $primary_color = "DEFAULT";
              }
          } else {
              $primary_color = $profile_data['primary_color'];
          }
      }

      if(isset($_POST['music_background_checkbox']) AND !empty($_POST['music_background_checkbox'])) {
          if(isset($_POST['youtube_link_text_box']) AND !empty($_POST['youtube_link_text_box'])) {
              $music_background_url = htmlspecialchars($_POST['youtube_link_text_box']);
              $regex_pattern = '~
                    ^(?:https?://)?
                     (?:www[.])?
                     (?:youtube[.]com/watch[?]v=|youtu[.]be/)
                     ([^&]{11})
                      ~x';
              $match;
              if(preg_match($regex_pattern, $music_background_url, $match)) {
                  if(isset($_POST['music_link_button_visible_checkbox']) AND !empty($_POST['music_link_button_visible_checkbox'])) {
                      $music_link_button_visible = 1;
                  } else {
                      $music_link_button_visible = 0;
                  }
                  $music_background = $music_background_url;
              } else {
                  $music_link_button_visible = 1;
                  $music_background = NULL;
              }
          } else {
              $music_link_button_visible = 1;
              $music_background = NULL;
          }
      } else {
          $music_link_button_visible = 1;
          $music_background = NULL;
      }

      if(isset($_POST['view_post_default_radio_button']) AND !empty($_POST["view_post_default_radio_button"])) {
        if($_POST['view_post_default_radio_button'] == 1 OR $_POST['view_post_default_radio_button'] == 2) {
          $view_post = $_POST['view_post_default_radio_button'];
        }
      } else {
        $view_post = 1;
      }

      if(isset($_POST['private_profile_checkbox']) AND !empty($_POST['private_profile_checkbox'])) {
          $private_profile = 1;
      } else {
          $private_profile = 0;
      }

      $sql = $database2->prepare('UPDATE profile SET no_border_avatar = ?, no_border_banner = ?, banner_scretch = ?, background_fixed = ?, background_scretch = ?, banner_color = ?, primary_color = ?, music_link_button_visible = ?, music_background = ?, view_post = ?, private = ? WHERE username_id = ?')->execute(array(
          $no_border_avatar,
          $no_border_banner,
          $banner_scretch,
          $background_fixed,
          $background_scretch,
          $banner_color,
          $primary_color,
          $music_link_button_visible,
          $music_background,
          $view_post,
          $private_profile,
          $_SESSION['id']
      ));

      $_SESSION['flash']['success'] = $success_sign . "Ton profil a été mis-à-jour.";
      header("Refresh:0");
      exit();
  }
}

if($is_there_msp_account == true) {
    $msp_username = $msp_account_data['msp_username'];
    $msp_username_1 = $msp_account_data['msp_username'];
    $msp_level = $msp_account_data['msp_level'];

    if($msp_account_data['is_vip'] == 1) {
        $is_vip = "oui";
    } else {
        $is_vip = "non";
    }

    if(isset($msp_account_data['avatar_link']) AND !empty($msp_account_data['avatar_link'])) {
        $msp_account_avatar_link = "../img/moviebook/msp_avatars/".$msp_account_data['avatar_link'];
    } else {
        $msp_account_avatar_link = "../img/no_avatar.jpg";
    }

    if($msp_account_data['is_confirmed'] == 1) {
        $msp_username = $msp_username.' <img data-toggle="tooltip" title="Ce compte MSP lui appartient" draggable="false" src="../img/moviebook/certified_badge.png" width="15" height="15">';
    }

} else {
    $msp_username = "Aucun compte MSP associé";
    $msp_account_avatar_link = "../img/no_avatar.jpg";
    $msp_username_1 = "/";
    $msp_level = "/";
    $is_vip = "/";
}

?>
<input type="hidden" name="profile_id_hidden" id="profile_id_hidden" value="<?= $profile_id ?>">
<br>
<style type="text/css">
   body {
   overflow-x: hidden;
   }
   div.banner_profile {
   <?php if($no_border_banner == 0) { ?>
   border: 2px solid white;
   <?php } ?>
   height: 300px;
   background-size: 1400px 300px;
   background-color: <?= $banner_color ?>;
   <?php if(isset($profile_data['banner']) AND !empty($profile_data['banner'])) { ?>
   background: url(<?= "../img/moviebook/banners/".$profile_data['banner'] ?>) no-repeat center;
   <?php if($banner_scretch == 1) { ?>
   -webkit-background-size: cover;
   -moz-background-size: cover;
   -o-background-size: cover;
   background-size: cover;
   <?php } ?>
   <?php } ?>
   }
   div.avatar_username {
   position: relative;
   }
   div.avatar_profile {
   <?php if($no_border_avatar == 0) { ?>
   border: 2px solid white;
   <?php } ?>
   <?php if($no_border_avatar == 1 AND $no_border_banner == 0) { ?>
   bottom: -118px;
   <?php } elseif($no_border_avatar == 0 AND $no_border_banner == 1 OR $no_border_avatar == 1 AND $no_border_banner == 1) { ?>
   bottom: -120px;
   <?php } else { ?>
   bottom: -118px;
   <?php } ?>
   position: relative;
   left: 38px;
   height: 180px;
   width: 180px;
   background-size: 180px 180px;
   background-image:url(<?php if(isset($profile_data['avatar']) AND !empty($profile_data['avatar'])) { echo "../img/moviebook/avatars/".$profile_data['avatar']; } else { echo "../img/moviebook/avatars/default_avatar_m.png"; } ?>);
   }
   div.username {
   text-shadow: 2px 2px #000000;
   position: absolute;
   left: 225px;
   top: 220px;
   }
   <?php if($primary_color != "DEFAULT" AND is_profile_exist($profile_id) == true) { ?>
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
   <?php if($background != "DEFAULT" AND is_profile_exist($profile_id) == true) { ?>
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
<div class="banner_profile">
   <div class="avatar_username">
      <div id="avatar_profile" class="avatar_profile"></div>
      <div class="username hidden-xs">
         <h2 class="text-white"><?= $username_profile ?> <?= badge_check($account_data['id'], "profile") ?></h2>
      </div>
   </div>
</div>
<?php if(is_profile_exist($profile_id) == true) { ?>
<br>
<div class="panel panel-default">
   <div class="panel-body">
      <?php if(isset($music_link_button_visible) AND !empty($music_link_button_visible AND $music_link_button_visible == true)) { ?>
      <a target="_blank" href="<?= $youtube_link_profile ?>" class="btn btn-default pull-right"><span class="glyphicon glyphicon-headphones"></span></a>
      <?php } ?>
      <?php if($music_background == true) { ?>
      <a id="update_music_background" type="disable" youtube_link="<?= $music_v ?>" class="btn btn-default pull-right"><span class="glyphicon glyphicon-volume-off"></span></a>
      <?php } ?>
      <?php if($it_is_my_profile == true) { ?>
      <a class="btn btn-success pull-right" data-toggle="modal" data-target="#custom_profile"><span class="glyphicon glyphicon-pencil"></span> Personnaliser mon profil</a>
      <?php } else { ?>
      <?php if($is_connected == true AND is_friends($_SESSION['id'], $profile_id) == true) { ?>
      <button class="btn btn-default delete_friend_button pull-right" page="profile" user_id="<?= $profile_id ?>" id="delete_friend_button" name="delete_friend_button"><span class="glyphicon glyphicon-minus"></span> Supprimer de mes amis</button>
      <?php } elseif($is_connected == true) {
         $from_query = $database2->prepare('SELECT * FROM friend_request WHERE from_id = ? AND to_id = ?');
         $from_query->execute(array($_SESSION['id'],$profile_id));
         $to_query = $database2->prepare('SELECT * FROM friend_request WHERE to_id = ? AND from_id = ?');
         $to_query->execute(array($_SESSION['id'],$profile_id));
         if($from_query->rowCount() == 1) { ?>
      <button class="btn btn-default pull-right" id="cancel_friend_request_button" name="cancel_friend_request_button"><span class="glyphicon glyphicon-remove"></span> Annuler la demande</button>
      <?php } elseif($to_query->rowCount() == 1) { ?>
      <span class="pull-right"><strong><?= $username_profile ?></strong> t'a demandé en ami ! L'accepter ? <button class="btn btn-success choice_friend_button" user_id="<?= $profile_id ?>" id="choice_friend_button" name="choice_friend_button" type="accept" form_request="false"><span class="glyphicon glyphicon-ok"></span></button> <button class="btn btn-danger choice_friend_button" user_id="<?= $profile_id ?>" id="choice_friend_button" name="choice_friend_button" type="decline" form_request="false"><span class="glyphicon glyphicon-remove"></span></button></span>
      <?php } else { ?>
      <button class="btn btn-default add_friend_button pull-right" user_id="<?= $profile_id ?>" id="add_friend_button" name="add_friend_button"><span class="glyphicon glyphicon-plus"></span> Ajouter comme ami</button>
      <?php } ?>
      <?php } else { ?>
      <a class="btn btn-default add_friend_button pull-right disabled"><span class="glyphicon glyphicon-plus"></span> Ajouter comme ami</a>
      <?php } ?>
      <?php if($account_data['banned'] == 1) { ?>
      <strong style="color: #e74c3c;">Cet utilisateur est banni.</strong>
      <?php } ?>
      <?php } ?>
      <?php if($it_is_my_profile AND $private_profile == 1) { ?>
      <strong>Ton profil est en privé, personne ne peut le voir.</strong> <span class="glyphicon glyphicon-lock"></span>
      <?php } ?>
      <div class="row">
         <div class="col-md-4">
            <span style="font-size: 120%;">Niveau <strong class="text-orange"><?= get_level($profile_id) ?></strong> <span style="font-size: 75%">(<?= number_format($profile_data['level_points'], 0, '', ' ') ?> pts)</span></span><a class="pull-right" href="level?u=<?= $profile_id ?>">Voir le niveau</a>
            <div class="progress" style="margin-bottom:0px;">
               <div class="progress-bar progress-bar-success" style="width: <?= get_width_progress_bar_level($profile_id, $profile_data['username_id'], $profile_data['level_points']) ?>%"></div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-5">
      <div class="panel panel-primary">
         <div class="panel-heading" align="center">
            <h4 class="text-white"><?php if($it_is_my_profile == true) { echo "Mon compte MSP"; } else { echo "Son compte MSP"; }?></h4>
         </div>
         <div class="panel-body">
            <div class="transition left">
               <div class="panel panel-info">
                  <div class="panel-heading">
                     <h3 class="panel-title" align="center"><?= $msp_username ?></h3>
                  </div>
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-6">
                           <label><strong>Pseudo :</strong> <?= $msp_username_1 ?></label><br>
                           <label><strong>Niveau :</strong> <?= $msp_level ?></label><br>
                           <label><strong>VIP :</strong> <?= $is_vip ?></label>
                        </div>
                        <div class="col-md-5">
                           <center>
                              <img id="avatar" draggable="false" class="img-thumbnail" style="height: 100px; width: 100px;" src="<?= $msp_account_avatar_link ?>">
                           </center>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php if($it_is_my_profile == true and $is_there_msp_account == false) { ?>
            <center>
               <a class="btn btn-info" data-toggle="modal" data-target="#link_msp_account">Associer un compte MSP</a>
            </center>
            <?php } elseif($it_is_my_profile == true and $is_there_msp_account == true) { ?>
            <center>
               <a type="button" class="btn btn-info" data-toggle="modal" data-target="#edit_msp_account_modal"><span class="glyphicon glyphicon-pencil"></span> Faire des modifications</a>
            </center>
            <?php } ?>
         </div>
      </div>
      <div class="panel panel-primary">
         <div class="panel-heading" align="center">
            <h4 class="text-white"><?php if($it_is_my_profile == true) { echo "A propos de moi"; } else { echo "A propos de <strong>".$account_data['username']."</strong>"; } ?></h4>
         </div>
         <div class="panel-body">
            <?php if($it_is_my_profile == true AND $is_there_info == false) { ?>
            <a href="#!" id="link_label_add_name"><span class="glyphicon glyphicon-plus"></span> Ajouter un prénom ou un surnom</a><br>
            <input type="text" class="form-control" id="name_text_box" name="name_text_box" maxlength="15" placeholder="Entre un prénom ou un surnom" style="display: none;">
            <a href="#!" id="link_label_add_birthday_date"><span class="glyphicon glyphicon-plus"></span> Ajouter une date d'anniversaire</a><br>
            <input type="text" class="form-control" id="date_of_birth_text_box" name="date_of_birth_text_box" maxlength="10" placeholder="Ajoute une date d'anniversaire (JJ/MM/AAAA)" style="display: none;">
            <a href="#!" id="link_label_add_description"><span class="glyphicon glyphicon-plus"></span> Ajouter une description</a>
            <textarea class="form-control" rows="5" id="description_text_box" name="description_text_box" maxlength="5000" placeholder="Parle-nous de toi... ;)" style="display: none; resize: none;"></textarea>
            <br>
            <div align="center">
               <button class="btn btn-success" name="mb_save_info_button" id="mb_save_info_button" style="display: none;"><span class="glyphicon glyphicon-ok"></span></button>
            </div>
            <?php } else { ?>
            <span id="name_label"><strong>Prénom/surnom :</strong> <?php if(isset($profile_data['name']) AND !empty($profile_data['name'])) { echo $profile_data['name']; } else { echo "aucun"; } ?></span><br>
            <span id="date_of_birth_label"><strong>Date d'anniversaire :</strong> <?php if(isset($profile_data['date_of_birth']) AND !empty($profile_data['date_of_birth'])) { echo $profile_data['date_of_birth']; } else { echo "aucune"; } ?></span><br>
            <?php if($it_is_my_profile == false) { ?><strong>Dernière connexion :</strong> <?= $last_connection ?><br> <?php } ?>
            <strong>Genre :</strong> <?php if(isset($sexe_profile) AND !empty($sexe_profile)) { echo $sexe_profile; } else { echo "aucun"; } ?>
            <span id="description_label"><?php if(isset($profile_data['description']) AND !empty($profile_data['description'])) { ?><br><br><?= return_string($profile_data['description']); } ?></span>
            <?php if($it_is_my_profile == true) { ?>
            <input type="text" class="form-control" id="name_text_box" name="name_text_box" maxlength="15" placeholder="Entre un prénom ou un surnom" value="<?= $profile_data['name'] ?>" style="display: none;">
            <input type="text" class="form-control" id="date_of_birth_text_box" name="date_of_birth_text_box" maxlength="10" placeholder="Ajoute une date d'anniversaire (JJ/MM/AAAA)" value="<?= $profile_data['date_of_birth'] ?>" style="display: none;">
            <textarea class="form-control" rows="10" id="description_text_box" name="description_text_box" maxlength="5000" placeholder="Parle-nous de toi... ;)" style="display: none; resize: none;"><?= $profile_data['description'] ?></textarea>
            <br>
            <div align="center">
               <button class="btn btn-success" name="mb_save_info_button" id="mb_save_info_button" style="display: none;"><span class="glyphicon glyphicon-ok"></span></button>
            </div>
            <a href="#!" id="link_label_update_info"><span class="glyphicon glyphicon-pencil"></span> Modifier mes informations</a>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
      <?php $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0 ORDER BY id DESC LIMIT 5');
         $sql->execute(array($profile_id));
         if($sql->rowCount() > 0) { ?>
      <div class="panel panel-primary">
         <div class="panel-heading" align="center">
            <h4 class="text-white"><span class="glyphicon glyphicon-comment"></span> <?php if($sql->rowCount() == 1) { if($it_is_my_profile == true) { echo "Mon dernier sujet"; } else { echo "Son dernier sujet"; } } elseif($it_is_my_profile == true) { echo "Mes <strong>".$sql->rowCount()."</strong> derniers sujets"; } else { echo "Ses <strong>".$sql->rowCount()."</strong> derniers sujets"; } ?></h4>
         </div>
         <div class="panel-body">
            <div class="list-group">
               <?php while($subject_data = $sql->fetch()) { ?>
               <a href="../forum/subject?id=<?= $subject_data['id'] ?>" target="_blank" class="list-group-item">
               <?= $subject_data['title'] ?>
               </a>
               <?php } ?>
            </div>
         </div>
      </div>
      <?php } ?>
      <div class="panel panel-primary">
         <div class="panel-heading" align="center">
            <h4 class="text-white"><span class="glyphicon glyphicon-user"></span> Amis (<strong><?= $total_friends_count ?></strong>)</h4>
         </div>
         <div class="panel-body">
            <div class="row">
               <?php if($total_friends_count != 0) { ?>
               <?php while($friends_data = $friends_sql->fetch()) {
                  if($friends_data['user_one'] == $profile_id) {
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
               <div class="col-md-4">
                  <div align="center">
                     <a href="profile?u=<?= $account_data['id'] ?>"><img src="../img/moviebook/avatars/<?= $profile_data['avatar'] ?>" width="64" height="64"></a><br>
                     <a href="profile?u=<?= $account_data['id'] ?>"><?php if(strlen($account_data['username']) >= 15) { echo substr($account_data['username'], 0, 15)."..."; } else { echo $account_data['username']; } ?></a> <?= badge_check($account_data['id'], "other") ?>
                  </div>
               </div>
               <?php } } else { ?>
               <div align="center">
                  <?php if($it_is_my_profile == true) { echo "Tu n'as aucun ami. :/"; } else { echo $username_profile." n'a aucun ami. :/"; } ?>
               </div>
               <?php } ?>
            </div>
            <?php if($total_friends_count > 9) { ?>
            <a class="pull-right" target="_blank" href="friends?u=<?= $profile_id ?>"><span class="glyphicon glyphicon-arrow-right"></span> Voir tous les amis</a>
            <?php } ?>
         </div>
      </div>
      <?php $sql = $database->prepare('SELECT * FROM contest WHERE username_id = ? AND deleted = 0');
         $sql->execute(array($profile_id));
         if($sql->rowCount() == 1) {
            $contest_data = $sql->fetch(); ?>
      <div class="panel panel-primary">
         <div class="panel-heading" align="center">
            <h4 class="text-white"><span class="glyphicon glyphicon-certificate"></span> Concours</h4>
         </div>
         <div class="panel-body">
            <div align="center">
               <p><strong><?= $username_profile ?></strong> a un concours en cours !</p>
               <a class="btn btn-primary" href="../contest/contest?c=<?= $contest_data['contest_id'] ?>">Accéder à son concours</a>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
   <div class="col-md-7">
      <div class="panel panel-default">
         <div class="panel-heading">
            <center>
               <h3><?php if($it_is_my_profile == true) { echo "Exprime toi !"; } else { echo "Poste quelque chose sur son mur !"; } ?></h3>
            </center>
         </div>
         <div class="panel-body">
            <textarea class="form-control" rows="3" placeholder="<?php if($is_connected == true) { echo "J'ai mangé des pâtes ce midi"; } else { echo "Connecte-toi pour écrire une publication"; } ?>" id="post_textarea" name="post_textarea" maxlength="5000" style="resize: none;" <?php if($is_connected == false) { echo "disabled"; } ?>></textarea>
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
</div>
<?php } ?>
<?php if($it_is_my_profile == true AND $is_there_msp_account == false) { ?>
<!-- Modal -->
<div class="modal fade" id="link_msp_account" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Associer un pseudo MSP</h4>
            </center>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div align="center">
                     <p>Associer un compte MSP, c'est montrer qui tu es sur MSP !<br>
                        C'est pratique pour les personnes qui veulent savoir qui tu es sur MSP.
                     </p>
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="mb_link_account_username_text_box">Ton pseudo MSP</label>
                     <input type="text" class="form-control" id="mb_link_account_username_text_box" name="mb_link_account_username_text_box"  maxlength="30" required>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <center>
                              <label class="control-label" for="mb_link_account_level_text_box">Le niveau de ton compte</label>
                           </center>
                           <input type="text" class="form-control" id="mb_link_account_level_text_box" name="mb_link_account_level_text_box" required maxlength="2" placeholder="Ne triche pas ! ;)">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <center>
                           <label>Ton compte est-il VIP ?</label>
                        </center>
                        <label><input type="radio" name="mb_link_account_is_vip_radio_button" id="yes_radio_button" value="yes">Oui</label><br>
                        <label><input type="radio" name="mb_link_account_is_vip_radio_button" id="no_radio_button" value="no" checked>Non</label>
                     </div>
                  </div>
                  <form id="msp_avatar_form">
                     Choisis une photo qui représente ta MovieStar ! <strong>(photo carrée)</strong>
                     <input type="hidden" name="type_1" id="type_1" value="new_msp_account">
                     <input type="hidden" name="type" id="type" value="upload_msp_avatar">
                     <input type="file" name="msp_avatar_file" id="msp_avatar_file" style="display:none;">
                     <label for="msp_avatar_file" class="btn btn-warning btn-xs" id="msp_avatar_button" name="msp_avatar_button">Choisir photo</label>
                     <br>
                     <div id="progress_bar_upload_msp_avatar" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar" style="width: 0%;"></div>
                     </div>
                  </form>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" name="link_msp_account_button" id="link_msp_account_button"><span class="glyphicon glyphicon-ok"></span></button>
               <a type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($it_is_my_profile == true AND $is_there_msp_account == true) { ?>
<!-- Modal -->
<div class="modal fade" id="edit_msp_account_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Modifier mon compte MSP associé</h4>
            </center>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div align="center">
                     <p>Modifie les informations de ton compte MSP associé. Si tu souhaites modifier le pseudo, tu dois dissocier ton compte MSP de ton profil MovieBook.</p>
                  </div>
                  <div class="form-group">
                     <label class="control-label">Ton pseudo MSP</label>
                     <input type="text" class="form-control" disabled value="<?= $msp_account_data['msp_username'] ?>">
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <center>
                              <label class="control-label" for="mb_edit_account_level_text_box">Le niveau de ton compte</label>
                           </center>
                           <input type="text" class="form-control" id="mb_edit_account_level_text_box" name="mb_edit_account_level_text_box" required maxlength="2" placeholder="Ne triche pas ! ;)" value="<?= $msp_account_data['msp_level'] ?>">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <center>
                           <label>Ton compte est-il VIP ?</label>
                        </center>
                        <label><input type="radio" name="mb_edit_account_is_vip_radio_button" id="yes_radio_button" value="yes" <?php if($msp_account_data['is_vip'] == 1) { echo "checked"; } ?>>Oui</label><br>
                        <label><input type="radio" name="mb_edit_account_is_vip_radio_button" id="no_radio_button" value="no" <?php if($msp_account_data['is_vip'] == 0) { echo "checked"; } ?>>Non</label>
                     </div>
                  </div>
                  <form id="msp_avatar_form">
                     Choisis une photo qui représente ta MovieStar ! <strong>(photo carrée)</strong>
                     <input type="hidden" name="type_1" id="type_1" value="edit_msp_account">
                     <input type="hidden" name="type" id="type" value="upload_msp_avatar">
                     <input type="file" name="msp_avatar_file" id="msp_avatar_file" style="display:none;">
                     <label for="msp_avatar_file" class="btn btn-warning btn-xs" id="msp_avatar_button" name="msp_avatar_button">Choisir photo</label>
                     <br>
                     <div id="progress_bar_upload_msp_avatar" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar" style="width: 0%;"></div>
                     </div>
                  </form>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" name="save_edits_msp_account_button" id="save_edits_msp_account_button"><span class="glyphicon glyphicon-ok"></span> Sauvegarder</button>
               <a class="btn btn-danger" data-toggle="modal" data-target="#dislink_msp_account_are_you_sure_modal" data-dismiss="modal"><span class="glyphicon glyphicon-trash"></span> Dissocier mon compte MSP</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="dislink_msp_account_are_you_sure_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Dissocier ton compte MSP</h4>
            </center>
            <div align="center">
               <p>Es-tu sûr de dissocier ton compte MSP de ton profil MovieBook ?<br>
                  Si ton compte MSP est certifié, il ne le sera plus une fois que tu l'auras dissocié.
               </p>
            </div>
            <div align="center">
               <button class="btn btn-success" name="dislink_msp_account_button" id="dislink_msp_account_button"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#edit_msp_account_modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($is_connected == true AND is_profile_exist($_SESSION['id']) == false) { ?>
<!-- Modal -->
<div class="modal fade" id="mb_tutorial" data-backdrop="static" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Bienvenu(e) sur MovieBook !</h4>
            </center>
            <center>
               <p>Eh bien, on dirait que c'est ta première venu. Je vais tout t'expliquer !<br>
                  <strong>MovieBook</strong> est un réseau social spécial pour les joueurs d'MSP. <strong>MovieBook</strong> te permettras d'avoir ton propre profil. Comme tout résau social, tu pourras poster, reposter des statuts, images, texte etc. <strong>MovieBook</strong> te permettras aussi de rencontrer des joueurs MSP, et de vous ajouter sur <strong>MovieStarPlanet</strong>. Vous pourrez ainsi faire du troc, afin d'échanger des rares par exemple. Ce réseau social spécial MSP t'offrira aussi la possibilité de former des groupes, pour vous aider dans vos boosts ! Retrouve plus d'informations en <a target="_blank" href="../news?id=5">cliquant ici</a>.
               </p>
               <legend></legend>
               <h5>Apprenons à nous connaître...</h5>
            </center>
            <div class="row">
               <div class="col-md-6">
                  <center>
                     <label>Es-tu une fille ou un garçon ?</label>
                  </center>
                  <label><input type="radio" name="sexe_radio" id="girl_radio_button" value="Fille" checked>Fille</label><br>
                  <label><input type="radio" name="sexe_radio" id="boy_radio_button" value="Garçon">Garçon</label>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <center>
                        <label class="control-label" for="color_select">Quelle est ta couleur préférée ?</label>
                     </center>
                     <select class="form-control" id="color_select" name="color_select">
                        <option>Rouge</option>
                        <option>Bleu</option>
                        <option>Vert</option>
                        <option>Jaune</option>
                        <option>Noir</option>
                        <option>Blanc</option>
                        <option>Violet</option>
                        <option>Orange</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="checkbox">
               <label>
               <input type="checkbox" name="accept_rules_checkbox" id="accept_rules_checkbox">J'ai lu et j'accepte <a target="_blank" href="../rules">les règles de MovieBook</a>
               </label>
            </div>
            <div align="center">
               <input type="button" class="btn btn-success" name="mb_create_account_button" id="mb_create_account_button" value="Crée-moi mon compte MovieBook !">
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if(is_profile_exist($profile_id) == false AND $it_is_my_profile == false) { ?>
<!-- Modal -->
<div class="modal fade" id="profile_do_not_exist" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Ce profil n'existe pas</h4>
            </center>
            <center>
               <p>Il semblerait que <strong><?= $username_profile ?></strong> n'ait pas encore créé son profil MovieBook... Si tu le/la connais, pourquoi ne pas lui proposer de le créer ? Ce serait cool ! :)</p>
            </center>
            <div align="center">
               <a class="btn btn-success" href="home"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
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
                     <button class="btn btn-success" id="confirm_report_post_button" name="confirm_report_post_button"><span class="glyphicon glyphicon-ok"></span></button>
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
               Cette action est <strong>irréversible.</strong>
            </p>
            <input type="hidden" name="delete_content_id_hidden" id="delete_content_id_hidden" value="">
            <input type="hidden" name="delete_type_hidden" id="delete_type_hidden" value="">
            <div align="center">
               <button class="btn btn-success" id="mb_delete_content_button" name="mb_delete_content_button" location_type="all_post"><span class="glyphicon glyphicon-ok"></span> Oui</button>
               <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Non</a>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<?= smileys_modal("mb_new_post") ?>
<?php if(is_profile_exist($profile_id) == true AND $it_is_my_profile == true) { ?>
<!-- Modal -->
<div class="modal fade" id="custom_profile" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Personnaliser mon profil</h4>
            </div>
            <div class="panel panel-default">
               <div class="panel-body">
                  <h5>Photos</h5>
                  <div class="row">
                     <div class="col-md-5">
                        <form id="avatar_form">
                           <center>
                              <input type="hidden" name="type" id="type" value="upload_avatar">
                              Photo de profil
                              <br>
                              <input type="file" name="avatar_file" id="avatar_file" style="display:none;">
                              <label for="avatar_file" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-picture"></span></label>
                           </center>
                           <br>
                           <div id="progress_bar_upload_avatar" class="progress progress-striped active" style="display: none;">
                              <div class="progress-bar" style="width: 0%;"></div>
                           </div>
                        </form>
                     </div>
                     <div class="col-md-5">
                        <form id="banner_form">
                           <center>
                              <input type="hidden" name="type" id="type" value="upload_banner">
                              Photo de couverture
                              <br>
                              <input type="file" name="banner_file" id="banner_file" style="display:none;">
                              <label for="banner_file" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-picture"></span></label>
                           </center>
                           <br>
                           <div id="progress_bar_upload_banner" class="progress progress-striped active" style="display: none;">
                              <div class="progress-bar" style="width: 0%;"></div>
                           </div>
                        </form>
                     </div>
                     <div class="col-md-5">
                        <form id="background_form">
                           <center>
                              <input type="hidden" name="type" id="type" value="upload_background">
                              Photo de fond
                              <br>
                              <input type="file" name="background_file" id="background_file" style="display:none;">
                              <label for="background_file" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-picture"></span></label>
                           </center>
                           <br>
                           <div id="progress_bar_upload_background" class="progress progress-striped active" style="display: none;">
                              <div class="progress-bar" style="width: 0%;"></div>
                           </div>
                        </form>
                     </div>
                     <div class="col-md-5">
                        <center>
                           Badge à côté du pseudo
                           <br>
                           <a class="btn btn-primary btn-sm" id="badge_button" name="badge_button" data-toggle="modal" data-target="#update_badge_modal" data-dismiss="modal">Choisir un badge</a>
                        </center>
                     </div>
                  </div>
                  <legend></legend>
                  <form method="POST">
                     <label><input type="checkbox" <?php if($no_border_avatar == 1) { echo "checked"; } ?> name="no_border_avatar_checkbox" id="no_border_avatar_checkbox">Pas de bords autour de la photo de profil</label><br>
                     <label><input type="checkbox" <?php if($no_border_banner == 1) { echo "checked"; } ?> name="no_border_banner_checkbox" id="no_border_banner_checkbox">Pas de bords autour de la photo de couverture</label><br>
                     <label><input type="checkbox" <?php if($banner_scretch == 1) { echo "checked"; } ?> name="banner_scretch_checkbox" id="banner_scretch_checkbox">Ajuster la photo de couverture au cadre</label><br><br>
                     <label><input type="checkbox" name="no_avatar_checkbox" id="no_avatar_checkbox">Supprimer la photo de profil</label><br>
                     <label><input type="checkbox" name="no_banner_checkbox" id="no_banner_checkbox">Supprimer la photo de couverture</label><br>
                     <label><input type="checkbox" name="no_background_checkbox" id="no_background_checkbox">Supprimer la photo de fond</label><br><br>
                     <label><input type="checkbox" <?php if($background_scretch == 1) { echo "checked"; } ?> name="background_scretch_checkbox" id="background_scretch_checkbox">Ajuster la photo de fond à l'écran</label><br>
                     <label><input type="checkbox" <?php if($background_fixed == 1) { echo "checked"; } ?> name="background_fixed_checkbox" id="background_fixed_checkbox">Photo de fond fixe</label><br>
                     <span class="glyphicon glyphicon-info-sign"></span> <small>L'option <strong>"Photo de fond fixe"</strong> rend ta photo fixe à l'écran lors du défilement de la page.</small>
                     <legend></legend>
                     <h5>Couleurs</h5>
                     <label><input type="color" value="<?= $banner_color ?>" name="color_1" id="color_1"> Couleur de la bannière</label><br>
                     <label><input type="color" value="<?= $primary_color ?>" name="color_2" id="color_2"></label> Couleur principale<a id="default_primary_color_link" name="default_primary_color_link" class="pull-right" href="#!">Par défault</a><br>
                     <span class="glyphicon glyphicon-info-sign"></span> <small>La couleur de la bannière s'affiche seulement si tu n'as pas de photo de couverture.</small>
                     <legend></legend>
                     <h5>Musique de fond</h5>
                     <div class="form-group">
                        <label for="music_background_checkbox"><input type="checkbox" id="music_background_checkbox" name="music_background_checkbox" <?php if($music_background == true) { echo "checked"; } ?>>Activer</label><input class="form-control" type="text" id="youtube_link_text_box" placeholder="Lien youtube (ex : https://www.youtube.com/watch?v=mXx9Big7f48)" <?php if($music_background == true) { echo 'value="'.$youtube_link.'"'; } ?> name="youtube_link_text_box" <?php if($music_background == false) { echo "disabled"; } ?>><label><input type="checkbox" id="music_link_button_visible_checkbox" name="music_link_button_visible_checkbox" <?php if($music_background == true AND $music_link_button_visible == true) { echo "checked"; } elseif($music_background == false) { echo "checked"; } ?> <?php if($music_background == false) { echo "disabled"; } ?>>Autoriser les autres à voir le lien</label>
                     </div>
                     <legend></legend>
                     <h5>Autre</h5>
                     Afficher par défaut :<br>
                     <label><input type="radio" name="view_post_default_radio_button" id="view_post_default_radio_button" value="1" <?php if($view_post == 1) { echo "checked"; } ?>>Mes publications et celles des autres</label><br>
                     <label><input type="radio" name="view_post_default_radio_button" id="view_post_default_radio_button" value="2" <?php if($view_post == 2) { echo "checked"; } ?>>Uniquement mes publications</label><br><br>
                     <label><input type="checkbox" name="private_profile_checkbox" id="private_profile_checkbox" <?php if($private_profile == 1) { echo "checked"; } ?>>Profil privé (personne ne pourra le visualiser, ni voir tes publications)</label>
               </div>
            </div>
            <div align="center">
            <input type="submit" class="btn btn-success" id="mb_custom_save_submit" name="mb_custom_save_submit" value="Sauvegarder"> <a class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="update_badge_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Choisir un badge</h4>
            </center>
            <?php if($badges_pictures_sql->rowCount() > 0) {
               while($badges_pictures_data = $badges_pictures_sql->fetch()) {
                 if($badges_pictures_data['active'] == 1) { ?>
            <span data-toggle="tooltip" title="Ceci est ton badge actuel."><a class="btn btn-default" disabled><img draggable="false" width="37" height="37" src="../img/moviebook/badges/<?= $badges_pictures_data['badge_path'] ?>" ></a></span>
            <?php } else { ?>
            <button class="btn btn-default update_badge_button" badge_id="<?= $badges_pictures_data['id'] ?>"><img draggable="false" width="37" height="37" src="../img/moviebook/badges/<?= $badges_pictures_data['badge_path'] ?>" ></button>
            <?php } ?>
            <?php } ?>
            <?php $sql = $database2->prepare('SELECT * FROM badge_pictures WHERE user_id = ? AND active = 1');
               $sql->execute(array(
                   $_SESSION['id']
               ));
               if($sql->rowCount() == 1) { ?>
            <br><br>
            <button class="btn btn-danger btn-sm" id="unactive_badge_button"><span class="glyphicon glyphicon-remove"></span> Retirer le badge actif</button>
            <?php } ?>
            <?php } else { ?>
            Tu n'as mis en ligne aucun badge.
            <?php } ?>
            <br><br>
            <legend></legend>
            Tu as <strong><?= $badge_token ?></strong> jeton(s) pour ajouter un badge.
            <?php if($badge_token > 0) { ?>
            <form id="badge_form">
               <input type="hidden" name="type" id="type" value="upload_badge">
               <input type="file" name="badge_file" id="badge_file" style="display:none;">
               <label for="badge_file" class="btn btn-primary btn-sm">Ajouter un badge</label> Taille : 180x180 || Max : 100Ko
               <div id="progress_bar_upload_badge" class="progress progress-striped active" style="display: none;">
                  <div class="progress-bar" style="width: 0%;"></div>
               </div>
            </form>
            <?php } else { ?>
            <br>
            <span data-toggle="tooltip" title="Tu n'as aucun jeton pour ajouter un badge."><a class="btn btn-primary btn-sm" disabled>Ajouter un badge</a></span> Taille : 180x180 || Max : 100Ko
            <?php } ?>
            <div align="center">
               <a class="btn btn-success" data-toggle="modal" data-target="#custom_profile" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if($music_background == true) { ?>
<iframe id="youtube_music_background" src="https://www.youtube.com/embed/<?= $music_v ?>?autoplay=1&rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen height="0" width="0"></iframe>
<?php } ?>
<?php require '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
<?php if($is_connected == true AND is_profile_exist($_SESSION['id']) == false) { ?>
$('#mb_tutorial').modal('show');
<?php } elseif($is_connected == true AND is_profile_exist($_SESSION['id']) == true AND $it_is_my_profile == true AND $is_there_msp_account == false AND isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "lma") { ?>
$('#link_msp_account').modal('show');
<?php } ?>
<?php if(is_profile_exist($profile_id) == false AND $it_is_my_profile == false) { ?>
$('#profile_do_not_exist').modal('show');
<?php } ?>
});
$("#post_elements_div").load("../npr/post_elements?u=<?= $profile_id ?>&p=<?= $current_page ?>&vp=<?= $view_post ?>", function() {
$('#loading').hide();
});
<?php if($it_is_my_profile) { ?>
$("#custom_profile").on('hidden.bs.modal', function(event) {
    if($('.modal:visible').length) {
        $('body').addClass('modal-open');
    }
});
$("#update_badge_modal").on('hidden.bs.modal', function(event) {
    if($('.modal:visible').length) {
        $('body').addClass('modal-open');
    }
});
<?php } ?>
</script>