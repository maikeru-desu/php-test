<?php

use LSS\Array2Xml;

require_once('enums/FormatType.php');

trait Formatter
{
    public function format($data, $format = 'html') {
        
        // return the right data format
        switch($format) {
            case FormatType::Xml:
                header('Content-type: text/xml');
                
                // fix any keys starting with numbers
                $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
                $xmlData = [];
                foreach ($data->all() as $row) {
                    $xmlRow = [];
                    foreach ($row as $key => $value) {
                        $key = preg_replace_callback('(\d)', function($matches) use ($keyMap) {
                            return $keyMap[$matches[0]] . '_';
                        }, $key);
                        $xmlRow[$key] = $value;
                    }
                    $xmlData[] = $xmlRow;
                }
                $xml = Array2XML::createXML('data', [
                    'entry' => $xmlData
                ]);
                return $xml->saveXML();
                break;
            case FormatType::Json:
                header('Content-type: application/json');
                return json_encode($data->all());
                break;
            case FormatType::Csv:
                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv";');
                if (!$data->count()) {
                    return;
                }
                $csv = [];
                
                // extract headings
                // replace underscores with space & ucfirst each word for a decent headings
                $headings = collect($data->get(0))->keys();
                $headings = $headings->map(function($item, $key) {
                    return collect(explode('_', $item))
                        ->map(function($item, $key) {
                            return ucfirst($item);
                        })
                        ->join(' ');
                });
                $csv[] = $headings->join(',');

                // format data
                foreach ($data as $dataRow) {
                    $csv[] = implode(',', array_values($dataRow));
                }
                return implode("\n", $csv);
                break;
            default: // html
                if (!$data->count()) {
                    return $this->htmlTemplate('Sorry, no matching data was found');
                }
                
                // extract headings
                // replace underscores with space & ucfirst each word for a decent heading
                $headings = collect($data->get(0))->keys();
                $headings = $headings->map(function($item, $key) {
                    return collect(explode('_', $item))
                        ->map(function($item, $key) {
                            return ucfirst($item);
                        })
                        ->join(' ');
                });
                $headings = '<tr><th>' . $headings->join('</th><th>') . '</th></tr>';

                // output data
                $rows = [];
                foreach ($data as $dataRow) {
                    $row = '<tr>';
                    foreach ($dataRow as $key => $value) {
                        $row .= '<td>' . $value . '</td>';
                    }
                    $row .= '</tr>';
                    $rows[] = $row;
                }
                $rows = implode('', $rows);
                return '<table>' . $headings . $rows . '</table>';
                break;
        }
    }
}