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
    });
});