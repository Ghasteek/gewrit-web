<?php
class EditorController extends Controller {
    public function process($param){
        // pouze pro prava mod a vice
        $this->authenticateUser(1);
        // vytvoreni instance modelu
        $contentHandler = new ContentHandler();

        // je poslany parametr validni
        if (!$this->isParamValid($param[0])) {
            $this->addMessage('Něco se pokazilo, zkuste to znovu prosím.', 'error');
            $this->redirect('uvod');
        }

        // nacti data k editaci
        $dataOutput = $contentHandler->getContent($param[0], $param[1]);
        // pokud polozka neni v db, ukaz error message
        if (!$dataOutput){
            $this->addMessage('Položka nenalezena.', 'error');
        }

        // zpracuj POST
        if ($_POST['save']){ // pokud posilam data na ulozeni
            if ($param[0] === "serial"){
                // priprava prazsneho pole pro data
                $dataInput = array(
                    'name' => '',
                    'image' => '',
                    'year' => '',
                    'genre' => '',
                    'linkCsfd' => '',
                    'linkImdb' => '',
                    'description' => '',
                    'onlyFor' => ''
                );
                // keys pro osekani POSTu
                $keys = array('name', 'image', 'year', 'genre', 'linkCsfd', 'linkImdb', 'description', 'onlyFor');
            } else {
                // priprava prazsneho pole pro data
                $dataInput = array(
                    'name' => '',
                    'image' => '',
                    'year' => '',
                    'genre' => '',
                    'linkAp' => '',
                    'description' => '',
                    'onlyFor' => ''
                );
                // keys pro osekani POSTu
                $keys = array('name', 'image', 'year', 'genre', 'linkAp', 'description', 'onlyFor');
            }
            // osekam POST data
            $dataInput = array_intersect_key($_POST, array_flip($keys));

            //ulozeni anime do DB
            $contentHandler->saveContent($param[0], $_POST['_id'], $dataInput);

            $this->addMessage('Položka úspěšně upravena.', 'info');

            if ($param[0] === 'serial'){ $param[0] = 'serialy'; }
            $this->redirect($param[0]);

        } elseif ($_POST['delete']){ // pokud posilam mazani
            // smazu polozku z db
            $contentHandler->deleteContent($param[0], $param[1]);

            // odstranim obrazek
            $imageDestination = "img/" . $param[0] . "/" . $param[1] . ".jpg";
            unlink($imageDestination);

            // smazu hodnoceni polozky z db
            $contentHandler->deleteRatingOfItem($param[0] , $param[1]);
            
            // informuji o smazani a redirectnu na kategorii
            $this->addMessage('Položka smazána.', 'info');
            $this->redirect($param[0]);
        }

        
        $this->head['title'] = "Editor";
        $this->data['inListCount'] = MujSeznamHandler::inListCount($param[0], $param[1]);
        $this->data['data'] = $dataOutput[0];
        $this->data['type'] = $param[0];
        $this->view = 'editor';
    }
}
?>