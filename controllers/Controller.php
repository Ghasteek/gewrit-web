<?php
    // vychozi controller
    abstract class Controller {
        // data predana sablone
        protected $data = array();
        // nazev sablony bez pripony
        protected $view = "";
        // hlavicka pro HTML stranku
        protected $head = array('tittle' => '', 'keywords' => '', 'description' => '');

        // hlavni metoda controlleru
        abstract function process($param);

        // vypise view uzivateli
        public function showView(){
            if ($this->view) {
                extract($this->sanitize($this->data));
                extract($this->data, EXTR_PREFIX_ALL, "");
                require("views/" . $this->view . ".phtml");
            }
        }

        // presmeruje na dane URL
        public function redirect($url){
            header("Location: /$url");
            header("Connection: close");
            exit;
        }

        // osetri vypis
        private function sanitize($x = null){
            if (!isset($x))
                return null;
            elseif (is_string($x))
                return htmlspecialchars($x, ENT_QUOTES);
            elseif (is_array($x))
            {
                foreach($x as $k => $v)
                {
                    $x[$k] = $this->sanitize($v);
                }
                return $x;
            }
            else
                return $x;
        }

        // odstrani specialni znaky
        public function cleanString($input){
            return preg_replace('/:|-|!|{|}|\[|\]|\$|\<|\>|\/|\(|\)| +|\'|`/', ' ', $input); // odstranit specialni znaky
        }

        // prida "message" do session, pro pozdejsi zobrazeni na view
        public function addMessage ($text, $type) {
            $array = array();
            if (isset($_SESSION['messages'])) {
                array_push($array, $text, $type);
                $_SESSION['messages'][] = $array;
            } else {
                array_push($array, $text, $type);
                $_SESSION['messages'] = array($array);
            }
        }

        // vycte "messages" ze session a vymaze je
        public function getMessages() {
            if (isset($_SESSION['messages'])) {
                $messages = $_SESSION['messages'];
                unset($_SESSION['messages']);
                return $messages;
            } else {
                return array();
            }
        }

        // overeni uzivatele, v parametru muzeme poslat jakou urovern prav musi uzivatel mit
        public function authenticateUser($level = false){
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            if (!$user || ($user['rights'] < $level)){
                $this->addMessage('Nedostatečné oprávnění', 'warning');
                $this->redirect('prihlaseni');
            }
        }
    }
?>