<?php
include 'db.php';

class TaskManager
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

    public function deleteTask($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = "Error deleting task: " . $this->conn->error;
            $stmt->close();
            return $error;
        }
    }
}


if (isset($_GET['id'])) {
    $taskManager = new TaskManager($conn);
    $id = $_GET['id'];
    $result = $taskManager->deleteTask($id);

    if ($result === true) {
        header("Location: index.php");
        exit();
    } else {
        echo $result;
    }
}

$conn->close();
?>
