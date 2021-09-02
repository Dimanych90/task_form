<?php

namespace app\controllers;

use app\models\Form;
use app\models\Register;
use app\models\User;
use Yii;
use yii\base\Request;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
     */
    public function actionIndex()
    {
        return $this->render('index');
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

            return $this->redirect(['form']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $model = new Register();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $user = new User();

            $user->username = $model->username;
            $user->password = Yii::$app->security->generatePasswordHash($model->password);
            $user->email = $model->email;
            $user->phone = $model->phone;
            $user->status = 10;
            $user->generateAuthKey();
            $user->created_at = $user->updated_at = time();
//
            if ($user->save()) {
                Yii::$app->user->switchIdentity($user);

                Yii::$app->session->setFlash('success', 'You successfully registered');
                return $this->redirect(['form']);
            }


        }

        return $this->render('register', [
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
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    //Регистрируемся или авторизуемся
    // пишем задачи и они сохраняются в порядке убывания
    public function actionForm()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('success', 'You need to login or register at first');
            return $this->redirect(['login']);
        }
        $model = new Form();

        $userId = Yii::$app->user->id;

        $user = Yii::$app->user->identity;

        $forms = Form::find()->where(['user_id' => $userId])->orderBy('id DESC')->all();


        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post('Form');


            if (isset($post)) {

                $model->user_id = $userId;
                $model->text = $post['text'];
                $model->status = 0;
                $model->created_at = $model->updated_at = time();

                if ($model->save()) {

                    Yii::$app->session->setFlash('success', 'your form is saved');
                    return $this->redirect(['form']);
                }
            }

        }

        return $this->render('form', [
            'model' => $model,
            'user' => $user,
            'forms' => $forms,
        ]);
    }

}
