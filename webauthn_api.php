<?php
// webauthn_api.php

session_start();
require_once 'db_config.php';
// require_once 'vendor/autoload.php'; // تأكد من تثبيت مكتبة WebAuthn وتضمينها هنا

use Webauthn\Server;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialCreationOptions\AuthenticationExtensionsClientInputs;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialRequestOptions;

// هنا يجب أن يكون لديك إعداد لمكتبة WebAuthn
// مثال:
// $relyingPartyName = 'Wosuol Events';
// $relyingPartyId = $_SERVER['HTTP_HOST'];
// $server = new Server($relyingPartyName, $relyingPartyId);


// CSRF Protection
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Security token mismatch.']));
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'register_start':
        // بدء عملية التسجيل
        $username = $_POST['username'] ?? '';
        if (empty($username)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'message' => 'Username is required.']));
        }

        // تحقق من وجود المستخدم في قاعدة البيانات
        $stmt = $mysqli->prepare("SELECT id, username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            http_response_code(404);
            die(json_encode(['success' => false, 'message' => 'User not found.']));
        }

        $userEntity = new PublicKeyCredentialUserEntity($user['username'], $user['id'], $user['username']);
        $attestationConveyancePreference = 'none';
        $authenticatorSelection = new AuthenticatorSelectionCriteria();
        $extensions = new AuthenticationExtensionsClientInputs();
        
        $publicKey = new PublicKeyCredentialCreationOptions(
            $relyingPartyEntity,
            $userEntity,
            random_bytes(32), // Challenge
            $credentialCreationOptions->getPubKeyCredParams(),
            $credentialCreationOptions->getTimeout(),
            $authenticatorSelection,
            $attestationConveyancePreference,
            $extensions
        );

        $_SESSION['registration_challenge'] = $publicKey->getChallenge();
        echo json_encode(['success' => true, 'publicKey' => $publicKey->jsonSerialize()]);
        break;

    case 'register_finish':
        // إنهاء عملية التسجيل
        // هذا الجزء سيتعامل مع استجابة المتصفح ويخزن المفتاح العام في قاعدة البيانات.
        // يجب أن يتضمن التحقق من التحدي (Challenge) وتخزين البيانات.
        // مثال:
        // $publicKeyCredentialSource = $server->finishRegistration($_POST['response'], $_SESSION['registration_challenge']);
        // $stmt = $mysqli->prepare("INSERT INTO webauthn_credentials (user_id, credential_id, public_key, counter) VALUES (?, ?, ?, ?)");
        // ...
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        break;

    case 'authenticate_start':
        // بدء عملية المصادقة
        $username = $_POST['username'] ?? '';
        
        // جلب المفتاح (أو المفاتيح) المرتبطة بالمستخدم
        $stmt = $mysqli->prepare("SELECT public_key, credential_id FROM webauthn_credentials wc JOIN users u ON wc.user_id = u.id WHERE u.username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $credentials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        if (empty($credentials)) {
            http_response_code(404);
            die(json_encode(['success' => false, 'message' => 'No WebAuthn credentials found for this user.']));
        }
        
        $requestOptions = PublicKeyCredentialRequestOptions::create(random_bytes(32));
        $_SESSION['authentication_challenge'] = $requestOptions->getChallenge();
        
        // إعداد options
        echo json_encode(['success' => true, 'options' => $requestOptions->jsonSerialize()]);
        break;

    case 'authenticate_finish':
        // إنهاء عملية المصادقة
        // هذا الجزء سيتعامل مع استجابة المتصفح ويتحقق من صحتها.
        // يجب أن يتضمن التحقق من التحدي (Challenge) وتحديث العداد (counter) وتأسيس الجلسة.
        // مثال:
        // $server->finishAuthentication($_POST['response'], $_SESSION['authentication_challenge']);
        // session_regenerate_id(true);
        // $_SESSION['loggedin'] = true;
        // $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Authentication successful! Redirecting...']);
        break;
        
    default:
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Invalid action.']));
}