<form method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"> . <br><br>
    Select sheet to upload:
    <input type="number" value="" name="submit_sheet"> . <br><br>
    <input type="submit" value="Upload file" name="submit">
</form>

<?php

require_once "Classes/PHPExcel.php";

$tmpfname = basename($_FILES["fileToUpload"]["name"]);
$dataAll = [];

if (empty($_POST["submit"])) {
    echo '<script>alert("Need to select file to upload and select the number of a sheet")</script>';
}

if (isset($_POST["submit"])) {

    $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
    $excelObj = $excelReader->load($tmpfname);

    //get the number of a sheet you want to use
    $numberOfSheet = $_POST["submit_sheet"];
    $worksheet = $excelObj->getSheet($numberOfSheet);
    $lastRow = $worksheet->getHighestRow();

    //get row A_B_C_D_E form a sheet in an excel file
    $data_a_sheet = [];
    for ($row = 2; $row <= $lastRow; $row++) {
        $data_a_sheet[] = [
            'A' => $worksheet->getCell('A' . $row)->getValue(),
            'B' => $worksheet->getCell('B' . $row)->getValue(),
            'C' => $worksheet->getCell('C' . $row)->getValue(),
            'D' => $worksheet->getCell('D' . $row)->getValue(),
            'E' => $worksheet->getCell('E' . $row)->getValue(),
            'F' => $worksheet->getCell('F' . $row)->getValue(),
        ];
    }

    $dataAll[] = $data_a_sheet;
}

$array = $dataAll;

function generateXML($data)
{
    //some values default of  a Quiz in ILIAS
    $arrayDefault = array(
        'nameOfFile' => 'file_quiz',
        'maxattempts' => '0',
        'duration' => 'P0Y0M0DT0H1M0S',
        'qtimetadatafield' => array(
            '0' => array(
                'fieldlabel' => 'ILIAS_VERSION',
                'fieldentry' => '7.10 2022-04-28',
            ),
            '1' => array(
                'fieldlabel' => 'QUESTIONTYPE',
                'fieldentry' => 'SINGLE CHOICE QUESTION',
            ),
            '2' => array(
                'fieldlabel' => 'AUTHOR',
                'fieldentry' => 'root user',
            ),
            '3' => array(
                'fieldlabel' => 'additional_cont_edit_mode',
                'fieldentry' => 'default',
            ),
            '4' => array(
                'fieldlabel' => 'externalId',
                'fieldentry' => '62b96929141b43.43514600',
            ),
            '5' => array(
                'fieldlabel' => 'ilias_lifecycle',
                'fieldentry' => 'draft',
            ),
            '6' => array(
                'fieldlabel' => 'lifecycle',
                'fieldentry' => 'draft',
            ),
            '7' => array(
                'fieldlabel' => 'thumb_size',
                'fieldentry' => '',
            ),
            '8' => array(
                'fieldlabel' => 'feedback_setting',
                'fieldentry' => '2',
            ),
            '9' => array(
                'fieldlabel' => 'singleline',
                'fieldentry' => '1',
            ),
        ),
        'qtimetadatafield_multichoice' => array(
            '0' => array(
                'fieldlabel' => 'ILIAS_VERSION',
                'fieldentry' => '7.10 2022-04-28',
            ),
            '1' => array(
                'fieldlabel' => 'QUESTIONTYPE',
                'fieldentry' => 'MULTIPLE CHOICE QUESTION',
            ),
            '2' => array(
                'fieldlabel' => 'AUTHOR',
                'fieldentry' => 'root user',
            ),
            '3' => array(
                'fieldlabel' => 'additional_cont_edit_mode',
                'fieldentry' => 'default',
            ),
            '4' => array(
                'fieldlabel' => 'externalId',
                'fieldentry' => '62df9a7b5b37e2.17133519',
            ),
            '5' => array(
                'fieldlabel' => 'ilias_lifecycle',
                'fieldentry' => 'draft',
            ),
            '6' => array(
                'fieldlabel' => 'lifecycle',
                'fieldentry' => 'draft',
            ),
            '7' => array(
                'fieldlabel' => 'thumb_size',
                'fieldentry' => '',
            ),
            '8' => array(
                'fieldlabel' => 'feedback_setting',
                'fieldentry' => '1',
            ),
            '9' => array(
                'fieldlabel' => 'singleline',
                'fieldentry' => '0',
            ),
        ),
    );

    $nameOfFile = $arrayDefault['nameOfFile'];
    $duration = $arrayDefault['duration'];
    $maxattempts = $arrayDefault['maxattempts'];

    $xmlDoc = new DOMDocument();

    //questestinterop
    $root = $xmlDoc->appendChild($xmlDoc->createElement("questestinterop"));

    $count_response_label = 0;
    $count_response_label_setvar = 0;
    $count_answer_default = 3;

    foreach ($data[0] as $dataTitle => $val_dataTitle) {
        $totalItem = array_slice($val_dataTitle, 0, 1);
        $totalTitle = array_slice($val_dataTitle, 1, 1);
        $totalCountAnswers = array_slice($val_dataTitle, 2, 1);
        $totalAnswers = array_slice($val_dataTitle, 3, 1);
        $totalTrueAnswer = array_slice($val_dataTitle, 4, 1);
        $totalTypeAnswer = array_slice($val_dataTitle, 5, 1);

        foreach ($totalTitle as $total_item => $val_total_item) {
            foreach ($totalTypeAnswer as $key_total_val_answer => $val_total_type_answer) {
                if (!empty($val_total_item)) {

                    $tabItem = $root->appendChild($xmlDoc->createElement('item'));
                    $tabItem->setAttribute('ident', rand(1000000000, 9999999999));

                    $tabItem->setAttribute('title', $val_total_item);
                    $tabItem->setAttribute('maxattempts', $maxattempts);

                    $rootQticomment = $tabItem->appendChild($xmlDoc->createElement("qticomment"));
                    $rootDuration = $tabItem->appendChild($xmlDoc->createElement("duration", $duration));

                    // itemmetadata
                    $rootItemmetadata = $tabItem->appendChild($xmlDoc->createElement("itemmetadata"));
                    $rootQtimetadata = $rootItemmetadata->appendChild($xmlDoc->createElement("qtimetadata"));

                    if ($val_total_type_answer == 0) {
                        foreach ($arrayDefault['qtimetadatafield'] as $qtimetadatafield) {
                            if (!empty($qtimetadatafield)) {
                                $tabQtimetadatafield = $rootQtimetadata->appendChild($xmlDoc->createElement('qtimetadatafield'));
                                foreach ($qtimetadatafield as $key => $val) {
                                    $tabQtimetadatafield->appendChild($xmlDoc->createElement($key, $val));
                                }
                            }
                        }

                        // presentation
                        $tabPresentation = $tabItem->appendChild($xmlDoc->createElement('presentation'));
                        $tabPresentation->setAttribute('label', $val_total_item);
                        $rootFlow = $tabPresentation->appendChild($xmlDoc->createElement("flow"));
                        $rootMaterial = $rootFlow->appendChild($xmlDoc->createElement("material"));
                        $rootMattext = $rootMaterial->appendChild($xmlDoc->createElement("mattext", 'H&#xE3;y ch&#x1ECD;n &#x111;&#xE1;p &#xE1;n &#x111;&#xFA;ng'));
                        $rootMattext->setAttribute("texttype", 'text/plain');

                        $rootResponse_lid = $rootFlow->appendChild($xmlDoc->createElement("response_lid"));
                        $rootResponse_lid->setAttribute("ident", 'MCSR');
                        $rootResponse_lid->setAttribute("rcardinality", 'Single');

                        $rootRender_choice = $rootResponse_lid->appendChild($xmlDoc->createElement("render_choice"));
                        $rootRender_choice->setAttribute("shuffle", 'Yes');

                        // resprocessing
                        $rootResprocessing = $tabItem->appendChild($xmlDoc->createElement("resprocessing"));
                        $rootOutcomes = $rootResprocessing->appendChild($xmlDoc->createElement("outcomes"));
                        $rootDecvar = $rootOutcomes->appendChild($xmlDoc->createElement("decvar"));

                    }

                    if ($val_total_type_answer == 1) {
                        foreach ($arrayDefault['qtimetadatafield_multichoice'] as $qtimetadatafield) {
                            if (!empty($qtimetadatafield)) {
                                $tabQtimetadatafield = $rootQtimetadata->appendChild($xmlDoc->createElement('qtimetadatafield'));
                                foreach ($qtimetadatafield as $key => $val) {
                                    $tabQtimetadatafield->appendChild($xmlDoc->createElement($key, $val));
                                }
                            }
                        }
                        // presentation
                        $tabPresentation = $tabItem->appendChild($xmlDoc->createElement('presentation'));
                        $tabPresentation->setAttribute('label', $val_total_item);
                        $rootFlow = $tabPresentation->appendChild($xmlDoc->createElement("flow"));
                        $rootMaterial = $rootFlow->appendChild($xmlDoc->createElement("material"));
                        $rootMattext = $rootMaterial->appendChild($xmlDoc->createElement("mattext", 'H&#xE3;y ch&#x1ECD;n &#x111;&#xE1;p &#xE1;n &#x111;&#xFA;ng'));
                        $rootMattext->setAttribute("texttype", 'text/plain');

                        $rootResponse_lid = $rootFlow->appendChild($xmlDoc->createElement("response_lid"));
                        $rootResponse_lid->setAttribute("ident", 'MCSR');
                        $rootResponse_lid->setAttribute("rcardinality", 'Multiple');

                        $rootRender_choice = $rootResponse_lid->appendChild($xmlDoc->createElement("render_choice"));
                        $rootRender_choice->setAttribute("shuffle", 'Yes');

                        // resprocessing
                        $rootResprocessing = $tabItem->appendChild($xmlDoc->createElement("resprocessing"));
                        $rootOutcomes = $rootResprocessing->appendChild($xmlDoc->createElement("outcomes"));
                        $rootDecvar = $rootOutcomes->appendChild($xmlDoc->createElement("decvar"));
                    }
                }
            }
        }

        foreach ($totalAnswers as $key_total_answer => $val_total_answer) {
            foreach ($totalTypeAnswer as $key_total_val_answer => $val_total_type_answer) {

                //setvar
                $true_answer = [0, 0, 0, 0];
                foreach ($totalTrueAnswer as $val_total_true_answer_str) {
                    $val_total_true_answer = str_split($val_total_true_answer_str);
                    if (!empty($val_total_true_answer_str)) {
                        foreach ($val_total_true_answer as $key => $val) {
                            if ($val != null) {
                                if ($val == 'A' || $val == 'a') {
                                    $true_answer[0] = 1;
                                }
                                if ($val == 'B' || $val == 'b') {
                                    $true_answer[1] = 1;
                                }
                                if ($val == 'C' || $val == 'c') {
                                    $true_answer[2] = 1;
                                }
                                if ($val == 'D' || $val == 'd') {
                                    $true_answer[3] = 1;
                                }
                            }
                        }
                    }
                }

                if ($val_total_type_answer == 0) {
                    $rootResponse_label = $rootRender_choice->appendChild($xmlDoc->createElement("response_label"));
                    $rootResponse_label->setAttribute("ident", $count_response_label);

                    $rootMaterial = $rootResponse_label->appendChild($xmlDoc->createElement("material"));

                    $rootMattext = $rootMaterial->appendChild($xmlDoc->createElement("mattext", $val_total_answer));
                    $rootMattext->setAttribute("texttype", 'text/plain');

                    if (array_sum($true_answer) > 0) {
                        foreach ($true_answer as $val_true_answer) {

                            $rootRespcondition = $rootResprocessing->appendChild($xmlDoc->createElement("respcondition"));
                            $rootRespcondition->setAttribute("continue", 'Yes');
                            $rootConditionvar = $rootRespcondition->appendChild($xmlDoc->createElement("conditionvar"));

                            $rootVarequal = $rootConditionvar->appendChild($xmlDoc->createElement("varequal", $count_response_label_setvar));
                            $rootVarequal->setAttribute("respident", 'MCSR');

                            $rootSetvar = $rootRespcondition->appendChild($xmlDoc->createElement("setvar", $val_true_answer));
                            $rootSetvar->setAttribute("action", 'Add');
                            $rootDisplayfeedback = $rootRespcondition->appendChild($xmlDoc->createElement("displayfeedback"));
                            $rootDisplayfeedback->setAttribute("feedbacktype", 'Response');
                            $rootDisplayfeedback->setAttribute("linkrefid", 'response_' . $count_response_label_setvar);

                            $count_response_label_setvar++;

                            if ($count_response_label_setvar > $count_answer_default) {
                                $count_response_label_setvar = 0;
                            }
                        }

                    }

                }
                //
                if ($val_total_type_answer == 1) {

                    foreach ($true_answer as $val_true_answer) {
                        $rootRespcondition = $rootResprocessing->appendChild($xmlDoc->createElement("respcondition"));
                        $rootRespcondition->setAttribute("continue", 'Yes');
                        $rootRespcondition->appendChild($xmlDoc->createElement("conditionvar"));

                        $rootNot = $rootRespcondition->appendChild($xmlDoc->createElement("not"));

                        $rootVarequal = $rootNot->appendChild($xmlDoc->createElement("varequal", $count_response_label_setvar));
                        $rootVarequal->setAttribute("respident", 'MCSR');

                        $rootSetvar = $rootRespcondition->appendChild($xmlDoc->createElement("setvar", 0));
                        $rootSetvar->setAttribute("action", 'Add');

                        $count_response_label_setvar++;

                        if ($count_response_label_setvar > $count_answer_default) {
                            $count_response_label_setvar = 0;
                        }
                    }

                    $rootResponse_label = $rootRender_choice->appendChild($xmlDoc->createElement("response_label"));
                    $rootResponse_label->setAttribute("ident", $count_response_label);

                    $rootMaterial = $rootResponse_label->appendChild($xmlDoc->createElement("material"));

                    $rootMattext = $rootMaterial->appendChild($xmlDoc->createElement("mattext", $val_total_answer));
                    $rootMattext->setAttribute("texttype", 'text/plain');
                }

                // itemfeedback
                $rootItemfeedback = $tabItem->appendChild($xmlDoc->createElement("itemfeedback"));
                $rootItemfeedback->setAttribute("ident", 'response_' . $count_response_label);
                $rootItemfeedback->setAttribute("view", 'All');

                $rootFlow_mat = $rootItemfeedback->appendChild($xmlDoc->createElement("flow_mat"));
                $rootMaterial = $rootFlow_mat->appendChild($xmlDoc->createElement("material"));
                $rootMattext = $rootMaterial->appendChild($xmlDoc->createElement("mattext"));

                $rootMattext->setAttribute("texttype", 'text/plain');

                $count_response_label++;

                if ($count_response_label > $count_answer_default) {
                    $count_response_label = 0;
                }
            }
        }
    }

    // Type content
    header("Content-Type: text/html; charset=UTF-8");
    //header("Content-Type: multipart/form-data; boundary=something");

    // Make the output
    $xmlDoc->formatOutput = true;

    // Save xml file
    $file_name = str_replace(' ', '_', $nameOfFile) . '.xml';

    $xmlDoc->save($file_name);

    // Return xml file name
    return $file_name;
}

generateXML($array);

echo '<br/>' . date('d-m-y h:i:s');
