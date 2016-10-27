<?php

class ContasController extends \HXPHP\System\Controller
{
    public function __construct($configs)
    {
        parent::__construct($configs);

        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);

    }

    public function cadastrarAction()
    {
        $this->view->setPath('index');
        $this->view->setFile('index');

        $this->request->setCustomFilters([
            'email' => FILTER_VALIDATE_EMAIL
        ]);

        $post = $this->request->post();

        if(!empty($post))
        {
            $cadastrarUsuario = Account::cadastrar($post);

            if($cadastrarUsuario->status == false)
            {
                $this->load('Helpers\Alert', [
                    'danger',
                    'Ops! Não foi possivel efetuar seu cadastro, verifique os erros abaixo:',
                    $cadastrarUsuario->errors
                ]);
            }
            else
            {
                $this->auth->login($cadastrarUsuario->user->id,  $cadastrarUsuario->user->name);
            }
        }

    }

    public function logarAction()
    {
        $this->view->setPath('index');
        $this->view->setFile('index');

        $post = $this->request->post();

        if(!empty($post))
        {
            $login = Account::login($post);

            if($login->status === true)
            {
                $this->auth->login($login->user->id, $login->user->name);

            }
            else
            {
                $this->load('Modules\Messages','auth');
                $this->messages->setBlock('alerts');
                $error = $this->messages->getByCode($login->code);
                $this->load('Helpers\Alert', $error);
            }
        }
    }

    public function solicitarredAction()
    {
        $this->view->setPath('index')->setFile('index');

        $this->load('Modules\Messages', 'password-recovery');
        $this->messages->setBlock('alerts');
        $error = null;

        $this->request->setCustomFilters([
            'email' => FILTER_VALIDATE_EMAIL
        ]);

        $email = $this->request->post('email');

        if(!is_null($email) && $email!==false)
        {
            $validar = Recoverie::validar($email);

            if($validar->status === false)
            {
                $error = $this->messages->getByCode($validar->code);
            }
            else
            {
                $this->load('Services\PasswordRecovery',$this->configs->site->url . $this->configs->baseURI . 'contas/redefinir/');
                Recoverie::create([
                    'accounts_id' => $validar->conta->id,
                    'token' => $this->passwordrecovery->token,
                    'status' => 0
                ]);
                $message = $this->messages->messages->getByCode('link-enviado',[
                    'message' => [
                        $validar->conta->name,
                        $this->passwordrecovery->link,
                        $this->passwordrecovery->link
                    ]
                ]);
                $this->load('Services\Email');
                $envioEmail = $this->email->send(
                    $validar->conta->email,
                    $message['subject'],
                    $message['message'],
                    [
                        'email' => $this->configs->mail->from_mail,
                        'remetente' => $this->configs->mail->from
                    ]
                );
                if(!$envioEmail)
                {
                    $error = $this->messages->getByCode('email-nao-enviado');
                }
            }
        }

        if(!is_null($error))
        {
            $this->load('Helpers\Alert', $error);
        }
        else
        {
            $sucesso = $this->messages->getByCode('link-enviado');
            $this->load('Helpers\Alert', $sucesso);
        }

    }

    public function redefinirAction($token)
    {

    }

    public function alterarSenhaAction($token)
    {

    }
}