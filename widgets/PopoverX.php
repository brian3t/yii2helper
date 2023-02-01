<?php
namespace soc\yii2helper\widgets;
class PopoverX extends \kartik\popover\PopoverX
{
    /**
     * Registers the needed assets
     * BN 5/10/20 uncomment $this->toggleButton, so that events are registered using
     *         'pluginEvents' => [
    "click.target.popoverX"=>'function() { console.log("click.target.popoverX"); }',
    "load.complete.popoverX"=>'function() { log("load.complete.popoverX"); }',
    ]
     */
    public function registerAssets()
    {
        $view = $this->getView();
        \kartik\popover\PopoverXAsset::register($view);
//        if ($this->toggleButton === null) {
            $this->registerPlugin($this->pluginName);
//        }
    }

}
