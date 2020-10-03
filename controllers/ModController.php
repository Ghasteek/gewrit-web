<?php 
    class ModController extends Controller {
        public function process ($param){
            // jen pro admina
            $this->authenticateUser(2);

            $modHandler = new ModHandler();

            if ($param[0] === 'promote'){
                $answer = $modHandler->promoteUser($param[1]);
                if ($answer) { $this->addMessage('Práva zvýšena.', 'info');} else { $this->addMessage('Práva již na maximální úrovni.', 'warning');}
                $this->redirect('mod');
            } elseif ($param[0] === 'degrade'){
                $answer = $modHandler->degradeUser($param[1]);
                if ($answer) { $this->addMessage('Práva snížena.', 'info');} else { $this->addMessage('Práva již na minimální úrovni.', 'warning');}
                $this->redirect('mod');
            }

            if ($_POST){
                $modHandler->promoteToMod(trim($_POST['username']));
                $this->addMessage('Uživatel povýšen na moderátora', 'info');
                $this->redirect('mod');
            }

            $mods = $modHandler->getMods();

            $this->data['mods'] = $mods;
            $this->head['title'] = 'Gewrit - Seznam moderátorů';
            $this->view = 'mod';
        }
    }
?>