<?php

namespace Edsp\ApiGastos\Controllers;

use Exception;
use PDO;
use Throwable;

abstract class Controller
{
    private ?PDO $connection = null;
    private string $dsn;
    private string $user;
    private string $pass;
    private bool $controllerStarted = false;

    public function getConnection(): PDO
    {
        $dbConfigFile = $_SERVER['DOCUMENT_ROOT'] . '/api-gastos/db.config.ini';

        if (!$this->controllerStarted) {
            $this->controllerStarted = true;
            try {
                $dbConfig = parse_ini_file($dbConfigFile);
                if (!$dbConfig)
                    throw new Exception("Não foi possível acessar o arquivo $dbConfigFile!");
                $this->dsn = $dbConfig['dsn'];
                $this->user = $dbConfig['user'];
                $this->pass = $dbConfig['pass'];
            } catch (Throwable $e) {
                throw new Exception("Erro ao acessar as configurações do banco de dados!", previous: $e);
            }
        }

        if (!$this->connection)
            $this->connection = new PDO($this->dsn, $this->user, $this->pass);

        return $this->connection;
    }

    public function stopConnection(): void
    {
        $this->connection = null;
    }
}