<?php
include('includes/config.php');
$current_page_title = "Se connecter";
require 'includes/header.php';

if(is_connected() == true) {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être déconnecté pour te connecter sur un autre compte.";
    header('Location: /');
    exit();
}

if(isset($_GET['user']) AND !empty($_GET['user']) AND strlen($_GET['user']) <= 30) {
    $user = htmlspecialchars($_GET['user']);
}

if(isset($_GET['r']) AND !empty($_GET['r'])) {
   $page_redirect = htmlspecialchars($_GET['r']);
} else {
   $page_redirect = NULL;
}

if(isset($_GET['uID']) AND !empty($_GET['uID']) AND isset($_GET['sKey']) AND !empty($_GET['sKey']) AND strlen($_GET['sKey']) == 10) {
    $uID = htmlspecialchars($_GET['uID']);
    $sKey = htmlspecialchars($_GET['sKey']);
    $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND sponsorship_key = ?');
    $sql->execute(array(
        $uID,
        $sKey
    ));
    $uID_exist = $sql->rowCount();
    if($uID_exist == 1) {
        $account_data = $sql->fetch();
    } else {
        $uID = NULL;
        $sKey = NULL;
    }
} else {
    $uID = NULL;
    $sKey = NULL;
}

$sql = $database->prepare('SELECT * FROM account');
$sql->execute();
$noa = $sql->rowCount();
$noa = round($noa - $noa % 1000);

?>
<br>
<center>
   <h1 class="page-title">Se connecter</h1>
</center>
<br>
<input type="hidden" id="page_redirect_hidden" name="page_redirect_hidden" value="<?= $page_redirect ?>">
<div class="row">
   <div class="col-md-8 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="form-group">
               <label class="control-label" for="username_text_box">Ton pseudo</label>
               <input type="text" class="form-control" id="username_text_box" name="username_text_box" value="<?php if(isset($user) AND !empty($user)) { echo $user; } ?>" maxlength="30" autofocus required>
            </div>
            <div class="form-group">
               <label class="control-label" for="password_text_box">Ton mot de passe</label>
               <input type="password" class="form-control" id="password_text_box" name="password_text_box" maxlength="30" required>
            </div>
            <div class="checkbox">
               <label>
               <input type="checkbox" checked name="remember_checkbox" id="remember_checkbox"> Se souvenir de moi
               </label>
            </div>
            <center>
               <input type="button" class="btn btn-primary" name="login_button" id="login_button" value="Se connecter"><br><br>
               <legend></legend>
               <a href="#!" id="create_account_label" style="color: #d06d42;">Pas de compte ?</a><br>
               <a href="lostPassword" style="color: #d06d42;">Mot de passe oublié ?</a><br>
               <a href="lostConfirmation" style="color: #d06d42;">Email de confirmation perdu ?</a>
            </center>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="create_account_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <center>
               <h4 class="modal-title">Créer un compte</h4>
               <p class="lead">Rejoins plus de <strong><?= $noa ?></strong> utilisateurs pour profiter des nombreuses fonctionnalités !</p>
            </center>
            <?php if(isset($uID_exist) AND $uID_exist == 1) { ?>
            <p>Tu as été invité par <strong><?= $account_data['username'] ?></strong> à t'inscrire.</p>
            <?php } ?>
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label class="control-label" for="username_text_box_register">Ton pseudo <strong>(minimum 4 caractères)</strong></label>
                     <input type="text" class="form-control" id="username_text_box_register" name="username_text_box_register" value="<?php if(isset($user) AND !empty($user)) { echo $user; } ?>" maxlength="15" required>
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="password_text_box_register">Ton mot de passe <strong>(minimum 5 caractères)</strong></label>
                     <input type="password" class="form-control" id="password_text_box_register" name="password_text_box_register" maxlength="30" required>
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="confirm_password_text_box_register">Confirme le mot de passe</label>
                     <input type="password" class="form-control" id="confirm_password_text_box_register" name="confirm_password_text_box_register" maxlength="30" required>
                  </div>
                  <div class="form-group">
                     <label class="control-label" for="email_address_text_box_register">Ton adresse email</label>
                     <input type="email" class="form-control" id="email_address_text_box_register" name="email_address_text_box_register" maxlength="30" required>
                  </div>
                  <div class="checkbox">
                     <label>
                     <input type="checkbox" name="accept_rules_checkbox" id="accept_rules_checkbox"> J'ai lu et j'accepte <a target="_blank" href="rules">les règles générales</a> de ce site
                     </label>
                  </div>
                  <input type="hidden" id="user_id_hidden" name="user_id_hidden" value="<?= $uID ?>">
                  <input type="hidden" id="sponsorship_key_hidden" name="sponsorship_key_hidden" value="<?= $sKey ?>">
                  <center>
                     <div class="g-recaptcha" data-sitekey="<?= $site_key ?>"></div>
                  </center>
               </div>
            </div>
            <div align="center">
               <button class="btn btn-success" id="register_button" name="register_button"><span class="glyphicon glyphicon-ok"></span></button>
               <button class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="account_banned_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <div align="center">
               <h4 class="modal-title">Ton compte est banni</h4>
            </div>
            <p><strong>Ton compte est banni</strong> car tu n'as pas respecté les règles du site. Voici la raison :</p>
            <center><p id="banned_reason_p"></p></center>
            <p>Tu pourras te connecter dans <strong id="time_left_strong"></strong> jour(s).</p>
            <p>Si tu penses que c'est une erreur, contacte moi par email <strong>(support@boubimsp.fr)</strong> afin d'en discuter. <a href="rules">Clique ici</a> pour voir les règles du site.</p>
            <div align="center">
               <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php require 'includes/footer.php'; ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
$("#create_account_label").click(function() {
    $('#create_account_modal').modal('show');
});
<?php if(isset($_GET['t']) AND !empty($_GET['t']) AND $_GET['t'] == "register") { ?>
$('#create_account_modal').modal('show');
<?php } ?>
</script>