<?php

$servername = "127.0.0.1:3306";
$username = "Flagg";
$password = " 246@Gkenya";
$dbname = "flagging";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    // die("Connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Ajouter ou modifier des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Fonction de validation pour chaque champ requis
    function validateInput($input, $fieldName)
    {
        if (empty(trim($input))) {
            return "$fieldName is required.";
        }
        return null;
    }

    // Valider les champs de texte
    $requiredFields = [
        'issuingBody' => 'Issuing Body',
        'certificateType' => 'Certificate Type',
        'cargoOrigin' => 'Cargo Origin',
        'shipmentRoute' => 'Shipment Route',
        'certificateNo' => 'Certificate No.',
        'customsDeclNo' => 'Customs Decl. No.',
        'importerName' => 'Importer Name',
        'importerContact' => 'Importer Contact',
        'exporterName' => 'Exporter Name',
        'agentName' => 'Clearing/forwarding agent',
        'agentContact' => 'Agent Contact',
        'transportMode' => 'Transport Mode',
        'transporterName' => 'Transporter Name',
        'vehicleNumber' => 'Vehicle Number',
        'dischargeLocation' => 'Discharge Location',
        'finalDestination' => 'Final Destination',
        'fobCurrency' => 'FOB Currency',
        'fobValue' => 'FOB Value',
        'incoterm' => 'Incoterm',
        'freightCurrency' => 'Freight Currency',
        'freightValue' => 'Freight Value',
        'validationNotes' => 'Validation Notes'
    ];

    foreach ($requiredFields as $field => $label) {
        $error = validateInput($_POST[$field] ?? '', $label);
        if ($error) {
            $errors[] = $error;
        }
    }

    // Valider les fichiers téléchargés
    $fileFields = ['feriAdCertificate', 'customsEntryDoc', 'invoiceCopy', 'supportingDocs'];
    $filePaths = [];

    foreach ($fileFields as $fileField) {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
            $errors[] = ucfirst(str_replace('_', ' ', $fileField)) . ' is required and must be a valid file.';
        } else {
            // Vérifier la taille du fichier
            if ($_FILES[$fileField]['size'] > 10 * 1024 * 1024) { // Limite de 10MB
                $errors[] = ucfirst(str_replace('_', ' ', $fileField)) . ' exceeds the 10MB size limit.';
            } else {
                // Déplacement du fichier vers le répertoire de destination
                $targetPath = $uploadDir . basename($_FILES[$fileField]['name']);
                if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $targetPath)) {
                    $filePaths[$fileField] = $targetPath;
                } else {
                    $errors[] = "Failed to move file for " . ucfirst(str_replace('_', ' ', $fileField));
                }
            }
        }
    }

    // Si des erreurs existent, les retourner
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Préparer les données pour l'insertion ou la mise à jour
    $issuingBody = $conn->real_escape_string($_POST['issuingBody']);
    $certificateType = $conn->real_escape_string($_POST['certificateType']);
    $cargoOrigin = $conn->real_escape_string($_POST['cargoOrigin']);
    $shipmentRoute = $conn->real_escape_string($_POST['shipmentRoute']);
    $certificateNo = $conn->real_escape_string($_POST['certificateNo']);
    $customsDeclNo = $conn->real_escape_string($_POST['customsDeclNo']);
    $importerName = $conn->real_escape_string($_POST['importerName']);
    $importerContact = $conn->real_escape_string($_POST['importerContact']);
    $exporterName = $conn->real_escape_string($_POST['exporterName']);
    $agentName = $conn->real_escape_string($_POST['agentName']);
    $agentContact = $conn->real_escape_string($_POST['agentContact']);
    $transportMode = $conn->real_escape_string($_POST['transportMode']);
    $transporterName = $conn->real_escape_string($_POST['transporterName']);
    $vehicleNumber = $conn->real_escape_string($_POST['vehicleNumber']);
    $dischargeLocation = $conn->real_escape_string($_POST['dischargeLocation']);
    $finalDestination = $conn->real_escape_string($_POST['finalDestination']);
    $fobCurrency = $conn->real_escape_string($_POST['fobCurrency']);
    $fobValue = $conn->real_escape_string($_POST['fobValue']);
    $incoterm = $conn->real_escape_string($_POST['incoterm']);
    $freightCurrency = $conn->real_escape_string($_POST['freightCurrency']);
    $freightValue = $conn->real_escape_string($_POST['freightValue']);
    $validationNotes = $conn->real_escape_string($_POST['validationNotes']);
    $customsEntryDoc = $conn->real_escape_string($_FILES['customsEntryDoc']['name']);
    $invoiceCopy = $conn->real_escape_string($_FILES["invoiceCopy"]["name"]);
    $feriAdCertificate = $conn->real_escape_string($_FILES["feriAdCertificate"]["name"]);

    // Vérifier si c'est une modification (si l'ID est présent)
    $id = $_POST['id'] ?? null;
    if ($id) {
        $sql = "UPDATE flag_data SET issuingBody='$issuingBody', certificateType='$certificateType', cargoOrigin='$cargoOrigin', shipmentRoute='$shipmentRoute', certificateNo='$certificateNo', customsDeclNo='$customsDeclNo', importerName='$importerName', importerContact='$importerContact', exporterName='$exporterName', agentName='$agentName', agentContact='$agentContact', transportMode='$transportMode', transporterName='$transporterName', vehicleNumber='$vehicleNumber', dischargeLocation='$dischargeLocation', finalDestination='$finalDestination', fobCurrency='$fobCurrency', fobValue='$fobValue', incoterm='$incoterm', freightCurrency='$freightCurrency', freightValue='$freightValue', validationNotes='$validationNotes' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO flag_data (issuingBody, certificateType, cargoOrigin,shipmentRoute, certificateNo, customsDeclNo, importerName, importerContact, exporterName, agentName, agentContact, transportMode, transporterName, vehicleNumber, dischargeLocation, finalDestination, fobCurrency, fobValue, incoterm, freightCurrency, freightValue, validationNotes,customsEntryDoc,invoiceCopy,feriAdCertificate) VALUES ('$issuingBody', '$certificateType', '$cargoOrigin', '$shipmentRoute', '$certificateNo', '$customsDeclNo', '$importerName', '$importerContact', '$exporterName', '$agentName', '$agentContact', '$transportMode', '$transporterName', '$vehicleNumber', '$dischargeLocation', '$finalDestination', '$fobCurrency', '$fobValue', '$incoterm', '$freightCurrency', '$freightValue', '$validationNotes','$customsEntryDoc','$invoiceCopy','$feriAdCertificate')";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        error_log("SQL Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }

    $conn->close();
}

// Supprimer des données
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $conn->real_escape_string($_DELETE['id']);

    $sql = "DELETE FROM flag_data WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }

    $conn->close();
}
