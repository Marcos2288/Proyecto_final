<?php
require_once __DIR__ . "/../config/bootstrap.php";

header("Content-Type: application/json; charset=UTF-8");

$q = trim($_GET["q"] ?? "");
$section = $_GET["section"] ?? "";

if ($q === "") {
    echo json_encode([]);
    exit();
}

$like = "%" . $q . "%";
$data = [];
$contentService = getContentService();
$productService = getProductService();

if ($section === "cine") {
    $data = $contentService->searchContentByTitle($like, 10);
    foreach ($data as &$item) {
        $item["url"] = "content_detail.php?id=" . $item["id"];
    }
} elseif ($section === "tienda") {
    $data = $productService->searchProductsByName($like, 10);
    foreach ($data as &$item) {
        $item["url"] = "product_detail.php?id=" . $item["id"];
    }
}

echo json_encode($data);
