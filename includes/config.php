<?php
date_default_timezone_set('Europe/Paris');
ob_start();
session_start();

$danger_sign = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ';
$success_sign = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ';
$warning_sign = '<span class="glyphicon glyphicon-alert" aria-hidden="true"></span> ';

$javascript_version = "4.1";
$javascript_post_elements_version = "4.1";
$css_version = "1.1";

$localhost_mode = false;
$localhost_mode_type = 1;

$maintenance_mode = false;

$current_page_title = "";

if($localhost_mode == true) {
    if($localhost_mode_type == 1) {
        $database = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=msph', 'root', '');
        $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $database2 = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=moviebook', 'root', '');
        $database2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $database2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $site_key = "6Lc8VigTAAAAAJy4ooKCWTZo9NhSMZmPw6svd9Pi";
        $secret_key = "6Lc8VigTAAAAAFCtF9A3NU-ZbJstHrrQdTIQTeAd";

        $actual_link = "http://localhost/Sites/Boubi%20MSP";
    } else {
        $database = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=msph', 'root', 'root');
        $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $database2 = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=moviebook', 'root', 'root');
        $database2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $database2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $site_key = "6Lc8VigTAAAAAJy4ooKCWTZo9NhSMZmPw6svd9Pi";
        $secret_key = "6Lc8VigTAAAAAFCtF9A3NU-ZbJstHrrQdTIQTeAd";

        $actual_link = "http://localhost:8888/Boubi%20MSP";
    }  
} else {
    $database = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=id16402617_main', 'id16402617_main1', 's&b5pzO1k2$$TlJWod0Z');
    $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $database2 = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=id16402617_moviebook', 'id16402617_moviebook1', 's&b5pzO1k2$$TlJWod0Z');
    $database2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $database2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $site_key = "6Lc_VYMaAAAAADMjP_bAApJrj8jpyRbMYEqpePap";
    $secret_key = "6Lc_VYMaAAAAAKW2K2LnwCzQh5eeF6TR6jX6nxzs";

    $actual_link = "https://acoustical-kettles.000webhostapp.com";
}

//CONTROL
$rare_limit = 60;

$login_enabled = true;

$create_account_enabled = true;

#Désactivé : confirmation du compte, envoie email oublie véirification, envoie email oublie de mdp, mdp oublié changement, changement de mot de passe, changement de pseudo MSP.
$account_enabled = true;

#Désactivé : commentez, et supprimer ses commentaires des News.
$news_comments_enabled = true;

#Désactivé : participer aux concours.
$contest_enabled = true;

#Désactivé : créer un compte MovieBook.
$mb_create_account_enabled = true;

#Désactivé : ajouté prénom/surnom, date d'anniversaire, description MovieBook.
$mb_add_info_enabled = true;

#Désactivé : poster et supprimer une publication sur un mur MovieBook.
$mb_post_wall_enabled = true;

#Désactivé : J'aime/Je n'aime pas d'un publication MovieBook.
$mb_likes_dislikes_post_wall_enabled = true;

#Désactivé : ajouter, supprimer, accepter/refuser amis MovieBook.
$mb_friends_enabled = true;

#Désactivé : signalement de contenu.
$report_enabled = true;

#Désactivé : changer bannière, couleur principale, photo de profil.
$mb_custom_enabled = true;

#Désactivé : répondre aux post sur les murs.
$mb_post_reply_enabled = true;

#Désactivé : épingler/dépingler des publications sur son mur.
$mb_pin_post_enabled = true;

if(isset($_COOKIE['auth']) AND !empty($_COOKIE['auth']) AND !isset($_SESSION['id']) AND empty($_SESSION['id'])) {
    $auth = $_COOKIE['auth'];
    $auth = explode('//', $auth);
    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $sql->execute(array(
        $auth[0]
    ));
    $account_data = $sql->fetch();
    $key = sha1($account_data['username'] . $account_data['password']);
    if($key == $auth[1]) {
        $_SESSION['id'] = $auth[0];
        setcookie('auth', $account_data['id'] . '//' . sha1($account_data['username'] . $account_data['password']), time() + 3600 * 24 * 7, null, null, false, true);
    } else {
        setcookie("auth", "", time() - 3600);
    }
}

if(is_connected() == true) {
    $session_time = 300;
    $time_now = time();
    $user_ip = $_SERVER['REMOTE_ADDR'];

    $sql = $database2->prepare('SELECT * FROM users_online WHERE username_id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 0) {
        $sql = $database2->prepare('INSERT INTO users_online(username_id, session_time, user_ip) VALUES (?,?,?)');
        $sql->execute(array(
            $_SESSION['id'],
            $time_now,
            $user_ip
        ));
    } else {
        $sql = $database2->prepare('UPDATE users_online SET user_ip = ?, session_time = ?, last_connection = NOW(), deleted = 0 WHERE username_id = ?')->execute(array(
            $user_ip,
            $time_now,
            $_SESSION['id']
        ));
    }

    $session_delete_time = $time_now - $session_time;
    $sql = $database2->prepare('UPDATE users_online SET deleted = 1 WHERE session_time < ?');
    $sql->execute(array(
        $session_delete_time
    ));

    $users_online_sql = $database2->prepare('SELECT * FROM users_online WHERE deleted = 0');
    $users_online_sql->execute();
    $users_online_count = $users_online_sql->rowCount();
}

if(is_connected()) {
    global $database;
    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    $account_data_3423 = $sql->fetch();
    $theme = $account_data_3423['theme'];
} else {
    $theme = 1;
}

function is_connected()
{
    if(isset($_SESSION['id']) AND !empty($_SESSION['id'])) {
        global $database;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND banned = 0');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            return true;
        } else {
            unset($_SESSION['id']);
            setcookie("auth", "", time() - 3600);
            return false;
        }
    } else {
        unset($_SESSION['id']);
        setcookie("auth", "", time() - 3600);
        return false;
    }
}

function str_random($lenght)
{
    $string = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    return substr(str_shuffle(str_repeat($string, $lenght)), 0, $lenght);
}

function send_email($email_address, $subject, $body)
{
    $email_from = 'noreply@boubi-msp001.000webhostapp.com';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "To: " . $email_address . " \r\n";
    $headers .= "From: <" . $email_from . "> \r\n";

    mail($email_address, $subject, $body, $headers);
}

function reduce_string($string, $word_limit, $lien)
{
    $string = strip_tags($string);
    $words = explode(' ', $string, ($word_limit + 1));
    if(count($words) > $word_limit) {
        array_pop($words);
    } else
        $fin = '';
    return implode(' ', $words) . "...";
}

function is_profile_exist($user_id)
{
    global $database2;
    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
    $sql->execute(array(
        $user_id
    ));
    if($sql->rowCount() == 1) {
        return true;
    } else {
        return false;
    }
}

function time_ago($time)
{
    $time_ago = strtotime("+1 hours", strtotime($time));
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if($seconds <= 60) {
        return "à l'instant";
    } elseif($minutes <= 59) {
        if($minutes == 1) {
            return "il y a une minute";
        } else {
            return "il y a $minutes minutes";
        }
    } elseif($hours <= 23) {
        if($hours == 1) {
            return "il y a une heure";
        } else {
            return "il y a $hours heures";
        }
    } elseif($days <= 7) {
        if($days == 1) {
            return "hier";
        } else {
            return "il y a $days jours";
        }
    } elseif($weeks <= 4.3) {
        if($weeks == 1) {
            return "il y a une semaine";
        } else {
            return "il y a $weeks semaines";
        }
    } elseif($months <= 12) {
        if($months == 1) {
            return "il y a un mois";
        } else {
            return "il y a $months mois";
        }
    } elseif($years == 1) {
        return "il y a un an";
    } else {
        return "il y a $years ans";
    }
}

function is_friends($user_one, $user_two)
{
    global $database2;
    $sql = $database2->prepare('SELECT * FROM friends WHERE (user_one = ? AND user_two = ?) OR (user_one = ? AND user_two = ?)');
    $sql->execute(array(
        $user_one,
        $user_two,
        $user_two,
        $user_one
    ));
    if($sql->rowCount() == 1) {
        return true;
    } else {
        return false;
    }
}

function adjustBrightness($hex, $steps)
{
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    if(strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach($color_parts as $color) {
        $color = hexdec($color);
        $color = max(0, min(255, $color + $steps));
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
    }
    return $return;
}

function insert_notification($to_id, $from_id, $type, $content_id)
{
    if(is_connected() == true) {
        if(empty($content_id)) {
            $content_id = NULL;
        }
        global $database2;
        $sql = $database2->prepare('INSERT INTO notifications(to_id,from_id,type,content_id) VALUES (?,?,?,?)');
        $sql->execute(array(
            $to_id,
            $from_id,
            $type,
            $content_id
        ));
    }
}

function return_string($string)
{
    global $actual_link;

    $string = nl2br($string);

    $string = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $string);

    $string = preg_replace("/((https?|ftp|file):[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#!\/%=~_|])/i", "<a target=\"_blank\" href=\"$1\">$1</a>", $string);

    $string = preg_replace("/\*\*(.*?)\*\*/", '<strong>$1</strong>', $string);
    $string = preg_replace("/\_\_(.*?)\_\_/", '<u>$1</u>', $string);
    $string = preg_replace("/\~\~(.*?)\~\~/", '<strike>$1</strike>', $string);
    $string = preg_replace("/\*(.*?)\*/", '<i>$1</i>', $string);

    $smiley_replace = array(
        ":roger:",
        ":roger_colere:",
        ":roger_triste:",
        ":roger_choque:",
        ":roger_content:",
        ":roger_drogue:",
        ":roger_thug:",
        ":roger_clin_doeil:",
        ":clin_doeil:",
        ":lunettes:",
        ":fouet:",
        ":fouet_1:",
        ":mmmh_big:",
        ":clin_doeil_big:",
        ":cool_big:",
        ":penseuze_big:",
        ":jannael_4_big:",
        ":jannael_3_big:",
        ":jannael_big:",
        ":jannael_2_big:",
        ":dab_big:",
        ":thug_dog_big:",
        ":oups_big:",
        ":risitas_big:",
        ":roger_big:",
        ":roger_colere_big:",
        ":roger_triste_big:",
        ":roger_choque_big:",
        ":roger_content_big:",
        ":roger_drogue_big:",
        ":roger_thug_big:",
        ":roger_clin_doeil_big:",
        ":roger_patate_big:"
    );
    $smiley_new = array(
        '<img src="' . $actual_link . '/img/smileys/roger.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_colere.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_triste.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_choque.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_content.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_drogue.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_thug.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/roger_clin_doeil.png" draggable="false" width="20" height="26">',
        '<img src="' . $actual_link . '/img/smileys/clin_doeil.png" draggable="false" width="25" height="25">',
        '<img src="' . $actual_link . '/img/smileys/lunettes.png" draggable="false" width="25" height="21">',
        '<img src="' . $actual_link . '/img/smileys/fouet.png" draggable="false" width="25" height="25">',
        '<img src="' . $actual_link . '/img/smileys/fouet_1.png" draggable="false" width="25" height="25">',
        '<img src="' . $actual_link . '/img/smileys/mmmh.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/clin_doeil.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/cool.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/penseuze.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/jannael_4.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/jannael_3.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/jannael.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/jannael_2.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/dab.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/thug_dog.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/oups.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/risitas.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_colere.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_triste.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_choque.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_content.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_drogue.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_thug.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_clin_doeil.png" draggable="false">',
        '<img src="' . $actual_link . '/img/smileys/roger_patate.png" draggable="false">'
    );
    $string = str_replace($smiley_replace, $smiley_new, $string);
    return $string;
}

function smileys_modal($location)
{
    return '
    <div class="modal fade" id="smileys_modal" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
          <div class="modal-content">
             <div class="modal-body">
                <center>
                   <h4 class="modal-title">Ajouter un smiley</h4>
                   <p>Personnalise et rend vie à ton contenu en ajoutant des smileys !</p>
                </center>
                Roger
                <legend></legend>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger:"><img src="../img/smileys/roger.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_colere:"><img src="../img/smileys/roger_colere.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_triste:"><img src="../img/smileys/roger_triste.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_choque:"><img src="../img/smileys/roger_choque.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_content:"><img src="../img/smileys/roger_content.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_drogue:"><img src="../img/smileys/roger_drogue.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_thug:"><img src="../img/smileys/roger_thug.png" draggable="false" width="20" height="26"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_clin_doeil:"><img src="../img/smileys/roger_clin_doeil.png" draggable="false" width="20" height="26"></a><br><br>
                Autres
                <legend></legend>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":clin_doeil:"><img src="../img/smileys/clin_doeil.png" draggable="false" width="22" height="22"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":lunettes:"><img src="../img/smileys/lunettes.png" draggable="false" width="25" height="21"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":fouet:"><img src="../img/smileys/fouet.png" draggable="false" width="25" height="25"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":fouet_1:"><img src="../img/smileys/fouet_1.png" draggable="false" width="25" height="25"></a><br><br>
                Géant
                <legend></legend>
                <center>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":mmmh_big:"><img src="../img/smileys/mmmh.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":clin_doeil_big:"><img src="../img/smileys/clin_doeil.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":cool_big:"><img src="../img/smileys/cool.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":penseuze_big:"><img src="../img/smileys/penseuze.png" draggable="false"></a><br><br>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":jannael_4_big:"><img src="../img/smileys/jannael_4.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":jannael_3_big:"><img src="../img/smileys/jannael_3.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":jannael_big:"><img src="../img/smileys/jannael.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":jannael_2_big:"><img src="../img/smileys/jannael_2.png" draggable="false"></a><br><br>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":dab_big:"><img src="../img/smileys/dab.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":thug_dog_big:"><img src="../img/smileys/thug_dog.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":oups_big:"><img src="../img/smileys/oups.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":risitas_big:"><img src="../img/smileys/risitas.png" draggable="false"></a><br><br>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_big:"><img src="../img/smileys/roger.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_colere_big:"><img src="../img/smileys/roger_colere.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_triste_big:"><img src="../img/smileys/roger_triste.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_choque_big:"><img src="../img/smileys/roger_choque.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_content_big:"><img src="../img/smileys/roger_content.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_drogue_big:"><img src="../img/smileys/roger_drogue.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_thug_big:"><img src="../img/smileys/roger_thug.png" draggable="false"></a>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_clin_doeil_big:"><img src="../img/smileys/roger_clin_doeil.png" draggable="false"></a><br><br>
                <a class="btn btn-default add_smiley_button" location="' . $location . '" smiley=":roger_patate_big:"><img src="../img/smileys/roger_patate.png" draggable="false"></a>
                </center>
                Mise en forme du texte
                <legend></legend>
                <ul>
                    <li><i>italique</i> = *italique*</li>
                    <li><strong>gras</strong> = **gras**</li>
                    <li><strike>barré</strike> = ~~barré~~</li>
                    <li><u>souligné</u> = __souligné__</li>
                </ul>
                <div align="center">
                   <a class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></a>
                </div>
             </div>
          </div>
       </div>
    </div>';
}

function badge_check($username_id, $location)
{
    global $database;
    global $database2;

    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $sql->execute(array(
        $username_id
    ));

    if($sql->rowCount() == 1) {
        $account_data = $sql->fetch();

        if($location == "profile") {
            $size = 37;
        } else {
            $size = 15;
        }

        $sql = $database2->prepare('SELECT * FROM badge_pictures WHERE user_id = ? AND active = 1');
        $sql->execute(array(
            $username_id
        ));
        if($sql->rowCount() == 1) {
            $badges_pictures_data = $sql->fetch();
            $is_there_badge = 1;
        } else {
            $is_there_badge = 2;
        }

        if(isset($account_data['admin_access']) AND !empty($account_data['admin_access'])) {
            if($account_data['admin_access'] == 1) {
                $admin_type = "Créateur/Administrateur";
            } elseif($account_data['admin_access'] == 2) {
                $admin_type = "Modérateur";
            } elseif($account_data['admin_access'] == 3) {
                $admin_type = "Les Aventures de Roger";
            } elseif($account_data['admin_access'] == 4) {
                $admin_type = "Certifié";
            } elseif($account_data['admin_access'] == 5) {
                $admin_type = "Livreur/se de rares";
            } elseif($account_data['admin_access'] == 6) {
                $admin_type= "un vipe";
            }

            if($is_there_badge == 1) {
                echo '<img data-toggle="tooltip" title="' . $admin_type . '" draggable="false" src="../img/moviebook/badges/' . $badges_pictures_data['badge_path'] . '" width="' . $size . '" height="' . $size . '">';
            } else {
                echo '<img data-toggle="tooltip" title="' . $admin_type . '" draggable="false" src="../img/moviebook/certified_badge.png" width="' . $size . '" height="' . $size . '">';
            }

        } elseif($is_there_badge == 1) {
            echo '<img draggable="false" src="../img/moviebook/badges/' . $badges_pictures_data['badge_path'] . '" width="' . $size . '" height="' . $size . '">';
        }
    }
}

$achievements = array(
    array(
        "name" => "number_of_friends",
        "type" => "various",
        "francais" => "Nombre d'amis",
        "state_1" => 100,
        "state_2" => 1000,
        "state_3" => 2000,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "number_of_posts",
        "type" => "various",
        "francais" => "Publications",
        "state_1" => 20,
        "state_2" => 100,
        "state_3" => 200,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "number_of_posts_received",
        "type" => "various",
        "francais" => "Publications reçues",
        "state_1" => 10,
        "state_2" => 50,
        "state_3" => 100,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "number_of_replies",
        "type" => "various",
        "francais" => "Réponses publications",
        "state_1" => 100,
        "state_2" => 300,
        "state_3" => 500,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "contests_ended",
        "type" => "various",
        "francais" => "Concours terminés",
        "state_1" => 2,
        "state_2" => 4,
        "state_3" => 6,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "subjects_created",
        "type" => "various",
        "francais" => "Sujets créés",
        "state_1" => 5,
        "state_2" => 10,
        "state_3" => 20,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "comments_subjects",
        "type" => "various",
        "francais" => "Commentaires sujets",
        "state_1" => 100,
        "state_2" => 300,
        "state_3" => 500,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "account_created_since",
        "type" => "various",
        "francais" => "Compte créé depuis",
        "state_1" => 6,
        "state_2" => 12,
        "state_3" => 24,
        "state_1_points" => 5000,
        "state_2_points" => 15000,
        "state_3_points" => 30000
    ),
    array(
        "name" => "profile_picture_added",
        "type" => "once",
        "francais" => "Photo de profil ajoutée",
        "state_1" => 1,
        "state_1_points" => 500
    ),
    array(
        "name" => "banner_picture_added",
        "type" => "once",
        "francais" => "Bannière ajoutée",
        "state_1" => 1,
        "state_1_points" => 500
    ),
    array(
        "name" => "background_picture_added",
        "type" => "once",
        "francais" => "Photo de fond ajoutée",
        "state_1" => 1,
        "state_1_points" => 500
    ),
    array(
        "name" => "music_background_added",
        "type" => "once",
        "francais" => "Musique de fond ajoutée",
        "state_1" => 1,
        "state_1_points" => 500
    ),
    array(
        "name" => "msp_account_linked",
        "type" => "once",
        "francais" => "Compte MSP associé",
        "state_1" => 1,
        "state_1_points" => 500
    ),
    array(
        "name" => "informations_added",
        "type" => "once",
        "francais" => "Informations ajoutées",
        "state_1" => 1,
        "state_1_points" => 500
    )
);

function update_achievements($username_id)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $achievements;
        $sql = $database2->prepare('SELECT * FROM achievements WHERE username_id = ?');
        $sql->execute(array(
            $username_id
        ));
        if($sql->rowCount() != count($achievements)) {
            //Créer les succès manquants
            for($i = 0; $i < count($achievements); ++$i) {
                $sql = $database2->prepare('SELECT * FROM achievements WHERE username_id = ? AND name = ?');
                $sql->execute(array(
                    $username_id,
                    $achievements[$i]['name']
                ));
                if($sql->rowCount() != 1) {
                    $sql = $database2->prepare('INSERT INTO achievements(username_id, name) VALUES (?,?)');
                    $sql->execute(array(
                        $username_id,
                        $achievements[$i]['name']
                    ));
                }
            }
        }
    }
}

function account_exist($account_id)
{
    global $database;
    $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
    $sql->execute(array(
        $account_id
    ));
    if($sql->rowCount() == 1) {
        return true;
    } else {
        return false;
    }
}

function get_level($username_id)
{
    global $database2;
    //8000 points par niveau
    //450000 points au total pour tous les niveaux
    $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
    $sql->execute(array(
        $username_id
    ));
    if($sql->rowCount() == 1) {
        $profile_data = $sql->fetch();
        $level = $profile_data['level_points'] / 8000;
        return round($level, 0, PHP_ROUND_HALF_DOWN);
    }
}

function get_width_progress_bar_level($profile_id, $username_id, $level_point)
{
  if(get_level($profile_id) > 0) {
    $calculation = 8000 * get_level($username_id);
    $calculation = $calculation + 8000;
    $calculation = $calculation - $level_point;
    $calculation = 8000 - $calculation;
    return $width = round(($calculation / 8000) * 100,2);
   } else {
     return $width = 0;
   }
}

function extra_points($type, $user_id, $content_id)
{
    global $database2;
    global $database;

    if($type == "account_created_sponsorship") {
        $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
        $sql->execute(array(
            $user_id
        ));
        if($sql->rowCount() == 1) {
            $profile_data = $sql->fetch();
            $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                $profile_data['level_points'] + 1000,
                $user_id
            ));

            $sql = $database->prepare('SELECT * FROM account WHERE invited_by = ?');
            $sql->execute(array(
                $user_id
            ));
            $account_data = $sql->fetch();

            add_points_history($user_id, $account_data['id'], $user_id, "account_created_sponsorship", NULL, 1000, NULL);
            return;
        } else {
            return;
        }
    }

    if(is_connected() == true) {
        $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $profile_data = $sql->fetch();
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array(
                $user_id
            ));
            if($sql->rowCount() == 1) {
                $profile_data_user_two = $sql->fetch();

                if($type == "friend") {
                    $points_to_add = 20;
                } elseif($type == "post_wall") {
                    $points_to_add = 100;
                } elseif($type == "like") {
                    $points_to_add = 5;
                } elseif($type == "contest") {
                    $points_to_add = 500;
                } elseif($type == "subject") {
                    $points_to_add = 200;
                } elseif($type == "rare_completed") {
                    $points_to_add = 10;
                } elseif($type == "subject_deleted") {
                    $points_to_remove = -200;
                } elseif($type == "post_deleted") {
                    $points_to_remove = -100;
                }

                if($type == "friend") {

                    $sql = $database2->prepare('SELECT * FROM points_history WHERE type = "new_friend" AND (from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $user_id,
                        $user_id,
                        $_SESSION['id']
                    ));
                    if($sql->rowCount() == 0) {
                        $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                            $profile_data['level_points'] + $points_to_add,
                            $_SESSION['id']
                        ));
                        $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                            $profile_data_user_two['level_points'] + $points_to_add,
                            $user_id
                        ));
                        add_points_history($user_id, $_SESSION['id'], $user_id, "new_friend", NULL, $points_to_add, $content_id, NULL);
                        add_points_history($_SESSION['id'], $_SESSION['id'], $user_id, "new_friend", NULL, $points_to_add, $content_id, NULL);
                    }

                } elseif($type == "post_wall") {

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data_user_two['level_points'] + $points_to_add,
                        $user_id
                    ));
                    add_points_history($user_id, $_SESSION['id'], $user_id, "received_post_wall", NULL, $points_to_add, $content_id);

                } elseif($type == "like") {

                    $sql = $database2->prepare('SELECT * FROM points_history WHERE type = "new_like" AND content_id = ?');
                    $sql->execute(array(
                        $content_id
                    ));
                    if($sql->rowCount() == 0) {
                        $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                            $profile_data_user_two['level_points'] + $points_to_add,
                            $user_id
                        ));
                        add_points_history($user_id, $_SESSION['id'], $user_id, "new_like", NULL, $points_to_add, $content_id);
                    }

                } elseif($type == "contest" OR $type == "subject") {

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data['level_points'] + $points_to_add,
                        $_SESSION['id']
                    ));
                    if($type == "contest") {
                        add_points_history($_SESSION['id'], $_SESSION['id'], $_SESSION['id'], "contest_finished", NULL, $points_to_add, $content_id);
                    } elseif($type == "subject") {
                        add_points_history($_SESSION['id'], $_SESSION['id'], $_SESSION['id'], "subject_created", NULL, $points_to_add, $content_id);
                    }

                } elseif($type == "rare_completed") {

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data['level_points'] + $points_to_add,
                        $_SESSION['id']
                    ));
                    add_points_history($_SESSION['id'], $_SESSION['id'], $_SESSION['id'], "rare_completed", NULL, $points_to_add, $content_id);

                } elseif($type == "subject_deleted") {

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data['level_points'] + $points_to_remove,
                        $_SESSION['id']
                    ));
                    add_points_history($_SESSION['id'], $_SESSION['id'], $_SESSION['id'], "subject_deleted", NULL, $points_to_remove, $content_id);

                } elseif($type == "post_deleted") {

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data_user_two['level_points'] + $points_to_remove,
                        $user_id
                    ));
                    add_points_history($user_id, $_SESSION['id'], $user_id, "post_deleted", NULL, $points_to_remove, $content_id);

                }
            }
        }
    }
}

function add_points_history($username_id, $from_id, $to_id, $type, $content, $points, $content_id)
{
    if(is_profile_exist($username_id) == true) {
        global $database2;
        $sql = $database2->prepare('INSERT INTO points_history(username_id, from_id, to_id, type, content, points, content_id) VALUES (?,?,?,?,?,?,?)');
        $sql->execute(array(
            $username_id,
            $from_id,
            $to_id,
            $type,
            $content,
            $points,
            $content_id
        ));
    }
}

function delete_content($content_id, $show_response, $admin)
{
    global $database2;

    $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
    $sql->execute(array(
        $content_id
    ));
    if($sql->rowCount() == 1) {
        $post_wall_data = $sql->fetch();

        /////////////SUPPRIMER TOUT LE CONTENU DE CETTE PUBLICATION, LIKES DISLIKES ET REPONSES
        $sql = $database2->prepare('DELETE FROM likes WHERE post_id = ?');
        $sql->execute(array(
            $content_id
        ));
        $sql = $database2->prepare('DELETE FROM dislikes WHERE post_id = ?');
        $sql->execute(array(
            $content_id
        ));
        $sql = $database2->prepare('UPDATE reply_post_wall SET deleted = 1 WHERE post_id = ? AND deleted = 0');
        $sql->execute(array(
            $content_id
        ));
        /////////////

        /////////////VERIFIER S'IL Y A UNE IMAGE DANS CETTE PUBLICATION ET LA SUPPRIMEE
        $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND is_picture = 1 AND picture_path IS NOT NULL');
        $sql->execute(array(
            $content_id
        ));
        if($sql->rowCount() == 1) {
            $path_min = $post_wall_data['picture_path'];
            if(is_file("img/moviebook/pictures/" . $path_min)) {
                unlink("img/moviebook/pictures/" . $path_min);
            }
        }
        /////////////

        /////////////SUPPRIMER TOUTES LES NOTIFICATIONS RELATIVES A CETTE PUBLICATION
        $sql = $database2->prepare('DELETE FROM notifications WHERE type = ? AND content_id = ? OR type = ? AND content_id = ? OR type = ? AND content_id = ?');
        $sql->execute(array(
            "new_post_on_wall",
            $content_id,
            "new_reply_on_post",
            $content_id,
            "new_reply_on_post_replied",
            $content_id
        ));
        /////////////


        /////////////ENLEVER LES POINTS SI LA PUBLICATION DATE DE MOINS DE 3 MOIS
        if(strtotime($post_wall_data['post_date']) > strtotime("-3 months")) {
            if($admin == false) {
                if($post_wall_data['wall_id'] != $_SESSION['id'] AND $post_wall_data['posted_by'] == $_SESSION['id']) {
                    extra_points("post_deleted", $post_wall_data['wall_id'], $content_id);
                } elseif($post_wall_data['wall_id'] == $_SESSION['id'] AND $post_wall_data['posted_by'] != $_SESSION['id']) {
                    extra_points("post_deleted", $_SESSION['id'], $content_id);
                }
            } elseif($admin == true) {
                if($post_wall_data['posted_by'] != $post_wall_data['wall_id']) {
                    extra_points("post_deleted", $post_wall_data['wall_id'], $content_id);
                }
            }
        }
        /////////////

        $sql = $database2->prepare('UPDATE post_wall SET deleted = 1 WHERE id = ?');
        $sql->execute(array(
            $content_id
        ));

        if($show_response == true) {
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
        }
    }
}

$rares_list = array(
    array(
        "id" => "1",
        "name" => "Hair Ball",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "2",
        "name" => "Faux Wear",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "3",
        "name" => "Double The Fun",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "4",
        "name" => "Pearly Bun",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "5",
        "name" => "Best of Basics",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "6",
        "name" => "Striped Candy",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "7",
        "name" => "Relaxed 'n Sweet",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "8",
        "name" => "Cute Crop Top",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "9",
        "name" => "Party Shimmer",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "10",
        "name" => "Party Glam",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "11",
        "name" => "Sugar Coated",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "12",
        "name" => "Sleek Party",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "13",
        "name" => "Sweet Love",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "14",
        "name" => "Frozen Cheeks",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "15",
        "name" => "Winter Cheeks",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "16",
        "name" => "Happy New Year!",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "17",
        "name" => "Magic Winter",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "18",
        "name" => "All The New",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "19",
        "name" => "In a Hurry",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "20",
        "name" => "Steady Going",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "21",
        "name" => "What Year Is it?",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "22",
        "name" => "Wonderland Girl",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "23",
        "name" => "Pink Wonderland",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "24",
        "name" => "Party Doo",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "25",
        "name" => "Shine Like New",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "26",
        "name" => "Diamondzzzz!!!",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "27",
        "name" => "Glitter Girl",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "28",
        "name" => "Best Dressed",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "29",
        "name" => "Spring Bouquet",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "30",
        "name" => "Bunny-Fly",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "31",
        "name" => "Cute Easter Rare Hair",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "32",
        "name" => "Christmas Hair",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "33",
        "name" => "Soft Party Curls",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "34",
        "name" => "Christmas Skirt",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "35",
        "name" => "Alice Dress",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "36",
        "name" => "Ornate Ribbon",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "37",
        "name" => "Cat Ears",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "38",
        "name" => "Loose Scarf",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "39",
        "name" => "Flower Tiara",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "40",
        "name" => "Let the Good Time Roll",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "41",
        "name" => "Wicked Tails",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "42",
        "name" => "Wet Tresses",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "43",
        "name" => "New Years Tresses",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "44",
        "name" => "Wonder Fair",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "45",
        "name" => "Messy Bun",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "46",
        "name" => "Salty Tresses",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "47",
        "name" => "Ice Queen",
        "vip" => 0,
        "available" => 0
    ),
    array(
        "id" => "48",
        "name" => "Center Part",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "49",
        "name" => "Crop With Sweater",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "50",
        "name" => "Shiny Pants",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "51",
        "name" => "Witch Hat",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "52",
        "name" => "Witch Dress",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "53",
        "name" => "Poweer Pufftop",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "54",
        "name" => "Lips Top",
        "vip" => 1,
        "available" => 0
    ),
    array(
        "id" => "55",
        "name" => "New Year Starlette",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "56",
        "name" => "Bunny Fly",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "57",
        "name" => "Bunny Fly",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "58",
        "name" => "All Seeing",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "59",
        "name" => "Magic Hairdo",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "60",
        "name" => "Tough Curls",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "61",
        "name" => "Sweet Girl",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "62",
        "name" => "Shippity Ship Ship",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "63",
        "name" => "Heart Breaker",
        "vip" => 0,
        "available" => 1
    ),
    array(
        "id" => "64",
        "name" => "Curly Doll",
        "vip" => 0,
        "available" => 1
    )
);

function convert_rare_id($rare_id)
{
    global $rares_list;
    if(in_array($rare_id, array_column($rares_list, 'id'))) {
        foreach($rares_list as $index => $rare) {
            if($rare['id'] == $rare_id) {
                return $rare['name'];
            }
        }
    }
}

function delete_contest_1($contest_id, $contest_id_2, $type)
{
    global $database;
    global $database2;

    $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND contest_id = ? AND deleted = 0');
    $sql->execute(array(
        $contest_id,
        $contest_id_2
    ));
    if($sql->rowCount() == 1) {

        $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ? AND notification_delete_contest = 1');
        $sql->execute(array(
            $contest_id
        ));

        while($contest_participants_data = $sql->fetch()) {
            if($type == "contest_deleted_by_author") {
                insert_notification($contest_participants_data['username_id'], $_SESSION['id'], "contest_deleted_by_author", "");
            } elseif($type == "contest_deleted_by_admin") {
                insert_notification($contest_participants_data['username_id'], $_SESSION['id'], "contest_deleted_by_admin", "");
            } elseif($type == "contest_time_out") {
                insert_notification($contest_participants_data['username_id'], 1, "contest_time_out", "");
            } elseif($type == "contest_ended") {
                insert_notification($contest_participants_data['username_id'], $_SESSION['id'], "contest_ended", $contest_id_2);
            }
        }

        $sql = $database->prepare('UPDATE contest SET deleted = 1 WHERE id = ?')->execute(array(
            $contest_id
        ));

        $sql = $database->prepare('DELETE FROM contest_participants WHERE contest_id = ?');
        $sql->execute(array(
            $contest_id
        ));

        $sql = $database->prepare('DELETE FROM contest_comments WHERE contest_id = ?');
        $sql->execute(array(
            $contest_id
        ));

        $sql = $database->prepare('DELETE FROM contest_prices WHERE contest_id = ?');
        $sql->execute(array(
            $contest_id
        ));

        $sql = $database->prepare('DELETE FROM contest_prices WHERE contest_id = ?');
        $sql->execute(array(
            $contest_id
        ));

        $sql = $database2->prepare('DELETE FROM notifications WHERE type = ? AND content_id = ? OR type = ? AND content_id = ? OR type = ? AND content_id = ? OR type = ? AND content_id = ? OR type = ? AND content_id = ?');
        $sql->execute(array(
            "new_participant_contest",
            $contest_id_2,
            "new_contest_comment",
            $contest_id_2,
            "participant_deleted_by_author",
            $contest_id_2,
            "new_contest_comment_by_author",
            $contest_id_2,
            "new_contest_friend",
            $contest_id_2
        ));

    }
}

function update_subject_last_comment_date($subject_id)
{
    global $database;
    $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND deleted = 0 ORDER BY id DESC LIMIT 1');
    $sql->execute(array(
        $subject_id
    ));
    if($sql->rowCount() == 1) {
        $last_comment_data = $sql->fetch();
        $last_comment_date = $last_comment_data['comment_date'];
    } else {
        $sql = $database->prepare('SELECT * FROM subject WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $subject_id
        ));
        if($sql->rowCount() == 1) {
            $subject_data = $sql->fetch();
            $last_comment_date = $subject_data['creation_date'];
        }
    }

    $sql = $database->prepare('UPDATE subject SET last_comment_date = ? WHERE id = ? AND deleted = 0')->execute(array(
        $last_comment_date,
        $subject_id
    ));
}

?>
