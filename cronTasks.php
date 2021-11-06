<?php
include('includes/config.php');

///Supprimer tous les concours qui datent de + d'1 mois
$sql = $database->prepare('SELECT * FROM contest WHERE deleted = 0 AND creation_date < DATE_SUB(NOW(), INTERVAL 1 MONTH)');
$sql->execute();
if($sql->rowCount() > 0) {
    $contests_deleted = $sql->rowCount();
    while($contest_data = $sql->fetch()) {
        delete_contest_1($contest_data['id'], $contest_data['contest_id'], "contest_time_out");

        $sql_1 = $database2->prepare('INSERT INTO notifications(to_id,from_id,content,type,content_id) VALUES (?,1,?,"important_custom_notification",NULL)');
        $sql_1->execute(array(
            $contest_data['username_id'],
            "ton concours a été supprimé car il existe depuis plus d'1 mois."
        ));
    }
} else {
    $contests_deleted = 0;
}

///Supprimer toutes les notifications qui datent de + de 3 mois
$sql = $database2->prepare('SELECT * FROM notifications WHERE notification_date < DATE_SUB(NOW(), INTERVAL 3 MONTH)');
$sql->execute();
if($sql->rowCount() > 0) {
    $notifications_deleted = $sql->rowCount();
    while($notifications_data = $sql->fetch()) {
        $sql_1 = $database2->prepare('DELETE FROM notifications WHERE id = ?');
        $sql_1->execute(array(
            $notifications_data['id']
        ));
    }
} else {
    $notifications_deleted = 0;
}

///Supprimer tous les comptes non confirmés depuis 1 semaine
$sql = $database->prepare('SELECT * FROM account WHERE confirm_account_token <> "USED" AND join_date < DATE_SUB(NOW(), INTERVAL 1 WEEK)');
$sql->execute();
if($sql->rowCount() > 0) {
    $accounts_deleted = $sql->rowCount();
    $sql = $database->prepare('DELETE FROM account WHERE confirm_account_token <> "USED" AND join_date < DATE_SUB(NOW(), INTERVAL 1 WEEK)');
    $sql->execute();
} else {
    $accounts_deleted = 0;
}

///Désépingle tous les sujets épinglés
$sql = $database->prepare('SELECT * FROM subject WHERE pinned = 1 AND deleted = 0 AND pin_date < DATE_SUB(NOW(), INTERVAL 1 WEEK)');
$sql->execute();
if($sql->rowCount() > 0) {
    $subjects_deleted = $sql->rowCount();
    while($subject_data = $sql->fetch()) {
        $sql_1 = $database->prepare('UPDATE subject SET pinned = 0 WHERE id = ?')->execute(array(
            $subject_data['id']
        ));
    }
} else {
    $subjects_deleted = 0;
}

send_email("pl.nitard@gmail.com", "Récapitulatif des trucs supprimés", "Concours supprimés : " . $contests_deleted . "<br>
Notifications supprimées : " . $notifications_deleted . "<br>
Comptes supprimés : " . $accounts_deleted . "<br>
Sujets dépinglés : " . $subjects_deleted);
?>