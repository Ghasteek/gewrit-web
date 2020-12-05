<?php 
    class IndexController extends Controller {
        public function process ($param){

            $this->data['allItemsCount'] = ContentHandler::getAllItemsCount();
            $this->data['allUserCount'] = UserAdministration::getUserCount();

            $this->head['title'] = 'Gewrit - Váš seznam';
            $this->view = 'index';
        }
    }
?>