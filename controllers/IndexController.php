<?php 
    class IndexController extends Controller {
        public function process ($param){
            $this->head['title'] = 'Gewrit - Váš seznam';
            $this->view = 'index';
        }
    }
?>