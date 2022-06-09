<?php

namespace Edsp\ApiGastos\Models;

use DateTime;

class Movimentacao extends Model
{
    public string $nome;
    public string $descricao;
    public DateTime $date;
    public float $valor;
}