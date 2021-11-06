<?php
include('includes/config.php');
$current_page_title = "Mot de passe oublié";
require 'includes/header.php';

if(isset($_SESSION['id']) AND !empty($_SESSION['id'])) {
    $_SESSION['flash']['danger'] = $danger_sign . 'Tu dois être déconnecté pour accèder à cette page.';
    header('Location: /');
    exit();
}

if(isset($_GET['id']) AND !empty($_GET['id']) AND isset($_GET['t']) AND !empty($_GET['t']) AND strlen($_GET['t']) == 60) {
    if($account_enabled == true) {
        $user_id = htmlspecialchars($_GET['id']);
        $reset_password_token = htmlspecialchars($_GET['t']);
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND reset_password_token = ? AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
        $sql->execute(array(
            $user_id,
            $reset_password_token
        ));
        $account_data = $sql->fetch();
        if($account_data) {
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
<br>
<center>
   <h1 class="page-title">Modifier son mot de passe</h1>
</center>
<br>
<div class="row">
   <div class="col-md-8 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="form-group">
               <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($_GET['id']) ?>">
               <input type="hidden" name="t" id="t" value="<?= htmlspecialchars($_GET['t']) ?>">
               <label class="control-label" for="password_text_box">Ton nouveau mot de passe <strong>(minimim 5 caractères)</strong></label>
               <input type="password" class="form-control" id="password_text_box" name="password_text_box" maxlength="30" required>
            </div>
            <div class="form-group">
               <label class="control-label" for="confirm_text_box">Confirmation</label>
               <input type="password" class="form-control" id="confirm_text_box" name="confirm_text_box" maxlength="30" required>
            </div>
            <center><input type="button" class="btn btn-success" id="reset_password_button" name="reset_password_button" value="Changer de mot de passe"></center>
         </div>
      </div>
   </div>
</div>
<?php require 'includes/footer.php'; ?>