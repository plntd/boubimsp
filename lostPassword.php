<?php
include('includes/config.php');
$current_page_title = "Mot de passe oublié";
require 'includes/header.php';

if(isset($_SESSION['id']) AND !empty($_SESSION['id'])) {
    $_SESSION['flash']['danger'] = $danger_sign."Tu dois être déconnecté pour accèder à cette page.";
    header('Location: /');
    exit();
}

?>
<br>
<center>
   <h1 class="page-title">Mot de passe oublié</h1>
</center>
<br>
<div class="row">
   <div class="col-lg-8 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="form-group">
               <label class="control-label" for="username_text_box">Ton pseudo</label>
               <input type="text" class="form-control" id="username_text_box" name="username_text_box" maxlength="30" required>
            </div>
            <div class="form-group">
               <label class="control-label" for="email_address_text_box">Ton adresse email</label>
               <input type="email" class="form-control" id="email_address_text_box" name="email_address_text_box" maxlength="30" required>
            </div>
            <center><input type="button" class="btn btn-success" id="send_email_password_button" name="send_email_password_button" value="Envoyer"></center>
         </div>
      </div>
   </div>
</div>
<?php require 'includes/footer.php'; ?>