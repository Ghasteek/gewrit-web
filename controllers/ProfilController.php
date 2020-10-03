<?php 
    class ProfilController extends Controller {
        public function process($param){
            $this->authenticateUser();
            $this->head['title'] = 'Gewrit - profil';
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();

            if (!empty($param[0]) && $param[0] == 'odhlasit'){
                try {
                    $userAdministration->logout();
                } catch (UserError $error) {
                    $this->addMessage($error->getMessage(), 'info');
                    $this->redirect('prihlaseni');
                }
                
            }
            
            if ($param[0]) {
                $this->authenticateUser(1);
                try {
                    $userById = $userAdministration->getUserById($param[0]);
                    $this->data['user'] = $userById;
                    $this->view = 'profil';
                } catch (UserError $error) {
                    $this->addMessage($error->getMessage(), 'warning');
                    $this->redirect('profil');
                }
            } else {
                $this->data['username'] = $user['username'];
                $this->data['rights'] = $user['rights'];
                $this->view = 'profilMuj';
            }

            
        }
    }
?>