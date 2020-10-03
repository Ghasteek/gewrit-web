<?php 
    class AnimeController extends Controller {
        public function process ($param){
            $contentHandler = new ContentHandler();
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            $this->data['rights'] = $user && $user['rights'];
            $this->data['userRights'] = $user['rights'];
            $this->data['seznamType'] = 'all';

            $table = 'anime';

            // zjisteni aktualni strany z URL
            if ($param){
                if (intval($param[0]) != 0){ // kontrola, jestli je parametr strany v url skutecne cislo
                    $currentPage = intval($param[0]);
                } else { 
                    $currentPage = 1; 
                    $this->redirect('anime');
                }
            } else { 
                $currentPage = 1; 
            }

            $itemsPerPage = 12;

            $searchString = $this->cleanString($_GET['hledat']);

            $rows = $contentHandler->getContentByAuthorCount($table, $user['_id'], $searchString);

            if ($rows[0][0] > 0){
                $pages = ceil($rows[0][0] / $itemsPerPage);

                if ($currentPage > $pages){
                    $this->addMessage('Tolik záznamů v databázi není.', 'warning');
                    $this->redirect('anime');
                }

                $offset = ($currentPage - 1) * $itemsPerPage;
                $animes = $contentHandler->getContentByAuthorPaginate($table, $user['_id'], $offset, $itemsPerPage, $searchString);

                $this->data['pages'] = $pages;
                $this->data['actualPage'] = $currentPage;
                $this->data['data'] = $animes;
            }
            
            $this->data['title'] = 'Seznam anime';
            $this->data['type'] = 'anime';
            $this->head['title'] = 'Gewrit - Anime';
            $this->view = 'seznamTemplate';
        }
    }
?>