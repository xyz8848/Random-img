<?php
error_reporting(0);
header('content-type:application/json');
header('Access-Control-Allow-Credentials:true');
// header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Origin:http://xyz8848.com');

const config = [
    "modelLocalDomain" => "http://xyz8848.com/",
];

$model = $_GET['model'] ?? "local";
if ($model !== "local" && $model !== "link") {
    exit(json_encode([
        "code" => 100,
        "msg" => "Invalid model data."
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

switch ($model) {
    case "local":
        $pathDir = "/";
        if (!is_dir($pathDir)) {
            exit(json_encode([
                "code" => 100,
                "msg" => "Invalid pathDir data."
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $library = $_GET['library'] ?? null;
        if (null !== $library) {
            if (!is_dir($pathDir . $library)) {
                exit(json_encode([
                    "code" => 100,
                    "msg" => "Invalid library data. For more information see http://docs.xyz8848.com/api/randomImg#library "
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
            $pathDir = $pathDir . "/" . $library;
        }

        $type = $_GET['type'] ?? "all";
        if (null !== $type) {
            if ($type !== "pc" && $type !== "pe" && $type !== "all") {
                exit(json_encode([
                    "code" => 100,
                    "msg" => "Invalid type data. For more information see http://docs.xyz8848.com/api/randomImg#type "
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }
        $fileData = [];
        switch ($type) {
            case "pc":
                $pathDir = $pathDir . "/pc/";
                break;
            case "pe":
                $pathDir = $pathDir . "/pe/";
                break;
        }

        if ($type != "all") {
            $fileData = glob($pathDir . "{*.jpg,*.png,*.gif}", GLOB_NOSORT | GLOB_BRACE);
        } else {
            $pcData = glob($pathDir . "/pc/" . "{*.jpg,*.png,*.gif}", GLOB_NOSORT | GLOB_BRACE);
            $peData = glob($pathDir . "/pe/" . "{*.jpg,*.png,*.gif}", GLOB_NOSORT | GLOB_BRACE);
            $fileData = array_merge($pcData, $peData);
        }

        if (empty($fileData)) {
            exit(json_encode([
                "code" => 100,
                "msg" => "Error! There are no pictures here"
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        $randNum = array_rand($fileData, 1);

        $return = $_GET['return'] ?? null;
        switch ($return) {
            case "view":
                header('Content-Type: image/png');
                echo(file_get_contents(config['modelLocalDomain'] . "/" . $fileData[$randNum]));
                break;
            case "json":
                exit(json_encode([
                    "code" => 200,
                    "url" => config['modelLocalDomain'] . "/" . $fileData[$randNum]
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            default:
                header("Location: " . config['modelLocalDomain'] . "/" . $fileData[$randNum]);
        }
        break;
    case "link":
        exit("External link random URL, under development...");
        break;
}
