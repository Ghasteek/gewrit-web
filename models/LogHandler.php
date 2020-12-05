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

            file_put_contents('log/log_'.date("W.Y").'.log', $log, FILE_APPEND);
        }
    }
?>