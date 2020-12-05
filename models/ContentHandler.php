<?php 
    // trida poskytuje metody pro spravu polozek v db
    class ContentHandler{

        // vrati vsechny polozky ze zadane tabulky
        public function getAllContent($table, $order){
            $sql = "SELECT * 
                FROM $table
                ORDER BY $order ASC";
            return Db::getAll($sql);
        }

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
                    $targetId = Db::getLastId();
                    LogHandler::logThis('Added new content into "' . $table . '" table.', $targetId);
                    return $targetId;
                } catch (PDOException $error) {
                    throw new UserError('Již je v databázi.');
                }
            } else {
                Db::change($table, $content, 'WHERE _id = ?', array($id));
                LogHandler::logThis('Edited item in "' . $table . '" table.', $id);
            }
        }

        //smaze ze zadane TABULKY zadane ID
        public function deleteContent($table, $id){
            $sql = "DELETE FROM $table
                WHERE _id = ?";
            Db::getChanges($sql, array($id));
            LogHandler::logThis('Deleted item "' . $id . '" from table "' . $table . '".', $id);
        }


/*----------------------------------------------------------
------------------- ZPRACOVANI HODNOCENI -------------------
----------------------------------------------------------*/

        // smaze vsechna hodnoceni zadane polozky
        public function deleteRatingOfItem($table, $id){
            $table = $table . "_rating";
            $sql = "DELETE FROM $table
                WHERE itemId = ?";
            Db::getChanges($sql, array($id));
        }

        // ziskej moje hodnoceni 
        public function getMyRating($table, $userId, $itemId){
            $table = $table . "_rating";
            $sql = "SELECT `rating` from $table
                WHERE userId = ? 
                AND itemId = ?";
            return Db::getAll($sql, array($userId, $itemId));
        }

        //smaz radek mojeho hodnoceni ze zadane tabulky
        public function deleteMyRating($table, $userId, $itemId){
            // odstrani zaznam meho hodnoceni
            $tableRating = $table . "_rating";
            $sql = "DELETE FROM $tableRating
                WHERE userId = ?
                AND itemId = ?";
            Db::getChanges($sql, array($userId, $itemId));
        }

        // uloz moje hodnoceni
        public function saveMyRating($table, $userId, $itemId, $rating){
            // zjisti, jestli jsem uz tuto polozku hodnotil
            $oldRating = self::getMyRating($table, $userId, $itemId);
            $tableRating = $table . "_rating";

            if (count($oldRating) == 0){
                // pokud jsem ji jeste nehodnotil, pridej nove hodnoceni
                Db::insert($tableRating, array('userId' => $userId, 'itemId' => $itemId, 'rating' => $rating));
                // a upravim hodnoty hodnoceni u dane polozky
                $newItemRating = self::getNewRating($table, $itemId, $rating, 'plus');
            } else {
                // pokud jsem jiz tuto polozku hodnotil
                if ($rating != 0){ // a nehodnotim ji 0 - cili nemazu sve hodnoceni
                    //upravim stavajici hodnoceni
                    Db::change($tableRating, array('rating' => $rating), 'WHERE userId = ? AND itemId = ?', array($userId, $itemId));
                    // a upravim hodnoty hodnoceni u dane polozky
                    $newItemRating = self::getNewRating($table, $itemId, $rating, 'change', $oldRating[0]['rating']);
                } else { // poslali jsme hodnoceni 0
                    //smazu zaznam o hodnoceni
                    self::deleteMyRating($table, $userId, $itemId);
                    // a upravim hodnoty hodnoceni u dane polozky
                    $newItemRating = self::getNewRating($table, $itemId, $rating, 'minus', $oldRating[0]['rating']);
                }
                
            }

            // zapisu zmenu do polozky
            Db::change($table, $newItemRating, 'WHERE _id = ?', array($itemId));
        }

        // vypocita nove hodnoty hodnoceni pro polozku -- TENDENCY je pomocna promenna pro yjisteni, jestli chci pricitat hodnoceni nebo ubirat. Muze byt "plus", "minus" a "change"
        public function getNewRating($table, $itemId, $rating, $tendency, $oldRating = 0){
            //nactu stare hodnoty hodnoceni
            $sql = "SELECT ratingSum, ratingQuantity, rating FROM $table
                    WHERE _id = ?";
            $oldItemRating = Db::getAll($sql, array($itemId));

            $newRatingSum = $oldItemRating[0]['ratingSum'] + $rating;
            if ($tendency == 'plus') { // pokud je tendency PLUS, prictu
                $newRatingSum = $oldItemRating[0]['ratingSum'] + $rating;
                $newRatingQuantity = $oldItemRating[0]['ratingQuantity'] + 1;
            } elseif ($tendency == 'minus') { // pokud je tendency MINUS, odectu
                $newRatingSum = $oldItemRating[0]['ratingSum'] - $oldRating;
                $newRatingQuantity = $oldItemRating[0]['ratingQuantity'] - 1;
            } else { // pokud je tendency CHANGE, necham quantity a zmenim jen sum
                $newRatingQuantity = $oldItemRating[0]['ratingQuantity'];
                $newRatingSum = ($oldItemRating[0]['ratingSum'] - $oldRating) + $rating;
            }
            
            if ($newRatingQuantity == 0){
                $newRating = 0;
            } else {
                $newRating = round($newRatingSum / $newRatingQuantity, 1);
            }
            $newItemRating = array(
                'ratingSum' => $newRatingSum,
                'ratingQuantity' => $newRatingQuantity,
                'rating' => $newRating);

            return $newItemRating;
        }


/*----------------------------------------------------------
------------------------ STATISTIKY ------------------------
----------------------------------------------------------*/


        // celkovy pocet polozek v DB
        public function getAllItemsCount($table = "all") {
            if ($table === "all"){
                $sql = "SELECT 
                    sum(a.count)
                    from
                        (select count(*) as count from `anime`
                        union all
                        select count(*) as count from `manga`
                        union all
                        select count(*) as count from `serial`) a";
            } else {
                $sql = "SELECT COUNT(_id) from $table WHERE onlyFor = 0";
            }
            return (Db::getAll($sql))[0][0];
        }
    }
?>