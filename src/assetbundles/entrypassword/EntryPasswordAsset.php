<?php

namespace bencarr\entrypassword\assetbundles\entrypassword;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EntryPasswordAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@bencarr/entrypassword/assetbundles/entrypassword/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/EntryPassword.css',
        ];

        parent::init();
    }
}
