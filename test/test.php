<?php

/*
    Create a MockModule to load our module into for testing.
*/

class MockModule {
    public $exports = array();
}
$module = new MockModule();

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
            assert($result === null);
        });

        it("should return GET /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-1.php");
            assert($result === "GET /");
        });

        it("should return POST /", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-2.php");
            assert($result === "POST /");
        });

        it("should return GET /page.html", function () {
            $fixtures = __DIR__ . "/fixtures";
            $result = \php_require\rewrite_routes\readRouteFromFile($fixtures . "/file-3.php");
            assert($result === "GET /page.html");
        });
    });
});