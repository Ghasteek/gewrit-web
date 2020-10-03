<?php 
    class ReportyHandler {
        public function getModReports(){
            return Db::getAll("
                SELECT * 
                FROM `report`
                WHERE `reportType` != ('uzivatel')
                AND `status` = ('open')
                ORDER BY `_id`
                DESC
                "); 
        }

        public function getAllReports(){
            return Db::getAll('
                SELECT * 
                FROM `report`
                ORDER BY `_id`
                DESC
            '); 
        }

        public function getReports($condition){
            $querry = "SELECT * FROM `report` WHERE " . $condition . " ORDER BY `_id` DESC";
            return Db::getAll($querry); 
        }

        public function getReport($id){
            return Db::getAll('
            SELECT * 
            FROM `report`
            WHERE `_id` = ?
        ', $id);
        }

        //
        public function saveReport($id, $answer){
            Db::change('report', $answer, 'WHERE _id = ?', array($id));
        }
    }
?>