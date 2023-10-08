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

class Utilities{
 
    public function getPaging($page, $total_rows, $records_per_page, $page_url){
 
        // paging array
        $paging_arr=array();
 
        // button for first page
        $paging_arr["first"] = $page>1 ? "{$page_url}page=1" : "";
 
        // count all products in the database to calculate total pages
        $total_pages = ceil($total_rows / $records_per_page);
 
        // range of links to show
        $range = 2;
 
        // display links to 'range of pages' around 'current page'
        $initial_num = $page - $range;
        $condition_limit_num = ($page + $range)  + 1;
 
        $paging_arr['pages']=array();
        $page_count=0;
         
        for($x=$initial_num; $x<$condition_limit_num; $x++){
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if(($x > 0) && ($x <= $total_pages)){
                $paging_arr['pages'][$page_count]["page"]=$x;
                $paging_arr['pages'][$page_count]["url"]="{$page_url}page={$x}";
                $paging_arr['pages'][$page_count]["current_page"] = $x==$page ? "yes" : "no";
 
                $page_count++;
            }
        }
 
        // button for last page
        $paging_arr["last"] = $page<$total_pages ? "{$page_url}page={$total_pages}" : "";
 
        // json format
        return $paging_arr;
    }
 
}

