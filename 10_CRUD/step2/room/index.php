<?php

require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Room listing";
    }

    protected function body(): string
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `room` ORDER BY `name`");
        $stmt->execute();
        return $this->m->render("roomList", ["rooms" => $stmt, "roomDetailName" => "roomDetail.php"]);
    }
}

(new Page())->render();
