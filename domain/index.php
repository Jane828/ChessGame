<?php
$uri =urldecode(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
$url = trim(file_get_contents("/wwwroot/domain/domain.txt"), PHP_EOL);
header("Cache-Control: max-age=5");
header('Location: http://'.$url.$uri);
