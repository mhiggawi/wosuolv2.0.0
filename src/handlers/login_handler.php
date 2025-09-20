<?php
// src/handlers/login_handler.php

// This file is included by public/login.php and assumes bootstrap.php has been included.
// It handles all the business logic for the login page.

// --- "Remember Me" & Automatic Login Check ---
if (!isset($_SESSION['loggedin']) && isset($_COOKIE['remember_me'])) {
    list($user_token, $token_hash) = explode(':', $_COOKIE['remember_me']);

    $stmt = $mysqli->prepare("SELECT u.username, u.role, u.event_id, rt.expires_at FROM users u JOIN remember_tokens rt ON u.username = rt.username WHERE rt.token_hash = ?");
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (time() < strtotime($row['expires_at'])) {
            // Valid token, log the user in
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['event_id_access'] = $row['event_id'];

            // Renew the token for security
            $new_token = bin2hex(random_bytes(32));
            $new_token_hash = password_hash($new_token, PASSWORD_DEFAULT);
            $new_expiry = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)); // 30 days
            $stmt_update = $mysqli->prepare("UPDATE remember_tokens SET token_hash = ?, expires_at = ? WHERE username = ?");
            $stmt_update->bind_param("sss", $new_token_hash, $new_expiry, $row['username']);
            $stmt_update->execute();
            $stmt_update->close();

            setcookie('remember_me', $row['username'] . ':' . $new_token_hash, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Determine redirect URL
            $redirect_url = '';
            switch ($row['role']) {
                case 'admin': $redirect_url = 'events.php'; break;
                case 'viewer': $redirect_url = 'dashboard.php?event_id=' . $row['event_id']; break;
                case 'checkin_user': $redirect_url = 'checkin.php?event_id=' . $row['event_id']; break;
            }
            header("Location: $redirect_url");
            exit;
        } else {
            // Expired token, remove from database
            $stmt_delete = $mysqli->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
            $stmt_delete->bind_param("s", $token_hash);
            $stmt_delete->execute();
            $stmt_delete->close();
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }
    $stmt->close();
}

// If the user is already logged in, redirect them.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // You might want to make this redirect more intelligent based on the user's role
    header('Location: events.php');
    exit;
}


// --- Security Settings ---
$MAX_ATTEMPTS = 5;
$LOCKOUT_TIME = 900; // 15 minutes
$RATE_LIMIT_TIME = 60; // 1 minute
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// --- Rate Limiting & Security ---
$attempts_key = 'login_attempts_' . md5($client_ip);
$lockout_key = 'login_lockout_' . md5($client_ip);
$last_attempt_key = 'last_attempt_' . md5($client_ip);

if (!isset($_SESSION[$attempts_key])) {
    $_SESSION[$attempts_key] = 0;
}

$message = '';
$messageType = 'error';
$attempts_remaining = $MAX_ATTEMPTS - $_SESSION[$attempts_key];
$is_locked_out = false;
$time_until_unlock = 0;

if (isset($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > time()) {
    $is_locked_out = true;
    $time_until_unlock = $_SESSION[$lockout_key] - time();
    $minutes_remaining = ceil($time_until_unlock / 60);
    $message = str_replace('{minutes}', $minutes_remaining, $t['error_account_locked']);
}

$rate_limited = false;
$rate_limit_seconds = 0;
if (isset($_SESSION[$last_attempt_key]) && $_SESSION[$attempts_key] >= 3) {
    $time_since_last = time() - $_SESSION[$last_attempt_key];
    if ($time_since_last < $RATE_LIMIT_TIME) {
        $rate_limited = true;
        $rate_limit_seconds = $RATE_LIMIT_TIME - $time_since_last;
        $message = str_replace('{seconds}', $rate_limit_seconds, $t['error_too_many_attempts']);
    }
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['switch_language'])) {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = $t['error_csrf'];
    }
    // Check if locked out or rate limited
    elseif ($is_locked_out || $rate_limited) {
        // Message is already set and will be displayed
    }
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);

        if (empty($username) || empty($password)) {
            $message = $t['error_fill_fields'];
            $_SESSION[$attempts_key]++;
            $_SESSION[$last_attempt_key] = time();
        } else {

            $sql = "SELECT username, password_hash, role, event_id FROM users WHERE username = ?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $username);

                if ($stmt->execute()) {
                    $stmt->store_result();

                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($db_username, $db_password_hash, $db_role, $db_event_id);

                        if ($stmt->fetch()) {
                            if (password_verify($password, $db_password_hash)) {

                                // Successful login
                                unset($_SESSION[$attempts_key], $_SESSION[$lockout_key], $_SESSION[$last_attempt_key]);
                                session_regenerate_id(true);

                                $_SESSION['loggedin'] = true;
                                $_SESSION['username'] = $db_username;
                                $_SESSION['role'] = $db_role;
                                $_SESSION['event_id_access'] = $db_event_id;

                                $message = $t['login_success'];
                                $messageType = 'success';

                                if ($remember_me) {
                                    $token = bin2hex(random_bytes(32));
                                    $token_hash = password_hash($token, PASSWORD_DEFAULT);
                                    $expires_at = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60));

                                    $stmt_delete = $mysqli->prepare("DELETE FROM remember_tokens WHERE username = ?");
                                    $stmt_delete->bind_param("s", $db_username);
                                    $stmt_delete->execute();
                                    $stmt_delete->close();

                                    $stmt_insert = $mysqli->prepare("INSERT INTO remember_tokens (username, token_hash, expires_at) VALUES (?, ?, ?)");
                                    $stmt_insert->bind_param("sss", $db_username, $token_hash, $expires_at);
                                    $stmt_insert->execute();
                                    $stmt_insert->close();

                                    setcookie('remember_me', $db_username . ':' . $token_hash, [
                                        'expires' => time() + (30 * 24 * 60 * 60),
                                        'path' => '/',
                                        'domain' => $_SERVER['HTTP_HOST'],
                                        'secure' => isset($_SERVER['HTTPS']),
                                        'httponly' => true,
                                        'samesite' => 'Lax'
                                    ]);
                                }

                                $redirect_url = '';
                                switch ($db_role) {
                                    case 'admin': $redirect_url = 'events.php'; break;
                                    case 'viewer': $redirect_url = 'dashboard.php?event_id=' . $db_event_id; break;
                                    case 'checkin_user': $redirect_url = 'checkin.php?event_id=' . $db_event_id; break;
                                }

                                if (empty($redirect_url)) {
                                    $message = $t['error_no_event_access'];
                                    $messageType = 'error';
                                } else {
                                    // The view will handle the redirect via JavaScript
                                }

                            } else {
                                $message = $t['error_invalid_credentials'];
                                $_SESSION[$attempts_key]++;
                                $_SESSION[$last_attempt_key] = time();
                            }
                        }
                    } else {
                        $message = $t['error_invalid_credentials'];
                        $_SESSION[$attempts_key]++;
                        $_SESSION[$last_attempt_key] = time();
                    }
                } else {
                    $message = $t['error_general'];
                }
                $stmt->close();
            } else {
                $message = $t['error_general'];
            }
        }

        if ($_SESSION[$attempts_key] >= $MAX_ATTEMPTS) {
            $_SESSION[$lockout_key] = time() + $LOCKOUT_TIME;
            $message = str_replace('{minutes}', ceil($LOCKOUT_TIME / 60), $t['error_account_locked']);
        }
        $attempts_remaining = max(0, $MAX_ATTEMPTS - $_SESSION[$attempts_key]);
    }
}
?>
