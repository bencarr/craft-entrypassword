<?php

namespace bencarr\entrypassword\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\helpers\Json;

class SetPasswordAction extends ElementAction
{
    public function getTriggerLabel(): string
    {
        return Craft::t('entry-password', 'actions.set.trigger');
    }

    public function getTriggerHtml(): void
    {
        $type = Json::encode(static::class);
        $prompt = Json::encode(Craft::t('entry-password', 'actions.set.prompt'));

        $js = <<<EOD
(function() {
    var trigger = new Craft.ElementActionTrigger({
        type: $type,
        batch: true,
        activate: function(\$selectedItems) {
            var data = {
                ids: \$selectedItems.map(function() {
                    return \$(this).data('id');
                }).toArray(),
            };
            
            data.password = prompt($prompt);
            if (data.password == null) {
                return;
            }

            Craft.elementIndex.setIndexBusy();
            
            var handleRename = function(response, textStatus) {
                Craft.elementIndex.setIndexAvailable();

                if (textStatus === 'success') {
                    if (response.success) {
                        Craft.cp.displayNotice(response.message);
                        Craft.elementIndex.updateElements();
                    }

                    if (response.error) {
                        Craft.cp.displayError(response.error);
                    }
                }
            }.bind(this);

            Craft.postActionRequest('entry-password/set-password', data, handleRename);
        }
    });
})();
EOD;

        Craft::$app->getView()->registerJs($js);
    }
}
