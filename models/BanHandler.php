<?php 
    class BanHandler {
        public function banLessUser($id){
            $currentRights = ($this->getUser($id))[0]['rights'];
            if ($currentRights >= 0) {
                return false; 
            } else {
                $newRights = $currentRights + 1;
                Db::change('users', array('rights' => $newRights), 'WHERE _id = ?', array($id));
                LogHandler::logThis('Changed user *' . $id . '* rights from "' . $currentRights . '" to "' . $newRights . '".');
                return true;
            }
        }

        public function banMoreUser($id){
            $currentRights = ($this->getUser($id))[0]['rights'];
            if ($currentRights <= -2) {
                return false; 
            } else {
                $newRights = $currentRights - 1;
                Db::change('users', array('rights' => $newRights), 'WHERE _id = ?', array($id));
                LogHandler::logThis('Changed user *' . $id . '* rights from "' . $currentRights . '" to "' . $newRights . '".');
                return true;
            }
        }

        public function banUser($name){
            Db::change('users', array('rights' => -1), 'WHERE username = ?', array($name));
            LogHandler::logThis('Banned user "' . $name . '".');
        }

        public function getUser($id){
            return Db::getAll('
                SELECT rights 
                FROM `users`
                WHERE `_id` = ?
            ', array($id)); 
        }

        public function getBans(){
            return Db::getAll('
                SELECT _id, username, email, rights 
                FROM `users`
                WHERE `rights` < 0
                ORDER BY `rights`
                ASC
            '); 
        }
    }
?>