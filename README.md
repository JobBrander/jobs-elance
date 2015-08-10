# Elance Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-elance.svg?style=flat-square)](https://github.com/JobBrander/jobs-elance/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-elance/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-elance)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-elance.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-elance/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-elance.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-elance)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-elance.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-elance)

This package provides Elance Jobs API support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-elance
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Elance` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Elance([
    'token' => 'ELANCE ACCESS TOKEN',
]);

$jobs = $client->setKeyword('designer') // A space-delimited list of keywords to search. If omitted, search returns a list of all jobs.
    ->setCategory(1)               // A positive integer representing a category ID that restricts search results to jobs in the specified category. A list of valid category IDs and associated names is available through the categories method. If the value of catFilter does not correspond to an existing category ID, the method returns an empty result set.
    ->setSubCategory(1)            // A positive integer representing a subcategory ID that restricts search results to jobs in the specified subcategory. A list of valid subcategory IDs and associated names is available through the categories method. If the value of subcatFilter does not correspond to an existing category ID, the method returns an empty result set.
    ->setType('startDate')         // The property by which to sort results. Valid values are: budget, numProposals, startDate, endDate
    ->setSort('asc')               // The sort order of the results (ascending or descending). If this value is specified without sortCol, it is ignored. Valid values are: asc, desc; The default is desc.
    ->setPage(2)                   // The requested page of result sets, numbered beginning from 1. Default is 1. If this number exceeds the value of the response property totalPages, the response will contain zero results.
    ->setCount(25)                 // The number of results requested per page. The default is 20. If more than 25 are requested, only 25 results are included in the response.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-elance/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/jobbrander/jobs-elance/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-elance/blob/master/LICENSE) for more information.
