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
                        $contentHandler->saveContent($_POST['type'], '', $content);
                        $this->addMessage('Úspěšně přidáno, čeká na schávlení.', 'info');
                        $this->redirect($redirect);
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                    
                } else {
                    try {
                        $scrapper = new Scrapper();
                        $answer = $scrapper->scrapUrl($_POST['url'], $_POST['antispam']);
                        $this->data['data'] = $answer;
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                }
            }
            $this->view = 'pridat';
        }
    }
?>