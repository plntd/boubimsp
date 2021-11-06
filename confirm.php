<?php
include('includes/config.php');

if(isset($_GET['id']) AND !empty($_GET['id']) AND isset($_GET['t']) AND !empty($_GET['t']) AND strlen($_GET['t']) == 60) {
    if($account_enabled == true) {
        $user_id = htmlspecialchars($_GET['id']);
        $confirm_account_token = htmlspecialchars($_GET['t']);
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $user_id
        ));
        $account_data = $sql->fetch();
        if($account_data['confirm_account_token'] == $confirm_account_token) {
            $sql = $database->prepare('UPDATE account SET confirm_account_token = ?, activated_at = NOW() WHERE id = ?')->execute(array(
                'USED',
                $user_id
            ));
            if(isset($account_data['invited_by']) AND !empty($account_data['invited_by'])) {
                extra_points("account_created_sponsorship", $account_data['invited_by']);
            }
            $_SESSION['flash']['success'] = $success_sign . "Le compte <strong>" . $account_data['username'] . "</strong> est maintenant activé !";
            header('Location: login?user=' . $account_data['username']);
            exit();
        } else {
            $_SESSION['flash']['danger'] = $danger_sign . "Ce token n\'est pas valide.";
            header('Location: login');
            exit();
        }
    } else {
        $_SESSION['flash']['danger'] = $danger_sign . "Désolé, mais le service de compte est actuellement indisponible.";
        header('Location: login');
        exit();
    }
} else {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu n\'as rien à faire ici.";
    header('Location: login');
    exit();
}
?>