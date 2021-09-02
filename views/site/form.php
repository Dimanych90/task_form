<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model app\models\Form */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;


$this->title = 'Forms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>


    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form', 'method' => 'post']); ?>

            <!--                --><? //= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

            <?php $form->field($model, 'user_id')->hiddenInput() ?>

            <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>


            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'form button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <?php foreach ($forms as $item): ?>


        <div class="row">
            <div class="col-lg-5">

                <?= DetailView::widget([
                    'model' => $item,
                    'attributes' => [

                        [
                            'label' => 'Пользователь',
                            'attribute' => 'user_id',
                            'value' => function ($row) {
                                $user = \app\models\User::find()->where(['id' => $row->user_id])->one();
                                return $user->username ?? '--';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'label' => 'Статус',
                            'value' => function ($row) {
                                return \app\models\Form::$statusLabel[$row->status];
                            },

                            //нужно что то придумать чтобы менять статус.
                            // и он через ajax менял данные в таблице

                        ],
                        [
                            'attribute' => 'text',
                            'label' => 'Текст',
                            'value' => function ($row) {
                                return $row->text ?? '--';
                            }
                        ],

                    ],

                ]) ?>
            </div>
        </div>

    <?php endforeach; ?>



