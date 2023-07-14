<?php

namespace App\Handlers;
/**
 * Encryption for encrypt and decrypt data.
 *
 * @author Vee W.
 * @license MIT
 */
class EncryptionHandler
{


    /**
     * @see __construct()
     * @var array The options.
     */
    protected $options = [];


    /**
     * Class constructor.
     *
     * @param array $options Associative array keys:<br>
     *      'algorithm' (string) Algorithm. Example: 'aes-256-gcm'.<br>
     *      'keyLength' (int) secret key or passphrase length. Default is 32.<br>
     *      'tagLength' (int) The length of the authentication tag. Read more at https://www.php.net/manual/en/function.openssl-encrypt.php
     */
    public function __construct(array $options = [])
    {
        $defaults = [
            'algorithm' => 'aes-256-gcm',
            'keyLength' => 32,
            'tagLength' => 16,
        ];

        $options = array_merge($defaults, $options);

        if (!in_array($options['algorithm'], openssl_get_cipher_methods())) {
            throw new \Exception(
                'The algorithm is not supported.'
            );
        }

        $this->options = $options;
    }// __construct


    /**
     * Decrypt the data.
     *
     * @param string $data The encrypted data to be decrypted.
     * @param string $key The secret key or passphrase. The key should hashed from `getKeyHashed()` method.
     * @return string|false Return the decrypted string on success, `false` on failure.
     */
    public function decrypt(string $data, string $key)
    {
        $b64Decoded = base64_decode($data);
        if (false === $b64Decoded) {
            return false;
        }

        $ivLength = $this->getIVLength();
        if (!is_numeric($ivLength)) {
            return false;
        }
        $iv = substr($b64Decoded, 0, $ivLength);
        $ciphertext = substr($b64Decoded, $ivLength, -$this->options['tagLength']);
        $tag = substr($b64Decoded, -$this->options['tagLength']);
        return openssl_decrypt($ciphertext, $this->options['algorithm'], $key, OPENSSL_RAW_DATA, $iv, $tag);
    }// decrypt


    /**
     * Encrypt the data.
     *
     * @param string $data The data to be encrypted.
     * @param string $key The secret key or passphrase. The key should hashed from `getKeyHashed()` method.
     * @param string|null $iv Initialization Vector. Set to `null` to auto generate.
     * @return string Return base64 encoded of encrypted data.
     */
    public function encrypt(string $data, string $key, string $iv = null): string
    {
        if (is_null($iv)) {
            $iv = $this->getIV();
        }

        $tag = '';
        $ciphertext = openssl_encrypt($data, $this->options['algorithm'], $key, OPENSSL_RAW_DATA, $iv, $tag, '', $this->options['tagLength']);
        return base64_encode($iv . $ciphertext . $tag);
    }// encrypt


    /**
     * Get Initialization Vector.
     *
     * @return string Return Initialization Vector string.
     */
    public function getIV(): string
    {
        $ivLength = $this->getIVLength();

        if (!is_numeric($ivLength)) {
            return '';
        }

        return openssl_random_pseudo_bytes($ivLength);
    }// getIV


    /**
     * Get Initialization Vector length.
     *
     * @return int|false Return `int` on success, `false` on failure.
     */
    protected function getIVLength()
    {
        return openssl_cipher_iv_length($this->options['algorithm']);
    }// getIVLength


    /**
     * Get input secret key as hashed.
     *
     * @param string $key The secret key or passphrase.
     * @return string Return hashed key and cut to the length.
     */
    public function getKeyHashed(string $key): string
    {
        $hashed = hash('sha256', $key, true);
        return substr($hashed, 0, $this->options['keyLength']);
    }// getKeyHashed


}
