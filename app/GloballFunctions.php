<?php
use App\Http\Controllers\FormEngine;
use Illuminate\Support\Str;

function generateDynamicCard($data, $actions, $rem = [], $MustHave = [], $CardClass = "mycard", $CardTitle = "Card Title")
{
    // Check if the data is empty
    if ($data->isEmpty()) {
        return '<p>No data available for ' . $CardTitle . '</p>';
    }

    $output = '';

    foreach ($data as $row) {
        $output .= '<div class="card ' . $CardClass . '">';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . $CardTitle . '</h5>';
        $output .= '<ul class="list-group list-group-flush">';

        foreach ($row as $key => $value) {
            if (in_array($key, $rem)) {
                continue;
            }
            $label = ucwords(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $key)));

            // Check if the value is date
            $date = DateTime::createFromFormat('Y-m-d', $value);
            if ($date && $date->format('Y-m-d') === $value) {
                $formattedDate = $date->format('F j, Y');
                $output .= '<li class="list-group-item"><span class="text-primary">' . e($label) . ':</span> ' . e($formattedDate) . '</li>';
            } elseif (is_numeric($value)) {
                // Check if the value is numeric (integer, float, or decimal) and format it using number_format()
                $output .= '<li class="list-group-item"><span class="text-primary">' . e($label) . ':</span> ' . e(number_format($value, 2)) . '</li>';
            } else {
                $output .= '<li class="list-group-item"><span class="text-primary">' . e($label) . ':</span> ' . e($value) . '</li>';
            }
        }

        // Add custom data if provided
        if (!empty($MustHave)) {
            foreach ($MustHave as $customData) {
                $output .= '<li class="list-group-item">' . $customData['data']($row) . '</li>';
            }
        }

        $output .= '</ul>';

        // Generate action buttons
        if (!empty($actions)) {
            $output .= '<div class="card-footer">';
            $output .= '<div class="dropdown">';
            $output .= '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Choose Action</button>';
            $output .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            foreach ($actions as $action) {
                $output .= $action($row->id);
            }
            $output .= '</ul></div>';
            $output .= '</div>';
        }

        $output .= '</div></div>';
    }

    return $output;
}
function generateDynamicTable($data, $actions, $rem = [], $MustHave = [], $TableClass = "mytable")
{
    // Check if the data is empty
    if ($data->isEmpty()) {
        return '<p>No data available.</p>';
    }

    // Get the first row to extract headers
    $firstRow = $data->first();

    // Generate table headers
    $output = '<table class="' . $TableClass . '  table table-rounded table-bordered border gy-3 gs-3">';
    $output .= '<thead><tr class="fw-bold text-gray-800 border-bottom border-gray-200">';

    foreach ($firstRow as $key => $value) {
        if (in_array($key, $rem)) {
            continue;
        }

        $header = ucwords(Str::snake($key, ' '));
        $output .= '<th>' . e($header) . '</th>';
    }

    // Add custom headers if provided
    if (!empty($MustHave)) {
        foreach ($MustHave as $customHeader) {
            $output .= '<th>' . $customHeader['header'] . '</th>';
        }
    }

    if (!empty($actions)) {
        $output .= '<th class="bg-dark text-light">Actions</th>';
    }
    $output .= '</tr></thead>';

    // Generate table data
    $output .= '<tbody>';
    foreach ($data as $row) {
        $output .= '<tr>';
        foreach ($row as $key => $value) {
            if (in_array($key, $rem)) {
                continue;
            }

            // Check if the column contains a PDF link or path
            $isPdf = preg_match('/(.pdf$)|(.pdf\?)/i', $value);

            if ($isPdf) {
                $output .= '<td>';
                $output .= '<a data-doc="' . e($row->id) . '" data-source="' . e(asset($value)) . '" data-bs-toggle="modal" href="#PdfJS" class="btn btn-sm PdfViewer btn-info"><i class="fas fa-file-pdf" aria-hidden="true"></i></a>';
                $output .= '</td>';
            } else {
                // Check if the value is numeric (integer, float, or decimal) and format it using number_format()
                if (is_numeric($value)) {
                    $output .= '<td>' . e(number_format($value, 2)) . '</td>';
                } else {
                    // Check if the value is a date/datetime column
                    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?$/', $value)) {
                        $output .= '<td>' . e(date('j F Y', strtotime($value))) . '</td>';
                    } else {
                        $output .= '<td>' . e($value) . '</td>';
                    }
                }
            }
        } // Add custom data if provided
        if (!empty($MustHave)) {
            foreach ($MustHave as $customData) {
                $output .= '<td>' . $customData['data']($row) . '</td>';
            }
        }

        // Generate action buttons
        if (!empty($actions)) {
            $output .= '<td>';
            $output .= '<div class="dropdown">';
            $output .= '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Choose Action</button>';
            $output .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            foreach ($actions as $action) {
                $output .= $action($row->id);
            }
            $output .= '</ul></div>';
            $output .= '</td>';
        }
        $output .= '</tr>';
    }
    $output .= '</tbody></table>';

    return $output;
}

// function generateDynamicTable($data, $actions, $rem = [], $MustHave = [], $TableClass = "mytable")
// {
//     // Check if the data is empty
//     if ($data->isEmpty()) {
//         return '<p>No data available.</p>';
//     }

//     // Get the first row to extract headers
//     $firstRow = $data->first();

//     // Generate table headers
//     $output = '<table class="' . $TableClass . '  table table-rounded table-bordered border gy-3 gs-3">';
//     $output .= '<thead><tr class="fw-bold text-gray-800 border-bottom border-gray-200">';

//     foreach ($firstRow as $key => $value) {
//         if (in_array($key, $rem)) {
//             continue;
//         }

//         $header = ucwords(Str::snake($key, ' '));
//         $output .= '<th>' . e($header) . '</th>';
//     }

//     // Add custom headers if provided
//     if (!empty($MustHave)) {
//         foreach ($MustHave as $customHeader) {
//             $output .= '<th>' . $customHeader['header'] . '</th>';
//         }
//     }

//     if (!empty($actions)) {
//         $output .= '<th class="bg-dark text-light">Actions</th>';
//     }
//     $output .= '</tr></thead>';

//     // Generate table data
//     $output .= '<tbody>';
//     foreach ($data as $row) {
//         $output .= '<tr>';
//         foreach ($row as $key => $value) {
//             if (in_array($key, $rem)) {
//                 continue;
//             }

//             // Check if the column contains a PDF link or path
//             $isPdf = preg_match('/(.pdf$)|(.pdf\?)/i', $value);

//             if ($isPdf) {
//                 $output .= '<td>';
//                 $output .= '<a data-doc="' . e($row->id) . '" data-source="' . e(asset($value)) . '" data-bs-toggle="modal" href="#PdfJS" class="btn btn-sm PdfViewer btn-info"><i class="fas fa-file-pdf" aria-hidden="true"></i></a>';
//                 $output .= '</td>';
//             } else {
//                 // Check if the value is numeric (integer, float, or decimal) and format it using number_format()
//                 if (is_numeric($value)) {
//                     $output .= '<td>' . e(number_format($value, 2)) . '</td>';
//                 } else {
//                     $output .= '<td>' . e($value) . '</td>';
//                 }
//             }
//         }

//         // Add custom data if provided
//         if (!empty($MustHave)) {
//             foreach ($MustHave as $customData) {
//                 $output .= '<td>' . $customData['data']($row) . '</td>';
//             }
//         }

//         // Generate action buttons
//         if (!empty($actions)) {
//             $output .= '<td>';
//             $output .= '<div class="dropdown">';
//             $output .= '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Choose Action</button>';
//             $output .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
//             foreach ($actions as $action) {
//                 $output .= $action($row->id);
//             }
//             $output .= '</ul></div>';
//             $output .= '</td>';
//         }
//         $output .= '</tr>';
//     }
//     $output .= '</tbody></table>';

//     return $output;

// }

function camel_case($str)
{
    $str    = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    $str[0] = strtolower($str[0]);
    return $str;
}

function UpdateModalHeader($Title, $ModalID)
{
    echo ' <div class="modal bg-white fade"  id="Update' .
        $ModalID .
        '" style="height:800px">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" >
        <div class="modal-content shadow-none">
            <div class="modal-header">
                <h5 class="modal-title">' .
        $Title .
        '</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                    data-bs-dismiss="modal" aria-label="Close">
                    <span class=" text-dark svg-icon svg-icon-2x">
                        <i class="fas fa-times fa-2x"></i>
                    </span>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body" style="">';
}

function _UpdateModalFooter()
{

    echo ' </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger shadow-lg"
            data-bs-dismiss="modal">Close</button>
        <button   data-bs-dismiss="modal" id="UpdateFormButton" type="submit" class="btn btn-dark shadow-lg">Update
            Record</button>


    </div>
    </div>
    </div>
    </div>';
}

function UpdateModalFooter()
{
    echo ' </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger shadow-lg"
            data-bs-dismiss="modal">Close</button>
        <button   data-bs-dismiss="modal" type="submit" class="btn btn-dark shadow-lg">Update
            Record</button>


    </div>
    </div>
    </div>
    </div>';
}

function MenuItem($link, $label, $class = 'null', $data_route = "null")
{
    echo ' <div class="menu-item">
    <a class="menu-link  ' . $class . '" href="' .
        $link .
        '" data-route="' . $data_route . '">
        <span class="menu-bullet">
        <i class="me-2 fas fa-circle-notch text-danger "></i>
        </span>
        <span class="menu-title fs-6">

        ' .
        $label .
        '</span>
    </a>
</div>';
}

function HeaderBtn($Toggle, $Class, $Label, $Icon, $BtnClass = 'btn-sm')
{
    echo '  <button type="button" class="btn mx-1 float-end   mb-2 ' .
        $BtnClass .
        ' ' .
        $Class .
        '" data-bs-toggle="modal" data-bs-target="#' .
        $Toggle .
        '">
    <i class="fas me-1 ' .
        $Icon .
        ' " aria-hidden="true"></i>' .
        $Label .
        '</button>';
}

function HeaderBtn2($Toggle, $Class, $Label, $Icon)
{
    echo '  <button type="button" class="btn mx-1 float-end btn-sm  mb-2 ' .
        $Class .
        '" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#' .
        $Toggle .
        '">
    <i class="fas me-1 ' .
        $Icon .
        ' " aria-hidden="true"></i>' .
        $Label .
        '</button>';
}

function FromCamelCase($input)
{
    $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
    preg_match_all($pattern, $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = strtoupper($match) == $match ? strtolower($match) : lcfirst($match);
    }

    return implode(' ', $ret);
}

function CreateInputText($data = [], $placeholder = null, $col = '4')
{
    echo ' <div class="col-md-' .
    $col .
    ' mb-3 mt-3 x_' .
    $data['name'] .
    '">
        <div class="mb-3">
            <label class="required form-label">' .
    ucfirst(FromCamelCase($data['name'])) .
        '</label>
            <input required type="text" name="' .
        $data['name'] .
        '" class="form-control " placeholder="' .
        $placeholder .
        '" />
        </div>
    </div>';
}

function CreateInputInteger($data = [], $placeholder = null, $col = '4')
{
    echo ' <div class="col-md-' .
    $col .
    ' mb-3 mt-3 x_' .
    $data['name'] .
    '">
        <div class="mb-3">
            <label class="required form-label">' .
    ucfirst(FromCamelCase($data['name'])) .
        '</label>
            <input required type="text" name="' .
        $data['name'] .
        '" class="form-control IntOnlyNow" placeholder="' .
        $placeholder .
        '" />
        </div>
    </div>';
}

function CreateInputDate($data = [], $placeholder = null, $col = '4')
{
    echo ' <div class="col-md-' .
    $col .
    ' mb-3 mt-3 x_' .
    $data['name'] .
    '">
        <div class="mb-3">
            <label class="required form-label">' .
    ucfirst(FromCamelCase($data['name'])) .
        '</label>
            <input required type="text" name="' .
        $data['name'] .
        '" class="form-control DateArea" placeholder="' .
        $placeholder .
        '" />
        </div>
    </div>';
}

function CreateInputEditor($data = [], $placeholder = null, $col = '12')
{
    echo ' <div class="col-md-' .
    $col .
    ' mb-3 mt-3 x_' .
    $data['name'] .
    '">
        <div class="mb-3">
            <label class="required form-label">' .
    ucfirst(FromCamelCase($data['name'])) .
        '</label>
            <textarea name="' .
        $data['name'] .
        '" class="form-control editorme"></textarea>
        </div>
    </div>';
}

function DescModal($ArrayData, $Title, $ModalID, $col)
{
    foreach ($ArrayData as $data) {
        echo '<div class="modal fade"  id="' .
        $ModalID .
        $data->id .
        '">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                       ' .
        $Title .
        '
                    </h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x">
                        <i class="fas fa-3x text-dark fa-times" aria-hidden="true"></i>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">


                        <div class="mb-10 col-md-12">
                            <label for="exampleFormControlInput1" class="required form-label">Description/Details</label>
                            <textarea name="Desc">
                             ' .
        $data->{$col} .
            '
                            </textarea>
                        </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-dark shadow-lg" data-bs-dismiss="modal">Close</button>


                </div>

            </div>
        </div>
    </div>';
    }
}

function ConfirmBtn(
    $data = [
        'msg'   => '',
        'route' => '',
        'label' => 'delete',
        'class' => 'dropdown-item deleteConfirm',
    ],
) {
    echo '
    <a data-msg="' .
        $data['msg'] .
        '
    " data-route="' .
        $data['route'] .
        '
    " class="' .
        $data['class'] .
        '
    " href="#">' .
        $data['label'] .
        '</a>';
}

function Alert($icon = 'fa-info', $class = 'alert-primary', $Title = '', $Msg = '')
{
    echo '<div class="alert ' .
        $class .
        ' shadow-lg">
   <!--begin::Icon-->
   <span class=" float-end svg-icon svg-icon-2hx svg-icon-primary me-3">

      <i class="fas ' .
        $icon .
        ' fa-2x" aria-hidden="true"></i>

   </span>
   <!--end::Icon-->

   <!--begin::Wrapper-->
   <div class="d-flex flex-column">
       <!--begin::Title-->
       <h4 class="mb-1 text-dark">' .
        $Title .
        '</h4>
       <!--end::Title-->
       <!--begin::Content-->
       <span>' .
        $Msg .
        '</span>
       <!--end::Content-->
   </div>
   <!--end::Wrapper-->
</div>
<!--end::Alert-->
';
}

function UpdateString($name = null, $label = null, $value = null)
{
    echo ' <div class="col-md-4 mb-3 mt-3 ">
    <div class="mb-3">
        <label class="required form-label">' .
        $label .
        '</label>
        <input required type="text" name="' .
        $name .
        '" class="form-control " placeholder="" value="' .
        $value .
        '"/>
    </div>
</div>';
}

function UpdateInteger($name = null, $label = null, $value = null)
{
    echo ' <div class="col-md-4 mb-3 mt-3">
     <div class="mb-3">
     <label class="required form-label">' .
        $label .
        '</label>
     <input required type="text" name="' .
        $name .
        '" class="form-control IntOnlyNow" placeholder="" value="' .
        $value .
        '"/>

     </div>
 </div>';
}

function UpdateDate($name = null, $label = null, $value = null)
{
    echo '  <div class="col-md-4 mb-3 mt-3">
      <div class="mb-3">
      <label class="required form-label">' .
        $label .
        '</label>
      <input required type="text" name="' .
        $name .
        '" class="form-control DateArea" placeholder="" value="' .
        $value .
        '"/>

      </div>
  </div>';
}

function UpdateDate2($name = null, $label = null, $value = null)
{
    echo '  <div class="col-md-12 mb-3 mt-3">
      <div class="mb-3">
      <label class="required form-label">' .
        $label .
        '</label>
      <input required type="text" name="' .
        $name .
        '" class="form-control DateArea" placeholder="" value="' .
        $value .
        '"/>

      </div>
  </div>';
}

function UpdateText($name = null, $label = null, $value = null)
{
    echo '<div class="col-md-12 mb-3 mt-3">
      <div class="mb-3">
      <label class="required form-label">' .
        $label .
        '</label>
          <textarea name="' .
        $name .
        '" class="form-control">
            ' .
        $value .
        '
          </textarea>
      </div>
  </div>
';
}

function RunUpdateModal($ModalID, $Extra, $csrf, $Title, $RecordID, $col, $te, $TableName)
{
    $FormEngine = new FormEngine();

    return $FormEngine->UpdateModal($ModalID, $Extra, $csrf, $Title, $RecordID, $col, $te, $TableName);
}

function RunUpdateModalFinal($ModalID, $Extra, $csrf, $Title, $RecordID, $col, $te, $TableName)
{
    $FormEngine = new FormEngine();

    return $FormEngine->UpdateModalFinal($ModalID, $Extra, $csrf, $Title, $RecordID, $col, $te, $TableName);
}
