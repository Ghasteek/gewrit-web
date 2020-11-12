<?php
    class PrihlaseniController extends Controller {
        public function process($param){
            $userAdministration = new UserAdministration();
            if ($userAdministration->getUser()){
                $this->redirect('profil');
            }
            $this->head['title'] = 'Gewrit - přihlášení';
            if ($_POST){
                try {
                    $userAdministration->login($_POST['username'], $_POST['password']);
                    $this->addMessage('Byl jste úspěšně přihlášen', 'info');
                    $this->redirect('uvod');
                } catch (UserError $error) {
                    $this->addMessage($error->getMessage(), 'warning');
                }
            }
            $this->view = 'prihlaseni';
        }
    }
?>