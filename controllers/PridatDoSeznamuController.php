<?php 
    class PridatDoSeznamuController extends Controller {
        public function process ($param){
            $this->authenticateUser(-1);
            $author = UserAdministration::getUser();
            $mujSeznamHandler = new MujSeznamHandler();

            try {
                $mujSeznamHandler->exist($param[0], $param[1] ); 
            } catch (UserError $error) {
                $this->addMessage($error->getMessage(), 'warning');
                $this->redirect($param[0]);
            }

            if (($param[0] === 'serial') || ($param[0] === 'manga') || ($param[0] === 'anime')) {
                $answer = ContentHandler::getContentIfMine($param[0], $param[1]) ;
            }

            if (count($answer) == 0){
                $this->addMessage('K této sérii nemáte přístup.', 'warning');
                $this->redirect($param[0]); 
            }

            if ($_POST) {
                $_POST['releaseDay'] ? $releaseDay = implode(",", $_POST['releaseDay']) : $releaseDay = '';
                $_POST['series'] ? $_POST['series'] = $_POST['series'] : $_POST['series'] = '0';
                $_POST['episode'] ? $_POST['episode'] = $_POST['episode'] : $_POST['episode'] = '0';
                $record = array(
                    'owner' => $author['_id'],
                    'type' => $param[0],
                    'item' => $param[1],
                    'series' => $_POST['series'],
                    'episode' => $_POST['episode'],
                    'link' => $_POST['link'],
                    'releaseDay' => $releaseDay,
                    'status' => $_POST['status']
                );
                // ulozim zaznam z POSTu
                $mujSeznamHandler->saveRecord($_POST['_id'], $record);

                $this->addMessage('Záznam uložen.', 'info');
                $this->redirect('muj-seznam');

            }

            $this->data['data'] = $answer[0];
            $this->data['type'] = $param[0];
            $this->data['id'] = $param[1];
            $this->data['author'] = $author['_id'];
            $this->head['title'] = 'Gewrit - Přidat do seznamu';
            $this->view = 'pridat-do-seznamu';
        }
    }
?>