<?php
include 'db.php';

class AddTask
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function isValidTask($task)
    {
        return strlen($task) > 0 && strlen($task) <= 255 && preg_match("/^[a-zA-Z0-9\s]+$/", $task);
    }

    public function addTask($task)
    {
        if (!$this->isValidTask($task)) {
            return "Invalid task description!";
        }

        $stmt = $this->conn->prepare("INSERT INTO tasks (task, completed) VALUES (?, 0)");
        $stmt->bind_param("s", $task);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = "Error adding task: " . $this->conn->error;
            $stmt->close();
            return $error;
        }
    }
}

if (isset($_POST['task'])) {
    $taskManager = new AddTask($conn);
    $task = trim($_POST['task']);
    $result = $taskManager->addTask($task);

    if ($result === true) {
        header("Location: index.php");
        exit();
    } else {
        echo $result;
    }
}

$conn->close();
?>
