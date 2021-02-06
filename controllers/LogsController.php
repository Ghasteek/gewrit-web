<?php 
    class LogsController extends Controller {
        public function process ($param){
            // jen pro admina
            $this->authenticateUser(2);

            $logHandler = new LogHandler();

            if ($_POST){

                $file = fopen("log/" . $_POST["file"] . ".log", "r");

                $data = [];
                if ($file){
                    while (($row = fgets($file, 4096)) !== false){

                        $row = explode("/", $row);

                        array_push($data, $row);

                    }
                    if (!feof($file)){
                        echo "Error!\n";
                    }
                    fclose($file);
                    $this->data["data"] = $data;
                }

            }

            
            $this->data['logFiles'] = $logHandler->getLogs();

            $this->head['title'] = 'Gewrit - Prohlížeč LOGů';
            $this->view = 'logs';
        }
    }
?>