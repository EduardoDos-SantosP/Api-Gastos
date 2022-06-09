<?php

namespace Edsp\ApiGastos\Controllers;

use Illuminate\Support\Collection;
use PDO;

class MovimentacaoController extends Controller
{
    public function consulteTodos(): Collection
    {
        $conn = $this->getConnection();

        $stmt = $conn->prepare('SELECT * FROM movimentacao');
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetchObject())
            $result[] = $row;

        $this->stopConnection();

        return new Collection($result);
    }
}