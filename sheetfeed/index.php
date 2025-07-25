<?php
date_default_timezone_set("Asia/Kolkata");

require __DIR__ . '/vendor/autoload.php';

function getClient(){
    $client = new \Google_Client();

    $client->setApplicationName('Google Sheets and PHP');

    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);

    $client->setAccessType('offline');

    $client->setAuthConfig(__DIR__ . '/credentials.json');

    return new Google_Service_Sheets($client);

}
#Read sheet
function read($service, $spreadsheetId, $get_range){
    //Reading data from spreadsheet.
    $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
    return $response->getValues();
}

#Write 
function write($service, $spreadsheetId, $update_range, $data=array()){
    $values = [$data];
    $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
    $params = ['valueInputOption' => 'RAW'];
    return $service->spreadsheets_values->update($spreadsheetId, $update_range, $body, $params);
}
function insertdb($data){
    $sql = "INSERT INTO appointment (`name`,`phone`,`clinic_location`,`appointment_on`) VALUES ('".$data[0]."','".$data[1]."','".$data[2]."','".date("Y-m-d H:i:s",strtotime($data[3]))."');"; 
    $servername = "localhost";
    $username = "wordpress";
    $db = "wordpress";
    $password ="new_password"; // "6c008a3713d3549f5fb9e9d404f4b5f5e20ec45e6fc75ff4";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $db);

    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return "Connection failed: " . $conn->connect_error;
    }

    if ($conn->query($sql) === TRUE) {
        error_log("New record created successfully");
        return "New record created successfully";
    } else {
        error_log("Error: " . $sql . "<br>" . $conn->error);
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    // $conn->close();
}

if($_POST){
    $spreadsheetId = "1nzcqt5iuBCX95QoaI9_y8HytppQIrZZcMciP42kEizQ";
    $get_range = "2022";

    $client = getClient();

    $read_value = read($client, $spreadsheetId, $get_range);
    $count = count($read_value) + 1;
    // print_r($read_value);
    $update_range = "2022!A$count:F$count";
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $appointment = strtotime($_POST['date']);
    $appointment_date = date("d F Y",$appointment);
    $appointment_month = date("F Y",$appointment);
    $created = date("H:i:s d-m-Y");
    $data=array($name,$phone,$location,$appointment_date,$appointment_month,$created);
    error_log("Data to be inserted: " . print_r($data, true));
    $status = insertdb($data);
    $write_response = write($client, $spreadsheetId, $update_range, $data);
    if ($write_response) {
        error_log("Write response: " . print_r($write_response, true));
        echo json_encode(array('success', $status));
    } else {
        error_log("Write response failed");
        echo json_encode(array('error', 'Write response failed'));
    }
// print_r($write_response);
}
