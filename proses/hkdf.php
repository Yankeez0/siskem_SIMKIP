<?php
// HKDF untuk derivasi kunci (menggunakan hash SHA-256)
function hkdf($ikm, $length = 32, $salt = '', $info = '') {
    return hash_hkdf('sha256', $ikm, $length, $info, $salt);
}
