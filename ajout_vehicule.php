<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tp_si"; 

    $conn = new mysqli($servername, $username, $password, $dbname);

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

   
    $stmt = $conn->prepare("INSERT INTO VEHICULE (NUM_IDENTIFICATION_, ID_CAMPUS_, TYPE_VEHICULE_, CAPACITE_MAX_, ETAT_, MARQUE_, MODELE_, ANNEE_FABRICATION_) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisisssi", $num_identification, $id_campus, $type_vehicule, $capacite_max, $etat, $marque, $modele, $annee_fabrication);

    $num_identification = $_POST["NUM_IDENTIFICATION_"];
    $id_campus = $_POST["ID_CAMPUS_"];
    $type_vehicule = $_POST["TYPE_VEHICULE_"];
    $capacite_max = $_POST["CAPACITE_MAX_"];
    $etat = $_POST["ETAT_"];
    $marque = $_POST["MARQUE_"];
    $modele = $_POST["MODELE_"];
    $annee_fabrication = $_POST["ANNEE_FABRICATION_"];

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Véhicule ajouté avec succès!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

