<?php
class DetailController extends Controller
{
    public function process($param){
        $contentHandler = new ContentHandler();

        $userAdministration = new UserAdministration();
        $user = $userAdministration->getUser();
        if ($user['rights'] >= 0) {
            $allowRating = true;
        } else {
            $allowRating = false;
        }

        if (intval($param[1]) != 0){
            if ($param[0] === 'serial'){
                $table = 'serial';
                $loadedContent = $contentHandler->getContent($table, $param[1]);
                
                if (!$loadedContent){
                    $allowRating = false;
                    $this->addMessage('Seriál nenalezen.', 'error');
                    $this->redirect('serialy');
                } 
            }
        }else{
            $this->addMessage('Adresa není správná, zkuste to znovu.', 'warning');
            $this->redirect('uvod');
        }

        $userRating = $contentHandler->getMyRating($table, $user['_id'], $param[1]);
        print_r($userRating);

        $this->data['inListCount'] = MujSeznamHandler::inListCount($table, $param[1]);
        $this->data['userRating'] = $userRating[0][0];
        $this->data['data'] = $loadedContent[0];
        $this->data['type'] = $table;
        $this->view = 'detail';
    }
}
?>