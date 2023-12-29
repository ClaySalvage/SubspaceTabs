<?php

// use MediaWiki\MediaWikiServices;

// $services = MediaWikiServices::getInstance();
// $namespaceInfo = $services->getNameSpaceInfo();
// $namespaces = $namespaceInfo->getCanonicalNamespaces();
// var_dump($namespaceInfo);

define("BRK", " ");

$endPoint = "http://www.virtualwongery.com/w/api.php";
// $endPoint = "https://www.wongery.com/w/api.php";
$params = [
    "action" => "query",
    "format" => "json",
    "meta" => "siteinfo",
    "siprop" => "namespaces"
];
$url = $endPoint . "?" . http_build_query($params);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// COMMENT THE FOLLOWING OUT FOR PRODUCTION VERSION
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$output = curl_exec($ch);
if (curl_errno($ch)) echo "<h1>ERROR :" . curl_error($ch) . "</h1>";
curl_close($ch);

function filter($ns)
{
    // return true;
    $var = $ns["id"];
    return ($var > 0) && !($var & 1);
}

// echo $output;
$result = array_filter(json_decode($output, true)["query"]["namespaces"], "filter");

// function children($id) {
//     $set = [];
//     foreach($result as $child)
//     {
//         if ($result.$parent === $id)
//     }
// }

// $top = ["0"];
$top = [];

foreach ($result as &$ns) {
    // $ns["doo"] = "dah";
    $explode = explode(BRK, $ns["canonical"]);
    if (count($explode) < 2) {
        if ($ns["id"] > 14) $top[] = $ns["id"];
        continue;
    }
    $parentString = implode(BRK, array_slice($explode, 0, count($explode) - 1));
    //    var_dump($parent);
    $parent = null;
    foreach ($result as &$prospect) {
        if ($prospect["canonical"] === $parentString) {
            $parent = $prospect["id"];
            $prospect["children"][] = $ns["id"];
            break;
        }
    }
    // var_dump($parent);
    if ($parent === null)
        $top[] = $ns["id"]; // Whoops!  This will never happen!
    else
        $ns["parent"] = $parent;
}

// "config": {
//     "RandomRulesCategories": {
//       "value": {
//         "list": [],
//         "include": false
//       },
//       "path": false,
//       "description": "Array of categories to include in or exclude from random page selection",
//       "descriptionmsg": "randomrules-config-randomexcludecategories",
//       "public": true
//     },

echo '"config": {' . "\n";
echo '  "SubspaceTabsChildren": {' . "\n";
echo '     "value" : {' . "\n";
foreach ($result as &$ns) {
    if (!array_key_exists("children", $ns)) continue;
    echo '      "';
    echo $ns["id"];
    echo '": [';
    echo implode(", ", $ns["children"]);
    echo "],\n";
}
echo '      "0": [' . implode(", ", $top) . "]\n";

echo "     },\n";
echo '     "path":false,' . "\n";
echo '     "description": "List by ids of child namespaces for each parent namespace",' . "\n";
echo '     "descriptionmsg": "subspacetabs-config-children",' . "\n";
echo '     "public": true,' . "\n";
echo '     "merge_strategy": "array_plus",' . "\n";
echo "   }\n";
echo "}\n";
// var_dump($result);
