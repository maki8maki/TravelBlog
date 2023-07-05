<?php
    include_once "basic.php";

    class PostingElement
    {
        public $title = "";
        public $contents = "";
        public $img_name = "";

        public function inputElement(PDO $pdo, string $title, string $contents, array $img_file=NULL)
        {
            if (!empty($title) && trim($title) != "" && !empty($contents) && trim($contents) != "") {
                $this -> title = htmlspecialchars($title);
                $this -> contents = nl2br(htmlspecialchars($contents));
                if ($img_file != NULL) {
                    $this -> img_name = basename($img_file["name"]);
                    move_uploaded_file($img_file["tmp_name"], "tmp/".$this->img_name);
                }
                return TRUE;
            } else {
                echo "空欄があります。<br>\n\t";
                return FALSE;
            }
        }

        public function postElement(PDO $pdo, string $table_name, string $img_tag, string $date=NULL)
        {
            if (!empty($this->img_name)) {
                $new_img_name = $img_tag . "_" . uniqid(mt_rand(), TRUE) . '.' . substr(strrchr($this->img_name, '.'), 1);
                rename("tmp/".$this->img_name, "imgs/".$new_img_name);
            } else {
                $new_img_name = "";
            }
            if ($date == NULL) {
                $sql = $pdo -> prepare("insert into ".$table_name." (title, contents, img_name) values (:title, :contents, :img_name)");
            } else {
                $sql = $pdo -> prepare("insert into ".$table_name." (title, contents, img_name, date) values (:title, :contents, :img_name, :date)");
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            }
            $sql -> bindParam(':title', $this->title, PDO::PARAM_STR);
            $sql -> bindParam(':contents', $this->contents, PDO::PARAM_STR);
            $sql -> bindParam(':img_name', $new_img_name, PDO::PARAM_STR);
            $sql -> execute();
        }

        public function displayElement($num)
        {
            if (!empty($this->title) && !empty($this->contents)) {
                echo $this->title."\n";
                echo "<form action='' method='post' style='display: inline'>\n\t";
                echo "<input type='hidden' name='elem_num' value=".$num.">";
                echo "<input type='submit' name='edit' value='編集'>\n\t";
                if ($num != 0) {
                    echo "\t<input type='submit' name='delete' value='削除'>\n\t";
                }
                echo "</form><br>\n\t";
                echo $this->contents."<br>\n\t";
                if (!empty($this->img_name)) {
                    echo "<img src='tmp/".$this->img_name."' style='width: 250px;'><br>\n\t";
                }
            }
        }

        public function reset()
        {
            $this -> title = "";
            $this -> contents = "";
            if (!empty($this->img_name)) {
                unlink("tmp/".$this->img_name);
            }
            $this -> img_name = "";
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

        public function inputMain(PDO $pdo, string $title, string $contents, array $img_file) {
            $_img_file = NULL;
            if (!empty($img_file["name"])) {
                if (exif_imagetype($img_file["tmp_name"])){
                    $_img_file = $img_file;
                } else {
                    echo $img_file["name"]."は画像ではありません。<br>\n\t";
                }
            }
            $this -> set_maintitle = $this -> main -> inputElement($pdo, $title, $contents, $_img_file);
        }

        public function inputSub(PDO $pdo, string $title, string $contents, array $img_file) {
            $_img_file = NULL;
            if (!empty($img_file["name"])) {
                if (exif_imagetype($img_file["tmp_name"])){
                    $_img_file = $img_file;
                }
            }
            $subelement = new PostingElement();
            $subelement -> inputElement($pdo, $title, $contents, $_img_file);
            array_push($this->subs, $subelement);
        }

        public function postAll(PDO $pdo, int $account_id)
        {
            $date = date("Y/m/d H:i:s");
            $i = 0;
            $this -> main -> postElement($pdo, $this->maintable_name, $account_id."_".$i, $date);
            $maincontents_id = $pdo -> lastinsertID();
            $subtable_name = "subcontents_".$maincontents_id;
            $sql = "create table if not exists ".$subtable_name
                ." ("
                . "subcontents_id INT AUTO_INCREMENT PRIMARY KEY,"
                . "title TEXT,"
                . "contents TEXT,"
                . "img_name TEXT"
                . ");";
            $pdo->query($sql);
            foreach ($this->subs as $sub) {
                $sub -> postElement($pdo, $subtable_name, $account_id."_".$maincontents_id);
            }
        }

        public function displayAll()
        {
            echo "<br>";
            $this -> main -> displayElement(0);
            $i = 1;
            foreach ($this->subs as $sub) {
                $sub -> displayElement($i);
                $i += 1;
            }
        }

        public function reset()
        {
            $this -> main -> reset();
            foreach ($this->subs as $sub) {
                $sub ->  reset();
            }
        }
    }
?>