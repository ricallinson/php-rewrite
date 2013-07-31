<?php
use \php_require\rewrite_routes;

$module = new stdClass();

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../index.php");

/*
    Now we test it.
*/

describe("php-rewrite", function () {

    describe("readRouteFromFile()", function () {

        it("should return null", function () {
            $result = \php_require\rewrite_routes\readRouteFromFile("foo");
            assert(count($result) === 0);
        });

        it("should return GET /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-1.php");
            assert($result[0] === "GET /");
        });

        it("should return POST /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-2.php");
            assert($result[0] === "POST /");
        });

        it("should return GET /page.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-3.php");
            assert($result[0] === "GET /page.html");
        });

        it("should return GET|POST|DELETE /page.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/multi-1.php");

            sort($result);

            assert($result[0] === "DELETE /page.html");
            assert($result[1] === "GET /page.html");
            assert($result[2] === "POST /page.html");
        });
    });

    describe("readRouteFromFile()", function () {

        it ("should return a full list", function () {
            $fixtures = __DIR__ . "/fixtures";
            $results = \php_require\rewrite_routes\findFiles($fixtures);
            assert(count($results) === 7);
        });

        it ("should return GET /sub.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $results = \php_require\rewrite_routes\findFiles($fixtures);

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
});
