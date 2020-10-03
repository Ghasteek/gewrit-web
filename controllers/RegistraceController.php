<?php
    class RegistraceController extends Controller {
        public function process($param){
            $this->head['title'] = 'Registrace';
            if ($_POST){
                try {
                    $userAdministration = new UserAdministration();
                    $userAdministration->register($_POST['username'], $_POST['password'], $_POST['rePassword'], $_POST['email'], $_POST['year']);
                    $userAdministration->login($_POST['username'], $_POST['password']);
                    $this->addMessage('Byl/a jste úspěšně registrován/a.', 'info');
                    $this->redirect('profil');
                } catch (UserError $error) {
                    $this->addMessage($error->getMessage(), 'warning');
                }
            }
        $this->view = 'registrace';
        }
    }
?>