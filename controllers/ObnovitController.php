<?php 
    class ObnovitController extends Controller {
        public function process ($param){

            $userAdministration = new UserAdministration();
            $sendEmail = new SendEmail();

            // pokud je zadan token v url adrese, vyhledam ho v db
            if ($param[0]){
                $this->data['tokenPassed'] = true;                
            } else {
                $this->data['tokenPassed'] = false;
            }

            if ($_POST){
                if ($_POST['newPassword']){
                    try{
                        //vyhledam token z url v db
                        $existingToken = $userAdministration->getTokenByToken($param[0]);

                        // pokud takovy token byl v db, 
                        if (count($existingToken) > 1){
                            $createdTokenTimestanmp = $existingToken['timestamp'];
                            $expireException = date("Y-m-d H:i:s", strtotime("-30 minutes"));
                            //$expireException = date("Y-m-d H:i:s", strtotime("-1 minutes"));

                            // a jeho timestamp je vetsi nez timestap pred 30 minutami - cili neni prosly
                            if (date($expireException) < date($createdTokenTimestanmp)){
                                // zmen heslo v db a vymaz token
                                $userAdministration->updatePassword($existingToken['email'], $_POST['newPassword'], $_POST['newPassword']);
                                $userAdministration->removeToken($existingToken['email']);
                                $this->addMessage('Heslo bylo úspúěšně změněno, prosím, přihlašte se.', 'info');
                                $this->redirect('prihlaseni');
                            } else {
                                // pokud byl prosly, vymaz ho a vyhod error
                                $userAdministration->removeToken($existingToken['email']);
                                throw new UserError('Žádost o obnovení hesla již vypršela, zkuste to znovu.', 'warning');
                            }
                        }
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                } else {
                    try {
                        // je email v db?
                        $userAdministration->isThereUser($_POST['email']);
                        // vezmu z password_recovery tabulky zaznam se zadanym emailem
                        $existingToken = $userAdministration->getTokenByEmail($_POST['email']);
                        //print_r($existingToken);  // debug vypis
                        //echo "<br>pocet timestamp - " . count($existingToken) . "<br>" . $existingToken;  // debug vypis

                        // vytvorim token
                        $token = openssl_random_pseudo_bytes(16);
                        $token = bin2hex($token);

                        // pokud takovy zaznam je,...
                        if (count($existingToken) > 1){
                            $createdTokenTimestanmp = $existingToken['timestamp'];
                            $expireException = date("Y-m-d H:i:s", strtotime("-30 minutes"));
                            //$expireException = date("Y-m-d H:i:s", strtotime("-1 minutes"));

                            // a jeho timestamp je vetsi nez timestap pred 30 minutami - cili neni prosly
                            if (date($expireException) < date($createdTokenTimestanmp)){
                                //vyhod chybu
                                throw new UserError('Obnovovací email již byl poslán.');
                            } else {
                                // jinak smaze stary zaznam a ulozi novy                            
                                $userAdministration->removeToken($_POST['email']);
                                //echo "mazu stary posilam novy " . $token;  // debug vypis
                                $userAdministration->insertToken(trim($_POST['email']), $token);
                                //echo "<br>ukladam novy token do db, email - " . $_POST['email'] . " / token - " . $token;  // debug vypis

                                // a poslu emailem
                                $sendEmail->sendToken($_POST['email'], $token);

                                $this->addMessage('Email s instrukcemi pro obnovení hesla odeslán na zadaný email.', 'info');
                            }
                        } else {
                            // pokud takovy zaznam neni, ulozim token do db
                            $userAdministration->insertToken(trim($_POST['email']), $token);
                            //echo "ukladam token do db, email - " . $_POST['email'] . " / token - " . $token;  // debug vypis
                            // a poslu emailem
                            $sendEmail->sendToken($_POST['email'], $token);

                            $this->addMessage('Email s instrukcemi pro obnovení odeslán', 'info');
                        }
                        


                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                }
            }

            $this->head['title'] = 'Gewrit - Obnova hesla';
            $this->view = 'obnovit';
        }
    }
?>