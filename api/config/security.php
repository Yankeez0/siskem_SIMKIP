<?php
class Security {
    private static $scryptOptions = [
        'cost' => 16384,
        'blockSize' => 8,
        'parallel' => 1,
        'keyLength' => 32
    ];
    
    private static $pepper = "SIMKIPSecureSystem2025"; // Change this in production
    private static $aesKey;
    
    public static function initializeAESKey() {
        if (!isset($_SESSION['encryption_key'])) {
            $salt = random_bytes(32);
            $key = hash_hkdf('sha256', self::$pepper, 32, 'aes-256-gcm', $salt);
            $_SESSION['encryption_key'] = $key;
            $_SESSION['encryption_salt'] = $salt;
        }
        self::$aesKey = $_SESSION['encryption_key'];
    }
    
    public static function hashPassword($password) {
        $salt = random_bytes(32);
        $hash = sodium_crypto_pwhash_scryptsalsa208sha256_str(
            $password . self::$pepper,
            self::$scryptOptions['cost'],
            self::$scryptOptions['blockSize']
        );
        return ['hash' => $hash, 'salt' => bin2hex($salt)];
    }
    
    public static function verifyPassword($password, $storedHash, $salt) {
        return sodium_crypto_pwhash_scryptsalsa208sha256_str_verify(
            $storedHash,
            $password . self::$pepper
        );
    }
    
    public static function encrypt($data) {
        self::initializeAESKey();
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES);
        $cipher = sodium_crypto_aead_aes256gcm_encrypt(
            $data,
            $nonce,
            $nonce,
            self::$aesKey
        );
        return base64_encode($nonce . $cipher);
    }
    
    public static function decrypt($encryptedData) {
        self::initializeAESKey();
        $decoded = base64_decode($encryptedData);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES, '8bit');
        $cipher = mb_substr($decoded, SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES, null, '8bit');
        
        return sodium_crypto_aead_aes256gcm_decrypt(
            $cipher,
            $nonce,
            $nonce,
            self::$aesKey
        );
    }
}

// Initialize session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize AES key for the session
Security::initializeAESKey();
?>
