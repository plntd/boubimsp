<?php
include('includes/config.php');
if(is_connected() == true) {
	unset($_SESSION['id']);
	setcookie("auth", "", time() - 3600);
    $_SESSION['flash']['success'] = $success_sign."Tu es déconnecté. A bientôt !";
}
header('Location: login');
exit();
?>