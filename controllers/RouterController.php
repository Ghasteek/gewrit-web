<?php 
    class RouterController extends Controller {
        protected $controller;

        public function process($param){
            $parsedURL = $this->parseURL($param[0]);

            if (empty($parsedURL[0])) $this->redirect('uvod');

            $classOfController = $this->dashIntoCamelCase(array_shift($parsedURL)) . "Controller";

            if (file_exists("controllers/" . $classOfController . ".php")) {
                $this->controller = new $classOfController;
            } else{
                $this->redirect('error');
            }

            $this->controller->process($parsedURL);

            $this->data['title'] = $this->controller->head['title'];
            $this->data['description'] = $this->controller->head['description'];
            $this->data['keywords'] = $this->controller->head['keywords'];
            $this->data['messages'] = $this->getMessages();

            $this->view = 'indexTemplate';
        }

        public function parseURL($url){
                // odstrani adresu od cesty
            $parsedURL = parse_url($url);
                // odstrani "/" na zacatku a na konci "path"
            $parsedURL["path"] = ltrim($parsedURL["path"], "/");
                // odstrani "bile znaky" v "path"
            $parsedURL["path"] = trim($parsedURL["path"]);
                // rozdelii "path" do pole, delici prvek je "/"
            $explodedPath = explode("/", $parsedURL["path"]);
                // vrati pole s jednotlivymi prvky cesty
            return  $explodedPath;
        }

            // odstrani "-" z paramatru "path" a prevede jej na CamelCase kvuli jine konvenci pouzivanych nazvu
        public function dashIntoCamelCase ($text){  
            $sentence = str_replace("-", " ", $text);
                // prvni pismenko v kazdem slovu vety prevede na velke
            $sentence = ucwords($sentence);
            $sentence = str_replace(" ", "", $sentence);
                // vrati parametr "path" v CamelCase
            return $sentence;
        }
    }
?>