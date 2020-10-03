<?php
class AdModule extends Gdn_Module {
    public function assetTarget() {
        return 'Panel';
    }

    public function toString() {
        return '<div id="nex-advertisement"></div>';
    }
}
?>