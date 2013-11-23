<?php

namespace database;

class FileStoreDAO implements DAO
{

    private $filePath;

    public function __construct()
    {
        $this->filePath = \components\Configuration::getValue("store_file", "path");

        if (!file_exists($this->filePath))
            $this->createFile(\components\Configuration::getValue("store_file", "root"));
    }

    private function createFile($rootElem)
    {
        $xml = new \DOMDocument();
        $xml->appendChild($xml->createElement($rootElem));

        $xml->formatOutput = true;
        $xml->save($this->filePath);

        $this->insert(["commission" => \components\Configuration::getValue("store_file", "default_commission")]);
    }

    public function insert($data)
    {
        $xml = new \DOMDocument();
        $xml->load($this->filePath);
        $xml_root = $xml->documentElement;

        foreach ($data as $key => $value) {
            $elem = $xml->createElement($key);
            $text = $xml->createTextNode($value);
            $elem->appendChild($text);
            $xml_root->appendChild($elem);
        }

        $xml->formatOutput = true;
        $xml->save($this->filePath);
    }

    public function update($data, $where = null)
    {
        $xml = new \DOMDocument();
        $xml->load($this->filePath);

        foreach ($data as $key => $value)
            $xml->getElementsByTagName($key)->item(0)->nodeValue = $value;

        $xml->formatOutput = true;
        $xml->save($this->filePath);

        return true;
    }

    public function select($data, $where = null)
    {
        $xml = new \DOMDocument();
        $xml->load($this->filePath);

        $result = array();
        foreach ($data as $key)
            $result[$key] = $xml->getElementsByTagName($key)->item(0)->nodeValue;

        return $result;
    }

    public function delete($where)
    {
        $xml = new \DOMDocument();
        $xml->load($this->filePath);
        $xml_root = $xml->documentElement;

        foreach ($where as $key) {
            foreach ($xml_root->getElementsByTagName($key) as $elem)
                $xml_root->removeChild($elem);
        }

        $xml->formatOutput = true;
        $xml->save($this->filePath);
    }

    public function query($statement)
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

}

?>
