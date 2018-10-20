<?php
//$file = fopen('example.log', 'r');
//private $log = '84.242.208.111- - [11/May/2013:06:31:00 +0200] "POST /chat.php HTTP/1.1" 200 354 "http://bim-bom.ru/" "Mozilla/5.0 (Windows NT 6.1; rv:20.0) Gecko/20100101 Firefox/20.0"';

class Parser
{
    //variables
    private $regex = '/^(\S*).*\[(.*)\]\s"(\S*)\s(\S*)\s([^"]*)"\s(\S*)\s(\S*)(\s(\S*)\s(\S*))?\s"([^"]*)"\s"([^"]*)"\s?$/';
    private $fp;



    //methods

    //Main method:
    public function parse($filename) {

        //opens the given file
        $this->openFile($filename);

        //local variable, counts number of lines in file
        $numberOfLines = 0;
        //local variable, used to collect URLs from parsed lines
        $usedURL= array();

        //this local array is used to collect main info from parsed lines
        $statArray = array(
            'views' =>0,
            'urls'=>0,
            'traffic'=>0
        );
        //this local array collects status codes from parsed lines
        $codesArray = array();

        //this local array contains info about browsers from parsed lines
        $crawlersArray = array();

        //reads line by line, parses it, takes info from it
        while (!feof($this->fp)) {
            $line=fgets($this->fp);
            $numberOfLines++;

            $parsedItemsArray = $this->parseLine($line); // this is now array with parsed items inside of it
            $statusCode = $parsedItemsArray[6]; //contains a status code from parsed line
            $newURL = $parsedItemsArray[11]; //contains a URL from parsed line
            $crawler = $parsedItemsArray[12]; //contains name of Browser from parsed line

            //checks whether a url from parsed line is already in local array, if not, increments the count of unique URL
            //and adds the url to usedURL array
            if (!in_array($newURL, $usedURL)){
                $statArray['urls']++;
                $usedURL[] = $newURL;
            }
            $statArray['traffic'] += $parsedItemsArray[7]; //adds traffic number from parsed line to the total traffic

            //
            if (array_key_exists($crawler, $crawlersArray)) {
                $crawlersArray[$crawler]++;
            } else {
                $crawlersArray[$crawler] = 1;
            }

            //checks whether a status code from parsed line is already in local array, else: increments count of the existing code
            if (array_key_exists($statusCode, $codesArray)) {
                $codesArray[$statusCode]++;
            } else {
                $codesArray[$statusCode]=1;
            }

        }
        //sets views to be equal to total number of lines
        $statArray['views'] = $numberOfLines;
        
        echo 'Main info:<br>';
        foreach ($statArray as $key => $value) {
            echo "$key : $value,<br>";
        }
        echo 'Crawlers:<br>';
        foreach ($crawlersArray as $key => $value) {
            echo "$key : $value,<br>";
        }

        echo 'Status Codes:<br>';
        foreach ($codesArray as $key => $value) {
            echo "$key : $value,<br>";
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
    private function parseLine ($line) {
        $logs = array();
        $logs = $this->findMatches($line);
        if (isset($logs)) {
            return $logs;

        } else {
            return false;
        }
    }



} //end of Class

$parser = new Parser;
$parser->parse('logs.txt');



