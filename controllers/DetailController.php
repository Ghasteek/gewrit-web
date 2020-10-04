<?php
class DetailController extends Controller
{
    public function process($param){
        $contentHandler = new ContentHandler();

        $userAdministration = new UserAdministration();
        $user = $userAdministration->getUser();
        $this->data['user'] = $user;
        $this->data['userRights'] = $user['rights'];

        $allowedParameters = 'anime, manga, serial';
        $param[1] = intval($param[1]);
        if (($param[1] !== 0) && (strpos($allowedParameters, $param[0]) !== false)){

            if ($_POST){
                if (($_POST['userRating'] >= 0) && ($_POST['userRating'] <= 10)){
                    $contentHandler->saveMyRating($param[0], $user['_id'], intval($param[1]), intval($_POST['userRating']));
                    if ($_POST['userRating'] == 0){
                        $this->addMessage('Hlasování smazáno' , 'info');
                    } else {
                        $this->addMessage('Hlasování uloženo' , 'info');
                    }
                } else {
                    $this->addMessage('Něco se pokazilo, zkuste to prosím znovu', 'warning');
                }
                
            }

            switch ($param[0]){
                case 'serial':
                    $pageTitle = 'Detail seriálu';
                    $table = 'serial';
                    $loadedContent = $contentHandler->getContent($table, $param[1]);
                break;
                case 'anime':
                    $pageTitle = 'Detail anime';
                    $table = 'anime';
                    $loadedContent = $contentHandler->getContent($table, $param[1]);
                break;
                case 'manga':
                    $pageTitle = 'Detail mangy';
                    $table = 'manga';
                    $loadedContent = $contentHandler->getContent($table, $param[1]);
            }

            if (!$loadedContent){
                $this->addMessage('Seriál nenalezen.', 'error');
                $this->redirect('$table');
            } 

        } else {
            $this->addMessage('Adresa není správná, zkuste to znovu.', 'warning');
            $this->redirect('uvod');
        }

        /*if (intval($param[1]) != 0){
            if ($param[0] === 'serial'){
                $pageTitle = 'Detail seriálu';
                $table = 'serial';
                $loadedContent = $contentHandler->getContent($table, $param[1]);
                
                if (!$loadedContent){
                    $this->addMessage('Seriál nenalezen.', 'error');
                    $this->redirect('serialy');
                } 
            } elseif ($param[0] === 'anime'){
                $pageTitle = 'Detail anime';
                $table = 'anime';
                $loadedContent = $contentHandler->getContent($table, $param[1]);
                
                if (!$loadedContent){
                    $this->addMessage('Anime nenalezeno.', 'error');
                    $this->redirect('anime');
                } 
            } elseif ($param[0] === 'manga'){
                $pageTitle = 'Detail mangy';
                $table = 'manga';
                $loadedContent = $contentHandler->getContent($table, $param[1]);
                
                if (!$loadedContent){
                    $this->addMessage('Manga nenalezena.', 'error');
                    $this->redirect('manga');
                } 
            } else {
                $this->addMessage('Něco se pokazilo, zkuste to prosím znovu', 'warning');
                $this->redirect('uvod');
            }
        } else {
            $this->addMessage('Adresa není správná, zkuste to znovu.', 'warning');
            $this->redirect('uvod');
        }*/


        // pokud je prihlasen uzivatel, nacti jeho hodnoceni
        if ($user){
            $userRating = $contentHandler->getMyRating($table, $user['_id'], $param[1]);
            $this->data['userRating'] = $userRating[0][0];
        }
        

        $this->data['inListCount'] = MujSeznamHandler::inListCount($table, $param[1]);
        $this->data['data'] = $loadedContent[0];
        $this->data['type'] = $table;
        $this->head['title'] = $pageTitle;
        $this->view = 'detail';
    }
}
?>