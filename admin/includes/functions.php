<?php
function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
