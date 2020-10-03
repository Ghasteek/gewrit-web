<?php 
    class BanController extends Controller {
        public function process ($param){
            // jen pro admina
            $this->authenticateUser(2);

            $banHandler = new BanHandler();

            if ($param[0] === 'more'){
                $answer = $banHandler->banMoreUser($param[1]);
                if ($answer) { $this->addMessage('BAN prohlouben.', 'info');} else { $this->addMessage('Již dosažena maximální úroveň banu.', 'warning');}
                $this->redirect('ban');
            } elseif ($param[0] === 'less'){
                $answer = $banHandler->banLessUser($param[1]);
                if ($answer) { $this->addMessage('BAN zlehčen.', 'info');} else { $this->addMessage('Uživatel již nemá ban.', 'warning');}
                $this->redirect('ban');
            }

            if ($_POST){
                $banHandler->banUser(trim($_POST['username']));
                $this->addMessage('Uživateli udělen první stupeň BANu', 'info');
                $this->redirect('ban');
            }

            $bans = $banHandler->getBans();

            $this->data['bans'] = $bans;
            $this->head['title'] = 'Gewrit - Seznam BANů';
            $this->view = 'ban';
        }
    }
?>