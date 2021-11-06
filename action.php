<?php
include('includes/config.php');

if($maintenance_mode == false) {

    if(isset($_POST['action']) AND !empty($_POST['action'])) {
        if($_POST['action'] == "login" AND isset($_POST['username']) AND !empty($_POST['username']) AND isset($_POST['password']) AND !empty($_POST['password']) AND isset($_POST['remember']) AND !empty($_POST['remember'])) {
            login(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['remember']));
        } elseif($_POST['action'] == "register" AND isset($_POST['username']) AND !empty($_POST['username']) AND isset($_POST['password']) AND !empty($_POST['password']) AND isset($_POST['email_address']) AND !empty($_POST['email_address']) AND isset($_POST['recaptcha_reponse']) AND !empty($_POST['recaptcha_reponse'])) {
            register(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['email_address']), htmlspecialchars($_POST['user_id_sponsorship']), htmlspecialchars($_POST['sponsorship_key']), htmlspecialchars($_POST['recaptcha_reponse']));
        } elseif($_POST['action'] == "update_password" AND isset($_POST['old']) AND !empty($_POST['old']) AND isset($_POST['password']) AND !empty($_POST['password']) AND isset($_POST['confirm']) AND !empty($_POST['confirm'])) {
            update_password(htmlspecialchars($_POST['old']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['confirm']));
        } elseif($_POST['action'] == "lost_password" AND isset($_POST['username']) AND !empty($_POST['username']) AND isset($_POST['email_address']) AND !empty($_POST['email_address'])) {
            lost_password(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['email_address']));
        } elseif($_POST['action'] == "reset_password" AND isset($_POST['password']) AND !empty($_POST['password']) AND isset($_POST['confirm']) AND !empty($_POST['confirm']) AND isset($_POST['id']) AND !empty($_POST['id']) AND isset($_POST['t']) AND !empty($_POST['t'])) {
            reset_password(htmlspecialchars($_POST['password']), htmlspecialchars($_POST['confirm']), htmlspecialchars($_POST['id']), htmlspecialchars($_POST['t']));
        } elseif($_POST['action'] == "lost_confirmation" AND isset($_POST['username']) AND !empty($_POST['username']) AND isset($_POST['email_address']) AND !empty($_POST['email_address'])) {
            lost_confirmation(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['email_address']));
        } elseif($_POST['action'] == "post_comment_news" AND isset($_POST['comment']) AND !empty($_POST['comment']) AND isset($_POST['news_id']) AND !empty($_POST['news_id'])) {
            post_comment_news(htmlspecialchars($_POST['comment']), htmlspecialchars($_POST['news_id']));
        } elseif($_POST['action'] == "delete_comment_news" AND isset($_POST['comment_id']) AND !empty($_POST['comment_id'])) {
            delete_comment_news(htmlspecialchars($_POST['comment_id']));
        } elseif($_POST['action'] == "mb_create_account" AND isset($_POST['color']) AND !empty($_POST['color']) AND isset($_POST['sexe']) AND !empty($_POST['sexe'])) {
            mb_create_account(htmlspecialchars($_POST['color']), htmlspecialchars($_POST['sexe']));
        } elseif($_POST['action'] == "mb_link_msp_account" AND isset($_POST['msp_username']) AND !empty($_POST['msp_username']) AND isset($_POST['msp_level']) AND !empty($_POST['msp_level']) AND isset($_POST['is_vip']) AND !empty($_POST['is_vip'])) {
            mb_link_msp_account(htmlspecialchars($_POST['msp_username']), htmlspecialchars($_POST['msp_level']), htmlspecialchars($_POST['is_vip']));
        } elseif($_POST['action'] == "dislink_msp_account") {
            dislink_msp_account();
        } elseif($_POST['action'] == "mb_update_info") {
            mb_update_info(htmlspecialchars($_POST['name']), htmlspecialchars($_POST['date_of_birth']), htmlspecialchars($_POST['description']));
        } elseif($_POST['action'] == "mb_post_wall" AND isset($_POST['content']) AND !empty($_POST['content']) AND isset($_POST['wall_id']) AND !empty($_POST['wall_id'])) {
            mb_post_wall(htmlspecialchars($_POST['content']), htmlspecialchars($_POST['wall_id']));
        } elseif($_POST['action'] == "delete_post_wall" AND isset($_POST['content_id']) AND !empty($_POST['content_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            delete_post_wall(htmlspecialchars($_POST['content_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "likes_dislikes_post_wall" AND isset($_POST['post_id']) AND !empty($_POST['post_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            likes_dislikes_post_wall(htmlspecialchars($_POST['post_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "mb_request_friend" AND isset($_POST['user_id']) AND !empty($_POST['user_id'])) {
            mb_request_friend(htmlspecialchars($_POST['user_id']));
        } elseif($_POST['action'] == "mb_cancel_request_friend" AND isset($_POST['user_id']) AND !empty($_POST['user_id'])) {
            mb_cancel_request_friend(htmlspecialchars($_POST['user_id']));
        } elseif($_POST['action'] == "mb_accept_decline_friend_request" AND isset($_POST['user_id']) AND !empty($_POST['user_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            mb_accept_decline_friend_request(htmlspecialchars($_POST['user_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "mb_delete_friend" AND isset($_POST['user_id']) AND !empty($_POST['user_id'])) {
            mb_delete_friend(htmlspecialchars($_POST['user_id']));
        } elseif($_POST['action'] == "mb_report" AND isset($_POST['content_id']) AND !empty($_POST['content_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            mb_report(htmlspecialchars($_POST['content_id']), htmlspecialchars($_POST['type']), htmlspecialchars($_POST['info']));
        } elseif($_POST['action'] == "mb_post_wall_send_reply" AND isset($_POST['post_id']) AND !empty($_POST['post_id']) AND isset($_POST['content']) AND !empty($_POST['content'])) {
            mb_post_wall_send_reply(htmlspecialchars($_POST['post_id']), htmlspecialchars($_POST['content']));
        } elseif($_POST['action'] == "mb_pin_post" AND isset($_POST['post_id']) AND !empty($_POST['post_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            mb_pin_post(htmlspecialchars($_POST['post_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "delete_notification" AND isset($_POST['notif_id']) AND !empty($_POST['notif_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            delete_notification(htmlspecialchars($_POST['notif_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "join_leave_contest" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            join_leave_contest(htmlspecialchars($_POST['contest_id']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "post_comment_contest" AND isset($_POST['comment']) AND !empty($_POST['comment']) AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            post_comment_contest(htmlspecialchars($_POST['comment']), htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "delete_comment_contest" AND isset($_POST['comment_id']) AND !empty($_POST['comment_id']) AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            delete_comment_contest(htmlspecialchars($_POST['comment_id']), htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "delete_contest" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            delete_contest(htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "remove_participant" AND isset($_POST['participant_id']) AND !empty($_POST['participant_id']) AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            remove_participant(htmlspecialchars($_POST['participant_id']), htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "update_participant_settings" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id']) AND isset($_POST['notification_comment']) AND !empty($_POST['notification_comment']) AND isset($_POST['notification_delete_contest']) AND !empty($_POST['notification_delete_contest']) AND isset($_POST['notification_end_contest']) AND !empty($_POST['notification_end_contest'])) {
            update_participant_settings(htmlspecialchars($_POST['contest_id']), htmlspecialchars($_POST['notification_comment']), htmlspecialchars($_POST['notification_delete_contest']), htmlspecialchars($_POST['notification_end_contest']));
        } elseif($_POST['action'] == "finish_contest" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id']) AND isset($_POST['final_text']) AND !empty($_POST['final_text'])) {
            finish_contest(htmlspecialchars($_POST['contest_id']), htmlspecialchars($_POST['final_text']));
        } elseif($_POST['action'] == "contest_report" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            contest_report(htmlspecialchars($_POST['contest_id']), htmlspecialchars($_POST['info']));
        } elseif($_POST['action'] == "admin_delete_contest" AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            admin_delete_contest(htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "mb_save_edits_msp_account" AND isset($_POST['msp_level']) AND !empty($_POST['msp_level']) AND isset($_POST['is_vip']) AND !empty($_POST['is_vip'])) {
            mb_save_edits_msp_account(htmlspecialchars($_POST['msp_level']), htmlspecialchars($_POST['is_vip']));
        } elseif($_POST['action'] == "notification_view_update") {
            notification_view_update();
        } elseif($_POST['action'] == "notification_contest" AND isset($_POST['type']) AND !empty($_POST['type']) AND isset($_POST['contest_id']) AND !empty($_POST['contest_id'])) {
            notification_contest(htmlspecialchars($_POST['type']), htmlspecialchars($_POST['contest_id']));
        } elseif($_POST['action'] == "update_username" AND isset($_POST['username']) AND !empty($_POST['username'])) {
            update_username(htmlspecialchars($_POST['username']));
        } elseif($_POST['action'] == "mb_update_post_wall_notification_settings" AND isset($_POST['post_id']) AND !empty($_POST['post_id'])) {
            mb_update_post_wall_notification_settings(htmlspecialchars($_POST['post_id']));
        } elseif($_POST['action'] == "forum_create_subject" AND isset($_POST['title']) AND !empty($_POST['title']) AND isset($_POST['description']) AND !empty($_POST['description']) AND isset($_POST['notification_new_subject_friend']) AND !empty($_POST['notification_new_subject_friend']) AND isset($_POST['notification_new_comment']) AND !empty($_POST['notification_new_comment']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            forum_create_subject(htmlspecialchars($_POST['title']), htmlspecialchars($_POST['description']), htmlspecialchars($_POST['notification_new_subject_friend']), htmlspecialchars($_POST['notification_new_comment']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "forum_delete_my_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id'])) {
            forum_delete_my_subject(htmlspecialchars($_POST['subject_id']));
        } elseif($_POST['action'] == "update_settings_my_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id']) AND isset($_POST['title']) AND !empty($_POST['title']) AND isset($_POST['description']) AND !empty($_POST['description']) AND isset($_POST['notification_new_comment']) AND !empty($_POST['notification_new_comment'])) {
            update_settings_my_subject(htmlspecialchars($_POST['subject_id']), htmlspecialchars($_POST['title']), htmlspecialchars($_POST['description']), htmlspecialchars($_POST['notification_new_comment']));
        } elseif($_POST['action'] == "forum_send_comment_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id']) AND isset($_POST['content']) AND !empty($_POST['content'])) {
            forum_send_comment_subject(htmlspecialchars($_POST['subject_id']), htmlspecialchars($_POST['content']));
        } elseif($_POST['action'] == "forum_delete_comment_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id']) AND isset($_POST['comment_id']) AND !empty($_POST['comment_id'])) {
            forum_delete_comment_subject(htmlspecialchars($_POST['subject_id']), htmlspecialchars($_POST['comment_id']));
        } elseif($_POST['action'] == "forum_update_notification_new_comments_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id'])) {
            forum_update_notification_new_comments_subject(htmlspecialchars($_POST['subject_id']));
        } elseif($_POST['action'] == "receive_award_achievement" AND isset($_POST['achievement_name']) AND !empty($_POST['achievement_name'])) {
            receive_award_achievement(htmlspecialchars($_POST['achievement_name']));
        } elseif($_POST['action'] == "create_sponsorship_link") {
            create_sponsorship_link();
        } elseif($_POST['action'] == "delete_sponsorship_link") {
            delete_sponsorship_link();
        } elseif($_POST['action'] == "admin_confirm_msp_account" AND isset($_POST['user_id']) AND !empty($_POST['user_id'])) {
            admin_confirm_msp_account(htmlspecialchars($_POST['user_id']));
        } elseif($_POST['action'] == "check_password_event_halloween" AND isset($_POST['string']) AND !empty($_POST['string']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            check_password_event_halloween(htmlspecialchars($_POST['string']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "admin_update_points" AND isset($_POST['user_id']) AND !empty($_POST['user_id']) AND isset($_POST['points']) AND !empty($_POST['points']) AND isset($_POST['reason']) AND !empty($_POST['reason'])) {
            admin_update_points(htmlspecialchars($_POST['user_id']), htmlspecialchars($_POST['points']), htmlspecialchars($_POST['reason']));
        } elseif($_POST['action'] == "admin_send_notification" AND isset($_POST['content']) AND !empty($_POST['content']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            admin_send_notification(htmlspecialchars($_POST['user_id']), htmlspecialchars($_POST['content']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "update_website_theme" AND isset($_POST['type']) AND !empty($_POST['type'])) {
            update_website_theme(htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "admin_delete_post" AND isset($_POST['post_id']) AND !empty($_POST['post_id'])) {
            admin_delete_post(htmlspecialchars($_POST['post_id']));
        } elseif($_POST['action'] == "admin_ban_account" AND isset($_POST['user_id']) AND !empty($_POST['user_id']) AND isset($_POST['reason']) AND !empty($_POST['reason']) AND isset($_POST['number_of_weeks']) AND !empty($_POST['number_of_weeks']) AND isset($_POST['type']) AND !empty($_POST['type'])) {
            admin_ban_account(htmlspecialchars($_POST['user_id']), htmlspecialchars($_POST['reason']), htmlspecialchars($_POST['number_of_weeks']), htmlspecialchars($_POST['type']));
        } elseif($_POST['action'] == "pin_my_subject" AND isset($_POST['subject_id']) AND !empty($_POST['subject_id'])) {
            pin_my_subject(htmlspecialchars($_POST['subject_id']));
        } elseif($_POST['action'] == "update_badge" AND isset($_POST['badge_id']) AND !empty($_POST['badge_id'])) {
            update_badge(htmlspecialchars($_POST['badge_id']));
        } elseif($_POST['action'] == "unactive_badge") {
            unactive_badge();
        } elseif($_POST['action'] == "rare_send_order" AND isset($_POST['msp_username']) AND !empty($_POST['msp_username']) AND isset($_POST['rare_id']) AND !empty($_POST['rare_id'])) {
            rare_send_order(htmlspecialchars($_POST['msp_username']), htmlspecialchars($_POST['rare_id']));
        } elseif($_POST['action'] == "rare_delete_order") {
            rare_delete_order();
        } elseif($_POST['action'] == "rare_complete_order" AND isset($_POST['order_id']) AND !empty($_POST['order_id'])) {
            rare_complete_order(htmlspecialchars($_POST['order_id']));
        } else {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

}

function login($username, $password, $remember)
{
    if(is_connected() == false) {
        global $login_enabled;
        if($login_enabled == true) {
            global $success_sign;
            global $database;
            $sql = $database->prepare('SELECT * FROM account WHERE username = ?');
            $sql->execute(array(
                $username
            ));
            $account_data = $sql->fetch();
            if($sql->rowCount() == 1) {
                if(password_verify($password, $account_data['password'])) {
                    $activated = $account_data['activated_at'];
                    $banned = $account_data['banned'];
                    if($activated != NULL) {
                        if($banned == 0) {

                            if($remember == "true") {
                                setcookie('auth', $account_data['id'] . '//' . sha1($account_data['username'] . $account_data['password']), time() + 3600 * 24 * 7, null, null, false, true);
                            }
                            $data = array(
                                'status' => 'SUCCESS'
                            );
                            echo json_encode($data);
                            $_SESSION['id'] = $account_data['id'];
                            $_SESSION['flash']['success'] = $success_sign . "Content de te revoir, <strong>" . $account_data['username'] . "</strong> !";

                        } else {
                            $sql = $database->prepare('SELECT * FROM ban_history WHERE user_id = ? ORDER BY date_of_ban DESC');
                            $sql->execute(array(
                                $account_data['id']
                            ));
                            if($sql->rowCount() > 0) {
                                $ban_history_data = $sql->fetch();
                                if(time() > strtotime($ban_history_data['date_of_deban'])) {
                                    $sql = $database->prepare('UPDATE account SET banned = 0 WHERE id = ? AND banned = 1')->execute(array(
                                        $account_data['id']
                                    ));

                                    if($remember == "true") {
                                        setcookie('auth', $account_data['id'] . '//' . sha1($account_data['username'] . $account_data['password']), time() + 3600 * 24 * 7, null, null, false, true);
                                    }
                                    $data = array(
                                        'status' => 'SUCCESS'
                                    );
                                    echo json_encode($data);
                                    $_SESSION['id'] = $account_data['id'];
                                    $_SESSION['flash']['success'] = $success_sign . "Te voilà débanni, <strong>" . $account_data['username'] . "</strong> !";

                                } else {
                                    $time_left = strtotime($ban_history_data['date_of_deban']) - time();
                                    $time_left = round($time_left / (60 * 60 * 24));

                                    $data = array(
                                        'status' => 'USER_BANNED',
                                        'reason' => $ban_history_data['reason'],
                                        'time_left' => $time_left
                                    );
                                    echo json_encode($data);
                                }
                            }
                        }
                    } else {
                        $data = array(
                            'status' => 'USER_NOT_ACTIVATED'
                        );
                        echo json_encode($data);
                    }
                } else {
                    $data = array(
                        'status' => 'INCORRECT_PASSWORD'
                    );
                    echo json_encode($data);
                }
            } else {
                $data = array(
                    'status' => 'USER_NOT_EXIST'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'LOGIN_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function register($username, $password, $email_address, $user_id_sponsorship, $sponsorship_key, $recaptcha_reponse)
{
    if(is_connected() == false) {
        global $database;
        global $create_account_enabled;
        global $secret_key;
        if($create_account_enabled == true) {
            $username = trim($username);
            $email_address = trim($email_address);
            if(!is_numeric($username)) {
                if(preg_match('/[A-Za-z]/', $password) AND preg_match('/[0-9]/', $password)) {
                    if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                        $username_lenght = mb_strlen($username, 'UTF-8');
                        $password_lenght = mb_strlen($password, 'UTF-8');
                        $email_addresss_lenght = mb_strlen($email_address, 'UTF-8');
                        if($username_lenght <= 15 AND $password_lenght <= 30 AND $email_addresss_lenght <= 30) {
                            if($username_lenght >= 4 AND $password_lenght >= 5) {
                                $password = password_hash($password, PASSWORD_BCRYPT);
                                $confirm_account_token = str_random(60);
                                $sql = $database->prepare('SELECT * FROM account WHERE username = ?');
                                $sql->execute(array(
                                    $username
                                ));
                                if($sql->rowCount() == 0) {
                                    require('recaptcha/autoload.php');
                                    $recaptcha = new \ReCaptcha\ReCaptcha($secret_key);
                                    $resp = $recaptcha->verify($recaptcha_reponse);
                                    if($resp->isSuccess()) {
                                        $sql = $database->prepare('INSERT INTO account(username, password, email_address, confirm_account_token) VALUES (?,?,?,?)');
                                        $sql->execute(array(
                                            $username,
                                            $password,
                                            $email_address,
                                            $confirm_account_token
                                        ));
                                        $user_id = $database->lastInsertId();

                                        if(isset($user_id_sponsorship) AND !empty($user_id_sponsorship) AND isset($sponsorship_key) AND !empty($sponsorship_key)) {
                                            $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND sponsorship_key = ?');
                                            $sql->execute(array(
                                                $user_id_sponsorship,
                                                $sponsorship_key
                                            ));
                                            if($sql->rowCount() == 1) {
                                                $sql = $database->prepare('UPDATE account SET invited_by = ? WHERE id = ?')->execute(array(
                                                    $user_id_sponsorship,
                                                    $user_id
                                                ));
                                            }
                                        }

                                        send_confirmation($username, $user_id, $confirm_account_token, $email_address);
                                    }
                                } else {
                                    $data = array(
                                        'status' => 'USER_ALREADY_TAKEN'
                                    );
                                    echo json_encode($data);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'CREATE_ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function send_confirmation($username, $user_id, $confirm_account_token, $email_address)
{
    global $success_sign;
    global $actual_link;
    $body = '
       <html>
       <body>
       <div align="center">
       <img src="https://embed.gyazo.com/8728d6b6a8e0f186fde54b3edb6acb22.jpg">
       <h2>Bienvenu(e), ' . $username . ' !</h2>
       <a href="' . $actual_link . '/confirm?id=' . $user_id . '&t=' . $confirm_account_token . '">Clique ici</a> pour activer ton compte.<br><br>
       <strong>Attention !</strong> Ton compte sera automatiquement supprimé si tu ne l\'actives pas.
       </div>
       </body>
       </html>';
    $subject = "Boubi MSP - Confirmation du compte";

    send_email($email_address, $subject, $body);

    $data = array(
        'status' => 'SUCCESS'
    );
    echo json_encode($data);
}

function update_password($old, $password, $confirm)
{
    if(is_connected() == true) {
        global $account_enabled;
        if($account_enabled == true) {
            global $database;
            if(preg_match('/[A-Za-z]/', $password) AND preg_match('/[0-9]/', $password)) {
                $password_lenght = mb_strlen($password, 'UTF-8');
                if($password_lenght <= 30) {
                    if($password_lenght >= 5) {
                        if($password == $confirm) {
                            $new_password = password_hash($password, PASSWORD_BCRYPT);
                            $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
                            $sql->execute(array(
                                $_SESSION['id']
                            ));
                            $account_data = $sql->fetch();
                            if(password_verify($old, $account_data['password'])) {
                                $sql = $database->prepare('UPDATE account SET password = ? WHERE id = ?')->execute(array(
                                    $new_password,
                                    $_SESSION['id']
                                ));
                                $data = array(
                                    'status' => 'SUCCESS'
                                );
                                echo json_encode($data);
                            } else {
                                $data = array(
                                    'status' => 'WRONG_PASSWORD'
                                );
                                echo json_encode($data);
                            }
                        }
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function lost_password($username, $email_address)
{
    if(is_connected() == false) {
        global $account_enabled;
        if($account_enabled == true) {
            global $database;
            if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                $sql = $database->prepare('SELECT * FROM account WHERE email_address = ? AND username = ?');
                $sql->execute(array(
                    $email_address,
                    $username
                ));
                if($sql->rowCount() == 1) {
                    $account_data = $sql->fetch();
                    $user_id = $account_data['id'];
                    if($account_data['reset_at'] != NULL) {
                        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND reset_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
                        $sql->execute(array(
                            $account_data['id']
                        ));
                        $reset_at = $sql->rowCount();
                        if($reset_at != 1) {
                            $data = array(
                                'status' => 'NEED_TO_WAIT'
                            );
                            echo json_encode($data);
                        } else {
                            lost_password_email($user_id, $username, $email_address);
                        }
                    } else {
                        lost_password_email($user_id, $username, $email_address);
                    }
                } else {
                    $data = array(
                        'status' => 'USER_EMAIL_NOT_MATCH'
                    );
                    echo json_encode($data);
                }
            }
        } else {
            $data = array(
                'status' => 'ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function lost_password_email($user_id, $username, $email_address)
{
    if(is_connected() == false) {
        global $success_sign;
        global $actual_link;
        global $database;
        $reset_password_token = str_random(60);
        $sql = $database->prepare('UPDATE account SET reset_password_token = ?, reset_at = NOW() WHERE id = ?')->execute(array(
            $reset_password_token,
            $user_id
        ));

        $body = '
           <html>
           <body>
           <div align="center">
           <img src="https://embed.gyazo.com/8728d6b6a8e0f186fde54b3edb6acb22.jpg">
           <h2>Salut, ' . $username . ' !</h2> <a href="' . $actual_link .'/reset?id=' . $user_id . '&t=' . $reset_password_token . '">Clique ici</a> pour réinitialiser ton mot de passe.<br><br>
           Dépêche-toi, ce lien expirera dans 30 minutes !
           </div>
           </body>
           </html>';
        $subject = "Boubi MSP - Mot de passe oublié";

        send_email($email_address, $subject, $body);

        $data = array(
            'status' => 'SUCCESS'
        );
        echo json_encode($data);
        $_SESSION['flash']['success'] = $success_sign . "Un email a été envoyé à l\'adresse <strong>" . $email_address . "</strong>.";
    }
}

function reset_password($password, $confirm, $id, $t)
{
    if(is_connected() == false) {
        global $account_enabled;
        if($account_enabled == true) {
            global $success_sign;
            global $database;
            if(preg_match('/[A-Za-z]/', $password) AND preg_match('/[0-9]/', $password)) {
                $password_lenght = mb_strlen($password, 'UTF-8');
                if($password_lenght <= 30) {
                    if($password_lenght >= 5) {
                        if($password == $confirm) {
                            $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND reset_password_token = ? AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
                            $sql->execute(array(
                                $id,
                                $t
                            ));
                            $account_data = $sql->fetch();
                            if($account_data) {
                                $password = password_hash($password, PASSWORD_BCRYPT);
                                $sql = $database->prepare('UPDATE account SET password = ?, reset_password_token = NULL, reset_at = NULL WHERE id = ?')->execute(array(
                                    $password,
                                    $id
                                ));
                                $data = array(
                                    'status' => 'SUCCESS',
                                    'username' => $account_data['username']
                                );
                                echo json_encode($data);
                                $_SESSION['flash']['success'] = $success_sign . "Le mot de passe du compte <strong>" . $account_data['username'] . "</strong> a bien été changé.";
                            } else {
                                $data = array(
                                    'status' => 'TOKEN_INVALID'
                                );
                                echo json_encode($data);
                            }
                        }
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function lost_confirmation($username, $email_address)
{
    if(is_connected() == false) {
        global $account_enabled;
        if($account_enabled == true) {
            global $warning_sign;
            global $database;
            $sql = $database->prepare('SELECT * FROM account WHERE email_address = ? AND username = ?');
            $sql->execute(array(
                $email_address,
                $username
            ));
            if($sql->rowCount() == 1) {
                $account_data = $sql->fetch();
                $user_id = $account_data['id'];
                if($account_data['activated_at'] == NULL) {
                    if($account_data['resend_confirmation_at'] != NULL) {
                        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND resend_confirmation_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
                        $sql->execute(array(
                            $account_data['id']
                        ));
                        $resend_time = $sql->rowCount();
                        if($resend_time != 1) {
                            $data = array(
                                'status' => 'NEED_TO_WAIT'
                            );
                            echo json_encode($data);
                        } else {
                            lost_confirmation_email($user_id, $username, $email_address);
                        }
                    } else {
                        lost_confirmation_email($user_id, $username, $email_address);
                    }
                } else {
                    $data = array(
                        'status' => 'USER_ALREADY_ACTIVATED'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['info'] = $warning_sign . 'Eh, ton compte est déjà activé ! Tu peux te connecter <strong>dès maintenant</strong>.';
                }
            } else {
                $data = array(
                    'status' => 'USER_EMAIL_NOT_MATCH'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function lost_confirmation_email($user_id, $username, $email_address)
{
    if(is_connected() == false) {
        global $success_sign;
        global $actual_link;
        global $database;
        $confirm_account_token = str_random(60);
        $sql = $database->prepare('UPDATE account SET confirm_account_token = ?, resend_confirmation_at = NOW() WHERE id = ?')->execute(array(
            $confirm_account_token,
            $user_id
        ));

        $body = '
       <html>
       <body>
       <div align="center">
       <img src="https://embed.gyazo.com/8728d6b6a8e0f186fde54b3edb6acb22.jpg">
       <h2>Bienvenu(e), ' . $username . ' !</h2>
       <a href="' . $actual_link . '/confirm?id=' . $user_id . '&t=' . $confirm_account_token . '">Clique ici</a> pour activer ton compte.<br><br>
       <strong>Attention !</strong> Ton compte sera automatiquement supprimé si tu ne l\'actives pas.
       </div>
       </body>
       </html>';
        $subject = "Boubi MSP - Nouvel email de confirmation";

        send_email($email_address, $subject, $body);

        $data = array(
            'status' => 'SUCCESS'
        );
        echo json_encode($data);
        $_SESSION['flash']['success'] = $success_sign . "Un email a été envoyé à l\'adresse <strong>" . $email_address . "</strong>.";
    }
}

function post_comment_news($comment, $news_id)
{
    if(is_connected() == true) {
        global $news_comments_enabled;
        if($news_comments_enabled == true) {
            global $success_sign;
            global $database;
            $comment = trim($comment);
            if(mb_strlen($comment, 'UTF-8') >= 10 AND mb_strlen($comment, 'UTF-8') <= 1000) {
                $sql = $database->prepare('INSERT INTO news_comments(news_id, comment_by, content) VALUES (?,?,?)');
                $sql->execute(array(
                    $news_id,
                    $_SESSION['id'],
                    $comment
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été posté.";
            }
        } else {
            $data = array(
                'status' => 'NEWS_COMMENTS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function delete_comment_news($comment_id)
{
    if(is_connected() == true) {
        global $success_sign;
        global $news_comments_enabled;
        if($news_comments_enabled == true) {
            global $database;
            $comment_exist = $database->prepare('SELECT * FROM news_comments WHERE comment_by = ? AND id = ?');
            $comment_exist->execute(array(
                $_SESSION['id'],
                $comment_id
            ));
            if($comment_exist->rowCount() == 1) {
                $delete_comment = $database->prepare('DELETE FROM news_comments WHERE comment_by = ? AND id = ?');
                $delete_comment->execute(array(
                    $_SESSION['id'],
                    $comment_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été supprimé.";
            } else {
                $data = array(
                    'status' => 'COMMENT_NOT_FOUND'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'NEWS_COMMENTS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_create_account($color, $sexe)
{
    if(is_connected() == true) {
        global $success_sign;
        global $mb_create_account_enabled;
        if($mb_create_account_enabled == true) {
            if($color == "Rouge" OR $color == "Bleu" OR $color == "Vert" OR $color == "Jaune" OR $color == "Noir" OR $color == "Blanc" OR $color == "Violet" OR $color == "Orange") {
                if($color == "Rouge") {
                    $color = "#e74c3c";
                } elseif($color == "Bleu") {
                    $color = "#3498db";
                } elseif($color == "Orange") {
                    $color = "#e67e22";
                } elseif($color == "Jaune") {
                    $color = "#f1c40f";
                } elseif($color == "Noir") {
                    $color = "#34495e";
                } elseif($color == "Blanc") {
                    $color = "#ecf0f1";
                } elseif($color == "Violet") {
                    $color = "#9b59b6";
                } else {
                    $color = "#56966d";
                }
                if($sexe == "Fille" OR $sexe == "Garçon") {
                    global $database;
                    global $database2;
                    if(is_profile_exist($_SESSION['id']) == false) {
                        if($sexe == "Fille") {
                            $avatar = "default_avatar_f.png";
                        } else {
                            $avatar = "default_avatar_m.png";
                        }
                        $sql = $database2->prepare('INSERT INTO profile(username_id,avatar,banner_color,sexe) VALUES (?,?,?,?)');
                        $sql->execute(array(
                            $_SESSION['id'],
                            $avatar,
                            $color,
                            $sexe
                        ));
                        $data = array(
                            'status' => 'SUCCESS'
                        );
                        echo json_encode($data);
                        $_SESSION['flash']['success'] = $success_sign . "Tu es maintenant inscris à MovieBook.";
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'MB_CREATE_ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_link_msp_account($msp_username, $msp_level, $is_vip)
{
    if(is_connected() == true) {
        global $success_sign;
        global $database;
        global $database2;
        $msp_username = trim($msp_username);
        $msp_level = trim($msp_level);
        if(is_profile_exist($_SESSION['id'])) {
            $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));
            if($sql->rowCount() == 0) {
                if(mb_strlen($msp_username, 'UTF-8') <= 30 AND mb_strlen($msp_level, 'UTF-8') <= 2 AND is_numeric($msp_level)) {
                    if($is_vip == "yes") {
                        $is_vip = 1;
                    } else {
                        $is_vip = 0;
                    }
                    $sql = $database2->prepare('INSERT INTO msp_account(username_id, msp_username, msp_level, is_vip) VALUES (?,?,?,?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $msp_username,
                        $msp_level,
                        $is_vip
                    ));

                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "Ton compte MSP a été associé.";
                }
            }
        }
    }
}

function dislink_msp_account()
{
    if(is_connected() == true) {
        global $success_sign;
        global $database2;
        $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $msp_account_data = $sql->fetch();

            if(is_file("img/moviebook/msp_avatars/" . $msp_account_data['avatar_link'])) {
                unlink("img/moviebook/msp_avatars/" . $msp_account_data['avatar_link']);
            }

            $sql = $database2->prepare('DELETE FROM msp_account WHERE username_id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Le compte MSP a été dissocié.";
        }
    }
}

function mb_update_info($name, $date_of_birth, $description)
{
    if(is_connected() == true) {
        global $mb_add_info_enabled;
        if($mb_add_info_enabled == true) {
            global $database2;
            $name = trim($name);
            $date_of_birth = trim($date_of_birth);
            $description = trim($description);
            if(is_profile_exist($_SESSION['id']) == true) {
                if(!empty($description)) {
                    if(mb_strlen($description, 'UTF-8') > 5000) {
                        exit();
                    }
                } else {
                    $description = NULL;
                }

                if(empty($name)) {
                    $name = NULL;
                } elseif(mb_strlen($name, 'UTF-8') > 15) {
                    exit();
                }

                if(empty($date_of_birth)) {
                    $date_of_birth = NULL;
                } elseif(mb_strlen($date_of_birth, 'UTF-8') > 10) {
                    exit();
                }

                $sql = $database2->prepare('UPDATE profile SET name = ?, date_of_birth = ?, description = ? WHERE username_id = ?')->execute(array(
                    $name,
                    $date_of_birth,
                    $description,
                    $_SESSION['id']
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'UPDATE_INFO_MB_ACCOUNT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_post_wall($content, $wall_id)
{
    if(is_connected() == true) {
        global $mb_post_wall_enabled;
        global $success_sign;
        global $database2;
        if($mb_post_wall_enabled == true) {
            $content = trim($content);
            if(is_profile_exist($_SESSION['id']) == true) {
                if(mb_strlen($content, 'UTF-8') <= 5000) {
                    $sql = $database2->prepare('INSERT INTO post_wall(content,posted_by,wall_id) VALUES (?,?,?)');
                    $sql->execute(array(
                        $content,
                        $_SESSION['id'],
                        $wall_id
                    ));
                    $last_id_post_wall = $database2->lastInsertId();
                    if($wall_id != $_SESSION['id']) {
                        extra_points("post_wall", $wall_id, $last_id_post_wall);
                        insert_notification($wall_id, $_SESSION['id'], "new_post_on_wall", $last_id_post_wall);
                    } else {
                        $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ?');
                        $friends_sql->execute(array(
                            $_SESSION['id'],
                            $_SESSION['id']
                        ));
                        while($friends_data = $friends_sql->fetch()) {
                            if($friends_data['user_one'] == $_SESSION['id']) {
                                $user_friend = "user_two";
                            } else {
                                $user_friend = "user_one";
                            }

                            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND from_id = ? AND type = "new_post_wall_friend"');
                            $sql->execute(array(
                                $friends_data[$user_friend],
                                $_SESSION['id']
                            ));
                            if($sql->rowCount() == 0) {
                                insert_notification($friends_data[$user_friend], $_SESSION['id'], "new_post_wall_friend", $_SESSION['id']);

                            }

                        }
                    }
                    $data = array(
                        'status' => 'SUCCESS',
                        'post_id' => $last_id_post_wall
                    );
                    echo json_encode($data);
                }
            }
        } else {
            $data = array(
                'status' => 'POST_WALL_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function delete_post_wall($content_id, $type)
{
    if(is_connected() == true) {
        global $mb_post_wall_enabled;
        global $success_sign;
        if($mb_post_wall_enabled == true) {
            global $database2;
            if($type == "post_wall") {
                $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND posted_by = ? AND deleted = 0');
                $sql->execute(array(
                    $content_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    delete_content($content_id, true, false);
                } else {
                    $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND wall_id = ? AND deleted = 0');
                    $sql->execute(array(
                        $content_id,
                        $_SESSION['id']
                    ));
                    if($sql->rowCount() == 1) {
                        delete_content($content_id, true, false);
                    }
                }
            } elseif($type == "reply_post_wall") {
                $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE id = ? AND username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $content_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    $sql = $database2->prepare('UPDATE reply_post_wall SET deleted = 1 WHERE id = ? AND username_id = ?');
                    $sql->execute(array(
                        $content_id,
                        $_SESSION['id']
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                }
            }
        } else {
            $data = array(
                'status' => 'POST_WALL_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function likes_dislikes_post_wall($post_id, $type)
{
    if(is_connected() == true) {
        global $mb_likes_dislikes_post_wall_enabled;
        if($mb_likes_dislikes_post_wall_enabled == true) {
            global $database2;
            $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));
            if($sql->rowCount() == 1) {
                $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
                $sql->execute(array(
                    $post_id
                ));
                if($sql->rowCount() == 1) {
                    $post_wall_data = $sql->fetch();
                    if($type == "like") {
                        $sql = $database2->prepare('SELECT * FROM likes WHERE post_id = ? AND username_id = ?');
                        $sql->execute(array(
                            $post_id,
                            $_SESSION['id']
                        ));
                        if($sql->rowCount() == 0) {
                            $sql = $database2->prepare('SELECT * FROM dislikes WHERE post_id = ? AND username_id = ?');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));
                            if($sql->rowCount() == 1) {
                                $sql = $database2->prepare('DELETE FROM dislikes WHERE post_id = ? AND username_id = ?');
                                $sql->execute(array(
                                    $post_id,
                                    $_SESSION['id']
                                ));
                                $revert = "true";
                            } else {
                                $revert = "false";
                            }
                            $sql = $database2->prepare('INSERT INTO likes(post_id,username_id) VALUES (?,?)');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));

                            if($post_wall_data['posted_by'] != $_SESSION['id']) {
                                extra_points("like", $post_wall_data['posted_by'], $post_id);
                            }

                            $data = array(
                                'status' => 'SUCCESS',
                                'type_like' => 'plus_like',
                                'revert' => $revert
                            );
                            echo json_encode($data);
                        } else {
                            $sql = $database2->prepare('DELETE FROM likes WHERE post_id = ? AND username_id = ?');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));
                            $data = array(
                                'status' => 'SUCCESS',
                                'type_like' => 'delete_like'
                            );
                            echo json_encode($data);
                        }
                    } elseif($type == "dislike") {
                        $sql = $database2->prepare('SELECT * FROM dislikes WHERE post_id = ? AND username_id = ?');
                        $sql->execute(array(
                            $post_id,
                            $_SESSION['id']
                        ));
                        if($sql->rowCount() == 0) {
                            $sql = $database2->prepare('SELECT * FROM likes WHERE post_id = ? AND username_id = ?');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));
                            if($sql->rowCount() == 1) {
                                $sql = $database2->prepare('DELETE FROM likes WHERE post_id = ? AND username_id = ?');
                                $sql->execute(array(
                                    $post_id,
                                    $_SESSION['id']
                                ));
                                $revert = "true";
                            } else {
                                $revert = "false";
                            }
                            $sql = $database2->prepare('INSERT INTO dislikes(post_id,username_id) VALUES (?,?)');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));
                            $data = array(
                                'status' => 'SUCCESS',
                                'type_like' => 'plus_dislike',
                                'revert' => $revert
                            );
                            echo json_encode($data);
                        } else {
                            $sql = $database2->prepare('DELETE FROM dislikes WHERE post_id = ? AND username_id = ?');
                            $sql->execute(array(
                                $post_id,
                                $_SESSION['id']
                            ));
                            $data = array(
                                'status' => 'SUCCESS',
                                'type_like' => 'delete_dislike'
                            );
                            echo json_encode($data);
                        }
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'LIKES_DISLIKES_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_request_friend($user_id)
{
    if(is_connected() == true) {
        global $mb_friends_enabled;
        global $success_sign;
        if($mb_friends_enabled == true) {
            global $database2;
            if($_SESSION['id'] != $user_id) {
                if(is_profile_exist($_SESSION['id']) == true AND is_profile_exist($user_id) == true) {
                    if(is_friends($user_id, $_SESSION['id']) == false) {
                        $sql = $database2->prepare('SELECT * FROM friend_request WHERE (from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?)');
                        $sql->execute(array(
                            $_SESSION['id'],
                            $user_id,
                            $user_id,
                            $_SESSION['id']
                        ));
                        if($sql->rowCount() == 0) {
                            $sql = $database2->prepare('INSERT INTO friend_request(from_id,to_id) VALUES (?,?)');
                            $sql->execute(array(
                                $_SESSION['id'],
                                $user_id
                            ));
                            $data = array(
                                'status' => 'SUCCESS'
                            );
                            echo json_encode($data);
                        }
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'FRIENDS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_cancel_request_friend($wall_id)
{
    if(is_connected() == true) {
        global $mb_friends_enabled;
        global $success_sign;
        if($mb_friends_enabled == true) {
            global $database2;
            $sql = $database2->prepare('SELECT * FROM friend_request WHERE from_id = ? AND to_id = ?');
            $sql->execute(array(
                $_SESSION['id'],
                $wall_id
            ));
            if($sql->rowCount() == 1) {
                $sql = $database2->prepare('DELETE FROM friend_request WHERE from_id = ? AND to_id = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    $wall_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'FRIENDS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_accept_decline_friend_request($user_id, $type)
{
    if(is_connected() == true) {
        global $mb_friends_enabled;
        global $success_sign;
        if($mb_friends_enabled == true) {
            global $database;
            global $database2;
            $sql = $database2->prepare('SELECT * FROM friend_request WHERE from_id = ? AND to_id = ?');
            $sql->execute(array(
                $user_id,
                $_SESSION['id']
            ));
            if($sql->rowCount() == 1) {
                $request_data = $sql->fetch();
                if($type == "accept") {
                    $sql = $database2->prepare('DELETE FROM friend_request WHERE from_id = ? AND to_id = ?');
                    $sql->execute(array(
                        $user_id,
                        $_SESSION['id']
                    ));
                    $sql = $database2->prepare('INSERT INTO friends(user_one,user_two) VALUES (?,?)');
                    $sql->execute(array(
                        $user_id,
                        $_SESSION['id']
                    ));

                    extra_points("friend", $user_id, NULL);
                    insert_notification($user_id, $_SESSION['id'], "friend_request_accepted", "");

                    $data = array(
                        'status' => 'SUCCESS',
                        'type' => 'accept',
                        'request_id' => $request_data['id']
                    );
                    echo json_encode($data);
                } elseif($type == "decline") {
                    $sql = $database2->prepare('DELETE FROM friend_request WHERE from_id = ? AND to_id = ?');
                    $sql->execute(array(
                        $user_id,
                        $_SESSION['id']
                    ));
                    $data = array(
                        'status' => 'SUCCESS',
                        'type' => 'decline',
                        'request_id' => $request_data['id']
                    );
                    echo json_encode($data);
                }
            }
        } else {
            $data = array(
                'status' => 'FRIENDS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_delete_friend($user_id)
{
    if(is_connected() == true) {
        global $mb_friends_enabled;
        global $success_sign;
        if($mb_friends_enabled == true) {
            global $database;
            global $database2;
            $sql = $database2->prepare('SELECT * FROM friends WHERE (user_one = ? AND user_two = ?) OR (user_one = ? AND user_two = ?)');
            $sql->execute(array(
                $user_id,
                $_SESSION['id'],
                $_SESSION['id'],
                $user_id
            ));
            if($sql->rowCount() == 1) {
                $friend_data = $sql->fetch();
                $sql = $database2->prepare('DELETE FROM friends WHERE (user_one = ? AND user_two = ?) OR (user_one = ? AND user_two = ?)');
                $sql->execute(array(
                    $user_id,
                    $_SESSION['id'],
                    $_SESSION['id'],
                    $user_id
                ));
                $data = array(
                    'status' => 'SUCCESS',
                    'user_id' => $user_id,
                    'friend_id' => $friend_data['id']
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'status' => 'FRIENDS_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_report($content_id, $type, $info)
{
    if(is_connected() == true) {
        global $report_enabled;
        if($report_enabled == true) {
            global $database2;
            $info = trim($info);
            if(is_profile_exist($_SESSION['id'])) {
                if(mb_strlen($info, 'UTF-8') <= 1000) {
                    $sql = $database2->prepare('SELECT * FROM report WHERE reported_by = ? AND content_id = ? AND type = ?');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $content_id,
                        $type
                    ));
                    if($sql->rowCount() == 0) {
                        if($type == "post_wall" OR $type == "reply_post_wall") {
                            if($type == "post_wall") {
                                $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
                                $sql->execute(array(
                                    $content_id
                                ));
                                if($sql->rowCount() != 1) {
                                    exit();
                                }
                            } elseif($type == "reply_post_wall") {
                                $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE id = ? AND deleted = 0');
                                $sql->execute(array(
                                    $content_id
                                ));
                                if($sql->rowCount() != 1) {
                                    exit();
                                }
                            }
                            if(empty($info)) {
                                $info = NULL;
                            }
                            $sql = $database2->prepare('INSERT INTO report(reported_by,type,info,content_id) VALUES (?,?,?,?)');
                            $sql->execute(array(
                                $_SESSION['id'],
                                $type,
                                $info,
                                $content_id
                            ));
                            $data = array(
                                'status' => 'SUCCESS'
                            );
                            echo json_encode($data);
                        }
                    } else {
                        $data = array(
                            'status' => 'ALREADY_SENT'
                        );
                        echo json_encode($data);
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'REPORT_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_post_wall_send_reply($post_id, $content)
{
    if(is_connected() == true) {
        global $mb_post_reply_enabled;
        global $success_sign;
        global $database;
        global $database2;
        if($mb_post_reply_enabled == true) {
            $content = trim($content);
            if(is_profile_exist($_SESSION['id'])) {
                if(mb_strlen($content, 'UTF-8') <= 1000) {
                    $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
                    $sql->execute(array(
                        $post_id
                    ));
                    if($sql->rowCount() == 1) {
                        $post_wall_data = $sql->fetch();

                        //////////Envoyer une notification à l'auteur de la publication
                        if($post_wall_data['posted_by'] != $_SESSION['id'] AND $post_wall_data['notification'] == 1) {
                            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? AND content_id = ?');
                            $sql->execute(array(
                                $post_wall_data['posted_by'],
                                "new_reply_on_post",
                                $post_id
                            ));
                            if($sql->rowCount() == 0) {
                                insert_notification($post_wall_data['posted_by'], $_SESSION['id'], "new_reply_on_post", $post_id);
                            }
                        }

                        //////////Envoyer une notification à toutes les personnes qui ont répondu à cette publication
                        $reply_post_wall_sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND username_id != ? AND username_id != ? AND notification = 1');
                        $reply_post_wall_sql->execute(array(
                            $post_id,
                            $_SESSION['id'],
                            $post_wall_data['posted_by']
                        ));
                        if($reply_post_wall_sql->rowCount() > 0) {
                            while($reply_post_wall_data = $reply_post_wall_sql->fetch()) {
                                $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? AND content_id = ?');
                                $sql->execute(array(
                                    $reply_post_wall_data['username_id'],
                                    "new_reply_on_post_replied",
                                    $post_wall_data['id']
                                ));
                                if($sql->rowCount() == 0) {
                                    insert_notification($reply_post_wall_data['username_id'], $_SESSION['id'], "new_reply_on_post_replied", $post_id);
                                }
                            }
                        }
                        //////////

                        $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND username_id = ? AND notification = 0');
                        $sql->execute(array(
                            $post_id,
                            $_SESSION['id']
                        ));
                        if($sql->rowCount() > 0) {
                            $notification = 0;
                        } else {
                            $notification = 1;
                        }

                        $sql = $database2->prepare('INSERT INTO reply_post_wall(post_id,username_id,content,notification) VALUES (?,?,?,?)');
                        $sql->execute(array(
                            $post_id,
                            $_SESSION['id'],
                            $content,
                            $notification
                        ));

                        $data = array(
                            'status' => 'SUCCESS'
                        );
                        echo json_encode($data);
                    }
                }
            }
        } else {
            $data = array(
                'status' => 'POST_WALL_REPLY_DISABLED'
            );
            echo json_encode($data);
        }
    }
}

function mb_pin_post($post_id, $type)
{
    if(is_connected() == true) {
        global $mb_pin_post_enabled;
        global $success_sign;
        if($mb_pin_post_enabled == true) {
            if(is_profile_exist($_SESSION['id'])) {
                global $database2;
                $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND posted_by = ? AND wall_id = ?');
                $sql->execute(array(
                    $post_id,
                    $_SESSION['id'],
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    $sql = $database2->prepare('SELECT * FROM post_wall WHERE pinned = 1 AND posted_by = ?');
                    $sql->execute(array(
                        $_SESSION['id']
                    ));
                    if($sql->rowCount() > 0) {
                        $sql = $database2->prepare('UPDATE post_wall SET pinned = 0 WHERE posted_by = ? AND pinned = 1')->execute(array(
                            $_SESSION['id']
                        ));
                    }
                    if($type == "pin") {
                        $sql = $database2->prepare('UPDATE post_wall SET pinned = 1 WHERE wall_id = ? AND posted_by = ? AND id = ?')->execute(array(
                            $_SESSION['id'],
                            $_SESSION['id'],
                            $post_id
                        ));
                    }
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                }
            }
        }
    }
}

function delete_notification($notif_id, $type)
{
    if(is_connected() == true) {
        global $database2;
        global $success_sign;
        if($type == "delete_one") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE id = ? AND to_id = ?');
            $sql->execute(array(
                $notif_id,
                $_SESSION['id']
            ));
            if($sql->rowCount() == 1) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE id = ? AND to_id = ?');
                $sql->execute(array(
                    $notif_id,
                    $_SESSION['id']
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));
            if($sql->rowCount() > 3) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ?');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $_SESSION['flash']['success'] = $success_sign . "<strong>C\'est du propre !</strong> Toutes tes notifications ont été supprimées.";
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_rare_order_completed") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "rare_order_completed"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "rare_order_completed"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_friend_request_accepted") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "friend_request_accepted"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "friend_request_accepted"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_new_post_on_my_wall") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_post_on_wall"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_post_on_wall"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_reply_post") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_reply_on_post",
                $_SESSION['id'],
                "new_reply_on_post_replied"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_reply_on_post",
                    $_SESSION['id'],
                    "new_reply_on_post_replied"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_my_contest") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_participant_contest",
                $_SESSION['id'],
                "new_contest_comment"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_participant_contest",
                    $_SESSION['id'],
                    "new_contest_comment"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_my_subjects") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_comment_my_subject"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_comment_my_subject"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_new_post") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_post_wall_friend"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_post_wall_friend"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_contest") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "contest_deleted_by_author",
                $_SESSION['id'],
                "contest_deleted_by_admin",
                $_SESSION['id'],
                "contest_time_out",
                $_SESSION['id'],
                "participant_deleted_by_author",
                $_SESSION['id'],
                "new_contest_comment_by_author",
                $_SESSION['id'],
                "new_contest_friend",
                $_SESSION['id'],
                "contest_ended"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ? OR to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "contest_deleted_by_author",
                    $_SESSION['id'],
                    "contest_deleted_by_admin",
                    $_SESSION['id'],
                    "contest_time_out",
                    $_SESSION['id'],
                    "participant_deleted_by_author",
                    $_SESSION['id'],
                    "new_contest_comment_by_author",
                    $_SESSION['id'],
                    "new_contest_friend",
                    $_SESSION['id'],
                    "contest_ended"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        } elseif($type == "delete_all_subject") {
            $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
            $sql->execute(array(
                $_SESSION['id'],
                "new_subject_friend",
                $_SESSION['id'],
                "new_comment_subject"
            ));
            if($sql->rowCount() >= 2) {
                $sql = $database2->prepare('DELETE FROM notifications WHERE to_id = ? AND type = ? OR to_id = ? AND type = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    "new_subject_friend",
                    $_SESSION['id'],
                    "new_comment_subject"
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        }
    }
}

function join_leave_contest($contest_id, $type)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $contest_id
        ));
        if($sql->rowCount() == 1) {
            $contest_data = $sql->fetch();
            if($contest_data['username_id'] != $_SESSION['id']) {
                if($type == "join_contest") {
                    $sql = $database->prepare('SELECT * FROM contest_participants WHERE username_id = ? AND contest_id = ?');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $contest_id
                    ));
                    if($sql->rowCount() == 0) {
                        if($contest_data['allow_to_participate'] == 1) {

                            $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
                            $sql->execute(array(
                                $_SESSION['id']
                            ));
                            $is_msp_account = $sql->rowCount();

                            if($contest_data['allow_only_msp_account_linked'] == 0 OR $contest_data['allow_only_msp_account_linked'] == 1 AND $is_msp_account == 1) {

                                $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ?');
                                $sql->execute(array(
                                    $contest_id
                                ));
                                $nop = $sql->rowCount();
                                if($contest_data['max_participants'] == "unlimited" OR $nop < $contest_data['max_participants']) {
                                    $sql = $database->prepare('INSERT INTO contest_participants(username_id,contest_id) VALUES (?,?)');
                                    $sql->execute(array(
                                        $_SESSION['id'],
                                        $contest_id
                                    ));
                                    if($contest_data['notification_new_participant'] == 1) {
                                        $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND from_id = ? AND type = ? AND content_id = ?');
                                        $sql->execute(array(
                                            $contest_data['username_id'],
                                            $_SESSION['id'],
                                            "new_participant_contest",
                                            $contest_data['contest_id']
                                        ));
                                        if($sql->rowCount() == 0) {
                                            insert_notification($contest_data['username_id'], $_SESSION['id'], "new_participant_contest", $contest_data['contest_id']);
                                        }
                                    }
                                    $data = array(
                                        'status' => 'SUCCESS'
                                    );
                                    echo json_encode($data);
                                }
                            } else {
                                $data = array(
                                    'status' => 'ALLOW_ONLY_MSP_ACCOUNT_LINKED'
                                );
                                echo json_encode($data);
                            }
                        }
                    }
                } elseif($type == "leave_contest") {
                    $sql = $database->prepare('SELECT * FROM contest_participants WHERE username_id = ? AND contest_id = ?');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $contest_id
                    ));
                    if($sql->rowCount() == 1) {
                        $sql = $database->prepare('DELETE FROM contest_participants WHERE username_id = ? AND contest_id = ?');
                        $sql->execute(array(
                            $_SESSION['id'],
                            $contest_id
                        ));
                        $data = array(
                            'status' => 'SUCCESS'
                        );
                        echo json_encode($data);
                    }
                }
            }
        }
    }
}

function post_comment_contest($comment, $contest_id)
{
    if(is_connected() == true) {
        global $success_sign;
        global $database;
        global $database2;

        $comment = trim($comment);
        if(mb_strlen($comment, 'UTF-8') <= 1000) {
            $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND comments_area = 1 AND deleted = 0');
            $sql->execute(array(
                $contest_id
            ));
            if($sql->rowCount() == 1) {
                $contest_data = $sql->fetch();

                $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ? AND username_id = ?');
                $sql->execute(array(
                    $contest_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    $is_in_contest = true;
                } else {
                    $is_in_contest = false;
                }

                if($contest_data['username_id'] == $_SESSION['id'] OR $contest_data['only_participants_allow_to_comment'] == 1 AND $is_in_contest == 1 OR $contest_data['only_participants_allow_to_comment'] == 0) {

                    if($contest_data['notification_comments'] == 1 AND $contest_data['username_id'] != $_SESSION['id']) {
                        $sql = $database2->prepare('SELECT * FROM notifications WHERE from_id = ? AND to_id = ? AND type = "new_contest_comment" AND content_id = ?');
                        $sql->execute(array(
                            $_SESSION['id'],
                            $contest_data['username_id'],
                            $contest_data['contest_id']
                        ));
                        if($sql->rowCount() == 0) {
                            insert_notification($contest_data['username_id'], $_SESSION['id'], "new_contest_comment", $contest_data['contest_id']);
                        }
                    } elseif($contest_data['username_id'] == $_SESSION['id']) {
                        $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ? AND notification_comment = 1');
                        $sql->execute(array(
                            $contest_id
                        ));
                        while($contest_participants_data = $sql->fetch()) {
                            $sql = $database2->prepare('SELECT * FROM notifications WHERE from_id = ? AND to_id = ? AND type = "new_contest_comment_by_author" AND content_id = ?');
                            $sql->execute(array(
                                $_SESSION['id'],
                                $contest_participants_data['username_id'],
                                $contest_data['contest_id']
                            ));
                            if($sql->rowCount() == 0) {
                                insert_notification($contest_participants_data['username_id'], $_SESSION['id'], "new_contest_comment_by_author", $contest_data['contest_id']);
                            }
                        }
                    }

                    $sql = $database->prepare('INSERT INTO contest_comments(username_id, contest_id, content) VALUES (?,?,?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $contest_id,
                        $comment
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été posté.";
                }
            }
        }
    }
}

function delete_comment_contest($comment_id, $contest_id)
{
    if(is_connected() == true) {
        global $success_sign;
        global $database;
        $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $contest_id
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('SELECT * FROM contest_comments WHERE username_id = ? AND id = ?');
            $sql->execute(array(
                $_SESSION['id'],
                $comment_id
            ));
            if($sql->rowCount() == 1) {
                $delete_comment = $database->prepare('DELETE FROM contest_comments WHERE username_id = ? AND id = ?');
                $delete_comment->execute(array(
                    $_SESSION['id'],
                    $comment_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été supprimé.";
            } else {
                $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $contest_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    $delete_comment = $database->prepare('DELETE FROM contest_comments WHERE id = ?');
                    $delete_comment->execute(array(
                        $comment_id
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "Le commentaire a été supprimé.";
                }
            }
        }
    }
}

function delete_contest($contest_id)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $sql = $database->prepare('SELECT contest_id FROM contest WHERE id = ? AND username_id = ? AND deleted = 0');
        $sql->execute(array(
            $contest_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $contest_data = $sql->fetch();
            delete_contest_1($contest_id, $contest_data['contest_id'], "contest_deleted_by_author");

            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton concours a été supprimé.";

        }
    }
}

function remove_participant($participant_id, $contest_id)
{
    if(is_connected() == true) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND username_id = ? AND deleted = 0');
        $sql->execute(array(
            $contest_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $contest_data = $sql->fetch();

            $sql = $database->prepare('SELECT * FROM contest_participants WHERE id = ? AND contest_id = ?');
            $sql->execute(array(
                $participant_id,
                $contest_id
            ));

            $contest_participants_data = $sql->fetch();
            insert_notification($contest_participants_data['username_id'], $_SESSION['id'], "participant_deleted_by_author", $contest_data['contest_id']);

            if($sql->rowCount() == 1) {
                $sql = $database->prepare('DELETE FROM contest_participants WHERE id = ? AND contest_id = ?');
                $sql->execute(array(
                    $participant_id,
                    $contest_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ce participant a été supprimé.";
            }
        }
    }
}

function update_participant_settings($contest_id, $notification_comment, $notification_delete_contest, $notification_end_contest)
{
    if(is_connected() == true) {
        global $database;
        $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $contest_id
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ? AND username_id = ?');
            $sql->execute(array(
                $contest_id,
                $_SESSION['id']
            ));
            if($sql->rowCount() == 1) {
                if($notification_comment == "true") {
                    $notification_comment = "1";
                } else {
                    $notification_comment = "0";
                }
                if($notification_delete_contest == "true") {
                    $notification_delete_contest = "1";
                } else {
                    $notification_delete_contest = "0";
                }
                if($notification_end_contest == "true") {
                    $notification_end_contest = "1";
                } else {
                    $notification_end_contest = "0";
                }
                $sql = $database->prepare('UPDATE contest_participants SET notification_comment = ?, notification_delete_contest = ?, notification_end_contest = ? WHERE username_id = ? AND contest_id = ?')->execute(array(
                    $notification_comment,
                    $notification_delete_contest,
                    $notification_end_contest,
                    $_SESSION['id'],
                    $contest_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            }
        }
    }
}

function finish_contest($contest_id, $final_text)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND username_id = ? AND deleted = 0 AND creation_date < DATE_SUB(NOW(), INTERVAL 1 DAY)');
        $sql->execute(array(
            $contest_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $final_text = trim($final_text);
            if(mb_strlen($final_text, 'UTF-8') <= 1000) {
                $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $contest_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() == 1) {
                    $contest_data = $sql->fetch();

                    $sql = $database->prepare('SELECT * FROM contest_participants WHERE contest_id = ?');
                    $sql->execute(array(
                        $contest_id
                    ));
                    $nop = $sql->rowCount();

                    delete_contest_1($contest_id, $contest_data['contest_id'], "contest_ended");

                    $sql = $database->prepare('INSERT INTO contest_ended(username_id, contest_id, content, number_participants, title, creation_date) VALUES (?,?,?,?,?,?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $contest_data['contest_id'],
                        $final_text,
                        $nop,
                        $contest_data['title'],
                        $contest_data['creation_date']
                    ));

                    extra_points("contest", $_SESSION['id'], $contest_id);

                    $data = array(
                        'status' => 'SUCCESS',
                        'contest_id' => $contest_data['contest_id']
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "Ton concours est maintenant terminé.";

                }
            }
        }
    }
}

function contest_report($contest_id, $info)
{
    if(is_connected() == true) {
        global $database;
        $info = trim($info);
        if(mb_strlen($info, 'UTF-8') <= 1000) {
            $sql = $database->prepare('SELECT * FROM contest_report WHERE username_id = ? AND contest_id = ?');
            $sql->execute(array(
                $_SESSION['id'],
                $contest_id
            ));
            if($sql->rowCount() == 0) {
                $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND deleted = 0');
                $sql->execute(array(
                    $contest_id
                ));
                if($sql->rowCount() == 1) {
                    if(empty($info)) {
                        $info = NULL;
                    }
                    $sql = $database->prepare('INSERT INTO contest_report(username_id,contest_id,info) VALUES (?,?,?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $contest_id,
                        $info
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                }
            } else {
                $data = array(
                    'status' => 'ALREADY_SENT'
                );
                echo json_encode($data);
            }
        }
    }
}

function admin_delete_contest($contest_id)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('SELECT contest_id FROM contest WHERE id = ?');
            $sql->execute(array(
                $contest_id
            ));
            if($sql->rowCount() == 1) {
                $contest_data = $sql->fetch();
                delete_contest_1($contest_id, $contest_data['contest_id'], "contest_deleted_by_admin");

                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Le concours a été supprimé.";
            }
        }
    }
}

function mb_save_edits_msp_account($msp_level, $is_vip)
{
    if(is_connected() == true) {
        global $success_sign;
        global $database2;
        $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $msp_level = trim($msp_level);
            if(mb_strlen($msp_level, 'UTF-8') <= 2 AND is_numeric($msp_level)) {
                if($is_vip == "yes") {
                    $is_vip = 1;
                } else {
                    $is_vip = 0;
                }
                $sql = $database2->prepare('UPDATE msp_account SET msp_level = ?, is_vip = ? WHERE username_id = ?')->execute(array(
                    $msp_level,
                    $is_vip,
                    $_SESSION['id']
                ));

                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton compte MSP a été mis-à-jour.";
            }
        }
    }
}

function notification_view_update()
{
    if(is_connected() == true) {
        global $database;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();
        if($account_data['notification_view'] == 0) {
            $sql = $database->prepare('UPDATE account SET notification_view = 1 WHERE id = ?')->execute(array(
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
        } else {
            $sql = $database->prepare('UPDATE account SET notification_view = 0 WHERE id = ?')->execute(array(
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
        }
    }
}

function notification_contest($type, $contest_id)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('SELECT * FROM contest WHERE id = ? AND deleted = 0');
            $sql->execute(array(
                $contest_id
            ));
            if($sql->rowCount() == 1) {
                $contest_data = $sql->fetch();
                if($type == "contest_need_password") {
                    $sql = $database2->prepare('INSERT INTO notifications(to_id,from_id,type,content_id,content) VALUES (?,?,?,?,?)');
                    $sql->execute(array(
                        $contest_data['username_id'],
                        "1",
                        "important_custom_notification",
                        $contest_data['contest_id'],
                        'ton concours a été supprimé car il ne respectait pas <a href="rules">les règles</a> : ton concours demandait un mot de passe.'
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                } elseif($type == "contest_price_do_not_have") {
                    $sql = $database2->prepare('INSERT INTO notifications(to_id,from_id,type,content_id,content) VALUES (?,?,?,?,?)');
                    $sql->execute(array(
                        $contest_data['username_id'],
                        "1",
                        "important_custom_notification",
                        $contest_data['contest_id'],
                        'ton concours a été supprimé car il ne respectait pas <a href="rules">les règles</a> : tu proposais une/des récompenses que tu ne possèdes pas.'
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                }
            }
        }
    }
}

function update_username($username)
{
    if(is_connected() == true) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND last_username_update_at < DATE_SUB(NOW(), INTERVAL 1 MONTH) OR id = ? AND last_username_update_at IS NULL;');
        $sql->execute(array(
            $_SESSION['id'],
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $username = trim($username);
            $sql = $database->prepare('SELECT * FROM account WHERE username = ?');
            $sql->execute(array(
                $username
            ));
            if($sql->rowCount() == 0) {
                if(mb_strlen($username) <= 15 AND mb_strlen($username) >= 4) {
                    $sql = $database->prepare('UPDATE account SET username = ?, last_username_update_at = NOW() WHERE id = ?')->execute(array(
                        $username,
                        $_SESSION['id']
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "Félicitation ! Ton pseudo est maintenant <strong>" . $username . "</strong>.";
                }
            } else {
                $data = array(
                    'status' => 'USER_ALREADY_TAKEN'
                );
                echo json_encode($data);

            }
        }
    }
}

function mb_update_post_wall_notification_settings($post_id)
{
    if(is_connected() == true) {
        global $database2;
        $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $post_id
        ));
        if($sql->rowCount() == 1) {
            $post_wall_data = $sql->fetch();
            if($post_wall_data['posted_by'] == $_SESSION['id']) {
                if($post_wall_data['notification'] == 1) {
                    $sql = $database2->prepare('UPDATE post_wall SET notification = 0 WHERE id = ? AND posted_by = ?')->execute(array(
                        $post_id,
                        $_SESSION['id']
                    ));
                    $data = array(
                        'status' => 'SUCCESS',
                        'notification' => 'DISABLE'
                    );
                    echo json_encode($data);
                } else {
                    $sql = $database2->prepare('UPDATE post_wall SET notification = 1 WHERE id = ? AND posted_by = ?')->execute(array(
                        $post_id,
                        $_SESSION['id']
                    ));
                    $data = array(
                        'status' => 'SUCCESS',
                        'notification' => 'ENABLE'
                    );
                    echo json_encode($data);
                }
            } else {
                $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE post_id = ? AND username_id = ?');
                $sql->execute(array(
                    $post_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() > 0) {
                    $reply_post_wall_data = $sql->fetch();
                    if($reply_post_wall_data['notification'] == 1) {
                        $sql = $database2->prepare('UPDATE reply_post_wall SET notification = 0 WHERE post_id = ? AND username_id = ?')->execute(array(
                            $post_id,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'status' => 'SUCCESS',
                            'notification' => 'DISABLE'
                        );
                        echo json_encode($data);
                    } else {
                        $sql = $database2->prepare('UPDATE reply_post_wall SET notification = 1 WHERE post_id = ? AND username_id = ?')->execute(array(
                            $post_id,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'status' => 'SUCCESS',
                            'notification' => 'ENABLE'
                        );
                        echo json_encode($data);
                    }
                }
            }
        }
    }
}

function forum_create_subject($title, $description, $notification_new_subject_friend, $notification_new_comment, $type)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $title = trim($title);
        $description = trim($description);
        if(mb_strlen($title) > 2 AND mb_strlen($title) <= 60 AND mb_strlen($description) > 2 AND mb_strlen($description) <= 5000) {
            $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0 AND creation_date > DATE_SUB(NOW(), INTERVAL 1 DAY)');
            $sql->execute(array(
                $_SESSION['id']
            ));
            if($sql->rowCount() < 3) {

                if($notification_new_comment == "true") {
                    $notification_new_comment = 1;
                } else {
                    $notification_new_comment = 0;
                }

                if($type != 1 AND $type != 2 AND $type != 3 AND $type != 4 AND $type != 5) {
                    $type = 1;
                }

                $sql = $database->prepare('INSERT INTO subject(username_id,title,description,notification_new_comment,category) VALUES (?,?,?,?,?)');
                $sql->execute(array(
                    $_SESSION['id'],
                    $title,
                    $description,
                    $notification_new_comment,
                    $type
                ));
                $subject_id = $database->lastInsertId();

                update_subject_last_comment_date($subject_id);
                extra_points("subject", $_SESSION['id'], $subject_id);

                if($notification_new_subject_friend == "true" AND is_profile_exist($_SESSION['id']) == true) {
                    $friends_sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ? ORDER BY id DESC');
                    $friends_sql->execute(array(
                        $_SESSION['id'],
                        $_SESSION['id']
                    ));
                    while($friends_data = $friends_sql->fetch()) {
                        if($friends_data['user_one'] == $_SESSION['id']) {
                            $user_friend = "user_two";
                        } else {
                            $user_friend = "user_one";
                        }
                        insert_notification($friends_data[$user_friend], $_SESSION['id'], "new_subject_friend", $subject_id);
                    }
                }

                $data = array(
                    'status' => 'SUCCESS',
                    'subject_id' => $subject_id
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton sujet a bien été créé.";
            }
        }
    }
}

function forum_delete_my_subject($subject_id)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND id = ? AND deleted = 0');
        $sql->execute(array(
            $_SESSION['id'],
            $subject_id
        ));
        if($sql->rowCount() == 1) {
            $subject_data = $sql->fetch();

            $sql = $database->prepare('UPDATE subject_comments SET deleted = 1 WHERE subject_id = ? AND deleted = 0')->execute(array(
                $subject_id
            ));

            $sql = $database->prepare('UPDATE subject SET deleted = 1 WHERE username_id = ? AND id = ? AND deleted = 0')->execute(array(
                $_SESSION['id'],
                $subject_id
            ));

            ////////////////////Supprimer notifications
            $sql = $database2->prepare('DELETE FROM notifications WHERE type = ? AND content_id = ? OR type = ? AND content_id = ?');
            $sql->execute(array(
                "new_comment_my_subject",
                $subject_data['id'],
                "new_subject_friend",
                $subject_data['id']
            ));
            ////////////////////

            ////////////////////Si le sujet date de moins de 3 jours, alors supprimé les points bonus
            if(strtotime($subject_data['creation_date']) > strtotime("-3 days")) {
                extra_points("subject_deleted", $_SESSION['id'], $subject_id);
            }
            ////////////////////

            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton sujet a bien été supprimé.";
        }
    }
}

function pin_my_subject($subject_id)
{
    if(is_connected()) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();
        if($account_data['subject_pin_token'] > 0) {
            $sql = $database->prepare('SELECT * FROM subject WHERE id = ? AND username_id = ? AND pinned = 0 AND deleted = 0');
            $sql->execute(array(
                $subject_id,
                $_SESSION['id']
            ));
            if($sql->rowCount() == 1) {
                $sql = $database->prepare('UPDATE subject SET pinned = 1, pin_date = NOW() WHERE id = ? AND username_id = ? AND pinned = 0 AND deleted = 0')->execute(array(
                    $subject_id,
                    $_SESSION['id']
                ));
                $sql = $database->prepare('UPDATE account SET subject_pin_token = ? WHERE id = ?')->execute(array(
                    $account_data['subject_pin_token'] - 1,
                    $_SESSION['id']
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton sujet est épinglé.";
            }
        }
    }
}

function update_settings_my_subject($subject_id, $title, $description, $notification_new_comment)
{
    if(is_connected() == true) {
        global $database;
        global $success_sign;
        $title = trim($title);
        $description = trim($description);
        if(mb_strlen($title) > 2 AND mb_strlen($title) <= 60 AND mb_strlen($description) > 2 AND mb_strlen($description) <= 5000) {
            if($notification_new_comment == "true") {
                $notification_new_comment = 1;
            } else {
                $notification_new_comment = 0;
            }
            $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND id = ? AND deleted = 0');
            $sql->execute(array(
                $_SESSION['id'],
                $subject_id
            ));
            if($sql->rowCount() == 1) {
                $sql = $database->prepare('UPDATE subject SET title = ?, description = ?, notification_new_comment = ? WHERE username_id = ? AND id = ? AND deleted = 0')->execute(array(
                    $title,
                    $description,
                    $notification_new_comment,
                    $_SESSION['id'],
                    $subject_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton sujet a bien été mis-à-jour.";
            }
        }
    }
}

function forum_send_comment_subject($subject_id, $content)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        $content = trim($content);
        if(mb_strlen($content) <= 5000) {
            $sql = $database->prepare('SELECT * FROM subject WHERE id = ? AND deleted = 0');
            $sql->execute(array(
                $subject_id
            ));
            if($sql->rowCount() == 1) {
                $subject_data = $sql->fetch();

                /////////////////Vérifier si le commentaire sera notification = 0 ou 1
                $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $subject_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() > 0) {
                    $subject_comments_data = $sql->fetch();
                    if($subject_comments_data['notification_new_comment'] == 1) {
                        $notification_new_comment = 1;
                    } else {
                        $notification_new_comment = 0;
                    }
                } else {
                    $notification_new_comment = 0;
                }
                /////////////////

                /////////////////Envoie d'une notification à tous ceux qui ont activés les notifications à ce sujet
                $subject_comments_sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND username_id != ? AND username_id != ? AND notification_new_comment = 1 AND deleted = 0');
                $subject_comments_sql->execute(array(
                    $subject_id,
                    $_SESSION['id'],
                    $subject_data['username_id']
                ));
                if($subject_comments_sql->rowCount() > 0) {
                    while($subject_comments_data = $subject_comments_sql->fetch()) {
                        $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? AND content_id = ?');
                        $sql->execute(array(
                            $subject_comments_data['username_id'],
                            "new_comment_subject",
                            $subject_id
                        ));
                        if($sql->rowCount() == 0) {
                            insert_notification($subject_comments_data['username_id'], $_SESSION['id'], "new_comment_subject", $subject_id);
                        }
                    }
                }
                /////////////////

                $sql = $database->prepare('INSERT INTO subject_comments(username_id,subject_id,content,notification_new_comment) VALUES (?,?,?,?)');
                $sql->execute(array(
                    $_SESSION['id'],
                    $subject_id,
                    $content,
                    $notification_new_comment
                ));


                if($subject_data['username_id'] != $_SESSION['id'] AND $subject_data['notification_new_comment'] == 1) {
                    $sql = $database2->prepare('SELECT * FROM notifications WHERE to_id = ? AND type = ? AND content_id = ?');
                    $sql->execute(array(
                        $subject_data['username_id'],
                        "new_comment_my_subject",
                        $subject_id
                    ));
                    if($sql->rowCount() == 0) {
                        insert_notification($subject_data['username_id'], $_SESSION['id'], "new_comment_my_subject", $subject_data['id']);
                    }
                }

                update_subject_last_comment_date($subject_id);

                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été publié.";
            }
        }
    }
}

function forum_delete_comment_subject($subject_id, $comment_id)
{
    if(is_connected() == true) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM subject_comments WHERE id = ? AND subject_id = ? AND username_id = ? AND deleted = 0');
        $sql->execute(array(
            $comment_id,
            $subject_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('UPDATE subject_comments SET deleted = 1 WHERE subject_id = ? AND username_id = ? AND id = ? AND deleted = 0')->execute(array(
                $subject_id,
                $_SESSION['id'],
                $comment_id
            ));

            update_subject_last_comment_date($subject_id);

            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton commentaire a été supprimé.";
        }
    }
}

function forum_update_notification_new_comments_subject($subject_id)
{
    if(is_connected() == true) {
        global $database;
        $sql = $database->prepare('SELECT * FROM subject WHERE id = ? AND deleted = 0');
        $sql->execute(array(
            $subject_id
        ));
        if($sql->rowCount() == 1) {
            $subject_data = $sql->fetch();
            if($subject_data['username_id'] != $_SESSION['id']) {
                $sql = $database->prepare('SELECT * FROM subject_comments WHERE subject_id = ? AND username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $subject_id,
                    $_SESSION['id']
                ));
                if($sql->rowCount() > 0) {
                    $comments_data = $sql->fetch();
                    if($comments_data['notification_new_comment'] == 0) {
                        $sql = $database->prepare('UPDATE subject_comments SET notification_new_comment = 1 WHERE subject_id = ? AND username_id = ? AND deleted = 0')->execute(array(
                            $subject_id,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'status' => 'SUCCESS',
                            'notifications' => 'ENABLED'
                        );
                        echo json_encode($data);
                    } else {
                        $sql = $database->prepare('UPDATE subject_comments SET notification_new_comment = 0 WHERE subject_id = ? AND username_id = ? AND deleted = 0')->execute(array(
                            $subject_id,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'status' => 'SUCCESS',
                            'notifications' => 'DISABLED'
                        );
                        echo json_encode($data);
                    }
                }
            }
        }
    }
}

function receive_award_achievement($achievement_name)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        global $success_sign;
        global $achievements;
        $sql = $database2->prepare('SELECT * FROM achievements WHERE username_id = ? AND name = ?');
        $sql->execute(array(
            $_SESSION['id'],
            $achievement_name
        ));
        if($sql->rowCount() == 1) {
            $achievements_data = $sql->fetch();

            $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));
            $account_data = $sql->fetch();

            if($achievements_data['name'] == "number_of_friends") {
                $sql = $database2->prepare('SELECT * FROM friends WHERE user_one = ? OR user_two = ?');
                $sql->execute(array(
                    $_SESSION['id'],
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 0;
            } elseif($achievements_data['name'] == "number_of_posts") {
                $sql = $database2->prepare('SELECT * FROM post_wall WHERE posted_by = ? AND deleted = 0');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 1;
            } elseif($achievements_data['name'] == "number_of_posts_received") {
                $sql = $database2->prepare('SELECT * FROM post_wall WHERE wall_id = ? AND posted_by != ? AND deleted = 0');
                $sql->execute(array(
                    $_SESSION['id'],
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 2;
            } elseif($achievements_data['name'] == "number_of_replies") {
                $sql = $database2->prepare('SELECT * FROM reply_post_wall WHERE username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 3;
            } elseif($achievements_data['name'] == "contests_ended") {
                $sql = $database->prepare('SELECT * FROM contest_ended WHERE username_id = ?');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 4;
            } elseif($achievements_data['name'] == "subjects_created") {
                $sql = $database->prepare('SELECT * FROM subject WHERE username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 5;
            } elseif($achievements_data['name'] == "comments_subjects") {
                $sql = $database->prepare('SELECT * FROM subject_comments WHERE username_id = ? AND deleted = 0');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 6;
            } elseif($achievements_data['name'] == "account_created_since") {
                $time_ago = strtotime($account_data['join_date']);
                $current_time = time();
                $time_difference = $current_time - $time_ago;
                $seconds = $time_difference;
                $achievement_progress_count = round($seconds / 2629440);
                $array_i = 7;
            } elseif($achievements_data['name'] == "profile_picture_added") {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND avatar != "default_avatar_m.png" AND avatar != "default_avatar_f.png"');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 8;
            } elseif($achievements_data['name'] == "banner_picture_added") {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND banner IS NOT NULL');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 9;
            } elseif($achievements_data['name'] == "background_picture_added") {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND background != "DEFAULT"');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 10;
            } elseif($achievements_data['name'] == "music_background_added") {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND music_background IS NOT NULL');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 11;
            } elseif($achievements_data['name'] == "msp_account_linked") {
                $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 12;
            } elseif($achievements_data['name'] == "informations_added") {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ? AND name IS NOT NULL AND description IS NOT NULL AND date_of_birth IS NOT NULL');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $achievement_progress_count = $sql->rowCount();
                $array_i = 13;
            } else {
                echo $achievement_name;
                exit();
            }

            if($achievements[$array_i]['type'] == "various") {
                if($achievements_data['state'] == 0) {
                    $achievement_state = 1;
                } elseif($achievements_data['state'] == 1) {
                    $achievement_state = 2;
                } elseif($achievements_data['state'] == 2 OR $achievements_data['state'] == 3) {
                    $achievement_state = 3;
                }
                if($achievement_progress_count < $achievements[$array_i]['state_' . $achievement_state] OR $achievements_data['state'] == 3) {
                    $up_state = 0;
                } else {
                    $up_state = 1;
                }
            } elseif($achievements[$array_i]['type'] == "once") {
                if($achievement_progress_count == 1 AND $achievements_data['state'] == 0) {
                    $achievement_state = 1;
                    $up_state = 1;
                } else {
                    $up_state = 0;
                }
            }

            if($up_state == 1) {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
                $sql->execute(array(
                    $_SESSION['id']
                ));
                $profile_data = $sql->fetch();

                $sql = $database2->prepare('UPDATE achievements SET state = ? WHERE name = ? AND username_id = ?')->execute(array(
                    $achievements_data['state'] + 1,
                    $achievement_name,
                    $_SESSION['id']
                ));

                $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE  username_id = ?')->execute(array(
                    $profile_data['level_points'] + $achievements[$array_i]['state_' . $achievement_state . '_points'],
                    $_SESSION['id']
                ));

                add_points_history($_SESSION['id'], $_SESSION['id'], $_SESSION['id'], $achievements_data['name'], NULL, $achievements[$array_i]['state_' . $achievement_state . '_points'], NULL);

                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
                $_SESSION['flash']['success'] = $success_sign . "Bravo ! Tu as gagné <strong>" . $achievements[$array_i]['state_' . $achievement_state . '_points'] . " points</strong> grâce à ce succès.";
            }
        }
    }
}

function create_sponsorship_link()
{
    if(is_connected() == true AND is_profile_exist($_SESSION['id']) == true) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND sponsorship_key IS NULL');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sponsorship_key = str_random(10);
            $sql = $database->prepare('UPDATE account SET sponsorship_key = ? WHERE id = ? AND sponsorship_key IS NULL')->execute(array(
                $sponsorship_key,
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS',
                'dsd' => $sponsorship_key
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton lien a bien été généré !";
        }
    }
}

function delete_sponsorship_link()
{
    if(is_connected() == true AND is_profile_exist($_SESSION['id']) == true) {
        global $database;
        global $success_sign;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND sponsorship_key IS NOT NULL');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database->prepare('UPDATE account SET sponsorship_key = NULL WHERE id = ? AND sponsorship_key IS NOT NULL')->execute(array(
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton lien a bien été supprimé.";
        }
    }
}

function admin_confirm_msp_account($user_id)
{
    if(is_connected() == 1) {
        global $database;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            global $database2;
            $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ? AND is_confirmed = 0');
            $sql->execute(array(
                $user_id
            ));
            if($sql->rowCount() == 1) {
                $sql = $database2->prepare('UPDATE msp_account SET is_confirmed = 1 WHERE username_id = ?')->execute(array(
                    $user_id
                ));
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'status' => 'NOT_FOUND'
                );
                echo json_encode($data);
            }
        }
    }
}

function check_password_event_halloween($string, $type)
{
    if(is_connected() == true) {
        if($type == "password_start") {
            $string = strtolower($string);
            if($string == "folie") {
                $data = array(
                    'status' => 'SUCCESS'
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'status' => 'WRONG_PASSWORD'
                );
                echo json_encode($data);
            }
        }
    }
}

function admin_update_points($user_id, $points, $reason)
{
    if(is_connected() == true) {
        global $database;
        global $database2;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            if(is_numeric($points)) {
                $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
                $sql->execute(array(
                    $user_id
                ));
                if($sql->rowCount() == 1) {
                    $profile_data = $sql->fetch();

                    $sql = $database2->prepare('UPDATE profile SET level_points = ? WHERE username_id = ?')->execute(array(
                        $profile_data['level_points'] + $points,
                        $user_id
                    ));
                    add_points_history($user_id, "1", $user_id, "custom", $reason, $points, NULL);
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                }
            }
        }
    }
}

function admin_send_notification($user_id, $content, $type)
{
    if(is_connected()) {
        global $database;
        global $database2;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            if($type == "true") {
                $sql = $database2->prepare('SELECT * FROM profile');
                $sql->execute();
                while($profile_data = $sql->fetch()) {
                    $sql_1 = $database2->prepare('INSERT INTO notifications(to_id,from_id,content,type,content_id) VALUES (?,?,?,"important_custom_notification",NULL)');
                    $sql_1->execute(array(
                        $profile_data['username_id'],
                        $_SESSION['id'],
                        $content
                    ));
                }
            } elseif(is_profile_exist($user_id)) {
                $sql = $database2->prepare('INSERT INTO notifications(to_id,from_id,content,type,content_id) VALUES (?,?,?,"important_custom_notification",NULL)');
                $sql->execute(array(
                    $user_id,
                    $_SESSION['id'],
                    $content
                ));
            } else {
                return;
            }
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
        }
    }
}

function update_website_theme($type)
{
    if(is_connected()) {
        global $database;
        global $success_sign;

        if($type == "basic") {
            $type = 1;
        } else {
            $type = 2;
        }

        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();

        if($account_data['theme'] != $type) {
            $sql = $database->prepare('UPDATE account SET theme = ? WHERE id = ?')->execute(array(
                $type,
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Voilà ton nouveau thème !";
        }
    }
}

function admin_delete_post($post_id)
{
    if(is_connected()) {
        global $database;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();
        if($account_data['admin_access'] == 1) {
            delete_content($post_id, true, true);
        }
    }
}

function admin_ban_account($user_id, $reason, $number_of_weeks, $type)
{
    if(is_connected()) {
        global $database;
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();
        if($account_data['admin_access'] == 1) {
            if($type == "ban" AND is_numeric($number_of_weeks)) {
                $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND banned = 0');
                $sql->execute(array(
                    $user_id
                ));
                if($sql->rowCount() == 1) {
                    $sql = $database->prepare('UPDATE account SET banned = 1 WHERE id = ? AND banned = 0')->execute(array(
                        $user_id
                    ));

                    $sql = $database->prepare('INSERT INTO ban_history(user_id, reason, date_of_deban) VALUES (?,?,NOW() + INTERVAL ? WEEK)');
                    $sql->execute(array(
                        $user_id,
                        $reason,
                        $number_of_weeks
                    ));
                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                } else {
                    $data = array(
                        'status' => 'ALREADY_BANNED'
                    );
                    echo json_encode($data);
                }
            } elseif($type == "deban") {
                $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND banned = 1');
                $sql->execute(array(
                    $user_id
                ));
                if($sql->rowCount() == 1) {
                    $sql = $database->prepare('UPDATE account SET banned = 0 WHERE id = ? AND banned = 1')->execute(array(
                        $user_id
                    ));

                    $sql = $database->prepare('SELECT * FROM ban_history WHERE user_id = ? ORDER BY date_of_ban DESC');
                    $sql->execute(array(
                        $user_id
                    ));
                    if($sql->rowCount() > 0) {
                        $ban_history_data = $sql->fetch();
                        $sql = $database->prepare('UPDATE ban_history SET date_of_deban = NOW() WHERE id = ? AND user_id = ?')->execute(array(
                            $ban_history_data['id'],
                            $user_id
                        ));
                        $data = array(
                            'status' => 'SUCCESS'
                        );
                        echo json_encode($data);
                    }
                } else {
                    $data = array(
                        'status' => 'NOT_BANNED'
                    );
                    echo json_encode($data);
                }
            }
        }
    }
}

function update_badge($badge_id)
{
    if(is_connected()) {
        global $database2;
        global $success_sign;
        $sql = $database2->prepare('SELECT * FROM badge_pictures WHERE id = ? AND user_id = ? AND active = 0');
        $sql->execute(array(
            $badge_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database2->prepare('UPDATE badge_pictures SET active = 0 WHERE user_id = ?')->execute(array(
                $_SESSION['id']
            ));
            $sql = $database2->prepare('UPDATE badge_pictures SET active = 1 WHERE user_id = ? AND id = ?')->execute(array(
                $_SESSION['id'],
                $badge_id
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton badge a été mis à jour !";
        }
    }
}

function unactive_badge()
{
    if(is_connected()) {
        global $database2;
        global $success_sign;
        $sql = $database2->prepare('SELECT * FROM badge_pictures WHERE active = 1 AND user_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            $sql = $database2->prepare('UPDATE badge_pictures SET active = 0 WHERE user_id = ?')->execute(array(
                $_SESSION['id']
            ));
            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ton badge a été enlevé.";
        }
    }
}

function rare_send_order($msp_username, $rare_id)
{
    if(is_connected()) {
        global $database;
        global $database2;
        global $success_sign;
        global $rare_limit;
        global $rares_list;

        $sql = $database->prepare('SELECT * FROM rare');
        $sql->execute();
        if($sql->rowCount() < $rare_limit) {
            if(mb_strlen($msp_username) <= 30) {

                if(is_numeric($rare_id)) {
                    $is_the_rare_exist = false;

                    for($i = 0; $i < count($rares_list); $i++) {
                        if($rares_list[$i]['id'] == $rare_id AND $rares_list[$i]['available'] == 1) {
                            $is_the_rare_exist = true;
                        }
                    }

                    if($is_the_rare_exist == true) {
                        $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND join_date < DATE_SUB(NOW(), INTERVAL 1 WEEK)');
                        $sql->execute(array(
                            $_SESSION['id']
                        ));
                        if($sql->rowCount() == 1) {
                            $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ? AND is_confirmed = 1');
                            $sql->execute(array(
                                $_SESSION['id']
                            ));
                            if($sql->rowCount() == 1) {
                                $sql = $database->prepare('SELECT * FROM rare WHERE user_id = ?');
                                $sql->execute(array(
                                    $_SESSION['id']
                                ));
                                if($sql->rowCount() == 0) {
                                    $sql = $database->prepare('SELECT * FROM rare WHERE msp_username = ?');
                                    $sql->execute(array(
                                        $msp_username
                                    ));
                                    if($sql->rowCount() == 0) {
                                        $sql = $database->prepare('INSERT INTO rare(user_id, msp_username, rare_id) VALUES (?,?,?)');
                                        $sql->execute(array(
                                            $_SESSION['id'],
                                            $msp_username,
                                            $rare_id
                                        ));

                                        $data = array(
                                            'status' => 'SUCCESS'
                                        );
                                        echo json_encode($data);
                                        $_SESSION['flash']['success'] = $success_sign . "Ta commande a été enregistrée !";
                                    } else {
                                        $data = array(
                                            'status' => 'MSP_USERNAME_ALREADY_IN_WAITING_LINE'
                                        );
                                        echo json_encode($data);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function rare_delete_order()
{
    if(is_connected()) {
        global $database;
        global $success_sign;

        $sql = $database->prepare('SELECT * FROM rare WHERE user_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));

        if($sql->rowCount() == 1) {
            $sql = $database->prepare('DELETE FROM rare WHERE user_id = ?');
            $sql->execute(array(
                $_SESSION['id']
            ));

            $data = array(
                'status' => 'SUCCESS'
            );
            echo json_encode($data);
            $_SESSION['flash']['success'] = $success_sign . "Ta commande a été supprimée.";
        }
    }
}

function rare_complete_order($order_id)
{
    if(is_connected()) {
        global $database;
        global $success_sign;
        if(is_numeric($order_id)) {
            $sql = $database->prepare('SELECT * FROM account WHERE id = ? AND admin_access = 1 OR id = ? AND admin_access = 5');
            $sql->execute(array(
                $_SESSION['id'],
                $_SESSION['id']
            ));

            if($sql->rowCount() == 1) {
                $sql = $database->prepare('SELECT * FROM rare WHERE id = ?');
                $sql->execute(array(
                    $order_id
                ));

                if($sql->rowCount() == 1) {
                    $rare_data = $sql->fetch();

                    $sql = $database->prepare('INSERT INTO rare_completed(deleted_by, ordered_by, rare_id, msp_username, order_date) VALUES (?,?,?,?,?)');
                    $sql->execute(array(
                        $_SESSION['id'],
                        $rare_data['user_id'],
                        $rare_data['rare_id'],
                        $rare_data['msp_username'],
                        $rare_data['order_date']
                    ));
                    $last_order_completed = $database->lastInsertId();

                    insert_notification($rare_data['user_id'], $_SESSION['id'], "rare_order_completed", $last_order_completed);

                    extra_points("rare_completed", $_SESSION['id'], $last_order_completed);

                    $sql = $database->prepare('DELETE FROM rare WHERE id = ?');
                    $sql->execute(array(
                        $order_id
                    ));

                    $data = array(
                        'status' => 'SUCCESS'
                    );
                    echo json_encode($data);
                    $_SESSION['flash']['success'] = $success_sign . "La commande a été complétée et supprimée de la liste.";
                } else {
                    $data = array(
                        'status' => 'ORDER_NOT_FOUND'
                    );
                    echo json_encode($data);
                }
            }
        }
    }
}

?>
