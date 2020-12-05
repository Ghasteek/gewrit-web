<?php
    class StatistikyController extends Controller {
        public function process($param){
            $userAdministration = new UserAdministration();
            $contentHandler = new ContentHandler();

            $this->data['totalUsers'] = $userAdministration->getUserCount();
            $this->data['totalMods'] = $userAdministration->getUserCount( array("1", "2") );
            $this->data['totalBannedUsers'] = $userAdministration->getUserCount( array("-1", "-2") );
            $this->data['totalRecords'] = MujSeznamHandler::getAllRecordsCount();

            $this->data['totalSerial'] = $contentHandler->getAllItemsCount('serial');
            $this->data['totalAnime'] = $contentHandler->getAllItemsCount('anime');
            $this->data['totalManga'] = $contentHandler->getAllItemsCount('manga');

            $this->head['title'] = 'Gewrit - Statistiky portálu';
            $this->view = 'statistiky';
        }
    }
?>