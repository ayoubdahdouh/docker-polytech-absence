<?php

function _post($url)
{
    // $ch = curl_init();

    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $result = curl_exec($ch);

    // curl_close($ch);

    // return $result;

    $cookieFile = "cookies.txt";
    if (!file_exists($cookieFile)) {
        $fh = fopen($cookieFile, "w");
        fwrite($fh, "");
        fclose($fh);
    }

    if (isset($_FILES) && !empty($_FILES)) {
        $file = $_FILES["fileToUpload"];
        $cf = new CURLFile(realpath($file["tmp_name"]), $file["type"], $file["name"]);
        $_POST["_FILES"] = $cf;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); // Cookie aware
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); // Cookie aware
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
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

function showa($a)
{
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}
