<?php

namespace peter\JobDetail;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;

class JobDetail
{
    public $jsonFile = null;
    public $rdArr = [];
    public $jobsArr = [];

    public function __construct()
    {

    }

    public function httpRequest()
    {
        $len = count($this->json);
        $client = new Client();
        for ($index=0;$index<$len;$index++) {
            $name = $this->json[$index]['job_name'];
            $link = $this->json[$index]['job_link'];
            $response = $client->request('GET', $link, ['verify' => false]);
            $this->parseJson($response->getBody()->getContents(), $index);
        }

        file_put_contents('jobs_rd.json', json_encode($this->rdArr));
    }

    public function getPage($url)
    {
        $contents = $this->requestInterface($url);
        $crawler = new Crawler($contents);
        $getNext = $crawler->filter('span[id="loadDone_2"]')->text();

        $getNext = trim($getNext);
        $search = ['，', '開始', '▼', '上', '下', '一', '跳', '第', '共', '頁', ' ', PHP_EOL];
        $getNext = str_replace($search, '', $getNext);
        $getNext = trim(preg_replace('/\t+/', '', $getNext));
        $getNext = substr($getNext, 1);

        return $getNext;
    }

    public function generateJson($url)
    {
        $contents = $this->requestInterface($url);

        if($contents === false) {
            return $contents;
        }

        $crawler = new Crawler($contents);
        $summaryTitle = $crawler->filter('ul.summary_tit');

        $index = count($this->jobsArr);

        foreach ($summaryTitle as $key => $value) {
            $crawler = new Crawler($value);
            $jobs = $crawler->filter('div[class="jobname_summary job_name"]');
            foreach ($jobs as $jobKey => $jobValue) {
                $crawler = new Crawler($jobValue);
                $jobLink = $crawler->filter('a')->attr('href');
                $jobName = $crawler->filter('a')->text();
                if (stristr($jobLink, 'javascript:void(0)') === false) {
                    $this->jobsArr[$index]['job_name'] = $jobName;
                    $this->jobsArr[$index]['job_link'] = $jobLink;
                    $index++;
                    file_put_contents('./jobs.txt', $jobName.PHP_EOL.$jobLink.PHP_EOL, FILE_APPEND);
                } else {
                    echo $jobName.PHP_EOL;
                    echo $jobLink.PHP_EOL;
                }
            }
        }

        return true;
    }
    
    public function parseJson()
    {
        $len = count($this->jobsArr);
        $indexRd = count($this->rdArr);
        $baseUrl = 'https://www.104.com.tw';

        for ($index=0;$index<$len;$index++) {
            $contents = $this->requestInterface($baseUrl.$this->jobsArr[$index]['job_link']);

            if (stristr($contents, '研發替代役') !== false) {
                $this->rdArr[$indexRd]['job_name'] = $this->jobsArr[$index]['job_name'];
                $this->rdArr[$indexRd]['job_link'] = $this->jobsArr[$index]['job_link'];
                $indexRd++;
            }
        }

        file_put_contents('./rd_jobs.json', json_encode($rdArr));
    }

    private function requestInterface($url)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $url, ['verify' => false]);
            $contents = $response->getBody()->getContents();
        } catch (ClientException $e) {
            return false;
        }

        return $contents;
    }

}
