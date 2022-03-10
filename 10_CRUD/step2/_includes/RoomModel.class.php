<?php

class RoomModel
{
    public ?int $room_id;
    public string $name = "";
    public string $no = "";
    public ?string $phone = null;

    private array $validationErrors = [];

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct()
    {
    }

    public function insert() : bool {

        $sql = "INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public function update() : bool
    {
        $sql = "UPDATE room SET name=:name, no=:no, phone=:phone WHERE room_id=:room_id";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':room_id', $this->room_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public static function deleteById(int $room_id) : bool
    {
        $sql = "DELETE FROM room WHERE room_id=:room_id";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':room_id', $room_id);

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->room_id);
    }

    public static function getFromPost() : self {
        $room = new RoomModel();

        $room->room_id = filter_input(INPUT_POST, "room_id", FILTER_VALIDATE_INT);
        $room->name = filter_input(INPUT_POST, "name");
        $room->no = filter_input(INPUT_POST, "no");
        $room->phone = filter_input(INPUT_POST, "phone");

        return $room;
    }

    public function validate() : bool {
        $isOk = true;
        $errors = [];

        if (!$this->name){
            $isOk = false;
            $errors["name"] = "Room name cannot be empty";
        }

        if (!$this->no){
            $isOk = false;
            $errors["no"] = "Room number cannot be empty";
        }
        if ($this->phone === ""){
            $this->phone = null;
        }

        $this->validationErrors = $errors;
        return $isOk;
    }
}
