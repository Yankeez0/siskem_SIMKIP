<?php
// Enkripsi & Dekripsi AES-256-GCM
function aes_gcm_encrypt($plaintext, $key) {
    $iv = random_bytes(12); // 96 bit IV
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function aes_gcm_decrypt($data, $key) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 12);
    $tag = substr($data, 12, 16);
    $ciphertext = substr($data, 28);
    return openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
}
