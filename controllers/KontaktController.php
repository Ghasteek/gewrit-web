<?php 
    class KontaktController extends Controller {
        public function process($param) {
            $this->head = array(
                'title' => 'Kontaktní formulář',
                'keywords' => 'kontakt, email, formulář',
                'description' => 'Kontaktní formulář našeho webu.'
            );
            if ($_POST){
                try{
                    $sendEmail = new SendEmail();
                    $text = "<h1>Zpráva z kontaktního formuláře od " . $from . "</h1><br>" . $_POST["text"];
                    $sendEmail->sendWithAntispam($_POST['year'], "gewrit.4fan@gmail.com", "Kontaktní formulář z webu GEWRIT", $text, $_POST["email"]);

                    $this->addMessage('Email byl úspěšně odeslán.', 'info');
                    $this->redirect('kontakt');

                } catch (UserError $error) {
                    $this->addMessage($error->getMessage(), 'warning');
                }
            }
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            $this->data['email'] = $user['email'];
            $this->view = 'kontakt';
        }
    }
?>