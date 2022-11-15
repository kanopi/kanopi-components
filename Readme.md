# Kanopi Components Library

PHP library to provide common model and service patterns for building other applications


## Documentation Reference

* [Structure](#structure)
* [Code Quality]()

## Structure

All structures are stored under the PSR-4 namespace `Kanopi\Components`.

### Logger

**Namespace**: `Kanopi\Components\Logger`

`ILogger` is the interface to wrap and proxy different logging methods.

### Model

**Namespace**: `Kanopi\Components\Model`

Data structures used by functional components, like Repositories and Services, provide patterns for 
Collections/Iterators with validity, Data Transformation, and Exceptions.

### Processor

**Namespace**: `Kanopi\Components\Processor`

Direct component to model an entire Import, Export, or other process. This exists to 
coordinate the efforts of multiple Repositories, Services, and Transformers. It will contain
the business rules to govern how, when, and why data is transformed and stored in each 
Service or Repository component.

### Repositories

**Namespace**: `Kanopi\Components\Repositories`

Interfaces in front of direct I/O operations in the concrete classes. This namespace
is intended to pattern interactions with direct sources like databases, files, etc.  

This, along with Dependency Injection, allows mocking other tests, for instance of services,
by providing a mock data repository backed by an array/iterator to the other service.

There are concrete implementations of WP_Query and the WP Post Meta for `ISetReader`. Other interfaces,
like `ISetStream` and `IStreamReader` are intended to wrap the data interface with external sources like
CSV or JSON files.

### Services

**Namespace**: `Kanopi\Components\Services`

Interfaces to coordinate data processing from external sources into an local system resource. 

### Transformers

**Namespace**: `Kanopi\Components\Transformers`

Components which consolidate and simplify the transformation of standard/scalar data types into 
coordinated structures of data for more readable and concise functionality. For instance,
string utilities which sanitize or convert delimiters.

## Code Quality 

### PHPCS

This project offers a PHPCS ruleset extended form rules made for Automattic's hosting services. A `Makefile` 
coordinates execution of these tests. Results of each test are piped into files
labeled with the version in the format `results-{version}.txt`.

Run all production level tests (PHP 7.4 and 8.0):

```shell
make
```

Run a specific language versions tests:

```shell
make {version}
```

where `{version}` is php74, php80, or php81

Currently PHP 8.1 is not supported by WPCS, it is considered experimental. You can 
run against that language level using the language specific tag or run all versions as:

```shell
make test-experimental
```

