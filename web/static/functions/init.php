<?php
    require_once 'connect.php';
    require_once 'function.php';

    abstract class ErrorMessage {
        const AUTH_WRONG = "ERROR 01 : Wrong username or password";
        const AUTH_INVALID_EMAIL_TOKEN = "ERROR 06 : That email confirmation token is invalid, is that expired?";
        const AUTH_INVALID_RESET_PASSWORD_TOKEN = "ERROR 07 : That reset password token is invalid, is that expired?";
        
        const USER_NOT_FOUND = "ERROR 10 : User not found";
        const USER_ERROR = "ERROR 19 : Found conflict user";

        const FILE_IO = "ERROR 20 : Cannot performing File IO";
        const FILE_UPLOAD_NOT_FOUND = "ERROR 21 : Cannot locate the uploaded file";

        const DATABASE_ESTABLISH = "ERROR 40 : Cannot established with the database";
        const DATABASE_QUERY = "ERROR 41 : Cannot query with the database";
        const DATABASE_ERROR = "ERROR 49 : Unexpected internal database error";
        const DATABASE_CONFLICT = "ERROR 42 : Database conflict with the database";

        const SESSION_INVALID = "ERROR 60 : Session is invalid";
        
        const PERMISSION_REQUIRE = "ERROR 90 : You do not have enough permission";
        const PERMISSION_ERROR = "ERROR 99 : Found conflict permission";
    }

    abstract class Role {
        const ADMIN = "admin";
        const ROLES = array(Role::ADMIN);
    }

    abstract class Language {
        const JAVA = "Java";
        const PYTHON = "Python";
        const C = "C";
        const CPP = "C++";
        const LCA = "LCA (Custom Template)";
        const LANGUAGES = array(Language::JAVA, Language::PYTHON, Language::C, Language::CPP, Language::LCA);
    }

    class Problem {
        protected int $id;
        protected String $codename, $name, $writer;
        protected int $score, $memory, $time;
        protected $properties;

        public function getID() {
            return $this->id;
        }
        public function setID(int $id) {
            $this->id = $id;
        }

        public function display() {
            return $this->name . " <span class='badge badge-coekku'>" . $this->codename . "</span>";
        }

        public function getCodename() {
            return $this->codename;
        }
        public function setCodename(String $codename) {
            $this->codename = $codename;
        }

        public function getName() {
            return $this->name;
        }
        public function setName(String $name) {
            $this->name = $name;
        }

        public function getAuthor() {
            return $this->writer;
        }
        public function setAuthor(String $writer) {
            $this->writer = $writer;
        }

        public function getScore() {
            return $this->score;
        }
        public function setScore(int $score) {
            $this->score = $score;
        }

        public function getMemory() {
            return $this->memory;
        }
        public function setMemory(int $memory) {
            $this->memory = $memory;
        }

        public function getTime() {
            return $this->time;
        }
        public function setTime(int $time) {
            return $this->time = $time;
        }

        public function properties() {
            return $this->properties;   
        }
        public function getProperties(String $key) {
            if (empty($this->properties)) return false;
            return array_key_exists($key, $this->properties()) ? $this->properties()[$key] : false;
        }
        public function setProperties(String $key, $val) {
            $this->properties[$key] = $val;
        }

        public function __construct(int $id) {
            $this->id = $id;
            $data = getProbData($id);
            if (!empty($data)) {
                $this->name = $data['name'];
                $this->codename = $data['codename'];
                $this->writer = empty($data['author']) ? "" : $data['author'];
                $this->score = $data['score'];
                $this->memory = $data['memory'];
                $this->time = $data['time'];
                $this->properties = json_decode($data['properties'], true);
            } else {
                $this->id = -1;
            }
        }
    }

    class User {
        protected int $id;
        protected String $user, $email, $name;
        public $profile, $properties;

        public function getID() {
            return $this->id;
        }
        public function setID(int $id) {
            $this->id = $id;
        }

        public function getUsername() {
            return $this->user;
        }
        public function setUsername(String $username) {
            $this->user = htmlspecialchars($username);
        }

        public function getName() {
            return $this->name;
        }
        public function getDisplayname() {
            $name = $this->name;
            if ($this->getProperties("rainbow")) $name = "<text class='rainbow'>$name</text>";
            if ($this->isAdmin()) $name .= " <span class='badge badge-coekku'>Admin</span>";
            return $name;
        }
        public function setName(String $name) {
            $this->name = htmlspecialchars($name);
        }

        public function getEmail() {
            return $this->email;
        }
        public function setEmail(String $email) {
            $this->email = htmlspecialchars($email);
        }

        public function getProfile() {
            if (empty($this->profile) || !file_exists($this->profile)) return "../static/elements/user.svg";
            return $this->profile;
        }
        public function setProfile(string $url) {
            $this->profile = $url;
        }

        public function properties() {
            return $this->properties;   
        }
        public function getProperties(String $key) {
            if (empty($this->properties)) return null;
            return array_key_exists($key, $this->properties()) ? $this->properties()[$key] : null;
        }
        public function setProperties(String $key, $val) {
            $this->properties[$key] = $val;
        }

        public function isAdmin() {
            return $this->getProperties("admin");
        }

        public function getInfo() {
            return array(
                "id" => $this->id,
                "username" => $this->getUsername(),
                "name" => $this->getName(),
                "email" => $this->getEmail(),
                "profile" => $this->getProfile(),
                
            );
        }

        public function __construct(int $id) {
            $this->id = $id;
            $data = getUserData($id);
            if (!empty($data)) {
                $this->name = htmlspecialchars($data['displayname']);
                $this->email = htmlspecialchars($data['email']);
                $this->user = htmlspecialchars($data['username']);
                $this->profile = $data['profile'];
                $this->properties = json_decode($data['properties'], true);
            } else {
                $this->id = -1;
            }
        }
    }
?>