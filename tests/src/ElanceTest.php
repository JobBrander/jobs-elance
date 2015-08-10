<?php namespace JobBrander\Jobs\Client\Providers\Test;

use DateTime;
use JobBrander\Jobs\Client\Job;
use JobBrander\Jobs\Client\Providers\Elance;
use Mockery as m;

class ElanceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->params = [
            'token' => 'mock_token',
        ];

        $this->client = new Elance($this->params);
    }

    public function testClientUsesListingsPath()
    {
        $listingsPath = $this->client->getListingsPath();

        $this->assertEquals('data.pageResults', $listingsPath);
    }

    public function testClientUsesJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testClientUsesGetMethod()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testUrlContainsTokenWhenProvided()
    {
        $url = $this->client->getUrl();

        $this->assertContains('access_token='.$this->params['token'], $url);
    }

    public function testUrlContainsSearchParametersWhenProvided()
    {
        $client = new \ReflectionClass(Elance::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        $searchParameters = array_values($searchMap);
        $params = [];

        array_map(function ($item) use (&$params) {
            $params[$item] = uniqid();
        }, $searchParameters);

        $newClient = new Elance(array_merge($this->params, $params));

        $url = $newClient->getUrl();

        array_walk($params, function ($v, $k) use ($url) {
            $this->assertContains($k.'='.$v, $url);
        });
    }

    public function testUrlContainsSearchParametersWhenSet()
    {
        $client = new \ReflectionClass(Elance::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        array_walk($searchMap, function ($v, $k) {
            $value = uniqid();
            $url = $this->client->$k($value)->getUrl();

            $this->assertContains($v.'='.$value, $url);
        });
    }

    public function testCreateJobObject()
    {
        $json = $this->getListingJson();
        $payload = json_decode($json, true);

        $job = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($payload['jobId'], $job->getSourceId());
        $this->assertEquals($payload['name'], $job->getName());
        $this->assertEquals($payload['description'], $job->getDescription());
        $this->assertEquals(new DateTime(date('Y-m-d H:i:s', $payload['postedDate']/1000)), $job->getDatePosted());
        $this->assertEquals($payload['category'], $job->getOccupationalCategory());
        $this->assertEquals($payload['clientName'], $job->getCompanyName());
        $this->assertEquals($payload['clientImageURL'], $job->getCompanyLogo());
    }

    protected function getListingJson()
    {
        return '{
            "jobId": "35031200",
            "name": "Scripting work - SKEW language",
            "description": "Need a programmer who knows the SKEW language backwards and forwards for a new gaming project. 5+ years experience with SKEW a must. In-depth knowledge of the TILT version control system a huge plus.",
            "budget": "Less than $500",
            "budgetMin": 20,
            "budgetMax": 499,
            "numProposals": 0,
            "postedDate": 1352352832,
            "startDate": 1352352832,
            "endDate": 1357536832,
            "clientUserId": "1787882",
            "clientUserName": "Dynos Systems, Inc.",
            "clientName": "DynosSystemsInc",
            "clientImageURL": "https://ws.elance.com/media/images/4.0/no-photo-64x80.jpg",
            "clientCountry": "United States",
            "clientCountryCode": "US",
            "clientRating": 0,
            "isLowAwardRatio": false,
            "isHourly": 0,
            "hourlyRateMin": 0,
            "hourlyRateMax": 0,
            "isFeatured": false,
            "jobCatId": 10183,
            "category": "IT & Programming",
            "subcategory": "Other IT & Programming",
            "keywords": "MySQL  JavaScript  PHP  SKEW",
            "timeLeft": "Closed",
            "jobURL": "https://www.elance.com/j/scripting-work-skew-language/35031200/"
         }';
    }
}
