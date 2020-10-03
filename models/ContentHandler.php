<?php 
    // trida poskytuje metody pro spravu polozek v db
    class ContentHandler{

        // vrati polozku ze zadane tabulky s danym ID
        public function getContent($table, $id){
            $sql = "SELECT * 
                FROM $table
                WHERE `_id` = ?";
            return Db::getAll($sql, array($id));
        }

        // vrati polozku se zadanym ID, pokud je moje
        public function getContentIfMine($table, $id){
            $user = UserAdministration::getUser();
            $sql = "SELECT * 
                FROM $table
                WHERE `_id` = ? AND
                (`onlyFor` = 0 OR `onlyFor` = ?)";
            return Db::getAll($sql, array($id, $user['_id']));
        }

        // vrati zadany pocet nejnovejsich pridanych polozek
        public function getNewestContent($table, $howMany){
            $sql = "SELECT *
                FROM $table
                WHERE onlyFor = 0
                ORDER BY _id DESC
                LIMIT $howMany";
            return Db::getAll($sql);
        }

        // vrati polozky schvalene a od autora, jehoz ID je v parametru y tabulky uvedene v parametru s offsetem a poctem na stranku taky z parametru
        public function getContentByAuthorPaginate($table, $id, $offset, $itemsPerPage, $searchString = ''){
            if (strlen($searchString) > 0) { $searchQuerry = $this->getSearchQuerry($searchString);}
            $sql = "SELECT * 
                FROM $table 
                WHERE (`onlyFor` = 0 OR `onlyFor` = ?) ". $searchQuerry . 
                " ORDER BY name " .
                "LIMIT " . $itemsPerPage . " OFFSET " . $offset;
            //echo "data: " . $sql . "<br>"; // debug vypis
            return Db::getAll($sql, array($id));
        }

        // vrati POCET polozek schvalenych a od autora, jehoz ID je v parametru z tabulky zadane v parametru
        public function getContentByAuthorCount($table, $id, $searchString = ''){
            if (strlen($searchString) > 0) { $searchQuerry = $this->getSearchQuerry($searchString);}
                // pokud je v seearchquerry mezera, vytvori se zprava
            if ($searchQuerry === ' ') { Controller::addMessage("Vyhledávání podporuje jen slova delší než 3 znaky.", "info");  }
            $sql = "SELECT COUNT(*)
                FROM $table
                WHERE (`onlyFor` = 0 OR `onlyFor` = ?)" . $searchQuerry;
            //echo "count: " . $sql . "<br>"; // debug vypis
            return Db::getAll($sql, array($id));
        }

        // ze stringu hledani vytvori doplnek hledani do querry
        public function getSearchQuerry($searchString){
                // rozdelit na pole podle mezery a odstranit slova kratsi nez 3
            $words = array_filter(explode(' ', $searchString), function($item){if(strlen($item) > 2) return $item;}); 
            
            if (count($words) == 0){ return " "; } // pokud neni zadne slovo delsi nez 3 znaky posle mezeru, ktera se zpracuje na zpravu u COUNTu
            $querry = " AND (";
            $i=1;
            $lastItem = count($words);
            foreach ($words as $i => $word){
                $i++;
                $querry .= "name LIKE CONCAT('%', '$word', '%')";
                if ($i != $lastItem) $querry .= " OR ";
            }
            $querry .= ")"; 
            return $querry;
        }

        //pokud poslu ID, zmeni stavajici obsah, pokud neposlu ID, ulozi novy
        public function saveContent($table, $id, $content){
            if (!$id){
                try {
                    Db::insert($table, $content);
                } catch (PDOException $error) {
                    throw new UserError('Již je v databázi.');
                }
            } else {
                Db::change($table, $content, 'WHERE _id = ?', array($id));
            }
        }

        //smaze ze zadane TABULKY zadane ID
        public function deleteContent($table, $id){
            $sql = "DELETE FROM $table
                WHERE _id = ?";
            Db::getChanges($sql, array($id));
        }

        /*
        *******************************************************************
        *** Po jistych problemech asi dropuju vyhledavani dle keywords  ***
        ***  a pokracuji s vyhledavanim skrze LIKE, byt je pomalejsi... ***
        *******************************************************************

        // vrati pole keywords z predaneho nazvu, pokud posleme i pole s jiz drive nactenyma keywords, vrati vsechny keywords i s temi puvodnimi
        public function getKeywords($name, $keywords = array()){
            $name = str_replace("'", ' ', $name); // stredniky prevest na mezery
            $name = preg_replace('/:|-/', '', $name); // dvojtecky a pomlcky odstranit
            $name = mb_strtolower($name); // vse na male pismenka

            $words = array_filter(explode(' ', $name), function($item){if(strlen($item) > 2) return $item;}); // rozdelit na pole podle mezery a odstranit slova kratsi nez 3

            $keywords = array_unique( array_merge($keywords, $words)); // mergnu obe pole a ponecham jen unikatni slova

            return $keywords; // vratim pole s keywords
        }
        */

    }
?>