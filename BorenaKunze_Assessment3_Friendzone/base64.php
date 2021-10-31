<?php
/*
 * We need base 64 to safely store the session token in a cookie.
 * Only a subset of ascii characters are allowed in a cookie
 */
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}
?>