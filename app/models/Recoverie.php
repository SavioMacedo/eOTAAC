<?php
class Recoverie extends \HXPHP\System\Model
{
    public static function validar($uemail)
    {
        $retorno = new \stdClass();
        $retorno->conta = null;
        $retorno->code = null;
        $retorno->status = false;

        $uemail_valid = Account::find_by_email($uemail);

        if(!is_null($uemail_valid))
        {
            $retorno->status = true;
            $retorno->conta = $uemail_valid;

            self::delete_all([
                'conditions' => [
                    'accounts_id = ?',
                    $uemail_valid->id
                ]
            ]);
        }
        else
        {
            $retorno->code = 'nenhum-usuario-encontrado';
        }

        return $retorno;
    }
}