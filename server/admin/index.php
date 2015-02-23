<?php
    $realm = 'eserrure';
    $nonce = uniqid();
    $digest = getDigest();

    if (is_null($digest)) requireLogin($realm,$nonce);
    $digestParts = digestParse($digest);

    $ini_array = parse_ini_file("../config.ini", true)['webadmin'];
    $validUser = $ini_array['login'];
    $validPass = $ini_array['password'];

    $A1 = md5("{$validUser}:{$realm}:{$validPass}");
    $A2 = md5("{$_SERVER['REQUEST_METHOD']}:{$digestParts['uri']}");

    $validResponse = md5("{$A1}:{$digestParts['nonce']}:{$digestParts['nc']}:{$digestParts['cnonce']}:{$digestParts['qop']}:{$A2}");

    if ($digestParts['response']!=$validResponse) requireLogin($realm,$nonce);

    header('location:manage.php');

    function getDigest() {

        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $digest = $_SERVER['PHP_AUTH_DIGEST'];

        } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']),'digest')===0)
              $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
        }

        return $digest;
    }

    function requireLogin($realm,$nonce) {
        header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . $nonce . '",opaque="' . md5($realm) . '"');
        header('HTTP/1.0 401 Unauthorized');
        echo (file_get_contents('view/login.html'));
        die();
    }

    function digestParse($digest) {

        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', $digest, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $data[$m[1]] = $m[2] ? $m[2] : $m[3];
            unset($needed_parts[$m[1]]);
        }
        return $needed_parts ? false : $data;
    }

?>