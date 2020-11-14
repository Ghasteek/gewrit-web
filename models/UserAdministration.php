<?php
    class UserAdministration{
        // vrati HASH predaneho hesla
        public function getHash($password){
            return password_hash($password, PASSWORD_DEFAULT);
        }

        // registruj noveho uzivatele 
        public function register($username, $password, $rePassword, $email, $antispam){
            if ($antispam != date('Y'))
                throw new UserError('Chybně vyplněný AntiSPAM.');
            if ($password != $rePassword) 
                throw new UserError('Hesla nesouhlasí.');
            $user = array(
                'username' => $username,
                'email' => $email,
                'password' => $this->getHash($password),
            );
            //print_r($user);
            try {
                Db::insert('users', $user);
            } catch (PDOException $error) {
                throw new UserError('Uživatel s tímto jménem je již registrovaný.');
            }
        }

        // registruj noveho uzivatele 
        public function updatePassword($email, $password, $rePassword){
            if ($password != $rePassword) 
                throw new UserError('Hesla nesouhlasí.');
            $user = array(
                'password' => $this->getHash($password),
            );
            //print_r($user);
            try {
                Db::change('users', $user, 'WHERE email = ?', array($email));
                //Db::insert('users', $user);
            } catch (PDOException $error) {
                throw new UserError('Něco se pokazilo. Zkuste to znovu.');
            }
        }

        // prihlaseni do systemu
        public function login($username, $password){
            $user = Db::getOne('
                SELECT * 
                FROM users
                WHERE username = ?',
                array($username));
            if (!$user || !password_verify($password, $user['password']))
                throw new UserError('Neplatné jméno nebo heslo.');

            Db::remove('password_recovery', 'email', $user['email']);

            $_SESSION['user'] = $user;
            $_SESSION['checking'] = array('anime', 'manga', 'serial');
        }

        // odhlaseni ze systemu
        public function logout(){
            unset($_SESSION['user']);
            throw new UserError('Odhášení úspěšné.');
        }

        // vrati aktualniho uzivatele
        public function getUser(){
            if (isset($_SESSION['user']))
                return $_SESSION['user'];
            return null;
        }

        // vrati uzivatele se zadanym ID
        public function getUserById($id){
            $user = Db::getOne('
                SELECT _id, username, email, rights 
                FROM users
                WHERE _id = ?',
                array($id));
            if (count($user) == 1)
                throw new UserError('Uživatel s tímto ID neexistuje.');
            return $user;
        }

        // overi pritomnost emailu v databazi
        public function isThereUser($email){
            $user = Db::getOne("
                SELECT COUNT(*)
                FROM users
                WHERE email = ?",
                array($email));
            if ($user[0] == 0)
                throw new UserError('Uživatel s tímto emailem neexistuje.');
        }

         // vrati radek s emailem a tokenem y db
         public function getTokenByEmail($email){
            return Db::getOne("
                SELECT *
                FROM password_recovery
                WHERE email = ?",
                array($email));
        }

        // vrati radek s emailem a tokenem y db
        public function getTokenByToken($token){
            return Db::getOne("
                SELECT *
                FROM password_recovery
                WHERE token = ?",
                array($token));
        }

        // ulozit token s emailem do db
        public function insertToken($email, $token){
            $line = array(
                'email' => $email,
                'token' => $token
            );
            try {
                Db::insert('password_recovery', $line);
            } catch (PDOException $error) {
                throw new UserError('Požadavek o obnovení hesla účtu s tímto emailem již existuje.');
            }
        }

         // vymaze token dle emailu
         public function removeToken($email){
            try {
                Db::remove('password_recovery', 'email', $email);
            } catch (PDOException $error) {
                throw new UserError('Něco se pokazilo');
            }
        }

        // zobrazi maly profil na uvodni obrazovce
        public function showProfile(){
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            if (count($user) == 0){
                echo "<li class='menu-item' title='Přejít na stránku přihlášení'><a href='prihlaseni'>Přihlásit se</a></li>|" . 
                    "<li class='menu-item' title='Registrujte se'><a href='registrace'>Registrovat se</a></li>";
            } else {
                $rights = $user['rights'];
                if ($rights >= 0) {
                    $items = "<li class='menu-item' title='Přidat obsah do databáze'><a href='pridat'>Přidat do DB</a></li>" . 
                        "<li class='menu-item' title='Vámi podané reporty nekorektního obsahu'><a href='reporty'>Mé reporty</a></li>";
                } 
                if ($rights >= 1) {
                    $items = $items . "<li class='menu-item' title='Nově přidaný obsah čekající na schválení'><a href='cekarna'>Ke schválení</a></li>";
                } 
                if ($rights >= 2) {
                    $items = $items . "<li class='menu-item' title='Správa moderátorů obsahu'><a href='mod'>Moderátoři</a></li>" . 
                    "<li class='menu-item' title='Správa zabanovaných uživatelů'><a href='ban'>Bany</a></li>";
                }

                echo "<li class='user-menu'><a href='profil'>" . $user['username'] . "</a>" . 
                    "<ul class='sub-menu'> " . $items . 
                    "<li title='Odhlásit se z portálu'><a href='profil/odhlasit'>Odhlásit</a></li>" . 
                    "</ul></li>";
            }
        }

        // zobrazi odkaz na muj seznam, pokud jsem prihlasen
        public function showMyList($title){
            $userAdministration = new UserAdministration();
            $user = $userAdministration->getUser();
            if (($title == 'Gewrit - Můj seznam') || ($title == 'Gewrit - Přidat do seznamu')){
                $active = 'class="active"';
            }
            if (count($user) != 0){
                echo "<li title='Můj seznam sledovaných'><a $active href='muj-seznam'>Můj seznam</a></li>";
            }
        }

        // vrati pocet uzivatelu
        public function getUserCount(){
            $sql = "SELECT COUNT(_id) from `users`";
            return Db::getAll($sql);
        }
    }
?>