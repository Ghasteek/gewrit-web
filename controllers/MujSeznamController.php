<?php 
    class MujSeznamController extends Controller {
        public function process ($param){
            $this->authenticateUser();

            $mujSeznamHandler = new MujSeznamHandler();
            $userAdministration = new UserAdministration();
            $contentHandler = new ContentHandler();
            $user = $userAdministration->getUser();
            $validParams = ' sleduji, pozastaveno, odlozeno, dokonceno, nesledovat, planovane'; // validní kategorie
            $itemsPerPage = 10; // definice kolik zaznamu je na strance

            $this->data['rights'] = $user && $user['rights'];
            $this->data['userRights'] = $user['rights'];
            $this->data['seznamType'] = 'my';

            if (!$_SESSION['checking']) { 
                $_SESSION['checking'] = 'anime,manga,serial';
                $this->data['checked'] = implode(", ", $_SESSION['checking']);
            };

            if ($param[0] && (strpos($validParams, $param[0]) == true)){
                $urlWithStatus = "muj-seznam/" . $param[0];
            } else {
                $urlWithStatus = "muj-seznam";
            }

            $this->data['urlWithStatus'] = $urlWithStatus;

            // zpracovani POST
            if ($_POST) {
                
                // vyber ktere zaznamy zobrazovat
                if ($_POST['form']){ 
                    if (count($_POST['checking']) == 0) { $_POST['checking'] = array('anime', 'manga', 'serial'); }
                    $_SESSION['checking'] = $_POST['checking'];
                    $this->data['checked'] = implode(", ", $_POST['checking']);

                // proved +1 u daneho zaznamu
                } elseif ($_POST['plusOne']) {
                    try {
                        $mujSeznamHandler->isMine($_POST['_id']); // je ID zaznamu v parametru moje?
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }

                    $mujSeznamHandler->pridejJeden($_POST['_id']); // pridej jednu episodu

                    $this->addMessage('Přičteno.' , 'info');
                    
                    if ($_POST['origin']) {
                        $this->redirect($_POST['origin']);
                    } else {
                        $this->redirect($urlWithStatus);
                    }

                //smaz zaznam
                } elseif ($_POST['delete']) {
                    try {
                        $mujSeznamHandler->isMine($_POST['_id']); // je ID zaznamu v parametru moje?
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }

                    $mujSeznamHandler->deleteRecord($_POST['_id']); // smaz zaznam

                    $this->addMessage('Záznam smazán.' , 'info');
                    $this->redirect($urlWithStatus);

                // uloz zmeny v zaznamu
                } else {
                    try {
                        $mujSeznamHandler->isMine($_POST['_id']); // je ID zaznamu v parametru moje?
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                        $this->redirect('muj-seznam');
                    }
    
                    $record = array(
                        'series' => $_POST['series'],
                        'episode' => $_POST['episode'],
                        'link' => $_POST['link'],
                        'releaseDay' => implode(",", $_POST['releaseDay']),
                        'status' => $_POST['status']
                    );
                    // ulozim zaznam z POSTu
                    $mujSeznamHandler->saveRecord($_POST['_id'], $record);
    
                    $this->addMessage('Záznam uložen.', 'info');
                    $this->redirect($urlWithStatus);
                }
            } else {
                $this->data['checked'] = implode(",", $_SESSION['checking']); // poslani templatu co je zaskrtnute ze $_SESSION
            }

            // vyhodnoceni parametru
            if ($param){
                // pokud je prvni parametr cislo, strankuj
                if (intval($param[0]) != 0){
                    $status = 'all';
                    $currentPage = intval($param[0]);
                } else {
                    // pokud neni cislo, zjisti, jestli je to validni kategorie
                    if (!$param[0]) $param[0] == "all";

                    if (strpos($validParams, $param[0]) == true){
                        // pokud je v parametru kategorie, zobraz ji
                        $status = $param[0];

                        if ($param[1]){
                            //pokud posilame dalsi parametr...
                            if (intval($param[1]) != 0){
                                // je-li to cislo, pouzij ho pro strankovani...
                                $currentPage = intval($param[1]);
                            } else {
                                // pokud ne, tak zobraz stranu 1 zadane kategorie
                                $currentPage = 1; 
                                $this->redirect($urlWithStatus);
                            }
                        } else {
                            // pokud dalsi parametr neni pritomen, zobraz stranu 1
                            $currentPage = 1; 
                        }
                            
                    } else {
                        //pokud neni validni kategorie, redirectni na kompletni seznam
                        $status = "all";
                        $currentPage = 1; 
                        $this->redirect('muj-seznam');
                    }
                }
            } else { 
                // pokud neni zadny parametr, zobraz kompletni seznam
                $status = "all";
                $currentPage = 1; 
            }

            $rows = $mujSeznamHandler->getRecordsByTypeCount($_SESSION['checking'], $status);

            // nacteni zaznamu podle strankovani
            if ($rows[0][0] > 0){
                $pages = ceil($rows[0][0] / $itemsPerPage);

                if ($currentPage > $pages){
                    $this->addMessage('Tolik záznamů v databázi není.', 'warning');
                    $this->redirect($urlWithStatus);
                }

                $offset = ($currentPage - 1) * $itemsPerPage;

                $records = $mujSeznamHandler->getRecordsByTypePaginate($offset, $itemsPerPage, $_SESSION['checking'], $status);

                $data = array();
                // nacitani obsahu k jednotlivym zaznamum
                foreach ($records as $line) {
                    $dataLine = $contentHandler->getContent($line['type'], $line['item']);
                    $dataLine[0]['type'] = $line['type']; // pridani type polozky pole kvuli zobrazeni
                    array_push($data, $dataLine[0]);
                }
                $this->data['pages'] = $pages;
                $this->data['actualPage'] = $currentPage;
                $this->data['data'] = $data;
                $this->data['records'] = $records;
            }
            $this->data['active'] = $status;
            $this->data['title'] = 'Můj seznam';
            $this->head['title'] = 'Gewrit - Můj seznam';
            $this->view = 'seznamTemplate';
        }
    }
?>