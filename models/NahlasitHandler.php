<?php 
    class NahlasitHandler {
        public function saveReport($type, $reportForId, $text, $author){
            try {
                Db::insert('report', array('reportForId' => $reportForId, 'reportType' => $type, 'text' => $text, 'author' => $author));
            } catch (PDOException $error) {
                throw new UserError('Reportování se pokazilo, zkuste to znovu.');
            }
        }
    }
?>