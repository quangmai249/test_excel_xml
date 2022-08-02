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

    $numberOfSheet = $_POST["submit_sheet"];
    $worksheet = $excelObj->getSheet($numberOfSheet);
    $lastRow = $worksheet->getHighestRow();

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
    $arrayDefault = array(
        'nameOfFile' => 'test',
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

    $name_of_file_xml = $arrayDefault['nameOfFile'];

    $xmlDoc = new DOMDocument();

    foreach ($data[0] as $key => $val) {
        $data_from_excel[] = $val;
    }

    $root = $xmlDoc->appendChild($xmlDoc->createElement("questestinterop"));

    $number_of_answers = 0;

    foreach ($data_from_excel as $key => $val) {
        $item = array_slice($val, 0, 1);
        $title = array_slice($val, 1, 1);
        $answers = array_slice($val, 2, 1);
        $info_answers = array_slice($val, 3, 1);
        $true_answers = array_slice($val, 4, 1);
        $type_answers = array_slice($val, 5, 1);

        foreach ($title as $key_title => $val_title) {
            foreach ($type_answers as $val_type_answers) {
                if (!empty($val_title)) {
                    $tab_item = $root->appendChild($xmlDoc->createElement('item'));
                    $tab_item->setAttribute('ident', rand(1000000000, 9999999999));

                    $tab_item->setAttribute('title', $val_title);
                    $tab_item->setAttribute('maxattempts', 0);

                    $qticomment = $tab_item->appendChild($xmlDoc->createElement("qticomment"));
                    $duration = $tab_item->appendChild($xmlDoc->createElement("duration", 'P0Y0M0DT0H1M0S'));
                    $itemmetadata = $tab_item->appendChild($xmlDoc->createElement("itemmetadata"));
                    $qtimetadata = $itemmetadata->appendChild($xmlDoc->createElement("qtimetadata"));

                    $qtimetadatafield = $qtimetadata->appendChild($xmlDoc->createElement('qtimetadatafield'));

                    if ($val_type_answers == 0) {
                        foreach ($arrayDefault['qtimetadatafield'] as $val_qtimetadatafield) {
                            if (!empty($qtimetadatafield)) {
                                $qtimetadatafield = $qtimetadata->appendChild($xmlDoc->createElement('qtimetadatafield'));
                                foreach ($val_qtimetadatafield as $key => $val) {
                                    $qtimetadatafield->appendChild($xmlDoc->createElement($key, $val));
                                }
                            }
                        }

                    }
                    if ($val_type_answers == 1) {
                        foreach ($arrayDefault['qtimetadatafield_multichoice'] as $val_qtimetadatafield) {
                            if (!empty($qtimetadatafield)) {
                                $qtimetadatafield = $qtimetadata->appendChild($xmlDoc->createElement('qtimetadatafield'));
                                foreach ($val_qtimetadatafield as $key => $val) {
                                    $qtimetadatafield->appendChild($xmlDoc->createElement($key, $val));
                                }
                            }
                        }
                    }

                    $presentation = $tab_item->appendChild($xmlDoc->createElement('presentation'));
                    $presentation->setAttribute('label', $val_title);
                    $flow = $presentation->appendChild($xmlDoc->createElement("flow"));
                    $material = $flow->appendChild($xmlDoc->createElement("material"));
                    $mattext = $material->appendChild($xmlDoc->createElement("mattext", 'H&#xE3;y ch&#x1ECD;n &#x111;&#xE1;p &#xE1;n &#x111;&#xFA;ng'));
                    $mattext->setAttribute("texttype", 'text/plain');

                    $response_lid = $flow->appendChild($xmlDoc->createElement("response_lid"));
                    $response_lid->setAttribute("ident", 'MCSR');

                    if ($val_type_answers == 0) {
                        $response_lid->setAttribute("rcardinality", 'Single');

                    }
                    if ($val_type_answers == 1) {
                        $response_lid->setAttribute("rcardinality", 'Multiple');

                    }

                    $render_choice = $response_lid->appendChild($xmlDoc->createElement("render_choice"));
                    $render_choice->setAttribute("shuffle", 'Yes');

                    $resprocessing = $tab_item->appendChild($xmlDoc->createElement("resprocessing"));
                    $outcomes = $resprocessing->appendChild($xmlDoc->createElement("outcomes"));
                    $decvar = $outcomes->appendChild($xmlDoc->createElement("decvar"));
                }
            }
        }

        foreach ($type_answers as $val_type_answers) {

            foreach ($info_answers as $val_info_answer) {

                $response_label = $render_choice->appendChild($xmlDoc->createElement("response_label"));
                $response_label->setAttribute("ident", $number_of_answers);

                $material = $response_label->appendChild($xmlDoc->createElement("material"));

                $mattext = $material->appendChild($xmlDoc->createElement("mattext", $val_info_answer));
                $mattext->setAttribute("texttype", 'text/plain');

                $respcondition = $resprocessing->appendChild($xmlDoc->createElement("respcondition"));
                $respcondition->setAttribute("continue", 'Yes');
                $conditionvar = $respcondition->appendChild($xmlDoc->createElement("conditionvar"));

                $varequal = $conditionvar->appendChild($xmlDoc->createElement("varequal", $number_of_answers));
                $varequal->setAttribute("respident", 'MCSR');

                $displayfeedback = $respcondition->appendChild($xmlDoc->createElement("displayfeedback"));
                $displayfeedback->setAttribute("feedbacktype", 'Response');
                $displayfeedback->setAttribute("linkrefid", 'response_' . $number_of_answers);

                $itemfeedback = $tab_item->appendChild($xmlDoc->createElement("itemfeedback"));
                $itemfeedback->setAttribute("ident", 'response_' . $number_of_answers);
                $itemfeedback->setAttribute("view", 'All');

                $flow_mat = $itemfeedback->appendChild($xmlDoc->createElement("flow_mat"));
                $material = $flow_mat->appendChild($xmlDoc->createElement("material"));
                $mattext = $material->appendChild($xmlDoc->createElement("mattext"));

                $mattext->setAttribute("texttype", 'text/plain');
            }
            $number_of_answers++;
            if ($number_of_answers > 3) {
                $number_of_answers = 0;
            }
        }

        // this is check answers true!!!
        foreach ($true_answers as $val_true_answers) {
            if (!empty($val_true_answers)) {
                $check_true_answer = str_split($val_true_answers);

                $setvar = $respcondition->appendChild($xmlDoc->createElement("setvar", 0));
                $setvar->setAttribute("action", 'Add');

            } else {
                $setvar = $respcondition->appendChild($xmlDoc->createElement("setvar", 0));
                $setvar->setAttribute("action", 'Add');
            }
        }
    }

    header("Content-Type: text/html; charset=UTF-8");
    $xmlDoc->formatOutput = true;
    $file_name = str_replace(' ', '_', $name_of_file_xml) . '.xml';
    $xmlDoc->save($file_name);
    return $file_name;
}

generateXML($array);
echo '<br/>' . date('d-m-y h:i:s');
?>