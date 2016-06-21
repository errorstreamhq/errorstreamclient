<?php
namespace ErrorStream\ErrorStreamClient;

use ErrorStream\ErrorStreamClient\ErrorStreamReport;
use GuzzleHttp\Client;

/**
 * ErrorStreamClient allows multiple different interfaces to send data to ErrorStream.com using
 * eithter exceptions or the ErrorStreamReport class.
 * Class ErrorStreamClient
 * @package ErrorStream\ErrorStreamClient
 */
class ErrorStreamClient {

    public $api_token;
    public $project_token;
    private $tags = [];
    private $context = [];

    /**
     * Tag this software. Good things to tag might be releases, servers, etc.
     * This will be appended to any reports that arise.
     * @param $tag
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * Add context to your application stack trace. Pass a string to represent
     * more details in your stack traces within the website. This will be appended
     * to any reports that arise.
     * @param $context
     */
    public function addContext($context)
    {
        $this->context[] = $context;
    }

    /**
     * Report an exception to the errorstream website.
     * @param \Exception $ex
     * @return string
     */
    public function reportException(\Exception $ex)
    {
        $report = new ErrorStreamReport();
        $report->error_group = $ex->getMessage().':'.$ex->getLine();
        $report->line_number = $ex->getLine();
        $report->file_name = $ex->getFile();
        $report->message = $ex->getMessage();
        $report->stack_trace = $ex->getTraceAsString();
        $report->severity = 3;
        return $this->report($report);
    }

    /**
     * Make a request sending the errorstream report.
     * @param \ErrorStream\ErrorStreamClient\ErrorStreamReport $report
     * @return string
     */
    public function report(ErrorStreamReport $report)
    {
        $report->tags = $this->tags;
        $report->context = $this->context;
        return $this->makeRequest($report);
    }


    /**
     * Simple request interface using guzzle to send JSON data to errorstream
     * @param $data
     * @return string
     */
    protected function makeRequest($data)
    {
        $url = 'https://www.errorstream.com/api/1.0/errors/create?'.http_build_query(['api_token' => $this->api_token, 'project_token' => $this->project_token]);

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;

        } catch (\Exception $ex){
            return $ex->getMessage();
        }
    }

}