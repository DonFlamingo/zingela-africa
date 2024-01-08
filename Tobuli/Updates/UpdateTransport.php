<?php namespace Tobuli\Updates;

trait UpdateTransport {
    private $update = [];

    public function __construct($update) {
        $this->update = $update;
    }

    public function setUp() {
        return 'OK';
    }
}