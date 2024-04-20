<?php
function generateRandomId($length, $db, $table, $idColumn) {
    $characters = '0123456789';
    $randomId = '';

    while (true) { // Boucle infinie pour générer un ID unique
        $randomId = ''; // Réinitialiser l'ID à chaque itération

        for ($i = 0; $i < $length; $i++) {
            $randomId .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Vérifier si l'ID existe déjà dans la base de données
        $sql = "SELECT COUNT(*) FROM $table WHERE $idColumn = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $randomId);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count === 0) { // ID unique trouvé, sortir de la boucle
            break;
        }
    }

    return $randomId;
}
?>