<?php
function getEncryptionKey(): string {
    $baseKey = 'kunci-rahasia-yang-panjang-dan-random';
    return hash_hkdf('sha256', $baseKey, 32, 'sim-kip-data', 'app-context');
}

function encryptData(string $plaintext): string {
    $key = getEncryptionKey();
    $iv = random_bytes(12); // 12 bytes untuk GCM
    $tag = '';
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function decryptData(string $encoded): string {
    $data = base64_decode($encoded);
    if ($data === false || strlen($data) < 28) { // minimal: 12 (IV) + 16 (TAG)
        return 'DECRYPT_ERROR';
    }

    $iv = substr($data, 0, 12);
    $tag = substr($data, 12, 16);
    $ciphertext = substr($data, 28);
    $key = getEncryptionKey();

    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

    return $plaintext === false ? 'DECRYPT_ERROR' : $plaintext;
}
