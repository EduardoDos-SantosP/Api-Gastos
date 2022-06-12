<?php

namespace Edsp\ApiGastos\Controllers;

use Illuminate\Support\Collection;

class MovimentacaoController extends Controller
{
    public function todos(): Collection
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

    public function peloId(int $id): ?object
    {
        $conn = $this->getConnection();

        $stmt = $conn->prepare('SELECT * FROM movimentacao WHERE id = ' . $id);
        $stmt->execute();

        $this->stopConnection();

        return ($obj = $stmt->fetchObject()) ? $obj : null;
    }
}