<?php
// Scrypt password hashing (menggunakan sodium bawaan PHP >= 7.2)
function scrypt_hash($password) {
    return sodium_crypto_pwhash_str(
        $password,
        SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
        SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
    );
}

function scrypt_verify($password, $hash) {
    return sodium_crypto_pwhash_str_verify($hash, $password);
}
