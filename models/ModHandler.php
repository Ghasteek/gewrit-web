<?php 
    class ModHandler {
        public function promoteUser($id){
            $currentRights = $this->getUser($id);
            if ($currentRights[0]['rights'] == 2) {
                return false; 
            } else {
                $newRights = $currentRights[0]['rights'] + 1;
                Db::change('users', array('rights' => $newRights), 'WHERE _id = ?', array($id));
                return true;
            }
        }

        public function degradeUser($id){
            $currentRights = $this->getUser($id);
            if ($currentRights[0]['rights'] == 0) {
                return false; 
            } else {
                $newRights = $currentRights[0]['rights'] - 1;
                Db::change('users', array('rights' => $newRights), 'WHERE _id = ?', array($id));
                return true;
            }
        }

        public function promoteToMod($name){
            Db::change('users', array('rights' => 1), 'WHERE username = ?', array($name));
        }

        public function getUser($id){
            return Db::getAll('
                SELECT rights 
                FROM `users`
                WHERE `_id` = ?
            ', array($id)); 
        }

        public function getMods(){
            return Db::getAll('
                SELECT _id, username, email, rights 
                FROM `users`
                WHERE `rights` > 0
                ORDER BY `rights`
                DESC
            '); 
        }
    }
?>