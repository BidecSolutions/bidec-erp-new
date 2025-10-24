<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Common JSON Response Handler
 */
if (!function_exists('makeApiRequest')) {
    function makeApiRequest($url, $method = 'GET', $data = [])
    {
        $client = new Client();
        $response = $client->request($method, $url, [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $message = '', $status = 'success', $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}

if (!function_exists('jsonWithWebResponse')) {
    function jsonWithWebResponse($data, $message = '', $status = 'success', $code = 200, $pageOptionType = 'normal', $page = '')
    {
        if ($pageOptionType == 'normal') {
            return redirect()
                ->route($page . 'index')
                ->with('success', $message);
        } else {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ], $code);
        }
    }
}





/**
 * Handle Web Response
 */

if (!function_exists('webResponse')) {
    function webResponse($page, $view, $data = [])
    {
        return view($page . $view, $data);
    }
}

if (!function_exists('generate_json')) {
    function generate_json($fileName, $deletedIds = [])
    {
        $filePath = storage_path('app/json_files/' . $fileName . '.json');

        // Get existing data from the JSON file
        $existingData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
        if (!is_array($existingData)) {
            $existingData = [];
        }

        // Convert to associative array keyed by 'id'
        $existingData = collect($existingData)->keyBy('id')->toArray();

        // Remove deleted IDs
        foreach ($deletedIds as $deletedId) {
            unset($existingData[$deletedId]);
        }

        // Retrieve the column names
        $columns = Schema::getColumnListing($fileName);
        $dbData = DB::table($fileName)->get();

        // Prepare new data for JSON
        $newData = $dbData->mapWithKeys(function ($row) use ($columns) {
            $rowData = [];
            foreach ($columns as $column) {
                if (isset($row->$column)) {
                    $rowData[$column] = $row->$column;
                }
            }
            return [$row->id => $rowData];
        })->toArray();

        // Merge new data, ensuring deletions are handled
        $mergedData = array_replace($existingData, $newData);

        // Write updated JSON file
        file_put_contents($filePath, json_encode(array_values($mergedData), JSON_PRETTY_PRINT));
    }
}
