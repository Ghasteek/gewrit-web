<?php
    class FaqController extends Controller {
        public function process($param){
            $contentHandler = new ContentHandler();
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();

            if ($param[0] == "edit"){
                // jen pro admina
                $this->authenticateUser(2);
                $data = $contentHandler->getContent("faq", $param[1]);
                $this->data['show'] = 'edit';
                $this->data['data'] = $data[0];
            } elseif ($param[0] == "add"){
                // jen pro admina
                $this->authenticateUser(2);
                $this->data['show'] = 'add';
            } elseif (($param[0] == "delete") && (intval($param[1]) != 0)){
                // jen pro admina
                $this->authenticateUser(2);
                $contentHandler->deleteContent("faq", $param[1]);
                $this->addMessage('FAQ bylo úspěšně odstraněno.', 'info');
                $this->redirect('faq');
            } elseif (!$param[0]){
                $data = $contentHandler->getAllContent("faq", "orderNumber");
                $this->data['show'] = 'faq';
                $this->data['data'] = $data;
            }

            if ($_POST){
                // jen pro admina
                $this->authenticateUser(2);
                $keys = array('question', 'answer', 'orderNumber');
                $data = array_intersect_key($_POST, array_flip($keys));
                //ulozeni faq do DB
                $contentHandler->saveContent("faq", $_POST['_id'], $data);
                $this->addMessage('FAQ bylo úspěšně upraveno.', 'info');
                $this->redirect('faq');
            }

            $this->data['userRights'] = $user['rights'];
            $this->head['title'] = 'Gewrit - otázky a odpovědi';
            $this->view = 'faq';
        }
    }
?>