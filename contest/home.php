<?php
include('../includes/config.php');

$current_page_title = "Concours";
require '../includes/headerParallax.php';

$contest_sql = $database->prepare('SELECT * FROM contest WHERE type = 1 AND deleted = 0 ORDER BY id DESC');
$contest_sql->execute();
$noc = $contest_sql->rowCount();

$last_three_contest_sql = $database->query('SELECT * FROM contest WHERE deleted = 0 ORDER BY id DESC LIMIT 3');
$last_three_contest_sql->execute();

if(is_connected() == true) {
    $sql = $database->prepare('SELECT * FROM contest WHERE username_id = ? AND deleted = 0');
    $sql->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 0) {
        $already_have_contest = false;
    } else {
        $contest_data = $sql->fetch();
        $my_contest_id = $contest_data['contest_id'];
        $already_have_contest = true;
    }
}

if(isset($_POST['create_contest_button_1']) AND !empty($_POST['create_contest_button_1'])) {
    $sql = $database->prepare('SELECT * FROM contest WHERE username_id = ? AND deleted = 0');
    $sql->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 0) {
        if(isset($_POST['title_contest_text_box']) AND !empty($_POST['title_contest_text_box']) AND isset($_POST['description_text_area']) AND !empty($_POST['description_text_area'])) {
            $title = htmlspecialchars($_POST['title_contest_text_box']);
            $description = htmlspecialchars($_POST['description_text_area']);
            $title = trim($title);
            $description = trim($description);
            if(mb_strlen($title, 'UTF-8') <= 30 OR mb_strlen($title, 'UTF-8') >= 5) {
                if(mb_strlen($description, 'UTF-8') <= 1000) {
                    if(isset($_POST['price_item_text_box']) AND !empty($_POST['price_item_text_box'])) {
                        if(isset($_POST['max_participant_combo_box']) AND !empty($_POST['max_participant_combo_box'])) {
                            if(isset($_POST['contest_type_radio']) AND !empty($_POST['contest_type_radio'])) {
                                if(isset($_POST['accept_rules_checkbox']) AND !empty($_POST['accept_rules_checkbox'])) {

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

                                    $contest_id = str_random(10);
                                    $sql = $database->prepare('SELECT * FROM contest WHERE contest_id = ? AND deleted = 0');
                                    $sql->execute(array(
                                        $contest_id
                                    ));
                                    if($sql->rowCount() == 0) {
                                        if(is_profile_exist($_SESSION['id']) == true AND isset($_POST['notification_new_contest_friend_checkbox']) AND !empty($_POST['notification_new_contest_friend_checkbox'])) {
                                            $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC');
                                            $friends_sql->execute(array(
                                                $_SESSION['id'],
                                                $_SESSION['id']
                                            ));
                                            while($friends_data = $friends_sql->fetch()) {
                                                if($friends_data['user_one'] == $_SESSION['id']) {
                                                    $user_friend = "user_two";
                                                } else {
                                                    $user_friend = "user_one";
                                                }
                                                insert_notification($friends_data[$user_friend], $_SESSION['id'], "new_contest_friend", $contest_id);
                                            }
                                        }
                                        $sql = $database->prepare('INSERT INTO contest(contest_id,username_id,title,description,type,category,max_participants,notification_new_participant,notification_comments,comments_area,only_participants_allow_to_comment,participate_button_list,allow_only_msp_account_linked) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
                                        $sql->execute(array(
                                            $contest_id,
                                            $_SESSION['id'],
                                            $title,
                                            $description,
                                            $type,
                                            $category_contest,
                                            $max_participants,
                                            $notification_new_participant,
                                            $notification_comments,
                                            $comments_area,
                                            $only_participants_allow_to_comment,
                                            $participate_button_list,
                                            $allow_only_msp_account_linked
                                        ));
                                        $contest_id_1 = $database->lastInsertId();
                                        $prices_array = $_POST['price_item_text_box'];
                                        for($i = 0; $i < count($prices_array); $i++) {
                                            $price = htmlspecialchars($prices_array[$i]);
                                            $price = trim($price);
                                            if(isset($price) AND !empty($price)) {
                                              $sql = $database->prepare('INSERT INTO contest_prices(name,contest_id) VALUES (?,?)');
                                              $sql->execute(array(
                                                  $price,
                                                  $contest_id_1
                                              ));
                                            }
                                        }
                                        $_SESSION['flash']['success'] = $success_sign . "Félicitation ! Ton concours est maintenant créé.";
                                        header("Location: contest?c=" . $contest_id);
                                        exit();
                                    } else {
                                        $_SESSION['flash']['danger'] = $danger_sign . "Désolé, il y a eu un petit problème lors de la création de ton concours. Réessaye.";
                                    }
                                } else {
                                    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois avoir lu et accepté les règles des concours.";
                                }
                            }
                        }
                    } else {
                        $_SESSION['flash']['danger'] = $danger_sign . "Tu dois mettre au moins une récompense.";
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
    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "Tu as déjà un concours en cours !";
    }
}

?>
<div class="parallax-window" data-parallax="scroll" data-image-src="<?php if($theme == 1) { echo "https://i.imgur.com/PUDsfIB.jpg"; } elseif($theme == 2) { echo "https://i.imgur.com/1QEdF9w.jpg"; } ?>">
   <br><br>
   <div align="center">
      <h1 class="page-title">Concours</h1>
      <p class="lead text-white" style="font-size: 200%;">Crée tes propres concours ou participe à des concours.</p>
      <a id="create_contest_button" name="create_contest_button" class="btn btn-primary btn-lg" style="margin-right: 150px;">Créer un concours</a>
      <a id="see_contests_button" name="see_contests_button" class="btn btn-primary btn-lg">Voir les concours</a>
   </div>
</div>
<div class="container">
   <div align="center">
      <br>
      <p class="lead" style="font-size: 200%;">Il y a actuellement <strong class="text-orange"><?= $noc ?></strong> concours en cours.</p>
   </div>
   <span style="font-size: 130%;">Les 3 derniers concours créés :</span>
   <?php if($noc > 0) { ?>
   <div class="row">
      <?php while($contest_data = $last_three_contest_sql->fetch()) {
         $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
         $sql->execute(array(
             $contest_data['username_id']
         ));
         $account_data = $sql->fetch();
         
         $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ?');
         $sql->execute(array(
             $contest_data['id']
         ));
         $nop = $sql->rowCount();
         
         if(is_profile_exist($account_data['id'])) {
             if(strlen($account_data['username']) >= 20) {
                 $username_str = '<span data-toggle="tooltip" title="' . $account_data['username'] . '"><a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . substr($account_data['username'], 0, 17) . "...</a></span>";
             } else {
                 $username_str = '<a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . $account_data['username'] . '</a>';
             }
         } elseif(strlen($account_data['username']) >= 20) {
             $username_str = '<span data-toggle="tooltip" title="' . $account_data['username'] . '">' . substr($account_data['username'], 0, 17) . "...</span>";
         } else {
             $username_str = $account_data['username'];
         }
         
         if(strlen($contest_data['title']) >= 20) {
             $title_str = '<span data-toggle="tooltip" title="' . $contest_data['title'] . '">' . substr($contest_data['title'], 0, 17) . '...</span>';
         } else {
             $title_str = $contest_data['title'];
         }
         
         if($contest_data['category'] == 1) {
             $catagory_str = "../img/contest/cover/vip_contest.png";
         } elseif($contest_data['category'] == 2) {
             $catagory_str = "../img/contest/cover/gift_contest.png";
         } elseif($contest_data['category'] == 3) {
             $catagory_str = "../img/contest/cover/rare_contest.png";
         } elseif($contest_data['category'] == 4) {
             $catagory_str = "../img/contest/cover/salut_contest.png";
         } else {
             $catagory_str = "../img/contest/cover/other_contest.png";
         } ?>
      <div class="col-md-4">
         <div class="panel panel-default">
            <div class="panel panel-body">
               <div class="media">
                  <div class="media-left">
                     <img class="media-object" draggable="false" src="<?= $catagory_str ?>" width="100" height="100">
                  </div>
                  <div class="media-body">
                     <h5 class="media-heading"><?= $title_str ?></h5>
                     <span class="time-ago"><?= time_ago($contest_data['creation_date']) ?></span>
                     <span id="number_of_participants_contest_span_<?= $contest_data['id'] ?>"><?= $nop ?></span>/<?php if($contest_data['max_participants'] == "unlimited") { echo "illimité"; } else { echo $contest_data['max_participants']; } ?><br>
                     par <?= $username_str ?><br>
                     <center>
                        <?php if(is_connected() AND $contest_data['username_id'] == $_SESSION['id']) { ?>
                        <a class="btn btn-primary btn-xs" href="contest?c=<?= $contest_data['contest_id'] ?>">Voir mon concours</a>
                        <?php } else { ?>
                        <a class="btn btn-info btn-xs" href="contest?c=<?= $contest_data['contest_id'] ?>">En savoir plus</a>
                        <?php } ?>
                     </center>
                  </div>
               </div>
            </div>
         </div>
         <br>
      </div>
      <?php } ?>
   </div>
   <?php } else { ?>
   <div align="center">
      <p>Oh... on dirait qu'il y a aucun concours en cours !</p>
   </div>
   <?php } ?>
</div>
<div class="parallax-window" data-parallax="scroll" data-image-src="<?php if($theme == 1) { echo "https://i.imgur.com/UN2Wsky.jpg"; } elseif($theme == 2) { echo "https://i.imgur.com/bmcropt.jpg"; } ?>">
   <br><br><br>
   <div class="container">
      <p class="lead text-white">Bienvenu(e) dans l'espace <strong>Concours</strong> ! Ici, tu peux participer à pleins de concours que d'autres utilisateurs d'MSP ont créés. Tu peux bien sûr toi même créé ton propre concours, en cliquant sur le bouton <strong>"Créer un concours"</strong>.<br>
         Personnalise ton concours ! Choisis son titre, sa description, les récompenses, etc.<br> <br>
         Créer un concours est pratique, par exemple si tu prévois un concours sur ta chaîne Youtube, tu peux organiser ton concours ici. Les participants de ton concours seront tous regroupés sous forme d'un tableau, tu pourras donc les consulter <strong>facilement</strong>.<br><br>
         Et la cerise sur la gâteau, c'est <strong>totalement gratuit</strong> ! Alors si tu es d'humeur généreuse et que tu as un concours en tête, pourquoi ne pas créer ton conours <strong>dès maintenant</strong> ?
      </p>
   </div>
   <br><br>
</div>
<?php if(is_connected() == true AND $already_have_contest == false) { ?>
<!-- Modal -->
<div class="modal fade" id="create_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Créer un concours</h4>
               <p class="lead">Crée ton propre concours, <strong>super facilement</strong> !</p>
            </center>
            <form method="post" id="create_contest_form">
               <div class="panel panel-default">
                  <div class="panel-body">
                     <div class="form-group">
                        <label class="control-label" for="title_contest_text_box">Titre du concours <strong>(5 caractères minimum)</strong></label>
                        <input type="text" class="form-control" placeholder="Ex : Gagne des snakes" id="title_contest_text_box" name="title_contest_text_box" maxlength="30" <?php if(isset($_POST['title_contest_text_box']) AND !empty($_POST['title_contest_text_box'])) { echo 'value="'.$_POST['title_contest_text_box'].'"'; } ?>>
                     </div>
                     <div class="form-group">
                        <label class="control-label" for="description_text_area">Description et règles</label>
                        <textarea class="form-control" rows="5" id="description_text_area" name="description_text_area" maxlength="1000" placeholder="Ex : Salut, je crée un concours pour vous remercier des 500 abonnés ! Les règles sont simples : tu dois faire un artbook sur le thème horreur et m'envoyer ton pseudo MSP par Skype. Je désignerai les meilleurs artbooks !&#x0a;Mon Skype : xxxxxxx&#x0a;Ma chaîne Youtube : xxxxxxx" style="resize: none;"><?php if(isset($_POST['description_text_area']) AND !empty($_POST['description_text_area'])) { echo $_POST['description_text_area']; } ?></textarea><br>
                        <a class="btn btn-default" data-toggle="modal" data-target="#smileys_modal"><span class="glyphicon glyphicon-plus"></span> Smiley</a>
                     </div>
                     <div class="form-group">
                        <label class="control-label">Récompense(s) <strong>(+ grande à la + petite)</strong></label>
                        <div class="row">
                           <div id="items_prices">
                              <div class="col-md-9">
                                 <input type="text" class="form-control" placeholder="Ex : salutation distinguée" id="price_item_text_box" name="price_item_text_box[]" maxlength="30">
                              </div>
                              <div class="col-md-3">
                                 <a class="btn btn-success add_price_button" id="add_price_button_1" name="add_price_button_1"><span class="glyphicon glyphicon-plus"></span></a>
                              </div>
                           </div>
                        </div>
                     </div>
                     <strong id="number_price_item_strong">1</strong> récompense(s) au total<br><br>
                     <legend></legend>
                     <div class="row">
                        <div class="col-md-6">
                           <center>
                              <label>Visibilité du concours</label>
                           </center>
                           <label><input type="radio" name="contest_type_radio" id="public_radio_button" value="public" checked>Publique</label><br>
                           <label><input type="radio" name="contest_type_radio" id="unlisted_radio_button" value="unlisted">Non listé</label>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <center>
                                 <label class="control-label" for="max_participant_combo_box">Nombre max. de participants</label>
                              </center>
                              <select class="form-control" id="max_participant_combo_box" name="max_participant_combo_box">
                                 <option id="1">5</option>
                                 <option id="2">10</option>
                                 <option id="3" selected>30</option>
                                 <option id="4">50</option>
                                 <option id="5">100</option>
                                 <option id="6">300</option>
                                 <option id="7">500</option>
                                 <option id="8">Illimité</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <center>
                                 <label class="control-label" for="category_contest_combo_box">Type de concours</label>
                              </center>
                              <select class="form-control" id="category_contest_combo_box" name="category_contest_combo_box">
                                 <option id="1">Concours VIP</option>
                                 <option id="2">Concours cadeaux</option>
                                 <option id="3">Concours rares</option>
                                 <option id="4">Concours salutations</option>
                                 <option id="5">Autre</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <label>
                     <input type="checkbox" name="comments_contest_checkbox" id="comments_contest_checkbox">Activer l'espace commentaire</a>
                     </label>
                     <label>
                     <input type="checkbox" name="only_participants_allow_to_comment_checkbox" id="only_participants_allow_to_comment_checkbox" disabled>Autoriser seulement les participants à commenter</a>
                     </label>
                     <label>
                     <input type="checkbox" name="participate_button_list_checkbox" id="participate_button_list_checkbox">Mettre le bouton "Participer" dans la liste des concours</a>
                     </label>
                     <label>
                     <input type="checkbox" name="allow_only_msp_account_linked_checkbox" id="allow_only_msp_account_linked_checkbox">Autoriser seulement ceux qui ont un compte MSP associé à participer</a>
                     </label>
                     <legend></legend>
                     <label>
                     <input type="checkbox" name="notification_new_participant_checkbox" id="notification_new_participant_checkbox">Recevoir une notification à chaque nouveau participant</a>
                     </label>
                     <label>
                     <input type="checkbox" name="notification_new_comment_checkbox" id="notification_new_comment_checkbox" disabled>Recevoir une notification à chaque nouveau commentaire</a>
                     </label>
                     <label>
                     <input type="checkbox" name="notification_new_contest_friend_checkbox" id="notification_new_contest_friend_checkbox" <?php if(is_profile_exist($_SESSION['id']) == false) { echo "disabled"; } ?>>Envoyer une notifications à tous mes amis MovieBook</a>
                     </label>
                     <label>
                     <input type="checkbox" name="accept_rules_checkbox" id="accept_rules_checkbox">J'ai lu et j'accepte <a target="_blank" href="../rules">les règles des concours</a>
                     </label><br>
                     <span class="glyphicon glyphicon-info-sign"></span><small> Toutes les données peuvent être modifiées par la suite, sauf les récompenses.</small>
                  </div>
               </div>
               <div align="center">
                  <input type="submit" class="btn btn-success" name="create_contest_button_1" id="create_contest_button_1" value="Crée-moi mon concours !">
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if(is_connected() == true) { ?>
<!-- Modal -->
<div id="see_contests_modal" class="modal fade ">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Les concours actifs <strong>(<?= $noc ?>)</strong></h4>
            </center>
            <div id="contest_elements_div"></div>
            <div id="loading" style="display:none;">
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
<?php if(is_connected() == false) { ?>
<!-- Modal -->
<div class="modal fade" id="need_to_be_connected_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Fonctionnalités seulement aux membres</h4>
               <p>Tu dois être connecté pour créer ou participer à des concours.<br>
                  <a href="../login?r=<?= $_SERVER['REQUEST_URI'] ?>">Se connecter</a> ou se <a href="../login?t=register&r=<?= $_SERVER['REQUEST_URI'] ?>">créer un compte</a>
               </p>
            </center>
            <div align="center">
               <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php if(is_connected() == true AND $already_have_contest == true) { ?>
<!-- Modal -->
<div class="modal fade" id="already_have_contest_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Oups...</h4>
               <p>Hé ! Tu as déjà un <strong>concours en cours</strong>. Si tu veux en créer un autre, il va falloir déjà terminer celui qui est en cours. <a href="contest?c=<?= $my_contest_id ?>">Clique ici</a> pour accèder à ton concours.</p>
            </center>
            <div align="center">
               <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } else { ?>
<!-- Modal -->
<?= smileys_modal("new_contest") ?>
<?php } ?>
<?php if(is_connected() == true) { ?>
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
<script type="text/javascript">
$(document).ready(function() {
	$("#create_contest_button").click(function() {
	    $('#<?php if(is_connected() == true AND $already_have_contest == false) { echo "create_contest_modal"; } elseif(is_connected() == true AND $already_have_contest == true) { echo "already_have_contest_modal"; } else { echo "need_to_be_connected_modal"; } ?>').modal('show');
	});
	$("#see_contests_button").click(function() {
	    $('#<?php if(is_connected() == true) { echo "see_contests_modal"; } else { echo "need_to_be_connected_modal"; } ?>').modal('show');
	});
  <?php if(isset($_GET['t']) AND !empty($_GET['t']) AND is_connected() == true) { ?>
  <?php if($_GET['t'] == "sc") { ?>
  $('#see_contests_modal').modal('show');
  $('#contest_elements_div').html('');
  $('#loading').show();
  $("#contest_elements_div").load("../npr/contest_elements_big", function() {
     $('#loading').hide();
  });
  <?php } elseif($_GET['t'] == "cc") { ?>
  $('#create_contest_modal').modal('show');
  <?php } ?>
  <?php } ?>
});
</script>