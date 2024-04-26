# Kanopi Components Library

PHP library to provide common model and service patterns for building other applications


## Documentation Reference

* [Definitions](#definitions)
* [Structure](#structure)
* [Code Quality](#code-quality)
* [How To]()

## Definitions

| Concept                  | Definition                                                                                                                                                                                                                                                                                           |
|:-------------------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Entity**               | Any structured model, class, interface, etc representing data flowing through the system. The model contains one or more data fields with free-form and indexed content. For instance, a Post Type or Node which represents a Location with a free-form Address and an indexed City/Region or State. |
| **Entity Iterator**      | PHP Iterator which takes a Class, Interface, or Scalar type and validates each member **Entity** is of the requested type                                                                                                                                                                            |
| **Indexed Entity**       | Any entity for a set using a numerical index, for instance, a Post Type in WordPress the numeric ID field.                                                                                                                                                                                           |
| **Indexed Group Entity** | A **set** of **Entities** associated with another **Indexed Entity** by a common index, for instance a City/Region taxonomy associated with a Location                                                                                                                                               |
| **Processor**            | Coordinate one or more **Services** or **Repositories** in a data process, for instance read a **Set** or **Entities** for a CSV to determine if there are **Entities** to create or update in the target system.                                                                                    |
| **Reader**               | Data repository in read-only mode.                                                                                                                                                                                                                                                                   |
| **Repository**           | Generic place to store data, could be a CSV file, HTTP/S Endpoint, Database, etc.                                                                                                                                                                                                                    |
| **Set**                  | A group of similar data, generally expected to be of the same **Entity** in this system, wrapped in an **Entity Iterator**                                                                                                                                                                           |
| **Service**              | Coordinate data management between one or more **Repositories** for a single **Entity** type, for instance coordinate data flow between the Post, Meta, and Taxonomy for a WordPress post type; or turn a CSV into a Post Type **Entity** for processing                                             |
| **Stream**               | Flow of data between **Repositories**, this system reads data from source **Repositories** and writes it to target **Repositories**                                                                                                                                                                  |
| **Writer**               | Data repository which allows reading/writing                                                                                                                                                                                                                                                         |


## Structure

All structures are stored under the PSR-4 namespace `Kanopi\Components`.

### Commands

**Namespace**: `Kanopi\Components\Commands`

Group of commonly used CLI Commands available to register and use on WordPress, and in the future Drupal. 

For WordPress, all commands are for WPCLI, and can be registered from this library by calling 
`Kanopi\Components\Commands\Registration::WPCLICommands()`.

| CLI Commands                          | Purpose                                                                |
|:--------------------------------------|:-----------------------------------------------------------------------|
| `wp kanopi-report-flex-content audit` | Report a WordPress sites ACF flexible content areas                    |
| `wp kanopi-report-post-type audit`    | Report a WordPress sites content in post types                         |
| `wp kanopi-report-post-type media`    | Report all referenced site media; show if each is in the Media Library |


### Logger

**Namespace**: `Kanopi\Components\Logger`

`ILogger` is the interface to wrap and proxy different logging methods. Use a Multiplex to log to more 
than one Logger target at once.

### Model

**Namespace**: `Kanopi\Components\Model`

Data structures used by functional components, like Repositories and Services, provide patterns for 
Collections/Iterators with validity, Data Transformation, and Exceptions.

Provides a set of Exception classes for standard interactions, please add custom exceptions or use 
this depending on your use case.


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
string utilities which sanitize or convert delimiters and array utilities for fluent, chained manipulation.

## Code Quality 

### PHPCS

This project offers a PHPCS ruleset extended form rules made for Automattic's WordPress hosting services,
including WPCS. A `Makefile` coordinates execution of these tests. Results of each test are piped into 
files labeled with the version in the format `phpcs-{version}.txt`.

Run all production level tests (PHP 8.0, 8.1, and 8.2):

```shell
make test
```

Run a specific language versions tests:

```shell
make {version}
```

where `{version}` is php80, php81, or php82

### PHPUnit

PHPUnit is implemented to run a suite of test across some current and future components. A `Makefile` 
coordinates execution of these tests. Results of each test are piped into files
labeled with the version in the format `phpunit-{version}.txt`.

Run all production level tests (PHP 8.0, 8.1, and 8.2):

```shell
make unit
```

Run a specific language versions tests:

```shell
make {version}
```

where `{version}` is unitphp80, unitphp81, or unitphp82

## Quality Notes

Both tests suites can be run using the shell command `make`.

## Testing without Make

If you are testing without Make available, you can run the tests directly with Composer installed with 
the target version of PHP. 

### Composer PHPCS/PHPCBF

```shell
composer -n phpcs -- --standard="{STANDARDS_FILE}" ./
composer -n phpcbf -- --standard="{STANDARDS_FILE}" ./
```

This runs using the version of PHP used by the CLI (outside of scope for these instructions)

Substitute the appropriate file name, from the project root directory for `{STANDARDS_FILE}`,
for instance `--standard="./.phpcs-8.2.xml.dist"` for PHP 8.2.

### Composer PHPUnit 

```shell
composer -n phpunit
```

This runs using the version of PHP used by the CLI (outside of scope for these instructions).


## How To

### WordPress: Use the Base Post Type Entity Model

The `Kanopi\Components\Model\Data\WordPress\BasePostType` abstract class is a consolidated helper class 
for many  common WordPress import situations. It will NOT support every use case, though is useful if 
your data follows the following requirements:

* Uses one or more scalar/simple meta fields
* Uses one or more **non-hierarchical** taxonomies
* Uses a cross-system Identifier which can be constructed/retrieved from any source/target repositories 
  to match data for updates

Implementation can follow this pattern, for instance a Location of post type `location`. The model 
has the following fields:

| Property                | Type          | Field Name     |
|-------------------------|---------------|----------------|
| Address                 | Meta Field    | `address`      |
| City                    | Taxonomy Term | `city`         |
| Cross-system Identifier | Meta Field    | `id`           |
| Modified Date           | Meta Field    | `modifiedDate` |
| Post Type               | Post Type     | `location`     | 

The model can be implemented using the following criteria: 

* Add scalar types of type string to your class for `address` and `city`
* Extend the class and implement `extraInsertFieldMapping`, `metaFieldMapping`, and `taxonomyTermMapping`, 
  which map the Meta and Taxonomy field names to the entity attributes. For instance, a Location with Address and City/Region:
    ```php
    class Location extends BasePostType implements IIndexedEntity {
        /**
         * @var string
         */
        public string $address = '';

        /**
         * @var string
         */
        public string $city = '';

        /**
         * @var string
         */
        public string $id = '';
  
        /**
         * @var string
         */
        public string $modifiedDate = '';
  
        /**
         * {@inheritDoc}
         */
        function entityName(): string {
            return 'location';
        }
        
        /**
         * {@inheritDoc}
         */
        function uniqueIdentifier(): string {
            return $this->id;
        }
    
        /**
         * {@inheritDoc}
         */
        function version(): string {
            return $this->modifiedDate;
        }
   
        /**
         * {@inheritDoc}
         */
         function extraInsertFieldMapping(): array {
            return [];
         }
        
         /**
          * {@inheritDoc}
          */
         function metaFieldMapping(): array {
            return [
                'address' => $this->address,
                'id' => $this->id,
                'modifiedDate' => $this->modifiedDate,
            ];
         }
  
         /**
          * {@inheritDoc}
          */
         function taxonomyTermMapping(): array {
            return [
                'city' => $this->city,
            ];
         }
    }
    ```
* Now, when using a service, like `BasePostTypeWriter`, the built-in implementation of `systemTransform` 
  returns an appropriate format for `wp_insert_post`



