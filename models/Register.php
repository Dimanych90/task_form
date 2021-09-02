<?php

namespace app\models;


use yii\base\Model;

class Register extends Model
{

    public $username;
    public $password;
    public $phone;
    public $email;


    public function rules()
    {
        return [
            [['username', 'password', 'email', 'phone'], 'required', 'message' => 'Заполните поле'],
//            ['username', 'unique', 'targetClass' => User::className(),  'message' => 'Этот логин уже занят']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'email' => 'email',
            'phone' => 'Телефон',
        ];
    }


}