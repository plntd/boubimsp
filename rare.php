<?php
include('includes/config.php');

if(is_connected()) {
    $is_connected = 1;
} else {
    $is_connected = 0;
}

if($is_connected == 1) {

    $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND join_date < DATE_SUB(NOW(), INTERVAL 1 WEEK)');
    $sql->execute(array(
        $_SESSION['id']
    ));
    $sql_2 = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ? AND is_confirmed = 1');
    $sql_2->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 1 AND $sql_2->rowCount() == 1) {
        $can_send_order = 1;
    } else {
        $can_send_order = 0;
    }

    $sql = $database->prepare('SELECT * FROM rare WHERE user_id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    $is_order_exist = $sql->rowCount();

    if($is_order_exist == 1) {
        $sql = $database->prepare('SELECT * FROM rare WHERE user_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $my_order_data = $sql->fetch();
    }

    $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 1) {
        $msp_account_data = $sql->fetch();
        $msp_username = $msp_account_data['msp_username'];
    }

    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    $account_data = $sql->fetch();

}

$sql = $database->prepare('SELECT id FROM rare');
$sql->execute();
$number_of_order = $sql->rowCount();

$sql = $database->prepare('SELECT id FROM rare_completed');
$sql->execute();
$number_of_order_completed = $sql->rowCount();

$sql = $database->prepare('SELECT rare_id, count(rare_id) id FROM rare_completed GROUP BY rare_id ORDER BY id DESC LIMIT 1;');
$sql->execute();
$rare_completed_data = $sql->fetch();
$statistics_popular_rare = convert_rare_id($rare_completed_data['rare_id']);

$sql = $database->prepare('SELECT complete_date FROM rare_completed ORDER BY id DESC LIMIT 1;');
$sql->execute();
$rare_completed_data = $sql->fetch();
$statistics_date = time_ago($rare_completed_data['complete_date']);

$sql = $database->prepare('SELECT deleted_by FROM rare_completed ORDER BY id DESC LIMIT 1;');
$sql->execute();
$rare_completed_data = $sql->fetch();
$sql = $database->prepare('SELECT username, id FROM account WHERE id = ?');
$sql->execute(array(
    $rare_completed_data['deleted_by']
));
$account_data_1 = $sql->fetch();
$statistics_last_order_deliver_username = $account_data_1['username'];
$statistics_last_order_deliver_id = $account_data_1['id'];

$sql = $database->prepare('SELECT deleted_by, count(deleted_by) id FROM rare_completed GROUP BY deleted_by ORDER BY id DESC LIMIT 1;');
$sql->execute();
$rare_completed_data = $sql->fetch();
$sql = $database->prepare('SELECT username, id FROM account WHERE id = ?');
$sql->execute(array(
    $rare_completed_data['deleted_by']
));
$account_data_1 = $sql->fetch();
$statistics_popular_deliver_username = $account_data_1['username'];
$statistics_popular_deliver_id = $account_data_1['id'];

$elements_per_page = 8;
$total_of_page = ceil($number_of_order / $elements_per_page);

if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
    $p = htmlspecialchars($_GET['p']);
    $current_page = $p;
} else {
    $current_page = 1;
}

$page_start = ($current_page - 1) * $elements_per_page;

$rare_sql = $database->prepare('SELECT * FROM rare ORDER BY id ASC LIMIT ' . $page_start . ',' . $elements_per_page);
$rare_sql->execute();

$current_page_title = "Commander un rare";
require 'includes/header.php';

?>
<br>
<center>
   <h1 class="page-title">Commander un rare</h1>
   <p class="lead text-white" style="font-size: 200%;">Obtenir des rares, hors semaine des rares ? Oui, c'est possible.</p>
</center>
<br>
<div class="row">
   <div class="col-md-5">
      <div class="panel panel-default">
         <div class="panel-body">
            <h4>Commander un rare</h4>
            <legend></legend>
            <div class="form-group">
               <label>Sélectionne un rare</small></label>
               <select class="form-control" id="select_rare_combo_box"<?php if($is_order_exist == 1 OR $is_connected == 0 OR $number_of_order >= $rare_limit) { echo " disabled"; } ?>>
                  <?php $rare_count = 0;
                     foreach($rares_list as $index => $rare) {
                       if($rare['vip'] == 0 AND $rare['available'] == 1) {
                         $rare_count++;
                       }
                     } ?>
                  <option disabled>--- RARES NON-VIP --- (<?= $rare_count ?>)</option>
                  <?php for($i = 0; $i < count($rares_list); $i++) {
                     if($rares_list[$i]['vip'] == 0 AND $rares_list[$i]['available'] == 1) { ?>
                  <option value="<?= $rares_list[$i]['id'] ?>"><?= $rares_list[$i]['name'] ?></option>
                  <?php } ?>
                  <?php } ?>
                  <?php $rare_count = 0;
                     foreach($rares_list as $index => $rare) {
                       if($rare['vip'] == 1 AND $rare['available'] == 1) {
                         $rare_count++;
                       }
                     } ?>
                  <option disabled>--- RARES VIP --- (<?= $rare_count ?>)</option>
                  <?php for($i = 0; $i < count($rares_list); $i++) {
                     if($rares_list[$i]['vip'] == 1 AND $rares_list[$i]['available'] == 1) { ?>
                  <option value="<?= $rares_list[$i]['id'] ?>"><?= $rares_list[$i]['name'] ?></option>
                  <?php } ?>
                  <?php } ?>
               </select>
            </div>
            <div class="form-group">
               <label class="control-label" for="msp_username_text_box">Ton pseudo MSP</label>
               <input type="text" class="form-control" id="msp_username_text_box" name="msp_username_text_box" placeholder="Entre un pseudo MSP" value="<?php if(isset($msp_username)) { echo $msp_username; } ?>" maxlength="30" autofocus required <?php if($is_connected == 0 OR $is_order_exist == 1 OR $number_of_order
                  >= $rare_limit) { echo " disabled"; } ?>>
            </div>
            <center>
               <?php if($is_connected == 1 AND $is_order_exist == 0 AND $number_of_order < $rare_limit AND $can_send_order == 1) { ?>
               <button class="btn btn-info" name="send_order_rare_button" id="send_order_rare_button">Envoyer la commande</button>
               <?php } elseif($is_connected == 1 AND $can_send_order == 0) { ?>
               <a class="btn btn-info" data-toggle="modal" data-target="#cant_send_order_modal">Envoyer la commande</a>
               <?php } else { ?>
               <button class="btn btn-info" name="send_order_rare_button" id="send_order_rare_button" disabled><?php if($number_of_order >= $rare_limit) { echo "La liste est pleine"; } elseif($is_connected == 1) { echo "Commande en cours"; } else { echo "Tu dois te connecter"; } ?></button>
               <?php } ?>
               <a type="button" class="btn btn-primary" data-toggle="modal" data-target="#rare_help">Comment ça marche ?</a>
            </center>
         </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <h4>Statistiques</h4>
          <legend></legend>
          • Il y a <strong><?= $number_of_order_completed ?></strong> commandes qui ont été complétées<br>
          • Le rare le plus commandé est <strong><?= $statistics_popular_rare ?></strong><br>
          • La dernière commande a été complétée <strong><?= $statistics_date ?></strong><br>
          • La dernière commande a été complétée par <strong><a href="moviebook/profile?u=<?= $statistics_last_order_deliver_id ?>"><?= $statistics_last_order_deliver_username ?></a></strong><br>
          • Le livreur de rare le plus motivé est <strong><a href="moviebook/profile?u=<?= $statistics_popular_deliver_id ?>"><?= $statistics_popular_deliver_username ?></a></strong>
        </div>
      </div>
      <?php if($is_connected == 1) {
         if($is_connected == 1 AND $account_data['admin_access'] == 5 OR $account_data['admin_access'] == 1) { ?>
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="form-group">
               <p><span class="glyphicon glyphicon-info-sign"></span> En tant que livreur de rares, tu peux compléter les commandes !</p>
               <label class="control-label" for="order_id_text_box"># de la commande</label>
               <input type="number" class="form-control" id="order_id_text_box" name="order_id_text_box" maxlength="30" required>
            </div>
            <center>
               <button class="btn btn-success" name="order_completed_button" id="order_completed_button">Compléter la commande</button>
            </center>
         </div>
      </div>
      <?php } ?>
      <?php } ?>
   </div>
   <div class="col-md-7">
      <div class="panel panel-default">
         <div class="panel-body">
            <h4>Liste d'attente (il y a <strong><?= $number_of_order."/".$rare_limit ?></strong> personnes)</h4>
            <legend></legend>
            <table class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>
                        <center>#</center>
                     </th>
                     <th>
                        <center>Pseudo MSP</center>
                     </th>
                     <th>
                        <center>Rare commandé</center>
                     </th>
                     <th>
                        <center>Date d'envoie</center>
                     </th>
                  </tr>
               </thead>
               <tbody>
                  <?php while($rare_data = $rare_sql->fetch()) {
                     if($is_connected == 1 AND $rare_data['user_id'] == $_SESSION['id']) {
                          $class = "success";
                        } else {
                          $class = "active";
                        } ?>
                  <tr class="<?= $class ?>">
                     <td>
                        <center><?= $rare_data['id'] ?></center>
                     </td>
                     <td>
                        <center><a href="moviebook/profile?u=<?= $rare_data['user_id'] ?>"><?= $rare_data['msp_username'] ?></a></center>
                     </td>
                     <td>
                        <center><?= convert_rare_id($rare_data['rare_id']) ?></center>
                     </td>
                     <td>
                        <center><?= strftime('%d/%m/%y à %H:%M', strtotime($rare_data['order_date'])); ?></center>
                     </td>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
            <?php if($number_of_order > 7) { ?>
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="rare?p=<?= $current_page - 1 ?>">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="rare?p=<?= $i ?>"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="rare?p=<?= $current_page + 1 ?>">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
            </center>
            <?php } ?>
         </div>
      </div>
      <?php if($is_connected == 1 AND $is_order_exist == 1) { ?>
      <div class="row">
         <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-danger">
               <div class="panel-heading">
                  <h3 class="panel-title">
                     <center>Ma commande (#<?= $my_order_data['id'] ?>)</center>
                  </h3>
               </div>
               <div class="panel-body">
                  <p>
                     Pseudo MSP : <strong><?= $my_order_data['msp_username'] ?></strong><br>
                     Rare commandé : <strong><?= convert_rare_id($my_order_data['rare_id']) ?></strong><br>
                     Date d'envoie : <strong><?= strftime('%d/%m/%y à %H:%M', strtotime($my_order_data['order_date'])) ?></strong>
                  </p>
                  <center>
                     <button class="btn btn-danger" name="delete_order_button" id="delete_order_button">Supprimer ma commande</button>
                  </center>
               </div>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="rare_help" tabindex="-1" role="dialog" aria-labelledby="rare_help_Label">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <h5>Comment envoyer une commande ?</h5>
            <p>
               Pour pouvoir bénificier des rares <strong>gratuitement</strong>, rien de plus simple.<br>
               Il faut tout d'abord que tu choisisses un rare dans la liste déroulante, puis appuie sur le bouton <strong>"Envoyer ma commande"</strong>. Tu seras directement enregistré dans la <strong>liste d'attente</strong>.<br>
               <span class="glyphicon glyphicon-info-sign"></span><small> L'heure de la date d'envoie de commande est reculée de 2h, à cause de la configuration du serveur.</small>
            </p>
            <h5>Qu'est-ce que la <strong>liste d'attente</strong> ?</h5>
            <p>
               La <strong>liste d'attente</strong> est un espace où toutes les personnes, comme toi, ont commandés un rare. Dès que la première personne en haut de la <strong>liste d'attente</strong> obtient son rare, cette dernière se supprime automatiquement de la <strong>liste d'attente</strong>. Et ainsi de suite, jusqu'à que ça soit ton tour. Tu peux bien sûr supprimer ta commande à tout moment, en cliquant sur le bouton <strong>"Supprimer ma commande"</strong>.
            </p>
            <h5>Comment les rares arriveront sur notre compte ?</h5>
            <p>
               J'ai la chance d'avoir une équipe <strong>formidable</strong>. Une équipe de <strong>confiance</strong>, qui se charge d'obtenir les rares commandés par les utilisateurs grâce à un faille de MovieStarPlanet. N'aie crainte, tu ne peux pas être bannis si tu commandes un rare. Ils offriront ensuite les rares obtenus aux pseudos <strong>MSP</strong> concernés. Elle fera <strong>tout son possible</strong> pour que tu obtiennes ton rare <strong>le plus vite possible</strong>.
            </p>
            <p>
              <?php $sql = $database->prepare('SELECT id, username FROM account WHERE admin_access = 5');
              $sql->execute();
              if($sql->rowCount() > 0) { ?>
                <u>Les personnes qui livrent actuellement les rares, sont :</u><br>
                <?php while($account_data_1 = $sql->fetch()) { ?>
                  - <a href="moviebook/profile?u=<?= $account_data_1['id'] ?>"><?= $account_data_1['username'] ?></a><br>
                <?php } ?>
              <?php } ?>
            </p>
            <div align="center">
               <a type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php if($is_connected == 1 AND $can_send_order == 0) { ?>
<!-- Modal -->
<div class="modal fade" id="cant_send_order_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Tu ne peux pas envoyer de commande</h4>
            </center>
            <p>Ton compte Boubi MSP ne respecte pas les critères pour envoyer une commande de rare.<br><br>
               <u>Voici les règles pour en envoyer une :</u><br>
               - Ton compte Boubi MSP doit être créé depuis <strong>1 semaine ou plus</strong><br>
               - Ton compte MSP associé à ton profil MovieBook doit être <strong>certifié</strong>, <a href="forum/subject?id=1194">clique ici</a> pour voir comment faire pour certifier ton compte MSP
            </p>
            <div align="center">
               <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<?php require 'includes/footer.php'; ?>