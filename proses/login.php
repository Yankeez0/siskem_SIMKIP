<?php
require_once 'config.php';
require_once 'scrypt.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? AND role = ?');
    $stmt->bind_param('ss', $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && scrypt_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        echo json_encode(['success' => true, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Login gagal!']);
    }
    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
}
