<?php

class MangasValidator {
    const TIPO_MANGA = 'MANGA';
    const TIPO_NOVEL = 'NOVEL';

    const STATUS_EM_ANDAMENTO = 'Em andamento';
    const STATUS_CONCLUIDO = 'Concluído';
    const STATUS_HIATO = 'Hiato';
    const STATUS_CANCELADO = 'Cancelado';
    const STATUS_NAO_INICIADO = 'Não iniciado';

    static function TiposValidos(): array {
        return [self::TIPO_MANGA, self::TIPO_NOVEL];
    }

    static function StatusValidos(): array {
        return [
            self::STATUS_EM_ANDAMENTO,
            self::STATUS_CONCLUIDO,
            self::STATUS_HIATO,
            self::STATUS_CANCELADO,
            self::STATUS_NAO_INICIADO,
        ];
    }

    static function ValidarTipo($Tipo){
        return in_array($Tipo, self::TiposValidos(), true);
    }

    static function ValidarStatus($Status){
        return in_array($Status, self::StatusValidos(), true);
    }
}

?>
