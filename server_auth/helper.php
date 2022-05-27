<?php

function _post($url)
{
    $fields = $_POST;

    $postvars = '';
    $sep = '';
    foreach ($fields as $key => $value) {
        $postvars .= $sep . urlencode($key) . '=' . urlencode($value);
        $sep = '&';
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

function modify_post($a)
{
    if (count($a) == 1) {
        $_POST["req"] = "";
    } else {
        unset($a[0]);
        $_POST["req"] = implode("_", $a);
    }
}

function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function sendMessage($data)
{
    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);
    die;
}

function sendError($reason)
{
    echo json_encode([
        'status' => 'error',
        'data'   => ['reason' => $reason]
    ]);
    die;
}
