<?php
    include "generateID.php";
    class Client {
        function getClientById($conn , $id) {
            $sql = "SELECT * FROM client WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id' , $id);
            $stmt->execute();
            $clients = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($clients);
        }
        public function getAllClients($conn) {
            $sql ="SELECT * FrOM client";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($clients);
        }
        public function getSold($conn) {
            $sql = "SELECT min(sold) AS minimum , max(sold) AS maximum , sum(sold) AS total FROM client";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $sold = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($sold);
        }
        public function addClient ($conn, $clients) {
            $id = generateRandomId(6, $conn, 'client', 'id');  // Ensure correct function arguments
            if ((float)$clients->sold < 1000) {
                $obs = "insuffisant";
            } else if ((float)$clients->sold >= 1000 && (float)$clients->sold <= 5000) {
                $obs = "moyen";
            } else if ((float)$clients->sold > 5000) {
                $obs = "élevé";
            }
        
            if (isset($clients->accountNumber ) && isset($clients->clientName ) || isset($clients->sold )) {
                $select = "SELECT * FROM client WHERE accountNumber = :accountNumber or clientName = :clientName";
                $request = $conn->prepare($select);
                $request->bindParam(':accountNumber', $clients->accountNumber);
                $request->bindParam(':clientName', $clients->clientName);
                $request->execute();
                $result = $request->fetch(PDO::FETCH_ASSOC);
                if(empty($result)){
                    $sql = "INSERT INTO client(id,accountNumber,clientName,sold,obs) VALUES(:id, :accountNumber, :clientName, :sold, :obs)";
                    $stmt = $conn->prepare($sql);
                    $sold = (float) $clients->sold;
                    // Bind parameters correctly (check for potential typos in variable names)
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':accountNumber', $clients->accountNumber);
                    $stmt->bindParam(':clientName', $clients->clientName);
                    $stmt->bindParam(':sold', $sold);
                    $stmt->bindParam(':obs', $obs);
            
                    if ($stmt->execute()) {
                        $response = ['status' => 1, 'message' => 'Record created successfully.'];
                    } else {
                        $response = [
                            'status' => 0,
                            'message' => 'Failed to create record.',
                            'error' => $stmt->error
                        ];
                    }
                    echo json_encode($response);
                } else {
                        $response = [
                        'status' => 0,
                        'message' => 'Ce client existe déja.',
                    ];
                    echo json_encode($response);
                }
            } else {
                $response = [
                    'status' => 0,
                    'message' => 'form not set'
                ];
                echo json_encode($response);
            }
        }
        
        public function updateClient ($conn,$clients) {
            if((float)$clients->sold < 1000 ){
                $obs = "insuffisant";
            }
            if((float)$clients->sold >= 1000 || (float)$clients->sold <= 5000 ){
                $obs = "moyen";
            }
            if((float)$clients->sold > 5000 ){
                $obs = "élevé";
            }
            $sold = (float) $clients->sold;
            if(isset($clients->accountNumber ) && isset($clients->clientName ) || isset($clients->sold )|| isset($clients->id)){
                $select = "SELECT * FROM client WHERE  id <> :id and accountNumber = :accountNumber or clientName = :clientName ";
                $request = $conn->prepare($select);
                $request->bindParam(':id' , $clients->id);
                $request->bindParam(':accountNumber', $clients->accountNumber);
                $request->bindParam(':clientName', $clients->clientName);
                $request->execute();
                $result = $request->fetch(PDO::FETCH_ASSOC);
                if(empty($result)){
                    $sql = "UPDATE client SET accountNumber = :accountNumber , clientName = :clientName ,sold = :sold ,obs = :obs WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id' , $clients->id);
                    $stmt->bindParam(':accountNumber' , $clients->accountNumber);
                    $stmt->bindParam(':clientName' , $clients->clientName);
                    $stmt->bindParam(':sold' , $sold);
                    $stmt->bindParam(':obs' , $obs);
                    
                    if ($stmt->execute()) {
                        $response = ['status' => 1, 'message' => 'Record updated successfully.'];
                    } else {
                        $response = [
                            'status' => 0,
                            'message' => 'Failed to update record.',
                            'error' => $stmt->error
                        ];
                    }
                    echo json_encode($response); 
                }
                else {
                    $response = [
                    'status' => 0,
                    'message' => 'Ce client éxiste déja.',
                    ];
                    echo json_encode($response);
                }
            } else {
                $response = [
                    'status' => 0,
                    'message' => 'form not set'
                ];
                echo json_encode($response);
            }
        }
        public function deleteUser($conn , $id){
            $sql = "DELETE FROM client WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam('id' , $id);
            if ($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
            } else {
                $response = [
                    'status' => 0,
                    'message' => 'Failed to delete record.',
                    'error' => $stmt->error
                ];
            }
            echo json_encode($response); 
        }
    }
?>