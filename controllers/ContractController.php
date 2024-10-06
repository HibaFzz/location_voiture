 
<?php
require_once '../models/Contract.php';

class ContractController {
    private $db;
    private $contract;

    public function __construct($db) {
        $this->db = $db;
        $this->contract = new Contract(db: $this->db);
    }

    public function createContract(): void {
        if ($_POST) {
            $this->contract->user_id = $_POST['user_id'];
            $this->contract->car_id = $_POST['car_id'];
            $this->contract->start_date = $_POST['start_date'];
            $this->contract->end_date = $_POST['end_date'];
            $this->contract->status = 'active'; // Default status

            if ($this->contract->create()) {
                $this->contract->updateStock(); // Update stock after creating a contract
                echo "Contract created successfully.";
            } else {
                echo "Failed to create contract.";
            }
        }
    }

    public function listContracts(): mixed {
        return $this->contract->getAllContracts();
    }

    public function editContract($id): mixed {
        $this->contract->id = $id;
        if ($_POST) {
            $this->contract->user_id = $_POST['user_id'];
            $this->contract->car_id = $_POST['car_id'];
            $this->contract->start_date = $_POST['start_date'];
            $this->contract->end_date = $_POST['end_date'];
            $this->contract->status = $_POST['status'];
            $this->contract->updateContract();
            $this->contract->updateStock(); // Update stock after editing contract
            header(header: "Location: /public/index.php");
            exit();
        }
        return $this->contract->getContractById(id: $id);
    }

    public function deleteContract($id): never {
        $this->contract->deleteContract(id: $id);
        header(header: "Location: /public/index.php");
        exit();
    }
}
?>
