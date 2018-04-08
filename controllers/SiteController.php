<?php

namespace app\controllers;

use app\models\ChartSeries;
use app\services\BalanceStackCounter;
use app\services\TransactionTableParser;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $model = DynamicModel::validateData(['file'], [['file', 'file', 'extensions' => 'html']]);

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstance($model, 'file');
            $series = $this->getSeries($file);

            if (empty($series)) {
                $this->goHome();
            }

            return $this->render('chart', [
                'series' => $series,
            ]);
        }

        return $this->render('index', ['model' => $model]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function getSeries(UploadedFile $file): array
    {
        if (!file_exists($file->tempName)) {
            return [];
        }

        // TODO: Example
//        $file = file_get_contents(\Yii::getAlias('@app/example.html'));
        $file = file_get_contents($file->tempName);
        $dom = new \DOMDocument();
        $dom->loadHTML($file);
        $parser = new TransactionTableParser($dom);
        $balanceStackCounter = new BalanceStackCounter($parser->run());
        $stack = $balanceStackCounter->run();
        $series = [];

        foreach ($stack as $name => $data) {
            $chartSeries = new ChartSeries();
            $chartSeries->setAttributes(compact('name', 'data'));

            if (!$chartSeries->validate()) {
                unset($chartSeries);
                continue;
            }

            $series[] = $chartSeries->toArray();
        }

        return $series;
    }
}
