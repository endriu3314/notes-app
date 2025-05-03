<?php

namespace NotesApi\Request;

class DumpRequestToFile
{
    public function execute(Request $request)
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->uri(),
            'headers' => $request->headers(),
            'body' => $request->all(),
        ];

        file_put_contents(__DIR__.'/../../logs/request.log', json_encode($data).PHP_EOL, FILE_APPEND);
    }
}
