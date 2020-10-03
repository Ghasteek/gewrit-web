<?php 
    class Db{
        private static $connection;
        private static $settings = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_EMULATE_PREPARES => false,
        );

        // Vytvoreni pripojeni
        public static function connect($host, $user, $password, $dbname){
            // Vytvorit pripojeni jen pokud zadnbe neexistuje
            if (!isset(self::$connection)){
                self::$connection = @new PDO(
                    "mysql:host=$host;dbname=$dbname",
                    $user,
                    $password,
                    self::$settings
                );
            }
        }

        // Dotaz na jeden zaznam
        public static function getOne($querry, $parameters = array()){
            $result = self::$connection->prepare($querry);
            $result->execute($parameters);
            return $result->fetch();
        }

        // Dotaz na vice zaznamu
        public static function getAll($querry, $parameters = array()){
            $result = self::$connection->prepare($querry);
            $result->execute($parameters);
            return $result->fetchAll();
        }

        // napr pro SELCT COUNT(*)...
        public static function getColumn($querry, $parameters = array()){
            $result = self::getOne($querry, $parameters);
            return $result[0];
        }


        // Spusti dotaz a vrati pocet ovlivnenych radku
        public static function getChanges($querry, $parameters = array()){
            $result = self::$connection->prepare($querry);
            $result->execute($parameters);
            return $result->rowCount();
        }

        // vlozi do DB zaznam, prvni parametr je tabulka kam vlozit a druhy je pole obsahujici data ktera vlozit (data musi byt klicovana podle tabulky)
        public static function insert($table, $data = array()){
            return self::getChanges("INSERT INTO `$table` (`".
                implode('`, `', array_keys($data)).
                "`) VALUES (".str_repeat('?,', sizeOf($data)-1)."?)",
                array_values($data));
        }

        // upravi zaznam v DB, zadavame "tabulku", "data", "podminku" ve tvaru "WHERE `_id` = ?" a na konci "parametr", ve kterem jsou parametry podminky a vrati pocet upravenych radku
        public static function change($table, $data = array(), $condition, $conditionParameters = array()){
            return self::getChanges("UPDATE `$table` SET `".
                implode('` = ?, `', array_keys($data)).
                "` = ? " . $condition,
                array_merge(array_values($data), $conditionParameters));
        }

        // autorizuje zaznamy ze zadane tabulka a zadanych ID
        public static function authorizeIds($table, $ids = array()){
            return self::getChanges("UPDATE `$table` SET `onlyFor` = 0 
                WHERE `_id` IN (".str_repeat('?,', sizeOf($ids)-1)."?)",
                $ids);
        }

        // vrati ID posledniho vlozeneho zaznamu
        public static function getLastId(){
            return self::$connection->lastInsertId();
        }

        // vymaze zaznam
        public static function remove($table, $column, $value){
            return self::getChanges("DELETE FROM `$table` WHERE $column = '$value'");
        }
    }
?>