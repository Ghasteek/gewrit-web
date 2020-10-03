<?php 
    // trida poskytuje metody pro spravu serialu
    class CekarnaHandler{

        // vrati vsechny serialy k autorizaci
        public function getSerialsToAck(){
            return Db::getAll('
                SELECT * 
                FROM `serial`
                WHERE `onlyFor` != 0
            ');
        }

        // vrati vsechny anime k autorizaci
        public function getAnimesToAck(){
            return Db::getAll('
                SELECT * 
                FROM `anime`
                WHERE `onlyFor` != 0
            ');
        }

        // vrati vsechny anime k autorizaci
        public function getMangasToAck(){
            return Db::getAll('
                SELECT * 
                FROM `manga`
                WHERE `onlyFor` != 0
            ');
        }

    }
?>