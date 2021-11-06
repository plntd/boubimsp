<?php
include('../includes/config.php');

if(is_connected() == false) {
    $_SESSION['flash']['danger'] = $danger_sign . "Tu dois être connecté pour accèder à cette page.";
    header('Location: ../login?r='.$_SERVER['REQUEST_URI']);
    exit();
}

if(is_profile_exist($_SESSION['id']) == false) {
    $_SESSION['flash']['danger'] = $danger_sign . 'Tu dois avoir un compte <strong>MovieBook</strong> pour accèder à cette page. Crée en toi un dès maintenant !';
    header('Location: profile?u='.$_SESSION['id']);
    exit();
}

///////////Les 3 dernières publications postées

$last_publications_sql = $database2->prepare("SELECT * FROM post_wall WHERE deleted = 0 ORDER BY post_date DESC LIMIT 3");
$last_publications_sql->execute();

///////////////////////////////////////////////

///////////La publication avec le plus de j'aime

$publication_with_the_most_likes_sql = $database2->prepare("SELECT post_id, count(*) as NUM FROM likes GROUP BY post_id ORDER BY NUM DESC LIMIT 1");
$publication_with_the_most_likes_sql->execute();
$publication_with_the_most_likes_data_1 = $publication_with_the_most_likes_sql->fetch();

$publication_with_the_most_likes_sql_1 = $database2->prepare("SELECT * FROM post_wall WHERE id = ? AND deleted = 0");
$publication_with_the_most_likes_sql_1->execute(array($publication_with_the_most_likes_data_1['post_id']));
$publication_with_the_most_likes_data = $publication_with_the_most_likes_sql_1->fetch();

///////////////////////////////////////////////

///////////La publication avec le plus de j'aime pas

$publication_with_the_most_dislikes_sql = $database2->prepare("SELECT post_id, count(*) as NUM FROM dislikes GROUP BY post_id ORDER BY NUM DESC LIMIT 1");
$publication_with_the_most_dislikes_sql->execute();
$publication_with_the_most_dislikes_data_1 = $publication_with_the_most_dislikes_sql->fetch();

$publication_with_the_most_dislikes_sql_1 = $database2->prepare("SELECT * FROM post_wall WHERE id = ? AND deleted = 0");
$publication_with_the_most_dislikes_sql_1->execute(array($publication_with_the_most_dislikes_data_1['post_id']));
$publication_with_the_most_dislikes_data = $publication_with_the_most_dislikes_sql_1->fetch();

///////////////////////////////////////////////

///////////La publication avec la dernière réponse postée

$last_reply_sql = $database2->prepare("SELECT * FROM reply_post_wall WHERE deleted = 0 ORDER BY post_date DESC LIMIT 1");
$last_reply_sql->execute();
$last_reply_data = $last_reply_sql->fetch();

$last_publication_reply_sql = $database2->prepare("SELECT * FROM post_wall WHERE id = ? AND deleted = 0");
$last_publication_reply_sql->execute(array($last_reply_data['post_id']));
$last_publication_reply_data = $last_publication_reply_sql->fetch();

///////////////////////////////////////////////

///////////Publication aléatoire
$random_publication_sql = $database2->prepare('SELECT * FROM post_wall WHERE deleted = 0 ORDER BY rand() LIMIT 1');
$random_publication_sql->execute();
$random_publication_data = $random_publication_sql->fetch();

///////////////////////////////////////////////

function showPublication($string)
{
	global $database;
	global $database2;
   $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
   $sql->execute(array($string['posted_by']));
   $account_data = $sql->fetch();
   $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
   $sql->execute(array($string['posted_by']));
   $profile_data = $sql->fetch();

   $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0');
   $reply_post_wall_sql->execute(array($string['id']));
   $total_reply = $reply_post_wall_sql->rowCount();

   $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND deleted = 0 ORDER BY id LIMIT 1');
   $reply_post_wall_sql->execute(array($string['id']));
   if($reply_post_wall_sql->rowCount() > 0) {
       $is_there_reply = true;
   } else {
       $is_there_reply = false;
   }
   ?>
   <div class="panel panel-default">
      <div class="panel-body">
         <div class="media">
            <div class="media-left media-top">
               <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
            </div>
            <div class="media-body">
               <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($string['post_date']) ?></small></label><br>
               <?= return_string($string['content']) ?>
            </div>
         </div>
         <?php if($string['is_picture'] == 1 AND isset($string['picture_path'])) { ?>
         <legend></legend>
         <div align="center">
            <div class="post_picture_group"><a href="../img/moviebook/pictures/<?= $string['picture_path'] ?>" data-lightbox="image-pinned" data-title="Image de <?= $account_data['username'] ?>"><img class="post_picture img-post-wall" src="../img/moviebook/pictures/<?= $string['picture_path'] ?>"></a></div>
         </div>
         <?php } ?>
      </div>
      <div class="panel-footer">
         <a class="btn btn-primary btn-sm" href="post?id=<?= $string['id'] ?>">Accéder à la publication</a>
         <?php
            $sql = $database2->prepare('SELECT * FROM dislikes WHERE post_id = ?');
            $sql->execute(array($string['id']));
            $total_dislikes_post = $sql->rowCount();
            $sql = $database2->prepare('SELECT * FROM likes WHERE post_id = ?');
            $sql->execute(array($string['id']));
            $total_likes_post = $sql->rowCount();
            ?>
         <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post" post_id="<?= $string['id'] ?>" type="dislike"><span class="glyphicon glyphicon-thumbs-down"></span> (<span id="number_of_dislikes_span_<?= $string['id'] ?>"><?php if(isset($total_dislikes_post) AND !empty($total_dislikes_post)) { echo $total_dislikes_post; } else { echo "0"; } ?></span>)</a>
         <a class="btn btn-default btn-sm pull-right mb_likes_dislikes_post" post_id="<?= $string['id'] ?>" type="like"><span class="glyphicon glyphicon-thumbs-up"></span> (<span id="number_of_likes_span_<?= $string['id'] ?>"><?php if(isset($total_likes_post) AND !empty($total_likes_post)) { echo $total_likes_post; } else { echo "0"; } ?></span>)</a>
      </div>
      <?php if($is_there_reply == true) {
         while($reply_string = $reply_post_wall_sql->fetch()) {
             $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
             $sql->execute(array($reply_string['username_id']));
             $account_data = $sql->fetch();
             $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
             $sql->execute(array($reply_string['username_id']));
             $profile_data = $sql->fetch();
            ?>
      <div class="panel-body">
         <div class="media">
            <div class="media-left media-top">
               <a href="profile?u=<?= $account_data['id'] ?>"><img class="media-object" height="64" width="64" src="<?= "../img/moviebook/avatars/".$profile_data['avatar'] ?>"></a>
            </div>
            <div class="media-body">
               <a href="profile?u=<?= $account_data['id'] ?>" class="media-heading"><?= $account_data['username'] ?></a> <?= badge_check($account_data['id'], "other") ?> <label><small><?= time_ago($reply_string['post_date']) ?></small></label><br>
               <?= return_string($reply_string['content']) ?>
            </div>
         </div>
      </div>
      <div class="panel-footer">
      	<?php if($total_reply > 1) { ?>
      	<small><label>Il y <?= $total_reply - 1 ?> <?php if($total_reply > 2) { echo "autres réponses"; } else { echo "autre réponse"; } ?> pour cette publication.</label></small>
      	<?php } ?>
      </div>
      <?php } ?>
      <?php } ?>
      </div>
<?php }

$current_page_title = "MovieBook - Accueil";
require '../includes/header.php';

?>
<br>
<center>
   <h1 class="page-title">MovieBook</h1>
</center>
<br>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-body">
				<h4>Publications récentes</h4>
            	<legend></legend>
				<?php while($last_publications_data = $last_publications_sql->fetch()) { ?>
			    <?= showPublication($last_publications_data) ?>
			    <?php } ?>
			</div>
		</div><br><br>
    <div class="panel panel-default">
      <div class="panel-body">
        <h4>Publication la plus likée</h4>
            <legend></legend>
          <?= showPublication($publication_with_the_most_likes_data) ?>
      </div>
    </div><br><br>
	</div>
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-body">
        <h4>Publication aléatoire</h4>
            <legend></legend>
          <?= showPublication($random_publication_data) ?>
      </div>
    </div><br><br>
    <div class="panel panel-default">
      <div class="panel-body">
        <h4>Publication avec la dernière réponse postée</h4>
            <legend></legend>
          <?= showPublication($last_publication_reply_data) ?>
      </div>
    </div><br><br>
		<div class="panel panel-default">
			<div class="panel-body">
				<h4>Publication la plus dislikée</h4>
	        	<legend></legend>
			    <?= showPublication($publication_with_the_most_dislikes_data) ?>
			  </div>
		</div>
	</div>
</div>
<br><center><a class="btn btn-lg btn-primary" href="profile?u=<?= $_SESSION['id'] ?>">Accéder à mon profil</a></center><br>
<?php require '../includes/footer.php'; ?>
<script src="../js/post_elements.min.js?v=<?= $javascript_post_elements_version ?>"></script>