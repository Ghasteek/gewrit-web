<?php 
    class LogsHandler {
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