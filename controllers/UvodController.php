<?php 
    class UvodController extends Controller {
        public function process ($param){
            $contentHandler = new ContentHandler();

            $user = UserAdministration::getUser();
            if ($user) { 
                $this->data['isLogged'] = true; 
            
                // nacteni dnes vydanych polozek--------------------
                $todayReleased = MujSeznamHandler::getTodayReleasedRecords();

                $today = array();
                    // nacitani obsahu k jednotlivym zaznamum
                    foreach ($todayReleased as $line) {
                        $dataLine = $contentHandler->getContent($line['type'], $line['item']);
                        $dataLine[0]['type'] = $line['type']; // pridani type polozky pole kvuli zobrazeni
                        array_push($today, $dataLine[0]);
                    }
                    
                $this->data['records'] = $todayReleased;
                $this->data['today'] = $today;
                //----------------------------------------------------
            }

            $itemsPerRow = 5; // kolik nejnovejsich zobrazit
            // nacteni nejnovejsich serialu ----------------------
            $latestSerials = $contentHandler->getNewestContent('serial', $itemsPerRow); // odkud a kolik
            $this->data['latestSerials'] = $latestSerials;
            //----------------------------------------------------

            // nacteni nejnovejsich anime ------------------------
            $latestAnime = $contentHandler->getNewestContent('anime', $itemsPerRow); // odkud a kolik
            $this->data['latestAnime'] = $latestAnime;
            //----------------------------------------------------

            // nacteni nejnovejsi mangy --------------------------
            $latestManga = $contentHandler->getNewestContent('manga', $itemsPerRow); // odkud a kolik
            $this->data['latestManga'] = $latestManga;
            //----------------------------------------------------

            $this->data['active'] = 'uvod';
            $this->head['title'] = 'Gewrit';
            $this->view = 'uvod';
        }
    }
?>