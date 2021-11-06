<?php
include('../includes/config.php');

if(is_connected() AND $maintenance_mode == false) {
    if(is_profile_exist($_SESSION['id']) == true) {
        $sql = $database2->prepare('SELECT * FROM profile WHERE username_id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $profile_data = $sql->fetch();
        if(isset($_POST['type']) AND !empty($_POST['type'])) {
            if($_POST['type'] == "picture_post" AND isset($_FILES['picture_file']) AND !empty($_FILES['picture_file']['name'])) {
                upload_picture_post();
            } elseif($_POST['type'] == "upload_avatar" AND isset($_FILES['avatar_file']) AND !empty($_FILES['avatar_file']['name'])) {
                upload_avatar();
            } elseif($_POST['type'] == "upload_banner" AND isset($_FILES['banner_file']) AND !empty($_FILES['banner_file']['name'])) {
                upload_banner();
            } elseif($_POST['type'] == "upload_background" AND isset($_FILES['background_file']) AND !empty($_FILES['background_file']['name'])) {
                upload_background();
            } elseif($_POST['type'] == "upload_msp_avatar" AND isset($_FILES['msp_avatar_file']) AND !empty($_FILES['msp_avatar_file']['name'])) {
                upload_msp_avatar();
            } elseif($_POST['type'] == "upload_badge" AND isset($_FILES['badge_file'])) {
                upload_badge();
            }
        }
    }
}

function upload_picture_post()
{
    global $database2;
    $taille_max = 10000000;
    if(isset($_POST['post_id_hidden']) AND !empty($_POST['post_id_hidden'])) {
        $post_id = htmlspecialchars($_POST['post_id_hidden']);
        $sql = $database2->prepare('SELECT * FROM post_wall WHERE id = ? AND posted_by = ?');
        $sql->execute(array(
            $post_id,
            $_SESSION['id']
        ));
        if($sql->rowCount() == 1) {
            if($_FILES['picture_file']['size'] <= $taille_max) {
                $valid_extensions = array(
                    'jpg',
                    'jpeg',
                    'png',
                    'gif'
                );
                $upload_extension = strtolower(substr(strrchr($_FILES['picture_file']['name'], '.'), 1));
                if(in_array($upload_extension, $valid_extensions)) {
                    if(is_numeric($post_id)) {
                        $tmp_file = $_FILES['picture_file']['tmp_name'];
                        $random_string = str_random(10);
                        $path = "../img/moviebook/pictures/" . $_SESSION['id'] . "_" . $post_id . "_picture_" . $random_string . "." . $upload_extension;
                        $path_min = $_SESSION['id'] . "_" . $post_id . "_picture_" . $random_string . "." . $upload_extension;
                        $result = move_uploaded_file($tmp_file, $path);
                        if($result) {
                            $sql = $database2->prepare('UPDATE post_wall SET is_picture = 1, picture_path = ? WHERE id = ? AND posted_by = ?')->execute(array(
                                $path_min,
                                $post_id,
                                $_SESSION['id']
                            ));
                            $data = array(
                                'result' => 'SUCCESS',
                                'type' => 'picture_post'
                            );
                            echo json_encode($data);
                        } else {
                            delete_content($post_id, false, false);
                            $data = array(
                                'result' => 'ERROR_IMPORTATION',
                                'type' => 'upload_avatar'
                            );
                            echo json_encode($data);
                        }
                    }
                } else {
                    delete_content($post_id, false, false);
                    $data = array(
                        'result' => 'INVALID_EXTENSION',
                        'type' => 'picture_post'
                    );
                    echo json_encode($data);
                }
            } else {
                delete_content($post_id, false, false);
                $data = array(
                    'result' => 'TOO_BIG',
                    'type' => 'picture_post'
                );
                echo json_encode($data);
            }
        }
    }
}

function upload_avatar()
{
    global $database2;
    global $profile_data;
    $taille_max = 5000000;
    $valid_extensions = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    if($_FILES['avatar_file']['size'] <= $taille_max) {
        $upload_extension = strtolower(substr(strrchr($_FILES['avatar_file']['name'], '.'), 1));
        if(in_array($upload_extension, $valid_extensions)) {
            if($profile_data['avatar'] != "default_avatar_m.png" AND $profile_data['avatar'] != "default_avatar_f.png") {
                if(is_file("../img/moviebook/avatars/" . $profile_data['avatar'])) {
                    unlink("../img/moviebook/avatars/" . $profile_data['avatar']);
                }
            }
            $random_string = str_random(10);
            $path = "../img/moviebook/avatars/" . $_SESSION['id'] . "_" . $random_string . "_avatar." . $upload_extension;
            $path_min = $_SESSION['id'] . "_" . $random_string . "_avatar." . $upload_extension;
            $result = move_uploaded_file($_FILES['avatar_file']['tmp_name'], $path);
            if($result) {
                $sql = $database2->prepare('UPDATE profile SET avatar = ? WHERE username_id = ?')->execute(array(
                    $path_min,
                    $_SESSION['id']
                ));
                $data = array(
                    'result' => 'SUCCESS',
                    'type' => 'upload_avatar'
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'result' => 'ERROR_IMPORTATION',
                    'type' => 'upload_avatar'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'result' => 'INVALID_EXTENSION',
                'type' => 'upload_avatar'
            );
            echo json_encode($data);
        }
    } else {
        $data = array(
            'result' => 'TOO_BIG',
            'type' => 'upload_avatar'
        );
        echo json_encode($data);
    }
}

function upload_banner()
{
    global $database2;
    global $profile_data;
    $taille_max = 10000000;
    $valid_extensions = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    if($_FILES['banner_file']['size'] <= $taille_max) {
        $upload_extension = strtolower(substr(strrchr($_FILES['banner_file']['name'], '.'), 1));
        if(in_array($upload_extension, $valid_extensions)) {
            if(is_file("../img/moviebook/banners/" . $profile_data['banner'])) {
                unlink("../img/moviebook/banners/" . $profile_data['banner']);
            }
            $random_string = str_random(10);
            $path = "../img/moviebook/banners/" . $_SESSION['id'] . "_" . $random_string . "_banner." . $upload_extension;
            $path_min = $_SESSION['id'] . "_" . $random_string . "_banner." . $upload_extension;
            $result = move_uploaded_file($_FILES['banner_file']['tmp_name'], $path);
            if($result) {
                $sql = $database2->prepare('UPDATE profile SET banner = ? WHERE username_id = ?')->execute(array(
                    $path_min,
                    $_SESSION['id']
                ));
                $data = array(
                    'result' => 'SUCCESS',
                    'type' => 'upload_banner'
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'result' => 'ERROR_IMPORTATION',
                    'type' => 'upload_banner'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'result' => 'INVALID_EXTENSION',
                'type' => 'upload_banner'
            );
            echo json_encode($data);
        }
    } else {
        $data = array(
            'result' => 'TOO_BIG',
            'type' => 'upload_banner'
        );
        echo json_encode($data);
    }
}

function upload_background()
{
    global $database2;
    global $profile_data;
    $taille_max = 10000000;
    $valid_extensions = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    if($_FILES['background_file']['size'] <= $taille_max) {
        $upload_extension = strtolower(substr(strrchr($_FILES['background_file']['name'], '.'), 1));
        if(in_array($upload_extension, $valid_extensions)) {
            if(is_file("../img/moviebook/backgrounds/" . $profile_data['background'])) {
                unlink("../img/moviebook/backgrounds/" . $profile_data['background']);
            }
            $random_string = str_random(10);
            $path = "../img/moviebook/backgrounds/" . $_SESSION['id'] . "_" . $random_string . "_background." . $upload_extension;
            $path_min = $_SESSION['id'] . "_" . $random_string . "_background." . $upload_extension;
            $result = move_uploaded_file($_FILES['background_file']['tmp_name'], $path);
            if($result) {
                $sql = $database2->prepare('UPDATE profile SET background = ? WHERE username_id = ?')->execute(array(
                    $path_min,
                    $_SESSION['id']
                ));
                $data = array(
                    'result' => 'SUCCESS',
                    'type' => 'upload_background'
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'result' => 'ERROR_IMPORTATION',
                    'type' => 'upload_background'
                );
                echo json_encode($data);
            }
        } else {
            $data = array(
                'result' => 'INVALID_EXTENSION',
                'type' => 'upload_background'
            );
            echo json_encode($data);
        }
    } else {
        $data = array(
            'result' => 'TOO_BIG',
            'type' => 'upload_background'
        );
        echo json_encode($data);
    }
}

function upload_msp_avatar()
{
    global $database2;
    $sql = $database2->prepare('SELECT * FROM msp_account WHERE username_id = ?');
    $sql->execute(array(
        $_SESSION['id']
    ));
    if($sql->rowCount() == 1) {
        $msp_account_data = $sql->fetch();
        $taille_max = 5000000;
        $valid_extensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        );
        if(isset($_POST['type_1']) AND !empty($_POST['type_1'])) {
            $type = htmlspecialchars($_POST['type_1']);
            if($_FILES['msp_avatar_file']['size'] <= $taille_max) {
                $upload_extension = strtolower(substr(strrchr($_FILES['msp_avatar_file']['name'], '.'), 1));
                if(in_array($upload_extension, $valid_extensions)) {
                    if(is_file("../img/moviebook/msp_avatars/" . $msp_account_data['avatar_link'])) {
                        unlink("../img/moviebook/msp_avatars/" . $msp_account_data['avatar_link']);
                    }
                    $random_string = str_random(10);
                    $path = "../img/moviebook/msp_avatars/" . $_SESSION['id'] . "_" . $random_string . "_msp_avatar." . $upload_extension;
                    $path_min = $_SESSION['id'] . "_" . $random_string . "_msp_avatar." . $upload_extension;
                    $result = move_uploaded_file($_FILES['msp_avatar_file']['tmp_name'], $path);
                    if($result) {
                        $sql = $database2->prepare('UPDATE msp_account SET avatar_link = ? WHERE username_id = ?')->execute(array(
                            $path_min,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'result' => 'SUCCESS',
                            'type' => 'upload_msp_avatar'
                        );
                        echo json_encode($data);
                    } else {
                        if($type == "new_msp_account") {
                            $sql = $database2->prepare('DELETE FROM msp_account WHERE username_id = ?');
                            $sql->execute(array(
                                $_SESSION['id']
                            ));
                        }
                        $data = array(
                            'result' => 'ERROR_IMPORTATION',
                            'type' => 'upload_msp_avatar'
                        );
                        echo json_encode($data);
                    }
                } else {
                    if($type == "new_msp_account") {
                        $sql = $database2->prepare('DELETE FROM msp_account WHERE username_id = ?');
                        $sql->execute(array(
                            $_SESSION['id']
                        ));
                    }
                    $data = array(
                        'result' => 'INVALID_EXTENSION',
                        'type' => 'upload_msp_avatar'
                    );
                    echo json_encode($data);
                }
            } else {
                if($type == "new_msp_account") {
                    $sql = $database2->prepare('DELETE FROM msp_account WHERE username_id = ?');
                    $sql->execute(array(
                        $_SESSION['id']
                    ));
                }
                $data = array(
                    'result' => 'TOO_BIG',
                    'type' => 'upload_msp_avatar'
                );
                echo json_encode($data);
            }
        }
    }
}

function upload_badge()
{
    global $database;
    global $database2;
    global $success_sign;
    $taille_max = 100000;
    $valid_extensions = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    $image_info = getimagesize($_FILES["badge_file"]["tmp_name"]);
    $image_width = $image_info[0];
    $image_height = $image_info[1];
    if($image_width == 180 AND $image_height == 180) {
        $sql = $database->prepare('SELECT * FROM account WHERE id = ?');
        $sql->execute(array(
            $_SESSION['id']
        ));
        $account_data = $sql->fetch();
        if($account_data['badge_token'] > 0) {
            if($_FILES['badge_file']['size'] <= $taille_max) {
                $upload_extension = strtolower(substr(strrchr($_FILES['badge_file']['name'], '.'), 1));
                if(in_array($upload_extension, $valid_extensions)) {
                    $random_string = str_random(10);
                    $path = "../img/moviebook/badges/" . $_SESSION['id'] . "_" . $random_string . "_badge." . $upload_extension;
                    $path_min = $_SESSION['id'] . "_" . $random_string . "_badge." . $upload_extension;
                    $result = move_uploaded_file($_FILES['badge_file']['tmp_name'], $path);
                    if($result) {
                        $sql = $database2->prepare('INSERT INTO badge_pictures(user_id, badge_path) VALUES (?,?)')->execute(array(
                            $_SESSION['id'],
                            $path_min
                        ));
                        $sql = $database->prepare('UPDATE account SET badge_token = ? WHERE id = ?')->execute(array(
                            $account_data['badge_token'] - 1,
                            $_SESSION['id']
                        ));
                        $data = array(
                            'result' => 'SUCCESS',
                            'type' => 'upload_badge'
                        );
                        echo json_encode($data);
                        $_SESSION['flash']['success'] = $success_sign . "Ton badge a été ajouté !";
                    } else {
                        $data = array(
                            'result' => 'ERROR_IMPORTATION',
                            'type' => 'upload_badge'
                        );
                        echo json_encode($data);
                    }
                } else {
                    $data = array(
                        'result' => 'INVALID_EXTENSION',
                        'type' => 'upload_badge'
                    );
                    echo json_encode($data);
                }
            } else {
                $data = array(
                    'result' => 'TOO_BIG',
                    'type' => 'upload_badge'
                );
                echo json_encode($data);
            }
        }
    } else {
        $data = array(
            'result' => 'INCORRECT_WITDH_HEIGHT',
            'type' => 'upload_badge'
        );
        echo json_encode($data);
    }
}

?>