<?php

$module = new stdClass();

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../index.php");

/*
    Now we test it.
*/

describe("php-rewrite", function () use ($module) {

    describe("readRouteFromFile()", function () {

        it("should return null", function () {
            $result = \php_require\php_rewrite\readRouteFromFile("foo");
            assert(count($result) === 0);
        });

        it("should return GET /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\php_rewrite\readRouteFromFile($fixtures . "/file-1.php");
            assert($result[0] === "GET /");
        });

        it("should return POST /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\php_rewrite\readRouteFromFile($fixtures . "/file-2.php");
            assert($result[0] === "POST /");
        });

        it("should return GET /page.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\php_rewrite\readRouteFromFile($fixtures . "/file-3.php");
            assert($result[0] === "GET /page.html");
        });

        it("should return GET|POST|DELETE /page.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\php_rewrite\readRouteFromFile($fixtures . "/multi-1.php");

            sort($result);

            assert($result[0] === "DELETE /page.html");
            assert($result[1] === "GET /page.html");
            assert($result[2] === "POST /page.html");
        });

        it("should return an empty array", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\php_rewrite\readRouteFromFile($fixtures . "/bad.php");
            assert(count($result) === 0);
        });
    });

    describe("readRouteFromFile()", function () {

        it ("should return a full list", function () {
            $fixtures = __DIR__ . "/fixtures";
            $results = \php_require\php_rewrite\findFiles($fixtures);
            assert(count($results) === 7);
        });

        it ("should return GET /sub.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $results = \php_require\php_rewrite\findFiles($fixtures);

            usort($results, function ($a, $b) {
                if ($a["route"] == $b["route"]) {
                    return 0;
                }
                return ($a["route"] < $b["route"]) ? -1 : 1;
            });

            // print_r($results);

            assert($results[4]["route"] === "GET /sub.html");
        });
    });

    describe("createApacheRewrite()", function () {

        it ("should return [RewriteRule ^$ /foo/bar]", function () {

            $route = array(
                "route" => "GET /",
                "filename" => "/foo/bar"
            );

            $result = \php_require\php_rewrite\createApacheRewrite($route);
            // echo $result;
            assert(strpos($result, "RewriteRule ^$ /foo/bar [L,QSA]") !== false);
        });

        it ("should return [RewriteCond %{REQUEST_METHOD} POST]", function () {

            $route = array(
                "route" => "POST /",
                "filename" => "/foo/bar"
            );

            $result = \php_require\php_rewrite\createApacheRewrite($route);
            // echo $result;
            assert(strpos($result, "RewriteCond %{REQUEST_METHOD} POST") !== false);
        });

        it ("should return [RewriteCond %{REQUEST_FILENAME} !-f]", function () {

            $route = array(
                "route" => "DELETE /foo/bar",
                "filename" => "/foo/bar"
            );

            $result = \php_require\php_rewrite\createApacheRewrite($route);
            // echo $result;
            assert(strpos($result, "RewriteCond %{REQUEST_FILENAME} !-f") !== false);
        });
    });

    describe("writeHtaccessFile()", function () {

        it ("should return []", function () {

            $fixtures = __DIR__ . "/fixtures";

            $routes = array(
                array(
                    "route" => "GET /",
                    "filename" => "/foo"
                ),
                array(
                    "route" => "POST /foo",
                    "filename" => "/foo/bar"
                ),
                array(
                    "route" => "DELETE /foo/bar",
                    "filename" => "/foo/bar/baz"
                )
            );

            file_put_contents($fixtures . "/.htaccess", "null");

            $result = \php_require\php_rewrite\writeHtaccessFile($fixtures, $routes);

            $htaccess = file_get_contents($fixtures . "/.htaccess");

            assert(unlink($fixtures . "/.htaccess"));
            assert($result === strlen($htaccess));
            assert(strpos($htaccess, "^$ /foo") !== false);
            assert(strpos($htaccess, "^foo$ /foo/bar") !== false);
            assert(strpos($htaccess, "^foo/bar$ /foo/bar/baz") !== false);
        });
    });

    describe("writeHtaccessFile()", function () use ($module) {

        it ("should return [file in bytes]", function () use ($module) {

            $rewrite = $module->exports;

            $fixtures = __DIR__ . "/fixtures";
            $type = ".htaccess";

            $result = $rewrite($fixtures, $type);
            $htaccess = file_get_contents($fixtures . "/.htaccess");

            assert(unlink($fixtures . "/.htaccess"));
            assert($result === strlen($htaccess));
        });

        it ("should return [file in bytes]", function () use ($module) {

            $rewrite = $module->exports;

            $fixtures = __DIR__ . "/fixtures";
            $type = "httpd.conf";

            $result = $rewrite($fixtures, $type);

            assert($result === null);
        });
    });
});
