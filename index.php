<?php
namespace php_require\rewrite_routes;

function createApacheRewrite($route) {

	$rule = "";

	// Get the method and path from the given route array.
	list($method, $url) = explode(" ", trim($route["route"]), 2);
	$filename = $route["filename"];

	// Remove the front slash
	$regex = ltrim($url, "/");

	// Swap * with (.*)
	$regex = str_replace("*", "(.*)", $regex);

	// Wrap the regex so it has to be an exact match.
	$regex = "^" . $regex . "$";

	// Make the rewrite rule.
	$rule = "RewriteCond %{REQUEST_METHOD} " . strtoupper($method) . "\n";
	$rule.= "RewriteCond %{REQUEST_FILENAME} !-f\n";
	$rule.= "RewriteRule " . $regex . " " . $filename . " [L,QSA]\n";

	// Return the rule.
	return $rule;
}

function writeHtaccessFile($dir, $routes) {

	$docroot = dirname($_SERVER["PHP_SELF"]);
	$filename = $dir . DIRECTORY_SEPARATOR . ".htaccess";
	$rules = array();

	foreach ($routes as $route) {
		array_push($rules, createApacheRewrite($route));
	}

	if (file_exists($filename)) {
		unlink($filename);
	}

	file_put_contents($filename, "RewriteEngine On\nRewriteBase " . $docroot . "\n\n" . implode("\n", $rules));
}

function readRouteFromFile($filename) {

	$marker = "@route";

	if (!file_exists($filename)) {
		return null;
	}

	$content = file_get_contents($filename);

	$start = strpos($content, $marker);

	if ($start === false) {
		return null;
	}

	$start = $start + strlen($marker);
	$end = strpos($content, "\n", $start);

	$routeString = trim(substr($content, $start, $end - $start));

	return $routeString;
}

$module->exports = function ($dir, $type) {

	$routes = array();

	if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if (strpos($entry, ".php") !== false) {
            	$filename = $dir . DIRECTORY_SEPARATOR . $entry;
                $route = readRouteFromFile($filename);
                if ($route) {
                	array_push($routes, array("route" => $route, "filename" => $filename));
                }
            }
        }
        closedir($handle);
    }

    if ($type === ".htaccess") {
    	return writeHtaccessFile($dir, $routes);
    } else if ($type === "httpd.conf") {
    	return null;
    }
};
