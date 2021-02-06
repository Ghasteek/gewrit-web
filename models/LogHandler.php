<?php 
    class LogHandler
    {
        // pridani akce do logu
        public function logThis($action, $resultId = " "){
            $time = date("Y-m-d H:i:s");
            $userId = (UserAdministration::getUser())['_id'];
            
            $log = $time . "/" . $userId . "/" . $action . "/" . $resultId . "\n";

            //pokud slozka neexistuje, vytvori ji
            if(!is_dir("log")){
                //Directory does not exist, so lets create it.
                mkdir("log", 0755, true);
            }

            file_put_contents('log/log_'.date("Y.W").'.log', $log, FILE_APPEND);
        }

        // get list of logs in log folder
        public function getLogs(){
            $logFiles = scandir("log/", 1);

            if (($logFiles) && (count($logFiles) >=2)){
                $logFiles = array_splice($logFiles, 0, count($logFiles) -2);
                foreach ($logFiles as $i=>$file) {
                    $logFiles[$i] = substr($file, 0, -4);
                }
            }

            return $logFiles;
        }
    }
?>