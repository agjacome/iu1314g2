<?php

namespace database;

class FileStoreDAO implements DAO
{

    private $filePath;

    public function __construct()
    {
        $this->filePath = \components\Configuration::getValue("store_file", "path");
        $this->rootElem = \components\Configuration::getValue("store_file", "root");

        if (!file_exists($this->filePath))
            $this->createFile();
    }

    private function createFile()
    {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;

        $xml->appendChild($xml->createElement($this->rootElem));

        $xml->save($this->filePath);
    }

    public function insert($data)
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function update($data, $where)
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function select($where)
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function delete($where)
    {
        trigger_error("Aun no implementado", E_USER_ERROR);
    }

    public function query($statement)
    {
        trigger_error("No tiene sentido una consulta arbitraria sobre un fichero XML", E_USER_ERROR);
    }

}

?>
