<?php

require('modules/class.admodule.php');
class NexFoundationThemeHooks extends Gdn_Plugin {
    /**
     *
     * @param Gdn_Controller $sender The object calling this method.
     */
    public function base_render_before($sender) {
        // Fetch the currently enabled locale (en by default)
        $adModule = new AdModule();
        $sender->addModule($adModule);
    }

    public function base_Register_handler($sender) {
        // die;
    }
}
?>
