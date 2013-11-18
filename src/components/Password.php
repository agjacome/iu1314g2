<?php

namespace components;

define('PASSWORD_BCRYPT', 1);
define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

/**
 * Clase estatica para el cifrado y verificacion de contraseñas. Implementa la 
 * misma funcionalidad existente en PHP 5.5 de forma nativa. Es necesaria 
 * puesto que en la maquina virtual esta instalado PHP 5.4 y no se dispone de 
 * estas funciones.
 *
 * Basada en la libreria password_compat, adaptada a una clase estatica:
 * https://github.com/ircmaxell/password_compat
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Password
{

    /**
     * Implementa una funcionalidad equivalente a password_hash() de PHP 5.5:
     * http://php.net/manual/en/function.password-hash.php
     *
     * @param string $password
     *     La contraseña a cifrar.
     * @param constant $algo
     *     Algoritmo de cifrado a utilizar, definidos como constantes globales: 
     *     PASSWORD_BCRYPT y PASSWORD_DEFAULT (que apunta a Bcrypt).
     * @param array $options
     *     Array de opciones para el algoritmo utilizado.
     *
     * @return string
     *     La contraseña cifrada, o false si se ha producido algun error.
     */
    public static function hash($password, $algo, array $options = array()) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
            return null;
        }
        if (!is_string($password)) {
            trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
            return null;
        }
        if (!is_int($algo)) {
            trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
            return null;
        }
        switch ($algo) {
        case PASSWORD_BCRYPT:
            // Note that this is a C constant, but not exposed to PHP, so we don't define it here.
            $cost = 10;
            if (isset($options['cost'])) {
                $cost = $options['cost'];
                if ($cost < 4 || $cost > 31) {
                    trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                    return null;
                }
            }
            // The length of salt to generate
            $raw_salt_len = 16;
            // The length required in the final serialization
            $required_salt_len = 22;
            $hash_format = sprintf("$2y$%02d$", $cost);
            break;
        default:
            trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
            return null;
        }
        if (isset($options['salt'])) {
            switch (gettype($options['salt'])) {
            case 'NULL':
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                $salt = (string) $options['salt'];
                break;
            case 'object':
                if (method_exists($options['salt'], '__tostring')) {
                    $salt = (string) $options['salt'];
                    break;
                }
            case 'array':
            case 'resource':
            default:
                trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
                return null;
            }
            if (strlen($salt) < $required_salt_len) {
                trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", strlen($salt), $required_salt_len), E_USER_WARNING);
                return null;
            } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                $salt = str_replace('+', '.', base64_encode($salt));
            }
        } else {
            $buffer = '';
            $buffer_valid = false;
            if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
                $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
                $buffer = openssl_random_pseudo_bytes($raw_salt_len);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && is_readable('/dev/urandom')) {
                $f = fopen('/dev/urandom', 'r');
                $read = strlen($buffer);
                while ($read < $raw_salt_len) {
                    $buffer .= fread($f, $raw_salt_len - $read);
                    $read = strlen($buffer);
                }
                fclose($f);
                if ($read >= $raw_salt_len) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
                $bl = strlen($buffer);
                for ($i = 0; $i < $raw_salt_len; $i++) {
                    if ($i < $bl) {
                        $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                    } else {
                        $buffer .= chr(mt_rand(0, 255));
                    }
                }
            }
            $salt = str_replace('+', '.', base64_encode($buffer));
        }
        $salt = substr($salt, 0, $required_salt_len);

        $hash = $hash_format . $salt;

        $ret = crypt($password, $hash);

        if (!is_string($ret) || strlen($ret) <= 13) {
            return false;
        }

        return $ret;
    }

    /**
     * Implementa una funcionalidad equivalente a password_get_info() de PHP 
     * 5.5: http://us2.php.net/password_get_info
     *
     * @param string $hash
     *     Un hash de contraseña cifrado previamente por hash()
     *
     * @return array
     *     Un array con la informacion sobre el algoritmo utilizado y las 
     *     opciones pasadas al mismo para crear el hash recibido como 
     *     parametro.
     */
    public static function getInfo($hash) {
        $return = array(
            'algo' => 0,
            'algoName' => 'unknown',
            'options' => array(),
        );
        if (substr($hash, 0, 4) == '$2y$' && strlen($hash) == 60) {
            $return['algo'] = PASSWORD_BCRYPT;
            $return['algoName'] = 'bcrypt';
            list($cost) = sscanf($hash, "$2y$%d$");
            $return['options']['cost'] = $cost;
        }
        return $return;
    }

    /**
     * Implementa una funcionalidad equivalente a password_needs_rehash() de 
     * PHP 5.5: http://us3.php.net/password_needs_rehash
     *
     * @param string $hash
     *     Un hash de contraseña cifrado previamente por hash()
     * @param constant $algo
     *     Algoritmo de cifrado (PASSWORD_DEFAULT y PASSWORD_BCRYPT).
     * @param array $options
     *     Opciones para el algoritmo de cifrado.
     *
     * @return boolean
     *     True si el hash proporcionado debe ser recalculado para adaptarse al 
     *     algoritmo y opciones proporcionadas, False en caso contrario.
     */
    public static function needsRehash($hash, $algo, array $options = array()) {
        $info = password_get_info($hash);
        if ($info['algo'] != $algo) {
            return true;
        }
        switch ($algo) {
        case PASSWORD_BCRYPT:
            $cost = isset($options['cost']) ? $options['cost'] : 10;
            if ($cost != $info['options']['cost']) {
                return true;
            }
            break;
        }
        return false;
    }

    /**
     * Implementa una funcionalidad equivalente a password_verify() de PHP 5.5: 
     * http://us3.php.net/function.password_verify
     *
     * @param string $password
     *     La contraseña a comprobar en texto plano.
     * @param string $hash
     *     La contraseña cifrada con hash()
     *
     * @return boolean
     *     True si la contraseña en texto plano y la contraseña cifrada son la 
     *     misma. False en caso contrario.
     */
    public static function verify($password, $hash) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
            return false;
        }
        $ret = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) != strlen($hash) || strlen($ret) <= 13) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < strlen($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }

        return $status === 0;
    }

}

?>
