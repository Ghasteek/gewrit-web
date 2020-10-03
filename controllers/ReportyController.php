<?php 
    class ReportyController extends Controller {
        public function process ($param){
            $this->data['seznamType'] = 'all';
            $this->authenticateUser(0);

            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            $this->data['userRights'] = $user['rights'];


            $reportyHandler = new ReportyHandler();

            // je zadane v parametrech / url ID reportu?
            if ($param[0]) { // pokud ano, zobraz detail reportu
                $this->authenticateUser(1);
                $data = $reportyHandler->getReport(array($param[0]));

                // nacteni ceho se report tyka
                if ($data[0]['reportType'] === 'uzivatel'){
                    try{
                        $target = $userAdministration->getUserById($data[0]['reportForId']);
                    } catch (UserError $error) {
                        $this->addMessage($error->getMessage(), 'warning');
                    }
                } else {
                    $target = ContentHandler::getContent($data[0]['reportType'], $data[0]['reportForId']);
                }
                $this->data['type'] = $data[0]['reportType'];

                if ($data[0]['status'] == "closed") $this->authenticateUser(2);

                $this->data['loggedId'] = $user['_id'];
                $this->data['target'] = $target;
                $this->data['data'] = $data[0];
                $this->head['title'] = 'Gewrit - Report';
                $this->view = 'report'; // zobrazeni detailu

                if ($_POST){
                    if ($_POST['status'] != "closed") { $_POST['status'] = "open";}
    
                    $reportyHandler->saveReport($_POST['_id'], array('answer' => $_POST['answer'], 'authorOfAnswer' => $_POST['authorOfAnswer'], 'status' => $_POST['status']));
    
                    $this->redirect('reporty');
                    //echo "odpoved od: " . $_POST['authorOfAnswer'] . " | Text odpovedi: " . $_POST['answer'] . " | je " . $_POST['status'];
                }

            } else { // pokud ne, zobraz podle prav reporty
                if ($_POST['form'] == "true"){
                    $this->authenticateUser(1);
                    $max = sizeof($_POST['type']);
                    for ($i=0; $i<$max; $i++){
                        $condition = $condition . "`reportType` = '" . $_POST['type'][$i] . "'" ;
    
                        if (($max-1) != $i) { $condition = $condition . " OR " ; };

                        $this->data[$_POST['type'][$i]] = true;
                    }
    
                    $condition = "(" . $condition . ")";
    
                    if ($_POST['open'] == 'open') { 
                        $condition = $condition . " AND `status` = 'open'"; 
                        $this->data['open'] = true;
                    }
        
                    if ($max != 0) { $data = $reportyHandler->getReports($condition);}

                } else {
                    if ($user['rights'] == 2){
                        $data = $reportyHandler->getAllReports();
                        $this->data['open'] = false;
                    } elseif ($user['rights'] == 1) {
                        $data = $reportyHandler->getModReports();
                        $this->data['open'] = true;
                    } else {
                        $condition = "`author` = ('" . $user['_id'] . "')";
                        $data = $reportyHandler->getReports($condition);
                    }
                    
                    
                    $this->data['anime'] = true;
                    $this->data['manga'] = true;
                    $this->data['serial'] = true;
                    $this->data['uzivatel'] = true;
                }
    
                $this->data['rights'] = $user['rights'];
                $this->data['data'] = $data;
                $this->head['title'] = 'Gewrit - Reporty';
                $this->view = 'reporty';
            }
        }
    }
?>