<?php 
    class IndexController extends Controller {
        public function process ($param){

            $this->data['allItemsCount'] = (ContentHandler::getAllItemsCount())[0][0];
            $this->data['allUserCount'] = (UserAdministration::getUserCount())[0][0];

            $this->head['title'] = 'Gewrit - Váš seznam';
            $this->view = 'index';
        }
    }
?>