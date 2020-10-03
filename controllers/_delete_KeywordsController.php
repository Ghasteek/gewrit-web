<?php 
/******************************************************************
*** Po jistych problemech asi dropuju vyhledavani dle keywords  ***
***  a pokracuji s vyhledavanim skrze LIKE, byt je pomalejsi... ***
******************************************************************/
    class KeywordsController extends Controller{
        public function process ($param){
            // jen pro admina
            $this->authenticateUser(2);
            if ($param[0] == 'calculate') {
                $contentHandler = new ContentHandler();
                $keywords = array();

                $sql = "(SELECT name FROM serial) UNION (SELECT name FROM manga) UNION (SELECT name FROM anime)";

                $names = Db::getAll($sql);

                $this->data['names'] = $names;

                foreach ($names as $name){
                    $keywords = $contentHandler->getKeywords($name[0], $keywords);
                }

                $this->$data['calc'] = 'true';
                $this->data['keywords'] = $keywords;

                if ($param[1] == 'write'){ // pokud je URL "/calculate/write" tak zapis nalezene keywords do tabulky keywords
                    foreach ($keywords as $keyword){
                        $word = array('word' => $keyword);
                        $contentHandler->saveContent('keywords', '', $word);
                    }
                    $this->redirect('keywords/calculate');
                }
            }  
            
            if ($param[0] == 'map'){ // pokud je "/map/..." prejdi k mapovani
                
                if ($param[1] == 'manga'){ // pokud mapuji tabulku "manga"
                    //$this->makeMapTable('manga'); // vztvor tabulku "manga_keywords"
                    // TODO!!!!!
                    echo "vytvorena tabulka manga_keywords <br>";

                }

            }

            $this->head['title'] = 'Gewrit - KeyWORDS';
            $this->view = 'keywords';
        }

        public function makeMapTable($name){
            $nameKeywords = $name . '_keywords';
            $nameId = $name . '_id';
            $sql = "CREATE TABLE '$nameKeywords' (
                '$nameId' INT NOT NULL,
                'keyword_id' INT NOT NULL,
                PRIMARY KEY ($nameId, keyword_id),
                FOREIGN KEY '$nameId' REFERENCES $name (_id),
                FOREIGN KEY 'keyword_id' REFERENCES keywords (_id)
              )";
              echo $sql . "<br>";
            $changes = Db::getChanges($sql);
        }
    }
?>