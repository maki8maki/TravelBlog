<?php
    class PostingElement
    {
        public $title = "";
        public $contents = "";
        // public $imgs;

        public function inputElement(PDO $pdo, string $title, $contents)
        {
            if (!empty($title) && trim($title) != "" && !empty($contents) && trim($contents) != "") {
                $this -> title = htmlspecialchars($title);
                $this -> contents = nl2br(htmlspecialchars($contents));
                return TRUE;
            } else {
                echo "空欄があります。";
                return FALSE;
            }
        }

        public function postElement(PDO $pdo, string $table_name, string $date=NULL)
        {
            if ($date == NULL) {
                $sql = $pdo -> prepare("insert into ".$table_name." (title, contents) values (:title, :contents)");
                $sql -> bindParam(':title', $this->title, PDO::PARAM_STR);
                $sql -> bindParam(':contents', $this->contents, PDO::PARAM_STR);
            } else {
                $sql = $pdo -> prepare("insert into ".$table_name." (title, contents, date) values (:title, :contents, :date)");
                $sql -> bindParam(':title', $this->title, PDO::PARAM_STR);
                $sql -> bindParam(':contents', $this->contents, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            }
            $sql -> execute();
        }

        public function displayElement()
        {
            if (!empty($this->title) && !empty($this->contents)) {
                echo $this->title."<br>\n";
                echo $this->contents."<br>\n\t";
            }
        }
    }

    class TotalPosting
    {
        public $main;
        public $subs = [];
        public $maintable_name;
        public $set_maintitle = FALSE;

        public function __construct(string $maintable_name)
        {
            $this -> main = new PostingElement();
            $this -> maintable_name = $maintable_name;
        }

        public function inputMain(PDO $pdo, string $title, string $contents) {
            $this -> set_maintitle = $this -> main -> inputElement($pdo, $title, $contents);
        }

        public function inputSub(PDO $pdo, string $title, string $contents) {
            $subelement = new PostingElement();
            $_ = $subelement -> inputElement($pdo, $title, $contents);
            array_push($this->subs, $subelement);
        }

        public function postAll(PDO $pdo)
        {
            $date = date("Y/m/d H:i:s");
            $this -> main -> postElement($pdo, $this->maintable_name, $date);
            $maincontents_id = $pdo -> lastinsertID();
            $subtable_name = "subcontents_".$maincontents_id;
            $sql = "create table if not exists ".$subtable_name
                ." ("
                . "subcontents_id INT AUTO_INCREMENT PRIMARY KEY,"
                . "title TEXT,"
                . "contents TEXT"
                . ");";
            $pdo->query($sql);
            foreach ($this->subs as $sub) {
                $sub -> postElement($pdo, $subtable_name);
            }
            // サブの投稿
        }

        public function displayAll()
        {
            $this -> main -> displayElement();
            foreach ($this->subs as $sub) {
                $sub -> displayElement();
            }
        }
    }
?>