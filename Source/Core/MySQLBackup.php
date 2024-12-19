<?php

namespace Source\Core;

class MySQLBackup {
    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct() {
        $host = CONF_DB_HOST;
        $username = CONF_DB_USER;
        $password = CONF_DB_PASS;
        $database = "test";//CONF_DB_NAME;
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

   public function backup($backupFilePath) {
        try {
            $tables = $this->pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            $backupSql = "";

            foreach ($tables as $table) {
                // Dump structure
                $createTableStmt = $this->pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
                $backupSql .= "\n\n" . $createTableStmt['Create Table'] . ";\n\n";

                // Dump data
                $rows = $this->pdo->query("SELECT * FROM `$table`");
                while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                    $columns = array_map(function($value) {
                        return "`$value`";
                    }, array_keys($row));

                    $values = array_map([$this->pdo, 'quote'], array_values($row));
                    $backupSql .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
                }
                $backupSql .= "\n";
            }
            $backupFileName = 'Angomanege_' . date('Y-m-d_H-i-s') . '.sql';

              // Força o download do arquivo
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backupFileName));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($backupSql));

            echo $backupSql;
            exit;

            file_put_contents($backupFilePath, $backupSql);
            echo "Backup realizado com sucesso em: " . $backupFilePath;

        } catch (PDOException $e) {
            echo "Erro ao realizar o backup: " . $e->getMessage();
        }
    }

    public function restore($backupFilePath) {
        try {
            $sql = file_get_contents($backupFilePath);
            $this->pdo->exec($sql);
            echo "Banco de dados restaurado com sucesso a partir de: " . $backupFilePath;

        } catch (PDOException $e) {
            echo "Erro ao restaurar o banco de dados: " . $e->getMessage();
        }
    }
}

