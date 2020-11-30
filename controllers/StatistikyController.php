<?php
    class StatistikyController extends Controller {
        public function process($param){
            $this->head['title'] = 'Gewrit - Statistiky portálu';
            $this->view = 'statistiky';
        }
    }
?>