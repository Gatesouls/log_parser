<?php

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



