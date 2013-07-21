<?
define("BOM", "\xEF\xBB\xBF");

class CsvIterator implements Iterator {
    private $position = 0;
    private $array;
    private $headers;
    private $delimiter;
    private $fields;
    private $fh;

    public function __construct($filename, $headers = true, $delimiter = ','){
      $this->position = 0;
      $this->array = array();
      $this->headers = $headers;
      $this->delimiter = $delimiter;

      if(($this->fh = fopen($filename, "r")) === FALSE) die('CSV - bad file');

      if(fgets($this->fh, 4) != BOM) rewind($this->fh);

      if($this->headers) $this->fields = fgetcsv($this->fh, 0, $this->delimiter);
      $this->next();
//      fclose($this->fh);
    }

    function rewind(){}
    function current(){return $this->row;}
    function key(){}
    function next(){
      $data = fgetcsv($this->fh, 0, $this->delimiter);
      if($this->headers && $data){
        $this->row = array_combine($this->fields, $data);
      } else {
        $this->row = $data;
      }
    }
    function valid(){return !!$this->row;}
}

class CSV{ 
  var $fh, $filename, $fields, $delimiter, $encoding;

  function __construct($filename, $fields, $delimiter = ',', $encoding = 'utf8'){
    $this->filename = $filename;
    $this->fields = $fields;
    $this->delimiter = $delimiter;
    $this->encoding = $encoding;
    $this->fh = fopen($filename, 'w');
    if('utf8' == $this->encoding) fputs($this->fh, BOM);
    fputcsv($this->fh, $fields, $this->delimiter);
  }

  function __destruct(){
    $this->close();
  }

  function close(){
    if(is_resource($this->fh)) fclose($this->fh);
  }

  function save($item){
    $arr = array();
    foreach($this->fields as $field){$arr[] = isset($item[$field]) ? $item[$field] : '';}
    fputcsv($this->fh, $arr, $this->delimiter);
  }

  public static function iterate($filename, $headers = true, $delimiter = ','){
    return new CsvIterator($filename, $headers, $delimiter);
  }

}
?>