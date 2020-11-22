<?php
    class GdprController extends Controller {
        public function process($param){
            $this->head['title'] = 'Gewrit - Souhlas se zpracováním osobních údajů';
            $this->view = 'gdpr';
        }
    }
?>