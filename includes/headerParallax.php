<?php

if(is_connected()) {
   $is_connected = true;
} else {
   $is_connected = false;
}

?>
<!DOCTYPE html>
<!--
MMMMMMMMMMMMMMMMMMMMMNMmNNNNmddddhhhhhdhhhyhdddhhdmNMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMNmdhhhhhhyyyhhyyyyyyyyssssyyyyyhdmmNNMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMNNmmmhhyyyyysyyyyssyyyysssssossyyyyyyhhhmNNMMMMMMMMMMMM
MMMMMMMMMMMMMNmdhhyyyyysssssssssssssooooosssooooosssyyyyhdmNMMMMMMMMMM
MMMMMMMMMMMNNdhhyyysssoosssssoossssoossssooooooooooosyssosyhhNMMMMMMMM
MMMMMMMMMNmddhyysyyysssooooooooo++++oooooo++++ooooooooooooosydmNMMMMMM
MMMMMMMMmmhhhyssssssssoosssoooooo+++o++++++oooooo++++++ooo+oosyhmMMMMM
MMMMMMMMdmhhyyssssssooosoooooo+++ooooooo+++++++++++++++++oooo+osyymMMM
MMMMMMMmdhhyysyyyysooooooooo+++++++++++++++++++++++++++++++++++++yymMM
MMMMMMNdhhyysssssooooooo++++++++ooo+++++++++++++++++++++++++++++++oddM
MMMMMmddhyyssoooossso+++++o++++++++++++++++++++++++++++++++++++++++myM
MMMMNddhyyssssoooooo+++++++++++++++++++++++++++++++++++++++++++++++sdN
MMMMMmmhyyyssoooooo++++++++++++++++++++++++++++++++++++++++++//:::/+ys
MMMMNmdyysssssooooo++++///+++++++++++++++++++++++++////::--..`````-/yy
MMMMNmdysssssoooooo++:.``......--------------.......```````````````-y+
MMMMNdhyssssoooo+++++.``````````````````````````````````````````````/s
MMMMosoyssssoooo+++++-```````````````````````````````````````````````h
MMMMNhsysssoooooo++++-```````````````````````````````````````````````+
MMMMMNhysssoooooooo++.```.-:---...```````````````````````````````````-
MMMMMNhyyssoooo++++++:+ossyyyyyyssoo+:-...``````````````````..-:---.`.
MMMMNmdyssssoooo+++osyyyyysyyyyyyyyyyyyyyso+/-`````````./osssyyyyyyy/.
MMMMMmdysssoooo++++-....```....-:/+osyyyyyyyyo.``````.oyyyyys+//-..-.`
MMMMMNmmyssoooo++/.`````....://////::-::/osso-```````-+///:::////...`.
hyoho+sysssooo++:.``````./yhhysoss++++ooo/.``````````-/+sssdy//+oyo.`.
o-`````..+ssooo/``````.+ds-`  ./dMm:  ```.-``````````.+-`/dMd. `.:ys``
:.```````.-+ooo/``````.shso+:-+mMMo...:/++:``````````.///+hmhsyhy+-.`.
:.``....````-oo+`````````.:/osyhddhyso+:.`````````````--....--..``````
h:``..::-.```/oo.`````````````````````````````````````.:.````````````.
so/.`.-:::-...+o-``````````````````````````````````````.:.```````````.
Mdh+-.`..::-.`-o:`````````````````````````````...```````.::--````````.
MMm+s:-.`.-````-+.``````````````````````````.:...`````````:---```````:
MMMMh++:-```````-.``````````````````````````.:......`````./::.```````.
MMMMMNss/.```````````````````````````````````.-::-:::::::-.``````````s
MMMMMMMNds/-....`````````````````````````````````````.``````````````/o
MMMMMMMMMNdysssy:````````````````````.`````````````.```````````````.yh
MMMMMMMMMMMMNNN++-````````````````.-//:-.``````````..````.-.``````.osM
MMMMMMMMMMMMMMMMo+:```````````````-:...-+osoooo++++++++oo:.``````.ooMM
MMMMMMMMMMMMMMMMMh/+.````````````````````.-:/ossyyyyso/-.````````osNMM
MMMMMMMMMMMMMMMMMMNdy+.````````````````````............`````````/+NMMM
MMMMMMMMMMMMMMMMMMMMMdyo:````````````````````...-----..````````/omMMMM
MMMMMMMMMMMMMMMMMMMMMMMhyo/.`````````````````````````````````./+NMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMNdso/.`````````````````````````````-+yMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMmhsoo/.```````````````````````-oydMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNhyo+/++-````.-++y++yooo+oymMMMMMMMMMM
-->
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="<?php if(isset($web_description) AND !empty($web_description)) { echo $web_description; } else { echo "Profite des nombreux concours et du réseau social spécial MovieStarPlanet."; } ?>">
      <meta name="keywords" content="boubi msp, boubi, aventures, roger, moviebook, concours, forum">
      <meta property="og:image" content="https://boubi-msp001.000webhostapp.com/img/basic_boubi_msp_logo.png">
      <meta name="author" content="Boubi MSP">
      <link rel="icon" href="<?= $actual_link ?>/img/roger_icon.ico">
      <title><?= $current_page_title ?> | Boubi MSP</title>
      <link href="<?= $actual_link ?>/css/bootstrap.min.css" rel="stylesheet">
      <link href="<?= $actual_link ?>/css/<?php if($theme == 1) { echo "basic_theme.min.css"; } elseif($theme == 2) { echo "halloween_theme.min.css"; } ?>" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
      <script src="<?= $actual_link ?>/js/parallax.min.js"></script>
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-93676568-1"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-93676568-1');
      </script>
   </head>
   <body>
      <nav class="navbar navbar-inverse navbar-fixed-top">
         <div class="container">
            <div class="navbar-header">
               <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               </button>
               <a href="<?= $actual_link ?>"><img src="<?php if($theme == 1) { echo "https://i.imgur.com/6qChxQP.png"; } elseif($theme == 2) { echo "https://i.imgur.com/jAH6WVZ.png"; } ?>"></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
               <ul class="nav navbar-nav">
                  <li class="<?php if($current_page_title == "News") { echo "active"; } ?>"><a href="<?= $actual_link ?>/news">News</a></li>
                  <li class="dropdown">
                     <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Fonctionnalités<span class="caret"></span></a>
                     <ul class="dropdown-menu" role="menu">
                        <li><a href="<?= $actual_link ?>/rare">Commander un rare</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header"><label>Forum</label></li>
                        <li><a href="<?= $actual_link ?>/forum/home">Accueil</a></li>
                        <?php if($is_connected) { ?>
                        <li><a href="<?= $actual_link ?>/forum/home?t=cs">Créer un sujet</a></li>
                        <?php } ?>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header"><label>Concours MSP</label></li>
                        <li><a href="<?= $actual_link ?>/contest/home">Accueil</a></li>
                        <?php if($is_connected) {
                        $contest_sql = $database->prepare('SELECT * FROM contest WHERE username_id = ? AND deleted = 0');
                        $contest_sql->execute(array($_SESSION['id']));
                        if($contest_sql->rowCount() == 1) {
                        $contest_header_data = $contest_sql->fetch(); ?>
                        <li><a href="<?= $actual_link ?>/contest/contest?c=<?= $contest_header_data['contest_id'] ?>">Mon concours</a></li>
                        <?php } else { ?>
                        <li><a href="<?= $actual_link ?>/contest/home?t=cc">Créer un concours</a></li>
                        <?php } ?>
                        <li><a href="<?= $actual_link ?>/contest/home?t=sc">Voir les concours</a></li>
                        <?php } ?>
                     </ul>
                  </li>
                  <?php if($is_connected == false) { ?>
                  <li><a href="<?= $actual_link ?>/login?t=register&r=<?= $_SERVER['REQUEST_URI'] ?>">Créer un compte</a></li>
                  <li class="<?php if($current_page_title == "Se connecter") { echo "active"; } ?>"><a href="<?= $actual_link ?>/login?r=<?= $_SERVER['REQUEST_URI'] ?>">Se connecter</a></li>
                  <?php } ?>
                  <?php if($is_connected) { ?>
                  <?php $friend_request_sql = $database2->prepare('SELECT * FROM friend_request WHERE to_id = ?');
                  $friend_request_sql->execute(array($_SESSION['id']));
                  $friend_request_count = $friend_request_sql->rowCount();
                  $notifications_sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ?');
                  $notifications_sql->execute(array($_SESSION['id']));
                  $notifications_count = $notifications_sql->rowCount();
                  $notifications = $friend_request_count + $notifications_count ?>
                  <li class="<?php if($current_page_title == "Mon compte") { echo "active"; } ?>"><a href="<?= $actual_link ?>/account">Mon compte <?php if($notifications > 0) { ?> <span class="badge"><?= $notifications ?></span> <?php } ?></a></li>
                  <li class="dropdown">
                     <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">MovieBook<span class="caret"></span></a>
                     <ul class="dropdown-menu" role="menu">
                     <?php if(is_profile_exist($_SESSION['id']) == true) { ?>
                        <li><a href="<?= $actual_link ?>/moviebook/home">Accueil</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header"><label>Mon espace</label></li>
                        <li><a href="<?= $actual_link ?>/moviebook/profile?u=<?= $_SESSION['id'] ?>">Mon profil</a></li>
                        <li><a href="<?= $actual_link ?>/moviebook/level">Mon niveau</a></li>
                        <li><a href="<?= $actual_link ?>/moviebook/friends">Mes amis</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header"><label>Explorer</label></li>
                        <li><a href="<?= $actual_link ?>/moviebook/discover">Découvrir des profils</a></li>
                        <li><a href="<?= $actual_link ?>/moviebook/usersOnline">Utilisateurs en ligne <span class="badge"><?= $users_online_count ?></span></a></li>
                        <li><a href="<?= $actual_link ?>/moviebook/post?id=random">Publication aléatoire</a></li>
                        <li><a href="<?= $actual_link ?>/moviebook/profile?u=random">Profil aléatoire</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="<?= $actual_link ?>/moviebook/search">Rechercher</a></li>
                     <?php } else { ?>
                        <li><a href="<?= $actual_link ?>/moviebook/profile?u=<?= $_SESSION['id'] ?>">Créer mon profil</a></li>
                     <?php } ?>
                     </ul>
                  </li>
                  <?php } ?>
                  <?php if($is_connected) { ?>
                  <li><a href="<?= $actual_link ?>/logout">Se déconnecter</a></li>
                  <?php } ?>
               </ul>
            </div>
         </div>
      </nav>
      <br>
      <style type="text/css">body {background-image: none !important;}</style>
