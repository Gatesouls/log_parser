<?php
//$file = fopen('logs.txt', 'r');
//private $log = '84.242.208.111- - [11/May/2013:06:31:00 +0200] "POST /chat.php HTTP/1.1" 200 354 "http://bim-bom.ru/" "Mozilla/5.0 (Windows NT 6.1; rv:20.0) Gecko/20100101 Firefox/20.0"';

class Parser
{
    private $regex = '/^(\S*).*\[(.*)\]\s"(\S*)\s(\S*)\s([^"]*)"\s(\S*)\s(\S*)\s"([^"]*)"\s"([^"]*)"$/';
    private $fp;



    //methods
    public function parse($filename) {
        $this->openFile($filename);
        while (!feof($this->fp)) {
            $line=fgets($this->fp);
            echo "This is a pure line from log: $line<br><br>";
            echo 'This is a parsed line from log:';
            $this->formatLine($line);
            echo '<br><br><br>';

        }
    $this->closeFile();
    }

private function openFile ($file) {
    $this->fp=fopen($file, 'r');

}
private function closeFile () {
        return fclose($this->fp);
}

private function findMatches ($line) {
preg_match($this->regex, $line, $matches);
return $matches;
}
private function formatLine ($line) {
        $logs = $this->findMatches($line);
        if (isset($logs)) {
            foreach ($logs as $key => $value) {
                echo "$key => $value<br>";
            }
        } else {
            return false;
        }
}



} //end of Class

$parser = new Parser;
$parser->parse('logs.txt');



