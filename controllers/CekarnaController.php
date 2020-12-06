<?php 
    class CekarnaController extends Controller {
        public function process ($param){
            $cekarnaHandler = new CekarnaHandler();
            $this->authenticateUser(1);
            
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            $this->data['rights'] = $user && $user['rights'];
            $this->data['userRights'] = $user['rights'];

            if ($_POST){
                if ($_POST['serial'] != 0) {
                    $answer = Db::authorizeIds('serial', $_POST['serial'] );
                    $message = "Autorizováno " . $answer . " seriálů.";
                    $this->addMessage($message, 'info');
                }
                if ($_POST['anime'] != 0) {
                    $answer = Db::authorizeIds('anime', $_POST['anime'] );
                    $message = "Autorizováno " . $answer . " anime.";
                    $this->addMessage($message, 'info');
                }
                if ($_POST['manga'] != 0) {
                    $answer = Db::authorizeIds('manga', $_POST['manga'] );
                    $message = "Autorizováno " . $answer . "  mangy.";
                    $this->addMessage($message, 'info');
                }
            }

            $serials = $cekarnaHandler->getSerialsToAck();
            $animes = $cekarnaHandler->getAnimesToAck();
            $mangas = $cekarnaHandler->getMangasToAck();

            $this->data['serials'] = $serials;
            $this->data['animes'] = $animes;
            $this->data['mangas'] = $mangas;
            $this->data['rights'] = 1;
            $this->head['title'] = 'Gewrit - K autorizaci';
            $this->view = 'cekarna';
        }
    }
?>