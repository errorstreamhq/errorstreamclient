<?php
namespace ErrorStream\ErrorStreamClient;

use ErrorStream\ErrorStreamClient\ErrorStreamReport;
use GuzzleHttp\Client;

class ErrorStreamClient {

    public $api_token;
    public $project_token;

    public function reportException($ex)
    {
        $report = new ErrorStreamReport();
        $report->error_group = $ex->getMessage().':'.$ex->getLine();
        $report->line_number = $ex->getLine();
        $report->file_name = $ex->getFile();
        $report->message = $ex->getMessage();
        $report->stack_trace = $ex->getTraceAsString();
        $report->severity = 3;
        return $this->makeRequest($report);
    }

    protected function makeRequest($data)
    {
        $guzzle = new Client();
        $url = 'http://errorstream.com/api/1.0/errors/create?'.http_build_query(['api_token' => $this->api_token, 'project_token' => $this->project_token]);

        try {

            $r = $guzzle->request('POST', $url, [
                'json' => $data,
            ]);

            return $r->getBody();

        } catch (\Exception $ex){
            return $ex->getMessage();
        }
    }

}