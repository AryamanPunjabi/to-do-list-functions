<?php
include 'db.php';

class UpdateTask
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

    public function updateTask($id, $completed = null, $task_description = null)
    {
        $id = intval($id);  // Sanitize ID

        // Prepare update query for completion status
        if ($completed !== null) {
            $completed = (int)$completed; // Cast the value to an integer for completion status
            $stmt = $this->conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
            $stmt->bind_param("ii", $completed, $id);  // Pass variables directly
            if (!$stmt->execute()) {
                $stmt->close();
                return "Error updating completion status: " . $this->conn->error;
            }
            $stmt->close();
        }

        // Prepare update query for task description
        if ($task_description !== null && $this->isValidTask($task_description)) {
            $stmt = $this->conn->prepare("UPDATE tasks SET task = ? WHERE id = ?");
            $stmt->bind_param("si", $task_description, $id);  // Pass variables directly
            if (!$stmt->execute()) {
                $stmt->close();
                return "Error updating task description: " . $this->conn->error;
            }
            $stmt->close();
        }

        return true;
    }
}

// Example usage
if (isset($_POST['id'])) {
    $taskManager = new UpdateTask($conn);
    $id = intval($_POST['id']); // Sanitize ID
    $completed = isset($_POST['complete']) ? 1 : 0;
    $task_description = isset($_POST['task']) ? trim($_POST['task']) : null;

    $result = $taskManager->updateTask($id, $completed, $task_description);

    if ($result === true) {
        header("Location: index.php");
        exit();
    } else {
        echo $result;
    }
}

$conn->close();
?>
