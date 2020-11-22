<?php
    class CookiesController extends Controller {
        public function process($param){
            $this->head['title'] = 'Gewrit - Cookies';
            $this->view = 'cookies';
        }
    }
?>