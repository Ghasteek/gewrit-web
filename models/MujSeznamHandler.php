<?php 
    // trida poskytuje metody pro spravu zaznamu
    class MujSeznamHandler{

        /*// vrati vsechny zaznamy
        public function getAllMyRecords($owner){
            return Db::getAll('
                SELECT * 
                FROM `records`
                WHERE `owner` = ?
            ', $id);
        }*/

        //ulozi zaznam do db
        public function saveRecord($id, $record){
            //print_r($record);
            if (!$id){
                try {
                    Db::insert('records', $record);
                } catch (PDOException $error) {
                    throw new UserError('Tohle již sledujete.');
                }
            } else {
                Db::change('records', $record, 'WHERE _id = ?', array($id));
            }
        }

        //odstrani zaznam z db
        public function deleteRecord($id){
            Db::getChanges('
                DELETE FROM `records`
                WHERE _id = ?', 
                array($id));
        }

        /*// vrati pozadovanou stranu zaznamu podle parametru
        public function getRecordsPaginate($offset, $itemsPerPage){
            $user = UserAdministration::getUser();
            $id = array($user['_id']);
            $id = array(implode(', ', $id));
            $sql = "SELECT * 
                FROM `records` 
                WHERE `owner` = ? 
                ORDER BY timestamp DESC
                LIMIT " . $itemsPerPage . " OFFSET " . $offset;
            return Db::getAll($sql, $id);
        }*/

        // vrati pozadovanou stranu zaznamu podle parametru typu zadaneho v parametru
        public function getRecordsByTypePaginate($offset, $itemsPerPage, $type, $status = ""){
            $parameters = $type;
            $user = UserAdministration::getUser();
            $id = array($user['_id']);
            array_unshift($parameters , $id[0]);

            $statusQuerry = $this->getStatusQuerry($status);

            $i=0;
            foreach ($type as $one) {
                if ($one == 'releaseDay'){
                    $releaseDay = " AND `releaseDay` LIKE :releaseDay ";
                    unset($type[$i]);
                    $parameters[$i + 1] = "%" . date('N') . "%";
                } else {
                    if ($i != 0) {$types = $types .  ", "; };
                    $types = $types . ":type" . $i ;
                }
                $i++;
            }

            $sql = "SELECT * 
                FROM `records` 
                WHERE `owner` = :own AND
                `type` IN (" . $types . ")" . $releaseDay . $statusQuerry . "
                ORDER BY timestamp DESC
                LIMIT " . $itemsPerPage . " OFFSET " . $offset;
            /*echo $querry . "<br>";
            print_r($parameters);*/
            return Db::getAll($sql, $parameters);
        }

        /*// vrati pocet zaznamu od aktualniho uzivatele
        public function getRecordsCount(){
            $user = UserAdministration::getUser();
            $id = array($user['_id']);
            return Db::getAll('
                SELECT COUNT(*)
                FROM `records`
                WHERE `owner` = ?
            ', $id);
        }*/

        // vrati pocet zaznamu od aktualniho uzivatele jen zadaneho typu
        public function getRecordsByTypeCount($type, $status = ""){
            $parameters = $type;
            $user = UserAdministration::getUser();
            $id = array($user['_id']);
            array_unshift($parameters , $id[0]);

            $statusQuerry = $this->getStatusQuerry($status);

            $i=0;
            foreach ($type as $one) {
                if ($one == 'releaseDay'){
                    $releaseDay = " AND `releaseDay` LIKE :releaseDay";
                    unset($type[$i]);
                    $parameters[$i+1] = "%" . date('N') . "%";
                } else {
                    if ($i != 0) {$types = $types .  ", "; };
                    $types = $types . ":type" . $i ;
                }
                $i++;
            }

            $querry = "SELECT COUNT(*)
                FROM `records`
                WHERE `owner` = :own AND
                `type` IN (" . $types . ")
                " . $releaseDay . $statusQuerry;
            
            return Db::getAll($querry, $parameters);
        }

        // vrati querry pro vyber statusu
        public function getStatusQuerry($status){
            $statusArray = array("sleduji" => 1,
                "pozastaveno" => 2,
                "odlozeno" => 3,
                "dokonceno" => 4,
                "nesledovat" => 5,
                "planovane" => 6);

            if (($status) && ($status != 'all')){
                $statusQuerry = " AND `status` = " . $statusArray[$status];
            } else {
                $statusQuerry = "";
            }
            return $statusQuerry;
        }

        // kontrola, jestli je zaznam s ID v parametru muj
        public function isMine($id){
            $user = UserAdministration::getUser();
            $owner = $user['_id'];
            $count = Db::getAll('
                SELECT COUNT(*)
                FROM `records`
                WHERE `owner` = ? AND
                `_id` = ?
                ', array($owner, $id));
            if ($count[0][0] == 0) {
                throw new UserError('Tento záznam není Váš!');
            }
        }

        // existuje jiz zaznam
        public function exist($type, $item){
            $user = UserAdministration::getUser();
            $owner = $user['_id'];
            $count = Db::getAll('
                SELECT COUNT(*)
                FROM `records`
                WHERE `owner` = ? AND
                `type` = ? AND
                `item` = ?
                ', array($owner, $type, $item));
            if ($count[0][0] != 0) {
                throw new UserError('Již je ve vašem seznamu.');
            }
        }

        // pridej jednu episodu / kapitolu k zaznamu v ID
        public function pridejJeden($id){
            $querry = "UPDATE records
                SET episode = episode + 1
                WHERE _id = ?";
            Db::getChanges($querry, array($id));
        }


        // vrati zaznamy s dnesnim dnem vydani a v seznamu "sleduji"
        public function getTodayReleasedRecords() {
            $user = UserAdministration::getUser();
            $id = array($user['_id']);
            $date = "%" . date('N') . "%";
            $parameters = array($id[0], $date);
            $sql = "SELECT * 
                FROM `records` 
                WHERE (`owner` = :own) AND
                (`releaseDay` LIKE :releaseDay) AND
                (`status` = 1)
                ORDER BY timestamp DESC";
            return Db::getAll($sql, $parameters);
        }


        //vrati pocet, kolikrat je serial/anime/manga v necich zaznamech (zatim jen pro ucely editoru, mozna casem i nekde jinde)
        public function inListCount($type, $id){
            $sql = "SELECT COUNT(*)
                FROM `records`
                WHERE `type` = ? 
                AND `item` = ?";
            $count = Db::getAll($sql, array($type, $id));
            return $count[0][0];
        }

    }
?>