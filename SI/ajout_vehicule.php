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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de Véhicule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-container h1 {
            color: #343a40;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-label {
            font-weight: bold;
        }

        .form-text {
            color: #6c757d;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="form-container">
        <h1 class="text-center mb-4">Ajout de Véhicule</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="NUM_IDENTIFICATION_" class="form-label">Numéro d'Identification :</label>
                <input type="text" class="form-control" id="NUM_IDENTIFICATION_" name="NUM_IDENTIFICATION_" required>
                <small class="form-text">Entrez le numéro unique du véhicule.</small>
            </div>

            <div class="mb-3">
                <label for="ID_CAMPUS_" class="form-label">ID Campus :</label>
                <input type="number" class="form-control" id="ID_CAMPUS_" name="ID_CAMPUS_" required>
                <small class="form-text">Entrez l'ID du campus associé à ce véhicule.</small>
            </div>

            <div class="mb-3">
                <label for="TYPE_VEHICULE_" class="form-label">Type de Véhicule :</label>
                <input type="text" class="form-control" id="TYPE_VEHICULE_" name="TYPE_VEHICULE_" required>
            </div>

            <div class="mb-3">
                <label for="CAPACITE_MAX_" class="form-label">Capacité Maximale :</label>
                <input type="number" class="form-control" id="CAPACITE_MAX_" name="CAPACITE_MAX_" required>
                <small class="form-text">Indiquez la capacité maximale de transport du véhicule.</small>
            </div>

            <div class="mb-3">
                <label for="ETAT_" class="form-label">État :</label>
                <select id="ETAT_" name="ETAT_" class="form-select" required>
                    <option selected disabled>Choisir...</option>
                    <option value="Neuf">Neuf</option>
                    <option value="Bon état">Bon état</option>
                    <option value="Usagé">Usagé</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="MARQUE_" class="form-label">Marque :</label>
                <input type="text" class="form-control" id="MARQUE_" name="MARQUE_" required>
            </div>

            <div class="mb-3">
                <label for="MODELE_" class="form-label">Modèle :</label>
                <input type="text" class="form-control" id="MODELE_" name="MODELE_" required>
            </div>

            <div class="mb-3">
                <label for="ANNEE_FABRICATION_" class="form-label">Année de Fabrication :</label>
                <input type="number" class="form-control" id="ANNEE_FABRICATION_" name="ANNEE_FABRICATION_" required>
                <small class="form-text">Entrez l'année de fabrication au format (AAAA).</small>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">Ajouter Véhicule</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
