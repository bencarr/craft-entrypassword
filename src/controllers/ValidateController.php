<?php

namespace bencarr\entrypassword\controllers;

use bencarr\entrypassword\EntryPassword;
use Craft;
use craft\elements\Entry;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use craft\web\Request;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ValidateController extends Controller
{
    public function init()
    {
        parent::init();

        $this->request = Craft::$app->getRequest();
    }

    // Protected Properties
    // =========================================================================

    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws MissingComponentException
     */
    public function actionIndex()
    {
        $this->requirePostRequest();
        $this->requireSiteRequest();

        $request = Craft::$app->getRequest();
        $entryId = $request->getValidatedBodyParam('entryId');
        $password = $request->getRequiredParam('password');

        $entry = Entry::findOne(['id' => $entryId]);
        if (!$entry) {
            throw new NotFoundHttpException('Entry does not exist.');
        }

        $isValid = $entry->getEntryPasswordFieldValue() === $password;
        if (!$isValid) {
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
        if ($this->request->getAcceptsJson()) {
            return $this->asJson([
                'success' => false,
                'errors' => $entry->getErrors(),
            ]);
        }

        Craft::$app->getSession()->setError(Craft::t('entry-password', 'Invalid password.'));

        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * @param Entry $entry
     * @return Response
     * @throws BadRequestHttpException
     */
    protected function returnSuccess(Entry $entry)
    {
        if ($this->request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
                'id' => $entry->id,
            ]);
        }

        return $this->redirectToPostedUrl($entry);
    }

    /** @var Request */
    protected $request;
    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = true;
}
