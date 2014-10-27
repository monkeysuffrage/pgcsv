<?php
/**
 * PGCSV - A 'pretty good' csv library
 * Website: https://github.com/monkeysuffrage/pgcsv
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @package PGCSV
 * @author P Guardiario <pguardiario@gmail.com>
 * @version 0.1
 */

define("BOM", "\xEF\xBB\xBF");

/**
 * CsvIterator
 * @package PGCSV
 */
class CsvIterator implements Iterator {
  private $position = 0;
  private $array;
  private $headers;
  private $delimiter;
  private $fields;
  private $fh;

  /**
   * Create a CsvIterator
   * @param string $filename
   * @param string $headers does the file have a header row?
   * @param string $delimiter
   */
  public function __construct($filename, $headers = true, $delimiter = ','){
    $this->headers = $headers;
    $this->delimiter = $delimiter;

    if(($this->fh = fopen($filename, "r")) === FALSE) die('CSV - bad file');

    if(fgets($this->fh, 4) != BOM) rewind($this->fh);

    if($this->headers) $this->fields = fgetcsv($this->fh, 0, $this->delimiter);
    $this->next();
  }

  /**
   * Close the file handle
   */
  public function close(){
    if(is_resource($this->fh)) fclose($this->fh);
  }

  /**
   * Not implemented
   */
  public function rewind(){}

  /**
   * Return the corrent row
   * @return array
   */
  public function current(){return $this->row;}

  /**
   * Not implemented
   */
  public function key(){}

  /**
   * Advance to the next row
   */
  public function next(){
    $data = fgetcsv($this->fh, 0, $this->delimiter);
    if($this->headers && $data){
      $this->row = array_combine($this->fields, $data);
    } else {
      $this->row = $data;
    }
  }

  /**
   * True if there is a current row
   * @return bool
   */
  public function valid(){
    if($this->row){
      return true;
    } else {
      $this->close();
      return false;
    }
  }
}

/**
 * CSV
 * @package PGCSV
 */
class CSV{ 
  private $fh;
  private $filename;
  private $fields;
  private $delimiter;
  private $encoding;

  /**
   * Create a CSV
   * @param string $filename
   * @param array $fields
   * @param string $delimiter
   * @param string $encoding
   */
  function __construct($filename, $fields, $delimiter = ',', $encoding = 'utf8'){
    $this->filename = $filename;
    $this->fields = $fields;
    $this->delimiter = $delimiter;
    $this->encoding = $encoding;
    if(!$this->fh = @fopen($filename, 'w')) die("Error opening $filename for writing, is it locked?");
    if('utf8' == $this->encoding) fputs($this->fh, BOM);
    fputcsv($this->fh, $fields, $this->delimiter);
  }

  /**
   * Ensure the file handle is closed
   */
  function __destruct(){
    $this->close();
  }

  /**
   * Close the file handle
   */
  public function close(){
    if(is_resource($this->fh)) fclose($this->fh);
  }

  /**
   * Save an associative array item
   * @param array $item
   */
  public function save($item){
    $arr = array();
    foreach($this->fields as $field){$arr[] = isset($item[$field]) ? $item[$field] : '';}
    fputcsv($this->fh, $arr, $this->delimiter);
  }

  /**
   * Return a new a CsvIterator
   * @param string $filename
   * @param string $headers does the file have a header row?
   * @param string $delimiter
   * @return CsvIterator
   */
  public static function iterate($filename, $headers = true, $delimiter = ','){
    return new CsvIterator($filename, $headers, $delimiter);
  }

  /**
   * Return the entire contents of a file as an array
   * @param string $filename
   * @param string $headers does the file have a header row?
   * @param string $delimiter
   * @return array
   */
  public static function read($filename, $headers = true, $delimiter = ','){
    $ret = array();
    foreach(CSV::iterate($filename, $headers, $delimiter) as $row){
      $ret[] = $row;
    }
    return $ret;
  }
}

?>