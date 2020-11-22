<?php
    class PodminkyUzitiController extends Controller {
        public function process($param){
            $this->head['title'] = 'Gewrit - Podmínky užití';
            $this->view = 'podminky-uziti';
        }
    }
?>