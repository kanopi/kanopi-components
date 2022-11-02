# Kanopi Utilities

PHP library to provide common model and service patterns for building other applications


## Documentation Reference

* [Structure](#structure)

## Structure

All structures are stored under the PSR-4 namespace `Kanopi\Utilities`.

### Logger

**Namespace**: `Kanopi\Utilties\Logger`

`ILogger` is the interface to wrap and proxy different logging methods.

### Model

**Namespace**: `Kanopi\Utilties\Model`

Helper models for other Repositories and Services, provides patterns for Collections/Iterators 
with validity and Exceptions.

### Repositories

**Namespace**: `Kanopi\Utilties\Repositories`

Interfaces in front of direct I/O operations in the concrete classes. This namespace
is intended to pattern interactions with direct sources like databases, files, etc.  

This, along with Dependency Injection, allows mocking other tests, for instance of services,
by providing a mock data repository backed by an array/iterator to the other service.

There are concrete implementations of WP_Query and the WP Post Meta for `ISetReader`.

### Services

**Namespace**: `Kanopi\Utilties\Services`

Interfaces to coordinate data processing from external sources into an local system resource. 
