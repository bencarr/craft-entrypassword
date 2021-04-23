<?php

namespace bencarr\entrypassword\controllers;

use Craft;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\web\Controller;
use Throwable;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SetPasswordController extends Controller
{
    /**
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();
        
        $request = Craft::$app->getRequest();
        $ids = $request->getRequiredBodyParam('ids');
        $password = $request->getRequiredBodyParam('password');
        
        if (empty($ids)) {
            throw new BadRequestHttpException(Craft::t('entry-password', 'actions.set.empty'));
        }
        
        $entries = Entry::find()->id($ids)->all();
        $updatedCount = 0;
        $errorCount = 0;
        foreach ($entries as $entry) {
            if ($field = $entry->getEntryPasswordField()) {
                $entry->setFieldValue($field->handle, $password);
                if (Craft::$app->elements->saveElement($entry)) {
                    $updatedCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }
        
        $message = [];
        if ($updatedCount) {
            $message[] = Craft::t('entry-password', 'actions.set.success', [
                'count' => $this->getCountText($updatedCount),
            ]);
        }
        
        if ($errorCount) {
            $message[] = Craft::t('entry-password', 'actions.set.error', [
                'count' => $this->getCountText($errorCount),
            ]);
        }
        
        return $this->asJson([
            'success' => true,
            'message' => implode(' ', $message),
        ]);
    }
    
    protected function getCountText(int $count): string
    {
        return $count === 1
            ? Craft::t('app', '1 entry')
            : Craft::t('app', '{num} entries', [
                'num' => $count
            ]);
    }
}
