<?php

namespace connection;

require_once ("Connection.php");
require_once(realpath(dirname(__FILE__) . '/../Item.php'));

use Exception;
use webstore\Item;

class StockConnection extends Connection
{
    /**
     * @param $subcategory
     * @return array
     * @throws Exception
     */
    public function fetchItemsBySubcategory($subcategory)
    {
        $subcategory = htmlentities($subcategory, ENT_QUOTES, "UTF-8");
        $query = "select id_item, item_name, subcategory_name, category_name, price, photo_url, description ".
                 "from stock s inner join subcategories sc on s.id_subcategory=? and s.id_subcategory = sc.id_subcategory ".
                 "inner join categories c on sc.id_category = c.id_category ".
                 "order by item_name";
        //mysqli_report(MYSQLI_REPORT_ALL);
        $stmt = $this->connection->prepare($query);
        if(!$stmt)
            throw new Exception("Query error");
        if(!$stmt->bind_param("d", $subcategory))
            throw new Exception("Query error".$stmt->errno);
        if(!$stmt->execute())
            throw new Exception("Error executing query ".$stmt->errno);
        $result = $stmt->get_result();
        $stmt->close();
        if(!$result)
            throw new Exception($this->connection->connect_errno);
        $items = array();
        while($item = $result->fetch_assoc())
        {
            $items[] = new Item($item["id_item"], $item["item_name"], $item["subcategory_name"],
                $item["category_name"], $item["price"], $item["photo_url"], $item["description"]);
        }
        return $items;
    }
}