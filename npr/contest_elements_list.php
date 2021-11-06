<?php include('../includes/config.php');

if(is_connected() == false) {
  exit();
}

$contest_sql = $database->prepare('SELECT * FROM contest WHERE type = 1 AND deleted = 0 ORDER BY id DESC');
$contest_sql->execute();
$noc = $contest_sql->rowCount();

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

?>
<div class="row">
   <div class="col-md-8">
      <ul class="pagination pagination-sm" style="margin: 0px;">
         <li><a href="#!" id="see_contest_large_mode_button"><span class="glyphicon glyphicon-th-large"></span></a></li>
         <li class="active" id="see_contest_list_mode_button"><a href="#!"><span class="glyphicon glyphicon-th-list"></span></a></li>
      </ul>
   </div>
   <div class="col-md-4">
      <select class="form-control pull-right">
         <option id="1">Catégorie (bientôt)</option>
         <option id="2">Tout</option>
         <option id="3" disabled>Concours VIP</option>
         <option id="4" disabled>Concours cadeaux</option>
         <option id="5" disabled>Concours rares</option>
         <option id="6" disabled>Concours salutations</option>
         <option id="7" disabled>Autre</option>
      </select>
      <br><br>
   </div>
</div>
<?php if($noc > 0) { ?>
<table class="table table-bordered table-hover">
   <thead>
      <tr>
         <th>
            <center>Créateur</center>
         </th>
         <th>
            <center>Titre</center>
         </th>
         <th>
            <center>Date de création</center>
         </th>
         <th>
            <center>Nbr de places</center>
         </th>
         <th>
            <center></center>
         </th>
      </tr>
   </thead>
   <tbody>
      <?php $i = 0; ?>
      <?php while($contest_data = $contest_sql->fetch()) {
         $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
         $sql->execute(array($contest_data['username_id']));
         $account_data = $sql->fetch();

         $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ?');
         $sql->execute(array($contest_data['id']));
         $nop = $sql->rowCount();

         $sql = $database->prepare('SELECT * FROM contest_participants WHERE username_id = ? AND contest_id = ?');
         $sql->execute(array($_SESSION['id'], $contest_data['id']));
         $is_in_contest = $sql->rowCount();

         if(is_profile_exist($account_data['id'])) {
             if(strlen($account_data['username']) >= 20) {
                 $username_str = '<span data-toggle="tooltip" title="'.$account_data['username'].'"><a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . substr($account_data['username'], 0, 17) . "...</a></span>";
             } else {
                 $username_str = '<a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . $account_data['username'] . '</a>';
             }
         } elseif(strlen($account_data['username']) >= 20) {
            $username_str = '<span data-toggle="tooltip" title="'.$account_data['username'].'">'.substr($account_data['username'], 0, 17) . "...</span>";
         } else {
             $username_str = $account_data['username'];
         }

         if(strlen($contest_data['title']) >= 20) {
            $title_str = '<span data-toggle="tooltip" title="'.$contest_data['title'].'">'.substr($contest_data['title'], 0, 17).'...</span>';
         } else {
            $title_str = $contest_data['title'];
         }

         $i++;
         if($i%2 == 0) {
           $class = "active";
         } else {
           $class = "success";
         } ?>
      <tr class="<?= $class ?>">
         <td>
            <center><?= $username_str ?></center>
         </td>
         <td>
            <center><?= $title_str ?></center>
         </td>
         <td>
            <center><?= time_ago($contest_data['creation_date']) ?></center>
         </td>
         <td>
            <center><span id="number_of_participants_contest_span_<?= $contest_data['id'] ?>"><?= $nop ?></span>/<?php if($contest_data['max_participants'] == "unlimited") { echo "illimité"; } else { echo $contest_data['max_participants']; } ?></center>
         </td>
         <td>
            <?php if($contest_data['username_id'] != $_SESSION['id']) { ?>
            <?php if($contest_data['participate_button_list'] == 1) { ?>
            <center><a class="btn btn-info btn-xs" href="contest?c=<?= $contest_data['contest_id'] ?>">Voir</a>
               <?php if($is_in_contest == 0) { ?>
               <?php if($contest_data['max_participants'] == "unlimited" OR $nop < $contest_data['max_participants']) {
                  if($contest_data['allow_to_participate'] == 1) { ?>
               <button class="btn btn-success btn-xs participate_contest_button" id="participate_contest_button_<?= $contest_data['id'] ?>" name="participate_contest_button_<?= $contest_data['id'] ?>" type="join_contest" location="contest_list" contest_id="<?= $contest_data['id'] ?>">Participer</button>
               <?php } else { ?>
               <a class="btn btn-warning btn-xs" disabled>Désactivé</a>
               <?php } ?>
               <?php } else { ?>
               <a class="btn btn-danger btn-xs" disabled>Complet</a>
               <?php } ?>
               <?php } else { ?>
               <button class="btn btn-danger btn-xs participate_contest_button" id="participate_contest_button_<?= $contest_data['id'] ?>" name="participate_contest_button_<?= $contest_data['id'] ?>" type="leave_contest" location="contest_list" contest_id="<?= $contest_data['id'] ?>">Désinscrire</button>
               <?php } ?>
            </center>
            <?php } else { ?>
            <center>
            <a class="btn btn-primary btn-xs" href="contest?c=<?= $contest_data['contest_id'] ?>">En savoir plus</a>
            <?php } ?>
            <?php } else { ?>
            <center>
            <a class="btn btn-primary btn-xs" href="contest?c=<?= $contest_data['contest_id'] ?>">Voir mon concours</a>
            <?php } ?>
         </td>
      </tr>
      <?php } ?>
   </tbody>
</table>
<?php } else { ?>
<div align="center">
   <p>Oh... on dirait qu'il y a aucun concours en cours !</p>
</div>
<?php } ?>
<div align="center">
   <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
</div>
<script src="../js/app.boubimsp.min.js?v=<?= $javascript_version ?>"></script>
