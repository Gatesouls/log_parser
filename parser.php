<?php
class Parser
{
    //variables
    private $regex = '/^(\S*).*\[(.*)\]\s"(\S*)\s(\S*)\s([^"]*)"\s(\S*)\s(\S*)(\s(\S*)\s(\S*))?\s"([^"]*)"\s"([^"]*)"\s?$/';
    private $fp; //file pointer
    private $numberOfLines; //number of successfully parsed lines
    private $badLines; //number of failed lines



    //methods

    //Main method:
    public function parse($filename) {

        //opens the given file
        $this->openFile($filename);


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

        //this local array contains info about crawlers from parsed lines
        $crawlersArray = array('Google' => 0, 'Bing' => 0, 'Baidu' => 0, 'Yandex' => 0);



        //reads file line by line, parses it, takes info from it
        while (!feof($this->fp)) {
            $line = fgets($this->fp);

            $parsedItemsArray = $this->parseLine($line); // this is now array with parsed items inside of it
            if ($parsedItemsArray !== false) {
                $this->numberOfLines++; //increments the number of successfully parsed lines
                $statusCode = $parsedItemsArray[6]; //contains a status code from parsed line
                $newURL = $parsedItemsArray[4]; //contains a URL from parsed line
                $crawler = $this->findCrawler($parsedItemsArray[12]); //contains name of Crawler from parsed line

                //checks whether a url from parsed line is already in local array, if not, increments the count of unique URL
                //and adds the url to usedURL array
                if (!in_array($newURL, $usedURL)) {
                    $statArray['urls']++;
                    $usedURL[] = $newURL;
                }

                $statArray['traffic'] += $parsedItemsArray[7]; //adds traffic number from parsed line to the total traffic number

                // checks if $crawler is not false and increments number of crawlers in the array
                if ($crawler !== false) {
                    if (array_key_exists($crawler, $crawlersArray)) {
                        $crawlersArray[$crawler]++;
                    }
                }

                //checks whether a status code from parsed line is not yet in local array, else: increments count of the existing code
                if (array_key_exists($statusCode, $codesArray)) {
                    $codesArray[$statusCode]++;
                } else {
                    $codesArray[$statusCode] = 1;
                }

            } else {
                //if the line is empty or not valid, parser ignores it
                $this->badLines++;
            }
        }
        //countes number of parsed lines
        $statArray['views']= $this->numberOfLines;
        
        //merging all arrays into one
        $arrayFinal = array ('Stats' => $statArray, 'Crawlers' => $crawlersArray, 'StatusCodes' => $codesArray);
        $json = json_encode($arrayFinal);
        $this->closeFile();
        return $json;
    }

    private function findCrawler ($line) {

        $validCrawlers = array ('Google', 'Bing', 'Baidu', 'Yandex'); // an array of valid crawlers

        foreach ($validCrawlers as $value) {
            if (strpos($line, $value) !== false) {
                return $value;
            }
        }

    }


    private function openFile ($file) {
        $path = __DIR__;
        $file = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($file)) {
            $this->fp = fopen($file, 'r');
        } else {
            die('File not found');
        }

    }
    private function closeFile () {
        return fclose($this->fp);
    }

    private function findMatches ($line) {
        preg_match($this->regex, $line, $matches);
        return $matches;
    }
    private function parseLine ($line) {
        $logs = $this->findMatches($line);
        //checks if all of crucial elements in array are present
        if (!$logs[4] || !$logs[6] || !$logs[7] || !$logs[12]) {
            return false;

        } else {
            return $logs;
        }
    }

} //end of Class
