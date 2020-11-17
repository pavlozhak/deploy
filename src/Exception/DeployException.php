<?php

namespace Deploy\Exception;

use Exception;

class DeployException {

    public static function set_exception($class, $message) {
        throw new Exception('Class [' . $class . '] ' . $message);
    }

}
