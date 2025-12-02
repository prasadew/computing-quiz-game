<?php
// Two-Factor Authentication (2FA) using TOTP
// includes/two-factor-auth.php

class TwoFactorAuth {
    private $issuer = 'Computing Quiz Game';
    private $timeStep = 30; // Google Authenticator uses 30 second time step
    private $codeLength = 6;
    private $window = 1; // Allow 1 time step before and after for clock skew

    /**
     * Generate a random secret key for 2FA
     * @return string Base32 encoded secret
     */
    public function generateSecret() {
        $randomBytes = random_bytes(20); // 160 bits = 32 base32 characters
        return $this->base32Encode($randomBytes);
    }

    /**
     * Generate QR code URL for setup
     * @param string $userEmail User's email
     * @param string $secret The secret key
     * @return string QR code URL
     */
    public function getQRCodeURL($userEmail, $secret) {
        $otpauth = "otpauth://totp/{$this->issuer}:$userEmail?secret=$secret&issuer={$this->issuer}";
        
        // Use QR Server API for QR code generation (more reliable than deprecated Google Charts API)
        $encodedOTP = urlencode($otpauth);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=$encodedOTP";
    }

    /**
     * Verify a TOTP code
     * @param string $secret The secret key
     * @param string $code The 6-digit code to verify
     * @return bool True if code is valid
     */
    public function verifyCode($secret, $code) {
        $code = trim($code);
        $code = preg_replace('/\s+/', '', $code); // Remove spaces
        
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        // Get current time
        $currentTime = floor(time() / $this->timeStep);

        // Check the code within the window (allow time skew)
        for ($i = -$this->window; $i <= $this->window; $i++) {
            $timeCounter = $currentTime + $i;
            $generatedCode = $this->generateTOTPCode($secret, $timeCounter);
            
            if ($generatedCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate TOTP code for a specific time counter
     * @param string $secret The secret key (base32 encoded)
     * @param int $timeCounter The time counter
     * @return string 6-digit code
     */
    private function generateTOTPCode($secret, $timeCounter) {
        // Decode the secret from base32
        $secretBinary = $this->base32Decode($secret);
        
        // Create HMAC
        $hmac = hash_hmac('sha1', pack('J', $timeCounter), $secretBinary, true);
        
        // Get the offset from the last nibble
        $offset = ord($hmac[strlen($hmac) - 1]) & 0x0f;
        
        // Get the 4-byte code from the HMAC
        $code = unpack('N', substr($hmac, $offset, 4))[1];
        
        // Ensure it's positive and take modulo 1000000
        $code = ($code & 0x7fffffff) % 1000000;
        
        // Pad with zeros
        return str_pad($code, $this->codeLength, '0', STR_PAD_LEFT);
    }

    /**
     * Generate backup codes for emergency access
     * @return array Array of 8 backup codes
     */
    public function generateBackupCodes() {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            // Generate 12-character alphanumeric codes
            $code = substr(bin2hex(random_bytes(6)), 0, 12);
            $code = strtoupper(preg_replace('/(.{4})(.{4})(.{4})/', '$1-$2-$3', $code));
            $codes[] = $code;
        }
        return $codes;
    }

    /**
     * Verify a backup code and remove it from the list
     * @param string $code The backup code to verify
     * @param array $codes The list of backup codes (stored as JSON)
     * @return array ['valid' => bool, 'remaining_codes' => array]
     */
    public function verifyBackupCode($code, $codes) {
        $code = strtoupper(trim($code));
        
        if (empty($codes) || !is_array($codes)) {
            return [
                'valid' => false,
                'remaining_codes' => []
            ];
        }

        $index = array_search($code, $codes);
        
        if ($index === false) {
            return [
                'valid' => false,
                'remaining_codes' => $codes
            ];
        }

        // Remove the used code
        unset($codes[$index]);
        
        return [
            'valid' => true,
            'remaining_codes' => array_values($codes) // Re-index the array
        ];
    }

    /**
     * Base32 encode a string
     * @param string $data Binary data to encode
     * @return string Base32 encoded string
     */
    private function base32Encode($data) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;
            
            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $alphabet[($v >> $vbits) & 0x1f];
            }
        }

        if ($vbits > 0) {
            $output .= $alphabet[($v << (5 - $vbits)) & 0x1f];
        }

        return $output;
    }

    /**
     * Base32 decode a string
     * @param string $data Base32 encoded string
     * @return string Binary decoded data
     */
    private function base32Decode($data) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        $data = strtoupper($data);
        
        for ($i = 0; $i < strlen($data); $i++) {
            $digit = strpos($alphabet, $data[$i]);
            
            if ($digit === false) {
                throw new Exception("Invalid base32 character: {$data[$i]}");
            }

            $v = ($v << 5) | $digit;
            $vbits += 5;

            if ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr(($v >> $vbits) & 0xff);
            }
        }

        return $output;
    }
}
?>
