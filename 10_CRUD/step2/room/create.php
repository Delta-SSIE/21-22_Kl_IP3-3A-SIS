<?php

require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{

    const STATE_FROM_REQUESTED = 1;
    const STATE_DATA_SENT = 2;
    const STATE_REPORT_RESULT = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private RoomModel $room;
    private int $state;
    private int $result;
    private array $validationErrors = [];

    public function __construct()
    {
        parent::__construct();
        $this->title = "Room listing";
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getState();

        if ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                $this->title = "Room created";
            } else {
                $this->title = "Room creation failed";
            }
            return;
        }

        if ($this->state === self::STATE_DATA_SENT) {
            $this->readPost();
            $ok = $this->validate();
            if ($ok) {
                //uložím
                $createOk = $this->insertData();
                if ($createOk) {
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FROM_REQUESTED;
                $this->title = "Invalid data";
            }
        } else {
            $this->title = "Create new room";
            $this->room = new RoomModel();
        }

    }


    protected function body(): string {
        if ($this->state === self::STATE_FROM_REQUESTED) {
            return $this->m->render("roomForm", ["room"=>$this->room, "errors"=>$this->validationErrors]);
        } elseif ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                return $this->m->render("reportSuccess", ["data"=>"Room created successfully"]);
            } else {
                return $this->m->render("reportFail", ["data"=>"Room creation failed. Please contact adiministrator or try again later."]);
            }

        }
    }

    private function getState() : void {
        //je už hotovo?
        $result = filter_input(INPUT_GET, "result", FILTER_VALIDATE_INT);
        if ($result === self::RESULT_SUCCESS) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_SUCCESS;
            return;
        } elseif ($result === self::RESULT_FAIL) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_FAIL;
            return;
        }

        //byl odeslán formulář
        $action = filter_input(INPUT_POST, "action");
        if ($action === "create") {
            $this->state = self::STATE_DATA_SENT;
            return;
        }

        $this->state = self::STATE_FROM_REQUESTED;
    }

    private function readPost() : void {
        $this->room = new RoomModel();
        $this->room->name = filter_input(INPUT_POST, "name");
        $this->room->no = filter_input(INPUT_POST, "no");
        $this->room->phone = filter_input(INPUT_POST, "phone");
    }

    private function validate() : bool {
        $isOk = true;
        $errors = [];

        if (!$this->room->name){
            $isOk = false;
            $errors["name"] = "Room name cannot be empty";
        }

        if (!$this->room->no){
            $isOk = false;
            $errors["no"] = "Room number cannot be empty";
        }
        if ($this->room->phone === ""){
            $this->room->phone = null;
        }

        $this->validationErrors = $errors;
        return $isOk;
    }

    private function insertData() : bool {
        $sql = "INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)";

        //dumpe($this->room);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $this->room->name);
        $stmt->bindParam(':no', $this->room->no);
        $stmt->bindParam(':phone', $this->room->phone);

        return $stmt->execute();
    }

    private function redirect(int $result) : void {
        //odkaz sám na sebe, bez query string atd.
        $location = strtok($_SERVER['REQUEST_URI'], '?');

        header("Location: {$location}?result={$result}");
        exit;
    }
}

(new Page())->render();
