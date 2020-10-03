<?php
class EditorController extends Controller {
    public function process($param){
        // pouze pro prava mod a vice
        $this->authenticateUser(1);
        //vytvoreni instance modelu
        $contentHandler = new ContentHandler();

        if ($param[0] === 'serial'){
            // hlavicka stranky
            $this->head['title'] = "Editor seriálů";
            $table = 'serial';
            
            // priprava praydneho serialu
            $serial = array(
                'name' => '',
                'image' => '',
                'year' => '',
                'genre' => '',
                'linkCsfd' => '',
                'linkImdb' => '',
                'description' => '',
                'onlyFor' => ''
            );

            if ($param[2] == 'smazat') {
                $contentHandler->deleteContent($table, $param[1]);
                $this->addMessage('Seriál smazán.', 'info');
                $this->redirect('serialy');
            } else {
                if ($_POST){
                    $keys = array('name', 'image', 'year', 'genre', 'linkCsfd', 'linkImdb', 'description', 'onlyFor');
                    $serial = array_intersect_key($_POST, array_flip($keys));
                    //ulozeni serialu do DB
                    $contentHandler->saveContent($table, $_POST['_id'], $serial);
                    $this->addMessage('Seriál byl úspěšně upraven.', 'info');
                    $this->redirect('serialy');
                } else if (!empty($param[1])){
                    $loadedSerial = $contentHandler->getContent($table, $param[1]);
                    if (!$loadedSerial){
                        $this->addMessage('Seriál nenalezen.', 'error');
                    } 
                }
                $this->data['inListCount'] = MujSeznamHandler::inListCount($table, $param[1]);
                $this->data['data'] = $loadedSerial[0];
                $this->data['type'] = 'serial';
                $this->view = 'editor';
            }
        } elseif ($param[0] === 'anime'){
            // hlavicka stranky
            $this->head['title'] = "Editor anime";
            $table = 'anime';
            // priprava praydneho serialu
            $anime = array(
                'name' => '',
                'image' => '',
                'year' => '',
                'genre' => '',
                'linkAp' => '',
                'description' => '',
                'onlyFor' => ''
            );

            if ($param[2] == 'smazat') {
                $contentHandler->deleteContent($table, $param[1]);
                $this->addMessage('Anime smazáno.', 'info');
                $this->redirect('anime');
            } else {
                if ($_POST){
                    $keys = array('name', 'image', 'year', 'genre', 'linkAp', 'description', 'onlyFor');
                    $anime = array_intersect_key($_POST, array_flip($keys));
                    //ulozeni anime do DB
                    $contentHandler->saveContent($table, $_POST['_id'], $anime);
                    $this->addMessage('Anime bylo úspěšně upraveno.', 'info');
                    $this->redirect('anime');
                } else if (!empty($param[1])){
                    $loadedAnime = $contentHandler->getContent($table, $param[1]);
                    if (!$loadedAnime){
                        $this->addMessage('Anime nenalezeno.', 'error');
                    } 
                }
                $this->data['inListCount'] = MujSeznamHandler::inListCount($table, $param[1]);
                $this->data['data'] = $loadedAnime[0];
                $this->data['type'] = 'anime';
                $this->view = 'editor';
            }
        } elseif ($param[0] === 'manga'){
            // hlavicka stranky
            $this->head['title'] = "Editor mangy";
            $table = 'manga';

            // priprava praydneho serialu
            $manga = array(
                'name' => '',
                'image' => '',
                'year' => '',
                'genre' => '',
                'linkAp' => '',
                'description' => '',
                'onlyFor' => ''
            );

            if ($param[2] == 'smazat') {
                $contentHandler->deleteContent($table, $param[1]);
                $this->addMessage('Manga smazána.', 'info');
                $this->redirect('manga');
            } else {
                if ($_POST){
                    $keys = array('name', 'image', 'year', 'genre', 'linkAp', 'description', 'onlyFor');
                    $manga = array_intersect_key($_POST, array_flip($keys));
                    //ulozeni mangy do DB
                    $contentHandler->saveContent($table, $_POST['_id'], $manga);
                    $this->addMessage('Manga byla úspěšně upravena.', 'info');
                    $this->redirect('manga');
                } else if (!empty($param[1])){
                    $loadedManga = $contentHandler->getContent($table, $param[1]);
                    if (!$loadedManga){
                        $this->addMessage('Manga nenalezena.', 'error');
                    } 
                }
                $this->data['inListCount'] = MujSeznamHandler::inListCount($table, $param[1]);
                $this->data['data'] = $loadedManga[0];
                $this->data['type'] = 'manga';
                $this->view = 'editor';
            }
        }
    }
}
?>