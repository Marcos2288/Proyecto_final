<?php
declare(strict_types=1);

$url = $_GET["url"] ?? "";

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit("Invalid image URL");
}

$scheme = parse_url($url, PHP_URL_SCHEME);

if (!in_array($scheme, ["http", "https"], true)) {
    http_response_code(400);
    exit("Unsupported image URL");
}

$headers = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36",
    "Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
    "Accept-Language: es-ES,es;q=0.9,en;q=0.8",
    "Referer: https://www.google.com/"
];

if (function_exists("curl_init")) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_ENCODING => "",
    ]);

    $data = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: "image/jpeg";
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($data === false || $status >= 400) {
        http_response_code(502);
        exit("Image could not be loaded".($error ? ": ".$error : ""));
    }
} else {
    $context = stream_context_create([
        "http" => [
            "timeout" => 12,
            "header" => implode("\r\n", $headers)
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false
        ]
    ]);

    $data = @file_get_contents($url, false, $context);
    $contentType = "image/jpeg";

    if ($data === false) {
        http_response_code(502);
        exit("Image could not be loaded");
    }
}

$contentType = strtolower(trim(explode(";", (string) $contentType)[0]));

if (strncmp($contentType, "image/", 6) !== 0) {
    $contentType = "image/jpeg";
}

header("Content-Type: ".$contentType);
header("Cache-Control: public, max-age=86400");
header("X-Content-Type-Options: nosniff");
echo $data;
