<?php

namespace Source\Core;


 class Backup {
    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct($host, $username, $password, $database) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }


    public function exportarDB($arquivo)
    {
        $dir=dirname(__DIR__,2)."/Backup/";

       
       if (!is_dir( $dir)) {
            mkdir($dir,0755);
       }

       
       $outputFilePath=time().mb_strstr($arquivo,".");
       $this->exportDatabase($outputFilePath);
    }

    private function exportDatabase($outputFilePath) {
        $conn = new \mysqli($this->host, $this->username, $this->password, $this->database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $tables = [];
        $result = $conn->query("SHOW TABLES");

        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

        $sql = "-- MySQL Database Backup\n";
        $sql .= "-- Created: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $result = $conn->query("SELECT * FROM $table");
            $numFields = $result->field_count;

            $sql .= "-- Table structure for table `$table`\n";
            $sql .= "CREATE TABLE IF NOT EXISTS `$table` (\n";

            for ($i = 0; $i < $numFields; $i++) {
                $fieldInfo = $result->fetch_field();
                $sql .= "  `" . $fieldInfo->name . "` " . $fieldInfo->type;

                if (!empty($fieldInfo->length)) {
                    $sql .= "(" . $fieldInfo->length . ")";
                }

                if ($fieldInfo->flags & MYSQLI_NOT_NULL_FLAG) {
                    $sql .= " NOT NULL";
                }

                if ($fieldInfo->def) {
                    $sql .= " DEFAULT '" . $fieldInfo->def . "'";
                }

                if ($i < $numFields - 1) {
                    $sql .= ",";
                }

                $sql .= "\n";
            }

            $sql .= ");\n\n";

            while ($row = $result->fetch_row()) {
                $sql .= "INSERT INTO `$table` VALUES (";

                for ($i = 0; $i < $numFields; $i++) {
                    $sql .= "'" . $conn->real_escape_string($row[$i]) . "'";

                    if ($i < $numFields - 1) {
                        $sql .= ",";
                    }
                }

                $sql .= ");\n";
            }

            $sql .= "\n";
        }

        $conn->close();

        file_put_contents($outputFilePath, $sql);

        echo "Database export successful.\n";
    }
}

// Uso da classe
/*$host = "localhost";
$username = "seu_usuario";
$password = "sua_senha";
$database = "seu_banco_de_dados";

$importExport = new DatabaseImportExport($host, $username, $password, $database);

// Exportar o banco de dados
$outputFilePath = "caminho/para/arquivo.sql";
$importExport->exportDatabase($outputFilePath);

// Importar o banco de dados
$inputFilePath = "caminho/para/arquivo.sql";
$importExport->importDatabase($inputFilePath);*/
