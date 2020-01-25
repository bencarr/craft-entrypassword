<?php

namespace bencarr\entrypassword\controllers;

use bencarr\entrypassword\EntryPassword;
use Craft;
use craft\elements\Entry;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use craft\web\Request;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ValidateController extends Controller
{
    /** @var Request */
    protected $request;

    protected $allowAnonymous = true;

    public function init()
    {
        parent::init();

        $this->request = Craft::$app->getRequest();
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $this->requirePostRequest();
        $this->requireSiteRequest();

        $request = Craft::$app->getRequest();
        if ($entryId = $request->getParam('entryId')) {
            $entry = Entry::findOne(['id' => $entryId]);
        } else {
            $entry = Entry::find()->uri($request->fullPath)->one();
        }

        if (!$entry || !$entry->isPasswordProtected()) {
            throw new NotFoundHttpException('Entry does not exist.');
        }

        $password = $request->getRequiredParam('password');
        $isValid = $entry->entryPasswordFieldValue === $password;
        if (!$isValid) {
            $entry->addError('password', 'Invalid password.');
            return $this->returnError($entry);
        }

        EntryPassword::getInstance()->cookie->set($entry);

        return $this->returnSuccess($entry);
    }

    /**
     * @param Entry $entry
     * @return Response|null
     * @throws MissingComponentException
     */
    protected function returnError(Entry $entry)
    {
        $message = Craft::t('entry-password', 'Invalid password.');

        if ($this->request->getAcceptsJson()) {
            return $this->asErrorJson($message)->setStatusCode(400);
        }

        Craft::$app->getSession()->setError($message);

        return null;
    }

    /**
     * @param Entry $entry
     * @return Response
     * @throws BadRequestHttpException
     */
    protected function returnSuccess(Entry $entry)
    {
        if ($this->request->getAcceptsJson()) {
            return $this->asJson([
                'id' => $entry->id,
            ]);
        }

        return $this->redirectToPostedUrl($entry);
    }
}
