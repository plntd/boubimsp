<?php
include('../includes/config.php');

if(is_connected() == false) {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accèder à cette page.";
    header('Location: ../login');
    exit();
}

if(isset($_GET['c']) AND !empty($_GET['c']) AND strlen($_GET['c']) > 0) {
    $contest_id = htmlspecialchars($_GET['c']);

    $contest_sql = $database->prepare('SELECT * FROM contest_ended WHERE contest_id = ?');
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
                    $msp_username = $msp_account_data['msp_username'] . ' <img data-toggle="tooltip" title="Ce compte MSP lui appartient" src="../img/moviebook/certified_badge.png" width="15" height="15">';
                } else {
                    $msp_username = $msp_account_data['msp_username'];
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
        } else {
            $sql = $database->prepare('SELECT * FROM contest_participants WHERE username_id = ? AND contest_id = ?');
            $sql->execute(array(
                $_SESSION['id'],
                $contest_data['id']
            ));
            $me_participant_data = $sql->fetch();
            $is_in_contest = $sql->rowCount();
            $is_my_contest = false;
        }

        if($contest_sql->rowCount() != 1) {
            $_SESSION['flash']['danger'] = $danger_sign . "L\'ID du concours est <strong>invalide</strong>.";
            header("Location: home");
            exit();
        }

        $account_sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $account_sql->execute(array(
            $contest_data['username_id']
        ));
        $account_data = $account_sql->fetch();

    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "L\'ID du concours est <strong>invalide</strong>.";
        header("Location: home");
        exit();
    }
} else {
    header("Location: home");
    exit();
}

$current_page_title = "Concours de " . $account_data['username'];
require '../includes/header.php';

if(is_profile_exist($account_data['id'])) {
    $username_str = '<a href="../moviebook/profile?u=' . $account_data['id'] . '" target="_blank">' . $account_data['username'] . '</a>';
} else {
    $username_str = $account_data['username'];
}
?>
<br>
<center>
   <a href="home?t=sc" class="btn btn-primary pull-left"><span class="glyphicon glyphicon-arrow-left"></span></a>
   <h1 class="page-title">Résultats du concours</h1>
</center>
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
            <?= return_string($contest_data['content']) ?>
         </div>
         <div class="col-md-6">
            <strong>Créateur :</strong> <?= $username_str ?><br>
            <strong>Nombre de participants au total :</strong> <?= $contest_data['number_participants'] ?><br>
            <?php $creation_date = ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($contest_data['creation_date']))); ?>
            <strong>Date de création :</strong> <?= $creation_date ?> <small><i>(<?= time_ago($contest_data['creation_date']) ?>)</i></small><br>
            <?php $end_date = ucfirst(strftime('%d/%m/%y à %H:%M', strtotime($contest_data['date']))); ?>
            <strong>Date de fin :</strong> <?= $end_date ?> <small><i>(<?= time_ago($contest_data['date']) ?>)</i></small>
            <?php if($is_msp_username == true) { ?>
            <br><strong>Pseudo MSP :</strong> <?= $msp_username ?>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
<?php require '../includes/footer.php'; ?>