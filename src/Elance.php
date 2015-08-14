<?php namespace JobBrander\Jobs\Client\Providers;

use DateTime;
use JobBrander\Jobs\Client\Job;

class Elance extends AbstractProvider
{
    /**
     * Access token
     *
     * @var string
     */
    protected $token;

    /**
     * Map of setter methods to search parameters
     *
     * @var array
     */
    protected $searchMap = [
        'setCategory' => 'catFilter',
        'setSubCategory' => 'subcatFilter',
        'setType' => 'sortCol',
        'setSort' => 'sortOrder',
        'setKeyword' => 'keywords',
        'setPage' => 'page',
        'setCount' => 'rpp',
    ];

    /**
     * Current search parameters
     *
     * @var array
     */
    protected $searchParameters = [
        'keywords' => null,
        'sortCol' => null,
        'sortOrder' => null,
        'page' => null,
        'catFilter'  => null,
        'subcatFilter' => null,
        'rpp' => null,
    ];

    /**
     * Create new authentic jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->searchMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->searchMap[$method]);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Returns the standardized job object.
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $job = new Job;

        $map = $this->getJobSetterMap();
        $payload = $this->parsePayloadDates($payload);

        array_walk($map, function ($path, $setter) use ($payload, &$job) {
            try {
                $value = static::getValue(explode('.', $path), $payload);
                $job->$setter($value);
            } catch (\OutOfRangeException $e) {
                // do nothing
            }
        });

        return $job;
    }

    /**
     * Get data format.
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Retrieves array that maps job setter methods to payload keys.
     *
     * @return array
     */
    protected function getJobSetterMap()
    {
        return [
            'setSourceId' => 'jobId',
            'setName' => 'name',
            'setDescription' => 'description',
            'setDatePosted' => 'postedDate',
            'setOccupationalCategory' => 'category',
            'setCompanyName' => 'clientName',
            'setCompanyLogo' => 'clientImageURL',
        ];
    }

    /**
     * Get listings path.
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'data.pageResults';
    }

    /**
     * Retrieves query string.
     *
     * @return string
     */
    protected function getQueryString()
    {
        $query = http_build_query($this->searchParameters);

        if ($query) {
            $query = '&' . $query;
        }

        return $query;
    }

    /**
     * Get url.
     *
     * @return  string
     */
    public function getUrl()
    {
        return 'https://api.elance.com/api2/jobs'.
            '?access_token='.$this->token.
            $this->getQueryString();
    }

    /**
     * Get http verb.
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }

    /**
     * Attempts to replace known dat evalues with DateTime object.
     *
     * @param  array   $payload
     *
     * @return array
     */
    protected function parsePayloadDates(array $payload)
    {
        $knownDates = ['postedDate','startDate','endDate'];

        array_map(function ($key) use (&$payload) {
            if (isset($payload[$key]) && is_numeric($payload[$key])) {
                $payload[$key] = new DateTime(date('Y-m-d H:i:s', $payload[$key]/1000));
            }
        }, $knownDates);

        return $payload;
    }

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return Elance
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->searchParameters)) {
            $this->searchParameters[$key] = $value;
        }

        return $this;
    }
}
