<?php
require_once 'simple_html_dom.php';

interface iRadovi {
    public function create($naziv_rada, $tekst_rada, $link_rada, $oib_tvrtke);
    public function save();
    public function read();
}

class DiplomskiRadovi implements iRadovi {
    private $naziv_rada;
    private $tekst_rada;
    private $link_rada;
    private $oib_tvrtke;
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=radovi", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec("SET NAMES 'utf8'");
        } catch (PDOException $e) {
            die("Connection failed");
        }
    }

    public function create($naziv_rada, $tekst_rada, $link_rada, $oib_tvrtke) {
        $this->naziv_rada = $naziv_rada;
        $this->tekst_rada = $tekst_rada;
        $this->link_rada = $link_rada;
        $this->oib_tvrtke = $oib_tvrtke;
    }

    public function save() {
        $sql = "INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke) 
                VALUES (:naziv, :tekst, :link, :oib)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':naziv' => $this->naziv_rada,
            ':tekst' => $this->tekst_rada,
            ':link' => $this->link_rada,
            ':oib' => $this->oib_tvrtke
        ]);
    }

    public function read() {
        $stmt = $this->db->query("SELECT * FROM diplomski_radovi");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getContent($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function scrapeData() {
        for ($page = 2; $page <= 6; $page++) {
            $url = "https://stup.ferit.hr/index.php/zavrsni-radovi/page/{$page}";
            $html = $this->getContent($url);

            if ($html === false || empty($html)) {
                continue;
            }

            echo "<h2>Povezano na stranicu: <a href='$url' target='_blank'>$url</a></h2>";
            echo "<pre>" . htmlspecialchars(substr($html, 0, 500)) . "...</pre><br>";

            $dom = str_get_html($html);
            if (!$dom) {
                continue;
            }

            foreach ($dom->find('article') as $article) {
                $titleLinks = $article->find('a');
                if (count($titleLinks) < 2) {
                    continue;
                }
                $naziv_rada = trim($titleLinks[1]->plaintext);
                $link_rada = $titleLinks[1]->href;

                $img = $article->find('img', 0);
                $oib_tvrtke = "";
                if ($img) {
                    preg_match('/\/(\d+)\.(png|jpg|jpeg)$/', $img->src, $matches);
                    if (isset($matches[1])) {
                        $oib_tvrtke = $matches[1];
                    }
                }

                $detailHtml = $this->getContent($link_rada);
                if (!$detailHtml) {
                    continue;
                }

                $detailDom = str_get_html($detailHtml);
                if (!$detailDom) {
                    continue;
                }

                $tekst_rada = "";
                foreach (['.entry-content', 'div.post-content', 'article .content'] as $selector) {
                    if ($content = $detailDom->find($selector, 0)) {
                        $tekst_rada = trim($content->plaintext);
                        break;
                    }
                }

                $this->create($naziv_rada, $tekst_rada, $link_rada, $oib_tvrtke);
                $this->save();

                echo "<h3>Spremljen rad:</h3>";
                echo "<strong>Naziv:</strong> " . htmlspecialchars($naziv_rada) . "<br>";
                echo "<strong>Link:</strong> <a href='" . htmlspecialchars($link_rada) . "' target='_blank'>" . htmlspecialchars($link_rada) . "</a><br>";
                echo "<strong>OIB:</strong> " . htmlspecialchars($oib_tvrtke) . "<br>";
                echo "<strong>Tekst:</strong> " . htmlspecialchars(substr($tekst_rada, 0, 100)) . "...<br><br>";

                $detailDom->clear();
            }
            $dom->clear();
        }
    }
}

$diplomski = new DiplomskiRadovi();
$diplomski->scrapeData();
?>