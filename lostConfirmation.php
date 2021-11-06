<?php
include('includes/config.php');
$current_page_title = "Email de confirmation perdu";
require 'includes/header.php';

if(isset($_SESSION['id']) AND !empty($_SESSION['id'])) {
    $_SESSION['flash']['danger'] = $danger_sign."Tu dois être déconnecté pour accèder à cette page.";
    header('Location: /');
    exit();
}

if(isset($_GET['u']) AND !empty($_GET['u']) AND strlen($_GET['u']) <= 30) {
    $u = htmlspecialchars($_GET['u']);
}

?>
<br>
<center>
   <h1 class="page-title">Email de confirmation perdu</h1>
</center>
<br>
<div class="row">
   <div class="col-lg-8 col-centered">
      <div class="panel panel-default">
         <div class="panel-body">
            <div class="form-group">
               <label class="control-label" for="username_text_box">Ton pseudo</label>
               <input type="text" class="form-control" id="username_text_box" name="username_text_box" value="<?php if(isset($u)) { echo $u; } ?>" maxlength="30" required>
            </div>
            <div class="form-group">
               <label class="control-label" for="email_address_text_box">Ton adresse email</label>
               <input type="email" class="form-control" id="email_address_text_box" name="email_address_text_box" maxlength="30" required>
            </div>
            <center><input type="button" class="btn btn-success" id="send_email_confirmation_button" name="send_email_confirmation_button" value="Envoyer"></center>
         </div>
      </div>
   </div>
</div>
<?php require 'includes/footer.php'; ?>