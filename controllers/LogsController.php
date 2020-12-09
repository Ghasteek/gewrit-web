<?php 
    class LogsController extends Controller {
        public function process ($param){
            // jen pro admina
            $this->authenticateUser(2);

            $logsHandler = new LogsHandler();

            
            $this->data['logFiles'] = $logsHandler->getLogs();

            $this->head['title'] = 'Gewrit - Prohlížeč LOGů';
            $this->view = 'logs';
        }
    }
?>