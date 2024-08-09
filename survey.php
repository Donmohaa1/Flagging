<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flagging";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    foreach ($fileFields as $fileField) {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
            $errors[] = ucfirst(str_replace('_', ' ', $fileField)) . ' is required and must be a valid file.';
        } else {
            // Vérifier la taille du fichier
            if ($_FILES[$fileField]['size'] > 10 * 1024 * 1024) { // Limite de 10MB
                $errors[] = ucfirst(str_replace('_', ' ', $fileField)) . ' exceeds the 10MB size limit.';
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

    // Vérifier si c'est une modification (si l'ID est présent)
    $id = $_POST['id'] ?? null;
    if ($id) {
        $sql = "UPDATE flag_data SET issuingBody='$issuingBody', certificateType='$certificateType', cargoOrigin='$cargoOrigin', shipmentRoute='$shipmentRoute', certificateNo='$certificateNo', customsDeclNo='$customsDeclNo', importerName='$importerName', importerContact='$importerContact', exporterName='$exporterName', agentName='$agentName', agentContact='$agentContact', transportMode='$transportMode', transporterName='$transporterName', vehicleNumber='$vehicleNumber', dischargeLocation='$dischargeLocation', finalDestination='$finalDestination', fobCurrency='$fobCurrency', fobValue='$fobValue', incoterm='$incoterm', freightCurrency='$freightCurrency', freightValue='$freightValue', validationNotes='$validationNotes' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO flag_data (issuingBody, certificateType, cargoOrigin, shipmentRoute, certificateNo, customsDeclNo, importerName, importerContact, exporterName, agentName, agentContact, transportMode, transporterName, vehicleNumber, dischargeLocation, finalDestination, fobCurrency, fobValue, incoterm, freightCurrency, freightValue, validationNotes) VALUES ('$issuingBody', '$certificateType', '$cargoOrigin', '$shipmentRoute', '$certificateNo', '$customsDeclNo', '$importerName', '$importerContact', '$exporterName', '$agentName', '$agentContact', '$transportMode', '$transporterName', '$vehicleNumber', '$dischargeLocation', '$finalDestination', '$fobCurrency', '$fobValue', '$incoterm', '$freightCurrency', '$freightValue', '$validationNotes')";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
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
