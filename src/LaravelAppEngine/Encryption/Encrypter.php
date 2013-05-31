<?php namespace LaravelAppEngine\Encryption;

class DecryptException extends \RuntimeException {}

class Encrypter extends \Illuminate\Encryption\Encrypter {

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher = 'AES-256-CBC';

    /**
     * The block size of the cipher.
     *
     * @var int
     */
    protected $block = 32;

	/**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     */
    public function encrypt($value)
    {

        $iv = '';
        srand(time() + ((microtime(true) * 1000000) % 1000000));
        while(strlen($iv) < $this->getIvSize()) {
            $iv .= chr(rand(0,255));
        }

        $value = base64_encode($this->padAndMcrypt($value, $iv));

        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.
        $iv = base64_encode($iv);

        $mac = $this->hash($value);

        return base64_encode(json_encode(compact('iv', 'value', 'mac')));
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     */
    public function decrypt($payload)
    {
        $payload = $this->getJsonPayload($payload);

        // We'll go ahead and remove the PKCS7 padding from the encrypted value before
        // we decrypt it. Once we have the de-padded value, we will grab the vector
        // and decrypt the data, passing back the unserialized from of the value.
        $value = base64_decode($payload['value']);

        $iv = base64_decode($payload['iv']);

        return unserialize($this->stripPadding($this->mcryptDecrypt($value, $iv)));
    }

    /**
     * Pad and use mcrypt on the given value and input vector.
     *
     * @param  string  $value
     * @param  string  $iv
     * @return string
     */
    protected function padAndMcrypt($value, $iv)
    {
        $value = $this->addPadding(serialize($value));

        return openssl_encrypt($value, $this->cipher, $this->key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
    }

    /**
     * Run the mcrypt decryption routine for the value.
     *
     * @param  string  $value
     * @param  string  $iv
     * @return string
     */
    protected function mcryptDecrypt($value, $iv)
    {
        return openssl_decrypt($value, $this->cipher, $this->key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
    }

	/**
	 * Get the IV size for the cipher.
	 *
	 * @return int
	 */
	protected function getIvSize()
	{
		return openssl_cipher_iv_length($this->cipher);
	}

}
