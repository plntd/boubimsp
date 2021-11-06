<?php
include('includes/config.php');

if(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id']) AND strlen($_GET['id']) > 0) {
    $id = htmlspecialchars($_GET['id']);

    $sql = $database->prepare('SELECT * FROM news WHERE id = ?');
    $sql->execute(array(
        $id
    ));

    if($sql->rowCount() == 1) {
        $news_data = $sql->fetch();
        $current_page_title = $news_data['title'];

        $total_of_comments_sql = $database->prepare('SELECT * FROM news_comments WHERE news_id = ?');
        $total_of_comments_sql->execute(array(
            $id
        ));
        $total_of_comments = $total_of_comments_sql->rowCount();

        $comments_per_page = 6;
        $total_of_page = ceil($total_of_comments / $comments_per_page);

        if(isset($_GET['p']) AND !empty($_GET['p']) AND is_numeric($_GET['p']) AND strlen($_GET['p']) > 0 AND $_GET['p'] <= $total_of_page) {
            $p = htmlspecialchars($_GET['p']);
            $current_page = $p;
        } else {
            $current_page = 1;
        }

        $page_start = ($current_page - 1) * $comments_per_page;

        $news_comments_sql = $database->prepare('SELECT * FROM news_comments WHERE news_id = ? ORDER BY id DESC LIMIT ' . $page_start . ',' . $comments_per_page);
        $news_comments_sql->execute(array(
            $news_data['id']
        ));
    } else {
        $current_page_title = "News";
    }
} else {
    $current_page_title = "News";
}

require 'includes/header.php';

if(isset($news_data) AND !empty($news_data)) { ?>
<br>
<center>
   <a href="news" class="btn btn-primary pull-left"><span class="glyphicon glyphicon-arrow-left"></span></a>
   <h1 class="page-title"><?= $news_data['title'] ?></h1>
</center>
<br>
<?php $date_news = ucfirst(strftime('%A %d ', strtotime($news_data['date'])));
$date_news .= ucfirst(strftime('%B %Y</i>, à <i>%H:%M</i>', strtotime($news_data['date']))); ?>
<div class="panel panel-default">
   <div class="panel-body">
      <div class="row">
         <div class="col-md-10">
            <p><?= $news_data['text'] ?></p>
            <small>Posté par <?= $news_data['news_by'] ?>, le <i><?= $date_news ?></i></small>
         </div>
         <div class="col-md-2">
            <center>
               <div class="avatar_img hidden-sm hidden-xs">
                  <div class="transition left">
                     <a href="moviebook/profile?u=<?= $news_data['mb_id'] ?>"><img src="<?= $news_data['avatar_url'] ?>" class="img-thumbnail center"></a>
                     <label><?= $news_data['news_by'] ?></label>
                  </div>
               </div>
            </center>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-10 col-centered">
      <div class="panel panel-default">
         <div class="panel-heading">
            <center>
               <h3 class="text-orange">Fais-nous partager ton avis sur cette news</h3>
            </center>
         </div>
         <div class="panel-body">
            <?php if(is_connected() == true) { ?>
            <textarea class="form-control" rows="3" id="comment_textarea" name="comment_textarea" maxlength="1000" style="resize: none;"></textarea>
            <br>
            <center><input type="button" class="btn btn-success" name="post_comment_button" id="post_comment_button" value="Poster le commentaire"></center>
            <input type="hidden" name="news_id" id="news_id" value="<?= $news_data['id'] ?>">
            <?php } else { ?>
            <textarea class="form-control" rows="3" id="comment_textarea" name="comment_textarea" maxlength="1000" style="resize: none;" disabled></textarea>
            <br>
            <center>
               <input type="button" class="btn btn-success" name="post_comment_button" id="post_comment_button" value="Poster le commentaire" disabled><br><br>
               <label>Tu dois être connecté pour poster un commentaire. <a href="login">Clique ici</a> pour te connecter.</label>
            </center>
            <?php } ?>
            <br>
            <?php
               if($news_comments_sql->rowCount() > 0) {
               while($comment_data = $news_comments_sql->fetch()) {
               $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
               $sql->execute(array(
                   $comment_data['comment_by']
               ));
               $account_data = $sql->fetch();

               $post_date = ucfirst(strftime('%A %d ', strtotime($comment_data['post_date'])));
               $post_date .= ucfirst(strftime('%B %Y</i>, à <i>%H:%M</i>', strtotime($comment_data['post_date'])));
               ?>
            <div class="panel panel-default">
               <div class="panel-body">
                  <p><?= return_string($comment_data['content']) ?></p>
                  <small>Posté par <strong><a href="moviebook/profile?u=<?= $account_data['id'] ?>" style="color: #d06d42;"><?= $account_data['username'] ?></a></strong>, le <i><?= $post_date ?></i></small>
               </div>
               <?php if(is_connected() == true AND $_SESSION['id'] == $comment_data['comment_by']) { ?>
               <div class="panel-footer">
                  <button class="btn btn-danger delete" comment_id="<?= $comment_data['id'] ?>"><span class="glyphicon glyphicon-trash"></span></button>
               </div>
               <?php } ?>
            </div>
            <?php } ?>
            <div id="c"></div>
            <center>
               <ul class="pagination" style="margin: 0px;">
                  <?php if($current_page == 1) { ?>
                  <li class="disabled"><a>&laquo;</a></li>
                  <?php } else { ?>
                  <li><a href="news?id=<?= $id ?>&p=<?= $current_page - 1 ?>#c">&laquo;</a></li>
                  <?php } ?>
                  <?php $min = max($current_page - 2, 1);
                     $max = min($current_page + 2, $total_of_page);
                     for($i = $min;$i <= $max;$i++) {
                        if($i == $current_page) { ?>
                  <li class="active"><a><?= $i ?></a></li>
                  <?php } else { ?>
                  <li><a href="news?id=<?= $id ?>&p=<?= $i ?>#c"><?= $i ?></a></li>
                  <?php }
                     } ?>
                  <?php if($current_page != $total_of_page) { ?>
                  <li><a href="news?id=<?= $id ?>&p=<?= $current_page + 1 ?>#c">&raquo;</a></li>
                  <?php } else { ?>
                  <li class="disabled"><a>&raquo;</a></li>
                  <?php } ?>
                  <center><small><?= $current_page ?> sur <?= $total_of_page ?></small></center>
               </ul>
            </center>
         </div>
      </div>
      <?php } else { ?>
      <center>
         <br>
         <h4>Il n'y a aucun commentaire. Sois le premier à en poster un !</h4>
      </center>
      <?php } ?>
   </div>
</div>
<?php } else { ?>
<br>
<center>
   <h1 class="page-title">News</h1>
</center>
<br>
<?php $news_sql = $database->prepare('SELECT * FROM news ORDER BY id DESC');
   $news_sql->execute(); ?>
<div class="row">
   <?php while($news_data = $news_sql->fetch()) {
      $date_news = ucfirst(strftime('%d/%m/%y ', strtotime($news_data['date']))); ?>
   <div class="col-sm-6 col-md-4">
      <div class="thumbnail">
         <img src="<?= $news_data['img_url'] ?>" class="img-circle transition img-news" width="240" height="200">
         <div class="caption">
            <center>
               <h4 class="text-primary" style="color: #d06d42;"><?= $news_data['title'] ?></h4>
               <p><?= reduce_string($news_data['text'], 50, 50) ?></p>
               <small>Posté par <?= $news_data['news_by'] ?>, le <?= $date_news ?></small><br>
               <a href="news?id=<?= $news_data['id'] ?>" class="btn btn-primary">Lire la suite</a>
            </center>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
<?php } ?>
<?php require 'includes/footer.php'; ?>