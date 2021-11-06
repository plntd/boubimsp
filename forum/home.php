<?php
include('../includes/config.php');

$current_page_title = "Forum";
require '../includes/header.php';

if(isset($_GET['t']) AND !empty($_GET['t'])) {
   if($_GET['t'] == 1 OR $_GET['t'] == 2 OR $_GET['t'] == 3 OR $_GET['t'] == 4 OR $_GET['t'] == 5 OR $_GET['t'] == 6 OR $_GET['t'] == 7) {
      $type = htmlspecialchars($_GET['t']);
      if($type == 7 AND is_connected() == false) {
        $type = 6;
      }
   } else {
      $type = 6;
   }
} else {
   $type = 6;
}

if($type == 6) {

   $sql = $database->prepare('SELECT * FROM subject WHERE deleted = 0');
   $sql->execute();
   $nos = $sql->rowCount();

   $subjects_per_page = 20;
   $total_of_page = ceil($nos / $subjects_per_page);

   if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
       $p = htmlspecialchars($_GET['p']);
       $current_page = $p;
   } else {
       $current_page = 1;
   }

   $page_start = ($current_page - 1) * $subjects_per_page;

   $subjects_pinned_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 1 AND deleted = 0 ORDER BY last_comment_date DESC');
   $subjects_pinned_sql->execute();

   $subjects_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 0 AND deleted = 0 ORDER BY last_comment_date DESC LIMIT ' . $page_start . ',' . $subjects_per_page);
   $subjects_sql->execute();

} elseif($type == 7 AND is_connected()) {

    $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0');
    $sql->execute(array($_SESSION['id']));
    $nos = $sql->rowCount();

    $subjects_per_page = 20;
    $total_of_page = ceil($nos / $subjects_per_page);

    if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
        $p = htmlspecialchars($_GET['p']);
        $current_page = $p;
    } else {
        $current_page = 1;
    }

    $page_start = ($current_page - 1) * $subjects_per_page;

    $subjects_pinned_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 1 AND deleted = 0 ORDER BY last_comment_date DESC');
    $subjects_pinned_sql->execute();

    $subjects_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 0 AND username_id = ? AND deleted = 0 ORDER BY last_comment_date DESC LIMIT ' . $page_start . ',' . $subjects_per_page);
    $subjects_sql->execute(array($_SESSION['id']));

  } else {
   $sql = $database->prepare('SELECT * FROM subject WHERE category = ? AND deleted = 0');
   $sql->execute(array($type));
   $nos = $sql->rowCount();

   $subjects_per_page = 20;
   $total_of_page = ceil($nos / $subjects_per_page);

   if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
       $p = htmlspecialchars($_GET['p']);
       $current_page = $p;
   } else {
       $current_page = 1;
   }

   $page_start = ($current_page - 1) * $subjects_per_page;

   $subjects_pinned_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 1 AND deleted = 0 ORDER BY last_comment_date DESC');
   $subjects_pinned_sql->execute();

   $subjects_sql = $database->prepare('SELECT * FROM subject WHERE pinned = 0 AND category = ? AND deleted = 0 ORDER BY last_comment_date DESC LIMIT ' . $page_start . ',' . $subjects_per_page);
   $subjects_sql->execute(array($type));

}

if(is_connected()) {
   $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0 AND creation_date > DATE_SUB(NOW(), INTERVAL 1 DAY)');
   $sql->execute(array(
       $_SESSION['id']
   ));
   if($sql->rowCount() < 3) {
      $can_create_subject = true;
   } else {
      $can_create_subject = false;
   }
}

?>
<br>
<center>
   <h1 class="page-title">Forum</h1>
</center>
<br>
<div class="panel panel-default">
   <div class="panel-body">
      <div class="row">
         <div class="col-md-9">
            <a class="btn btn-primary" id="create_subject_show_modal_button"><span class="glyphicon glyphicon-plus"></span> Créer un sujet</a>
         </div>
         <div class="col-md-3">
            <select class="form-control pull-right" id="switch_category_subject_combobox">
               <option <?php if($type == 6) { echo "selected"; } ?> value="6">Tout</option>
               <option <?php if($type == 1) { echo "selected"; } ?> value="1">Jeux</option>
               <option <?php if($type == 2) { echo "selected"; } ?> value="2">Pubs</option>
               <option <?php if($type == 3) { echo "selected"; } ?> value="3">Papotage</option>
               <option <?php if($type == 4) { echo "selected"; } ?> value="4">Tutoriels</option>
               <option <?php if($type == 5) { echo "selected"; } ?> value="5">Autres</option>
               <?php if(is_connected()) { ?><option <?php if($type == 7) { echo "selected"; } ?><?php } ?> value="7">Mes sujets</option>
            </select>
            <br><br>
         </div>
      </div>
      <?php if($nos > 0) { ?>
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>
                  <center>Sujet</center>
               </th>
               <th>
                  <center>Auteur</center>
               </th>
               <th>
                  <center>NB</center>
               </th>
               <th>
                  <center>Dernier msg</center>
               </th>
            </tr>
         </thead>
         <tbody>
           <?php while($subject_pinned_data = $subjects_pinned_sql->fetch()) {
              $account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
              $account_sql->execute(array($subject_pinned_data['username_id']));
              $account_data = $account_sql->fetch();

              $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND deleted = 0');
              $sql->execute(array($subject_pinned_data['id']));
              $number_of_msg = $sql->rowCount();
              if(is_connected() == true AND $subject_pinned_data['username_id'] == $_SESSION['id']) { ?>
           <tr class="success">
              <?php } else { ?>
           <tr class="active">
              <?php } ?>
              <td>
                 <span style="display:inline-block;margin-right:10px; width:200px;" class="glyphicon glyphicon-pushpin"></span> <center style="display:inline-block; width:200px;"><a href="subject?id=<?= $subject_pinned_data['id'] ?>"><?= $subject_pinned_data['title'] ?></a></center>
              </td>
              <td>
                 <center><a href="../moviebook/profile?u=<?= $subject_pinned_data['username_id'] ?>"><?= $account_data['username'] ?></a></center>
              </td>
              <td>
                 <center><?= $number_of_msg ?></center>
              </td>
              <td>
                 <center><?= ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($subject_pinned_data['last_comment_date']))); ?></center>
              </td>
           </tr>
           <?php } ?>
            <?php while($subject_data = $subjects_sql->fetch()) {
               $account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $account_sql->execute(array($subject_data['username_id']));
               $account_data = $account_sql->fetch();

               $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND deleted = 0');
               $sql->execute(array($subject_data['id']));
               $number_of_msg = $sql->rowCount();

               if(is_connected() == true AND $subject_data['username_id'] == $_SESSION['id']) { ?>
            <tr class="success">
               <?php } else { ?>
            <tr class="active">
               <?php } ?>
               <td>
                  <center><a href="subject?id=<?= $subject_data['id'] ?>"><?= $subject_data['title'] ?></a></center>
               </td>
               <td>
                  <center><a href="../moviebook/profile?u=<?= $subject_data['username_id'] ?>"><?= $account_data['username'] ?></a></center>
               </td>
               <td>
                  <center><?= $number_of_msg ?></center>
               </td>
               <td>
                  <center><?= ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($subject_data['last_comment_date']))); ?></center>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
      <?php if($nos > 20) { ?>
      <center>
         <ul class="pagination" style="margin: 0px;">
            <?php if($current_page == 1) { ?>
            <li class="disabled"><a>&laquo;</a></li>
            <?php } else { ?>
            <li><a href="home?p=<?= $current_page - 1 ?>&t=<?= $type ?>">&laquo;</a></li>
            <?php } ?>
            <?php $min = max($current_page - 2, 1);
               $max = min($current_page + 2, $total_of_page);
               for($i = $min;$i <= $max;$i++) {
                  if($i == $current_page) { ?>
            <li class="active"><a><?= $i ?></a></li>
            <?php } else { ?>
            <li><a href="home?p=<?= $i ?>&t=<?= $type ?>""><?= $i ?></a></li>
            <?php }
               } ?>
            <?php if($current_page != $total_of_page) { ?>
            <li><a href="home?p=<?= $current_page + 1 ?>&t=<?= $type ?>"">&raquo;</a></li>
            <?php } else { ?>
            <li class="disabled"><a>&raquo;</a></li>
            <?php } ?>
            <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
         </ul>
      </center>
      <?php } ?>
      <?php } else { ?>
      <div align="center">
         <h5>Oh, on dirait qu'il n'y a aucun sujet... Vite, crée en un maintenant, on ne peut pas rester comme ça.</h5>
      </div>
      <?php } ?>
   </div>
</div>
<?php if(is_connected() == true) { ?>
<!-- Modal -->
<div class="modal fade" id="create_subject_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <?php if($can_create_subject == true) { ?>
            <center>
               <h4 class="modal-title">Créer un sujet</h4>
               <p class="lead">Partage des choses avec les autres utilisateurs.</p>
            </center>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label class="control-label" for="subject_name_text_box">Titre du sujet</label>
                     <input type="text" class="form-control" id="subject_name_text_box" placeholder="Ecris le titre de ton sujet..." name="subject_name_text_box" maxlength="60">
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="description_text_area">Description</label>
                     <textarea class="form-control" rows="5" id="description_text_area" name="description_text_area" maxlength="5000" placeholder="Ecris ta description..." style="resize: none;"></textarea>
                  </div>
                  <div class="row">
                     <div class="col-md-8">
                        <a class="btn btn-default" data-toggle="modal" data-target="#smileys_modal"><span class="glyphicon glyphicon-plus"></span> Smiley</a>
                     </div>
                     <div class="col-md-4">
                        <select class="form-control pull-right" id="switch_category_subject_combobox_1">
                           <option <?php if($type == 1) { echo "selected"; } ?> value="1">Jeu</option>
                           <option <?php if($type == 2) { echo "selected"; } ?> value="2">Pub</option>
                           <option <?php if($type == 3) { echo "selected"; } ?> value="3">Papotage</option>
                           <option <?php if($type == 4) { echo "selected"; } ?> value="4">Tutoriel</option>
                           <option <?php if($type == 5) { echo "selected"; } ?> value="5">Autre</option>
                        </select>
                     </div>
                  </div>
                  <br>
                  <label>
                  <input type="checkbox" name="notification_new_subject_friend_checkbox" id="notification_new_subject_friend_checkbox" <?php if(is_profile_exist($_SESSION['id']) == false) { echo "disabled"; } ?>> Envoyer une notifications à tous mes amis MovieBook</a>
                  </label>
                  <label>
                  <input type="checkbox" name="notification_new_comment_checkbox" id="notification_new_comment_checkbox"> Recevoir une notification à chaque nouveau commentaire</a>
                  </label>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" id="create_subject_button" name="create_subject_button">Crée-moi mon sujet !</button>
            </div>
         </div>
         <?php } else { ?>
         <center>
            <h4>Impossible de créer un sujet</h4>
            <p><u>Tu as atteint la limite :</u> tu ne peux pas créer plus de <strong>3 sujets par jour</strong>. Si tu veux absolument en poster un, alors supprime un de tes sujets qui datent de <strong>moins d'un jour</strong>.</p>
            <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
         </center>
         <?php } ?>
      </div>
   </div>
</div>
<!-- Modal -->
<?= smileys_modal("forum_new_subject") ?>
<?php } ?>
<?php if(is_connected() == false) { ?>
<!-- Modal -->
<div class="modal fade" id="need_to_be_connected_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Fonctionnalités seulement aux membres</h4>
               <p>Tu dois être connecté pour créer un sujet.<br>
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
<?php require '../includes/footer.php'; ?>
<script type="text/javascript">
$(document).ready(function() {
	$("#create_subject_show_modal_button").click(function() {
	    $('#<?php if(is_connected() == true) { echo "create_subject_modal"; } else { echo "need_to_be_connected_modal"; } ?>').modal('show');
	});
	<?php if(isset($_GET['t']) AND !empty($_GET['t']) AND is_connected() == true AND $_GET['t'] == "cs") { ?>
	$('#create_subject_modal').modal('show');
	<?php } ?>
});
</script>