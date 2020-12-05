<?php 
    class PridatController extends Controller {
        public function process($param){
            $this->authenticateUser(0);
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            $contentHandler = new ContentHandler();
            $this->head['title'] = 'Gewrit - Přidat ';
            $this->data['user'] = $user['_id'];

            if ($_POST){
                if (($_POST['type'] == 'serial') || ($_POST['type'] == 'anime') || ($_POST['type'] == 'manga')) {
                    try{
                        if ($_POST['type'] == 'serial'){ 
                            $keys = array('name', 'image', 'year', 'genre', 'linkCsfd', 'linkImdb', 'description', 'onlyFor');
                            $redirect = 'serialy';
                        } else {
                            $keys = array('name', 'image', 'year', 'genre', 'linkAp', 'description', 'onlyFor');
                            $redirect = $_POST['type'];
                        }
                        $content = array_intersect_key($_POST, array_flip($keys));
                        //ulozeni clanku do DB
                        $id = $contentHandler->saveContent($_POST['type'], '', $content);

                        // jmeno obrazku
                        $imageName = $id . '.jpg';

                        // kam obrazek ulozit
                        $directoryName = 'img/' . $_POST['type'];
                        
                        //pokud slozka neexistuje, vytvori ji
                        if(!is_dir($directoryName)){
                            //Directory does not exist, so lets create it.
                            mkdir($directoryName, 0755, true);
                        }

                        //stahne obrazek
                        $imageDestination = $directoryName . '/' . $imageName;
                        copy($content['image'], $imageDestination);

                        // zmeni v db adresu obrazku
                        $contentHandler->saveContent($_POST['type'], $id, array('image' => $imageDestination));

                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }

                    $this->addMessage('Úspěšně přidáno, čeká na schávlení.', 'info');
                        $this->redirect($redirect);
                    
                } else {
                    try {
                        $scrapper = new Scrapper();
                        $data = $scrapper->scrapUrl($_POST['url'], $_POST['antispam']);
                        $errorInScrap = FALSE;
                        foreach ($data as $item){
                            if ($item == ''){
                                $errorInScrap = TRUE;
                            }
                        }
                        if ($errorInScrap){
                            LogHandler::logThis('Scrapper returned blank info. URL={ ' . $_POST['url'] . '}.');
                        }
                        $this->data['data'] = $data;
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                }
            }
            $this->view = 'pridat';
        }
    }
?>