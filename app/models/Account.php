<?php

class Account extends \HXPHP\System\Model
{
    static $validates_presence_of = [
        [
            'name',
            'message' => 'É obrigatorio a presença do campo usuario.'
        ],
        [
            'password',
            'message' => 'É obrigatorio a presença do campo Senha.'
        ],
        [
            'nome',
            'message' => 'É obrigatorio a presença do campo Apelido.'
        ],
        [
            'email',
            'message' => 'É obrigatorio a presença do campo E-mail.'
        ],
    ];

    static $validates_uniqueness_of = [
        [
            'name',
            'message' => 'Já existe um usuario com este nome de usuario cadastrado.'
        ],
        [
            'email',
            'message' => 'Já existe um usuario com este e-mail cadastrado.'
        ]
    ];

    public static function cadastrar(array $post)
    {
        $userObj = new \stdClass();
        $userObj->user = null;
        $userObj->status = false;
        $userObj->errors = array();

        if($post['password'] != $post['senha2'])
        {
            array_push($userObj->errors, 'Os campos de senha não coinscidem.');
            return $userObj;
        }

        $post['password'] = sha1($post['password']);

        unset($post['senha2']);

        $cadastrar =  self::create($post);

        if($cadastrar->is_valid())
        {
            $userObj->user = $cadastrar;
            $userObj->status = true;
            return $userObj;
        }

        $errors = $cadastrar->errors->get_raw_errors();

        foreach ($errors as $field => $message)
        {
            array_push($userObj->errors, $message[0]);
        }

        return $userObj;
    }

    public static function login(array $post)
    {
        $userObj = new \stdClass();
        $userObj->user = null;
        $userObj->status = false;
        $userObj->code = null;

        $user =  self::find_by_name($post['name']);

        if(!is_null($user))
        {
            $password = sha1($post['password']);


            if ($password === $user->password)
            {
                $userObj->user = $user;
                $userObj->status = true;
            }
            else
                $userObj->code = 'dados-incorretos';
        }
        else
            $userObj->code = 'usuario-inexistente';

        return $userObj;
    }
}