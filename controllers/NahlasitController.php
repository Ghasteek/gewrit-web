<?php 
    class NahlasitController extends Controller {
        public function process ($param){
            $this->authenticateUser(0);
            $author = UserAdministration::getUser();

            $nahlasitHandler = new NahlasitHandler();

            if ($param[0] == 'uzivatel'){
                $answer = UserAdministration::getUserById($param[1]) ;
            } else {
                $answer = ContentHandler::getContent($param[0], $param[1]) ;
            }

            if ($_POST){
                //echo $_POST['reportForId'] . " | " . $_POST['type'] . " | " . $_POST['text'] . " | " . $_POST['author'];
                $nahlasitHandler = new NahlasitHandler();
                $nahlasitHandler->saveReport(trim($_POST['type']), trim($_POST['reportForId']), $this->cleanString(trim($_POST['text'])), trim($_POST['author']));
                $this->addMessage('Report odeslán.', 'info');
                $this->redirect('profil');
            }

            $this->data['data'] = $answer;
            $this->data['type'] = $param[0];
            $this->data['id'] = $param[1];
            $this->data['author'] = $author['_id'];
            $this->head['title'] = 'Gewrit - Nahlásit problém';
            $this->view = 'nahlasit';
        }
    }
?>