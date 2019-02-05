<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 05-Feb-19
 * Time: 12:37
 */


use Psr\Container\ContainerInterface;

class ParserController extends \Controller
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function home($request, $response, $args) {
        return $response;
    }

    public function parse($request, $response, $args) {
        $rows = [];
        if ($xlsx = SimpleXLSX::parse('book.xlsx')) {
            $rows = $xlsx->rows();
        } else {
            echo SimpleXLSX::parseError();
        }

        $count = 0;
        if($rows) {

            foreach ($rows as $k => $row) {
                if($k < $this->container['settings']['skip'] || !$row[0]) {
                    continue;
                }

                ///to content
                try {
                    $into = "INSERT INTO content (title, url, cat_url, prev_text, category, post_status, author, publish_date, lang) VALUES (:name, :link, :cat_url, '<p>1</p>', :category, :post_status, :author,:datepub, :lang)";
                    $stmt = $this->container['db']->prepare($into);
                    $params = [
                        ':name' => $row[0],
                        ':link' => $this->getAlias($row[0]."-".$row[1]),
                        ':cat_url' => $this->container['settings']['cat_url'],
                        ':category' => $this->container['settings']['category'],
                        ':post_status' => $this->container['settings']['post_status'],
                        ':author' => $this->container['settings']['author'],
                        ':datepub' => time(),
                        ':lang' => $this->container['settings']['lang'],
                    ];

                    $stmt->execute($params);
                    $lastId = $this->container['db']->lastInsertId();

                    if($lastId) {
                        $into2 = "INSERT INTO content_fields_data (item_id, item_type, field_name, data) VALUES (:id, 'page', 'field_summa',:summ)";
                        $stmt2 = $this->container['db']->prepare($into2);
                        $params2 = [
                            ':id' => $lastId,
                            ':summ' => round($row[2],2),
                        ];
                        $stmt2->execute($params2);
                        $count++;
                    }
                }
                catch( \PDOException $e ) {
                        die("Oh noes! There an error in the query!");
                }
            }

            echo "<p> Импортировано - ".$count." товаров</p>";
        }
    }

}