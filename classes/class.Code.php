<?php

// class.Code.php

class Code {

    private $key;

    public function __construct() {
        $this->key = 'EpYy3ERr';
    }

    public function __destruct() {
        unset($this->key);
    }

    public function enCode($str = '') {

        if (!empty($str)) {
            $block = mcrypt_get_block_size('des', 'ecb');
            $pad = $block - (strlen($str) % $block);
            $str .= str_repeat(chr($pad), $pad);
            return mcrypt_encrypt(MCRYPT_DES, $this->key, $str, MCRYPT_MODE_ECB);
        }
    }

    public function deCode($str = '') {

        if (!empty($str)) {
            $str = mcrypt_decrypt(MCRYPT_DES, $this->key, $str, MCRYPT_MODE_ECB);
            $pad = ord($str[($len = strlen($str)) - 1]);
            return substr($str, 0, strlen($str) - $pad);
        }
    }

}
