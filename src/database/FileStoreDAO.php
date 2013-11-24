<?php

namespace database;

/**
 * DAO para el acceso al fichero de parametros de tienda donde se establecera 
 * la comision obtenida por la misma (y mas posibles parametros futuros). 
 * Utilizara para ello un fichero XML en la ruta especificada en el fichero de 
 * configuracion (seccion "store_file", parametro "path").
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class FileStoreDAO implements DAO
{

    private $filePath;  // ruta al fichero XML

    /**
     * Construye el DAO de aceso al fichero XML. Se encarga de crear el fichero 
     * si no existe.
     */
    public function __construct()
    {
        // carga la ruta desde el fichero de configuracion
        $this->filePath = \components\Configuration::getValue("store_file", "path");

        // si el fichero no existe, lo crea
        if (!file_exists($this->filePath))
            $this->createFile(\components\Configuration::getValue("store_file", "root"));
    }

    /**
     * Metodo privado para la creacion del fichero XML y la insercion de datos 
     * por defecto (solo la comision por ahora).
     *
     * @param String $rootElem
     *     Elemento raiz del fichero XML a crear.
     */
    private function createFile($rootElem)
    {
        // crea el fichero XML y establece $rootElem como elemento raiz del 
        // mismo
        $xml = new \DOMDocument();
        $xml->appendChild($xml->createElement($rootElem));

        // guarda el fichero en disco
        $xml->formatOutput = true;
        $xml->save($this->filePath);

        // inserta la comision por defecto, establecida en el fichero de 
        // configuracion
        $this->insert(["commission" => \components\Configuration::getValue("store_file", "default_commission")]);
    }

    /**
     * Implementa la insercion de datos de la interfaz DAO.
     *
     * @see DAO
     */
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

    /**
     * Implementa la modificacion de datos de la interfaz DAO.
     *
     * @see DAO
     */
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

    /**
     * Implementa la consulta de datos de la interfaz DAO.
     *
     * @see DAO
     */
    public function select($data, $where = null)
    {
        $xml = new \DOMDocument();
        $xml->load($this->filePath);

        $result = array();
        foreach ($data as $key)
            $result[$key] = $xml->getElementsByTagName($key)->item(0)->nodeValue;

        return $result;
    }

    /**
     * Implementa la eliminacion de datos de la interfaz DAO.
     *
     * @see DAO
     */
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

    /**
     * Las consultas arbitrarias de la interfaz DAO no son aplicables sobre el 
     * fichero de parametros de tienda. Se lanzara un error si se intenta 
     * ejecutar este metodo.
     *
     * @see DAO
     */
    public function query($statement)
    {
        trigger_error("No aplicable", E_USER_ERROR);
    }

}

?>
