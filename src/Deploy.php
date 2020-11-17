<?php

namespace Deploy;

use Deploy\Handlers\Git;

class Deploy {

    public static function run() {
        $git = new Git();
        $git->update();
    }

}
