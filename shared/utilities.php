<?php

function raise_error($errorCode, $errorMessage, $httpStausCode) {
    response(false, "[{$errorCode}]:{$errorMessage}", $httpStausCode, []);
}

function response($status, $message, $httpStausCode= 200, $data= false) {
    $data= $data == false ? json_decode("{}") : $data;
    ob_clean();
    http_response_code($httpStausCode == 200 && !$status ? 500 : $httpStausCode);

    echo json_encode(array(
        "result" => $status,
        "message" => $message,
        "data" => $data,
    ), JSON_UNESCAPED_UNICODE);
    die();
}




function renderCompanyCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'companies', 'id', 'company_name', $selected, $class);
}
function renderDivisionCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'divisions', 'id', 'division_name', $selected, $class);
}
function renderDepartmentCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'departments', 'id', 'department_name', $selected, $class);
}



function renderAccountsCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'account_heads', 'id', 'account_name', $selected, $class);
}
function renderCostCenterTypeCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'cost_types', 'id', 'type_name', $selected, $class);
}
function renderCostCenterCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'cost_centers', 'id', 'cost_center_name', $selected, $class);
}
function renderInvoiceTypeCombo($comboId, $selected= 0, $class= "") {
    return renderACombo($comboId, 'invoice_types', 'id', 'type_name', $selected, $class);
}


function renderACombo($comboId, $table, $idField, $valueField, $selected= 0, $class= "", $attribs= "", $where= "") {
    $connection= App::db();

    $query= "SELECT $idField, $valueField FROM $table $where ORDER BY $valueField";
    $stmt = $connection->prepare( $query );
    
    
    if($stmt->execute()){
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html= '<select class="form-control '.$class.'" '.($comboId==""?"":'id="' . $comboId . '"').' '.$attribs.'>';
        $html.= '<option value="0" ' . ($selected==0?"selected":"") . ' >Not selected</option>';
        foreach($rows as $idx => $row) {
            $html.= '<option value="'. $row[$idField] .'" ' . ($selected==$row[$idField]?"selected":"") . ' >'. $row[$valueField] .'</option>';
        }
        $html.= '</select>';
        return $html;
    } else {
        Database::stop($stmt);
    }
    return "";
}

function getValue($array, $key, $default= "") {
    if(!is_array($array)) {
        return $default;
    }

    if(!isset($array[$key])) {
        return $default;
    }

    return $array[$key];
}