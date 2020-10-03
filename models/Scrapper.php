<?php
    class Scrapper {
        //
        public function scrapUrl($url, $year){
            if ($year != date('Y'))
                throw new UserError('Chybně vyplněný AntiSPAM.');

            $url = filter_var($url, FILTER_SANITIZE_URL);

            if (strpos($url, 'www.csfd.cz') !== false) {
                $serial = $this->scrapCsfd($url);
                return $serial;
            } elseif (strpos($url, 'www.anime-planet.com') !== false) {
                $anime = $this->scrapAnimePlanet($url);
                return $anime;
            } else {
                throw new UserError('Nepodporovaný portál. Prosím, vkládejte jen odkaz na CSFD nebo ANIME-PLANET.');
            }
        }

        //
        public function scrapCsfd($url) {
            // scrap CSFD
            $id = (int) filter_var($url, FILTER_SANITIZE_NUMBER_INT);
            $dom = new domDocument;
            $csfdUrl = "http://www.csfd.cz/film/$id";
            $csfd = file_get_contents($csfdUrl);
            $html = (ord($csfd[0]) == 31) ? gzdecode($csfd) : $csfd;
            @$dom->loadHTML($html);
            $dom->preserveWhiteSpace = false;

            $xpath = new DOMXPath($dom);
            $nazvy = array();
            $zeme = array();
            $names_other = "";
            $nodes = $xpath->query("//h1[@itemprop='name']");
            $names_cs = $nodes->item(0)->nodeValue;
            if (!strpos($names_cs, "(TV seriál)")) {
                throw new UserError('Nejedná se o odkaz na seriál. Pokud se jedná o seriál, prosím, kontaktujte nás.');
            }
            $names_cs = str_replace("(TV seriál)", "", $names_cs);

            foreach($xpath->query("//ul[@class='names']/li/h3") as $li) {
                $nazvy[] = $li->nodeValue;
            }
            foreach($xpath->query("//ul[@class='names']/li/img") as $li) {
                $zeme[] = $li->getAttribute('alt');
            }
            for($i=0;$i<count($nazvy);$i++){
                if($i==count($nazvy)-1)
                    $names_other .= $nazvy[$i]." - ".$zeme[$i];
                else
                    $names_other .= $nazvy[$i]." - ".$zeme[$i].";<br>";
            }

            $nodes = $xpath->query("//p[@class='genre']");
            $genre = str_replace(' / ', ', ', $nodes->item(0)->nodeValue);

            $nodes = $xpath->query("//span[@itemprop='dateCreated']");
            $rok = $nodes->item(0)->nodeValue;

            $nodes = $xpath->query("//div[@data-truncate='570']");
            $popis = $nodes->item(0)->nodeValue;

            $nodes = $xpath->query("//img[@class='film-poster']");
            $poster_url = "http:".$nodes->item(0)->getAttribute('src');

            $nodes = $xpath->query("//a[@title='profil na IMDb.com']");
            $imdb = $nodes->item(0)->getAttribute('href');

            $serial = array(
                'name' => trim($names_cs),
                'image' => trim($poster_url),
                'year' => trim($rok),
                'genre' => trim($genre),
                'linkCsfd' => trim($url),
                'linkImdb' => trim($imdb),
                'description' => trim($popis),
                'type' => 'serial'
            );

            return $serial;
        }

        //
        public function scrapAnimePlanet($url) {

            if (strpos($url, 'www.anime-planet.com/manga/') !== false) {
                $type = 'manga';
            } else {
                $type = 'anime';
            }
                    
            // scrap Anime Planet
            $dom = new domDocument;
            $ap = file_get_contents($url);
            $html = (ord($ap[0]) == 31) ? gzdecode($ap) : $ap;
            @$dom->loadHTML($html);
            $dom->preserveWhiteSpace = false;

            $xpath = new DOMXPath($dom);
            $nodes = $xpath->query("//h1[@itemprop='name']");
            $names_cs = $nodes->item(0)->nodeValue;

            $nodes = $xpath->query("//li[@itemprop='genre']");
            $genre = array();
            foreach ($nodes as $item) {
                $string = trim($item->nodeValue);
                array_push($genre, $string);
            }
            $genre = implode(', ', $genre);

            $nodes = $xpath->query("//div[@itemprop='description']");
            $popis = str_replace(' <p> ', '', $nodes->item(0)->nodeValue);
            $popis = str_replace(' </p> ', '', $popis);

            $nodes = $xpath->query("//span[@class='iconYear']");
            $rok = $nodes->item(0)->nodeValue;
            $rok = str_replace(' ', '', $rok);

            $nodes = $xpath->query("//img[@class='screenshots']");
            $poster_url = "https://www.anime-planet.com/".$nodes->item(0)->getAttribute('src');

            $anime = array(
                'name' => trim($names_cs),
                'image' => trim($poster_url),
                'year' => trim($rok),
                'genre' => trim($genre),
                'linkAp' => trim($url),
                'description' => trim($popis),
                'type' => $type
            );

            return $anime;
        }
    }
?>